<?php

  use Neu\Pipe7\CollectionPipe;
  use Neu\Pipe7\UnprocessableObject;

  $data = require 'testData.php';

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

  it('should throw on an invalid construction input', function () use ($data) {
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
    function gen() {
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

  it('should return the first element', function() use ($data) {
    $first = pipe($data)->first();
    expect($first)->toEqual($data[0]);
  });

  it('should return the first element which matches the predicate', function() use ($data) {
    $first = pipe($data)->first(function(User $u) { return $u->getAge() < 35; });
    expect($first)->toEqual($data[1]);
  });

  it('should return the last element', function() use ($data) {
    $last = pipe($data)->last();
    expect($last)->toEqual($data[5]);
  });

  it('should return the last element which matches the predicate', function() use ($data) {
    $last = pipe($data)->last(function(User $u) { return $u->getAge() > 50; });
    expect($last)->toEqual($data[4]);
  });

  it('should return null, if no fitting first element can be found', function() use ($data) {
    $first = pipe($data)->first(function (User $u) { return $u->getAge() > 100; });
    expect($first)->toBeNull();
  });
