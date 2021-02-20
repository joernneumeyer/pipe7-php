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
    expect($atLeastForty)->toMatchArray([0 => $data[0], 2 => $data[2], 3 => $data[3],
                                          4 => $data[4]]);
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
    $p = pipe($data)->reduce(function($carry, $p) { $carry[] = $p->getUsername(); return $carry; }, [], true);
    expect($p)->toBeInstanceOf(CollectionPipe::class);
    expect($p->toArray())->toMatchArray(['foobar', 'example', 'Matrixx', 'neo', 'rick', 'morty']);
  });
