<?php

  use Neu\Pipe7\CollectionPipe;
  use Neu\Pipe7\InvalidOperator;
  use Neu\Pipe7\StatefulOperator;
  use Neu\Pipe7\StatefulOperatorStubs;
  use Neu\Pipe7\UnprocessableObject;
  use function Neu\Pipe7\pipe;

  $data = require 'testData.php';

  function addTwo(int $val): int {
    return $val + 2;
  }

  it('should map properly', function () use ($data) {
    $ages = CollectionPipe::from($data)->map(function ($x) {
      return $x->getAge();
    })->toArray();
    expect($ages)->toMatchArray([42, 33, 66, 45, 70, 14]);
  });

  it('should filter properly', function () use ($data) {
    $atLeastForty = pipe($data)->filter(function ($p) {
      return $p->getAge() >= 40;
    })->toArray();
    expect($atLeastForty)->toMatchArray([0 => $data[0], 2 => $data[2], 3 => $data[3], 4 => $data[4]]);
  });

  it('should reduce properly', function () use ($data) {
    $totalAge = pipe($data)->reduce(function ($carry, $p) {
      return $carry + $p->getAge();
    }, 0);
    expect($totalAge)->toEqual(270);
  });

  it('should throw on an invalid construction input', function () {
    /** @phpstan-ignore-next-line */
    pipe(45);
  })->throws(UnprocessableObject::class);

  it('shall return a reduced value as a new pipe, if it has been specified', function () use ($data) {
    $p = pipe($data)->reduce(function ($carry, $p) {
      $carry[] = $p->getUsername();
      return $carry;
    }, [], true);
    expect($p)->toBeInstanceOf(CollectionPipe::class);
    expect($p->toArray())->toMatchArray(['foobar', 'example', 'Matrixx', 'neo', 'rick', 'morty']);
  });

  it('should also work with a generator', function () {
    /**
     * @return Generator<int>
     */
    function gen(): Generator {
      yield 1;
      yield 2;
      yield 3;
    }

    $result = pipe(gen())->toArray();
    expect($result)->toMatchArray([1, 2, 3]);
  });

  it('should not affect the previous pipe', function () {
    $p = pipe([1, 2, 3]);
    $p->map(function ($x) {
      return $x * 2;
    })->toArray();
    $original = $p->toArray();
    expect($original)->toMatchArray([1, 2, 3]);
  });

  it('should always return new instances, when an operation is being queued', function () {
    $p = pipe([1, 2, 3]);
    $q = $p->map(function ($x) {
      return $x * 2;
    });
    expect($q)->not->toEqual($p);
  });

  it('should rewind stateful operators', function () {

    pipe([1, 2, 3])->filter(new class implements StatefulOperator {
      use StatefulOperatorStubs;

      public function apply(...$args) {
        return true;
      }

      public function rewind(): void {
        expect(true)->toBeTrue();
      }
    })->toArray();
  });

  it('should map keys properly', function () {
    $res = pipe([3, 4, 5])->mapKeys(function($k, $v) {return $k + $v; })->toArray();
    expect($res)->toMatchArray([3 => 3, 5 => 4, 7 => 5]);
  });

  it('should throw, if an invalid operator has been supplied', function () {
    /** @phpstan-ignore-next-line */
    pipe([1, 2, 3])->filter(4);
  })->throws(Exception::class);

  it('map - should throw on invalid operator', function () {
    pipe([])->map(new stdClass());
  })->throws(InvalidOperator::class);

  it('should iterate over each element', function() {
    $expected = [1,2,3];

    pipe([1,2,3])->forEach(function($e) use (&$expected) {
      expect($e)->toEqual(array_splice($expected, 0, 1)[0]);
    });
  });

  it('should work with an iterator aggregate', function() {
    $a = new class implements IteratorAggregate {
      public function getIterator(): ArrayIterator {
        return new ArrayIterator([2,4,6]);
      }
    };
    $result = pipe($a)->toArray();
    expect($result)->toMatchArray([2,4,6]);
  });

  it('should throw with an invalid iterator aggregate', function() {
    $a = new class implements IteratorAggregate {
      public function getIterator() {
        throw new Exception();
      }
    };
    expect(function () use ($a) { pipe($a); })->toThrow(UnprocessableObject::class);
  });

  it('should convert a callable to a Closure', function () {
    $result = pipe([1,2,3])->map('addTwo')->toArray();
    expect($result)->toMatchArray([3,4,5]);
  });
