<?php


  use Neu\Pipe7\ArrayPipe;
  use Neu\Pipe7\CollectionPipe;
  use Neu\Pipe7\InvalidOperator;
  use Neu\Pipe7\StatefulOperator;
  use Neu\Pipe7\StatefulOperatorStubs;
  use Neu\Pipe7\UnprocessableObject;
  use function Neu\Pipe7\arrayPipe;

  $data = require 'testData.php';

  function addThree(int $val): int {
    return $val + 3;
  }

  it('should map properly', function () use ($data) {
    $ages = arrayPipe($data)->map(function ($x) {
      return $x->getAge();
    })->toArray();
    expect($ages)->toMatchArray([42, 33, 66, 45, 70, 14]);
  });

  it('should filter properly', function () use ($data) {
    $atLeastForty = arrayPipe($data)->filter(function ($p) {
      return $p->getAge() >= 40;
    })->toArray();
    expect($atLeastForty)->toMatchArray([0 => $data[0], 2 => $data[2], 3 => $data[3], 4 => $data[4]]);
  });

  it('should reduce properly', function () use ($data) {
    $totalAge = arrayPipe($data)->reduce(function ($carry, $p) {
      return $carry + $p->getAge();
    }, 0);
    expect($totalAge)->toEqual(270);
  });

  it('shall return a reduced value as a new pipe, if it has been specified', function () use ($data) {
    $p = arrayPipe($data)->reduce(function ($carry, $p) {
      $carry[] = $p->getUsername();
      return $carry;
    }, [], true);
    expect($p)->toBeInstanceOf(ArrayPipe::class);
    expect($p->toArray())->toMatchArray(['foobar', 'example', 'Matrixx', 'neo', 'rick', 'morty']);
  });

  it('should not affect the previous pipe', function () {
    $p = arrayPipe([1, 2, 3]);
    $p->map(function ($x) {
      return $x * 2;
    })->toArray();
    $original = $p->toArray();
    expect($original)->toMatchArray([1, 2, 3]);
  });

  it('should always return new instances, when an operation is being queued', function () {
    $p = arrayPipe([1, 2, 3]);
    $q = $p->map(function ($x) {
      return $x * 2;
    });
    expect($q)->not->toEqual($p);
  });

  it('should map keys properly', function () {
    $res = arrayPipe([3, 4, 5])->mapKeys(function ($k, $v) {
      return $k + $v;
    })->toArray();
    expect($res)->toMatchArray([3 => 3, 5 => 4, 7 => 5]);
  });

  it('should throw, if an invalid operator has been supplied', function () {
    /** @phpstan-ignore-next-line */
    arrayPipe([1, 2, 3])->filter(4);
  })->throws(Exception::class);

  it('should iterate over each element', function () {
    $expected = [1, 2, 3];

    arrayPipe([1, 2, 3])->forEach(function ($e) use (&$expected) {
      expect($e)->toEqual(array_splice($expected, 0, 1)[0]);
    });
  });

  it('should convert a callable to a Closure', function () {
    $result = arrayPipe([1, 2, 3])->map('addThree')->toArray();
    expect($result)->toMatchArray([4, 5, 6]);
  });
