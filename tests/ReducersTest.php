<?php

  use Neu\Pipe7\Reducers;
  use function Neu\Pipe7\pipe;

  $data = require 'testData.php';

  it('should calculate the total age', function () use ($data) {
    $totalAge = pipe($data)->map(function (User $p) {
      return $p->getAge();
    })->reduce(Reducers::sum());
    expect($totalAge)->toEqual(270);
  });

  it('should also be able to sum on a specific field', function () use ($data) {
    $totalAge = pipe($data)->reduce(Reducers::sum(function (User $p) {
      return $p->getAge();
    }));
    expect($totalAge)->toEqual(270);
  });

  it('should calculate the average age', function () use ($data) {
    $averageAge = pipe($data)->map(function (User $p) {
      return $p->getAge();
    })->reduce(Reducers::average(), 0);
    expect($averageAge)->toEqual(45);
  });

  it('should be able to calculate the average on a specific field', function () use ($data) {
    $averageAge = pipe($data)->reduce(Reducers::average(function (User $p) {
      return $p->getAge();
    }), 0);
    expect($averageAge)->toEqual(45);
  });

  it('should calculate the product properly', function () use ($data) {
    $product = pipe($data)->map(function (User $p) {
      return $p->getId();
    })->reduce(Reducers::product(), 1);
    expect($product)->toEqual(2 * 3 * 4 * 5 * 6);
  });

  it('should calculate the product properly on a specific field', function () use ($data) {
    $product = pipe($data)->reduce(Reducers::product(function (User $p) {
      return $p->getId();
    }), 1);
    expect($product)->toEqual(2 * 3 * 4 * 5 * 6);
  });

  it('should be able to group items based on a specific field', function () {
    $data   = [];
    $colors = ['red', 'blue'];
    $sizes  = ['40', '42', '44'];
    foreach ($colors as $color) {
      foreach ($sizes as $size) {
        $data[] = ['color' => $color, 'size' => $size,];
      }
    }
    $grouped = pipe($data)->reduce(Reducers::groupBy(function ($x) {
      return $x['color'];
    }));
    expect($grouped)->toMatchArray(['red' => [['color' => 'red', 'size' => '40'], ['color' => 'red', 'size' => '42'],
      ['color' => 'red', 'size' => '44'],], 'blue' => [['color' => 'blue', 'size' => '40'],
      ['color' => 'blue', 'size' => '42'], ['color' => 'blue', 'size' => '44'],],
                                   ]);
  });

  it('should return the first element', function() {
    $data = [4,32,6];
    $first = pipe($data)->reduce(Reducers::first());
    expect($first)->toEqual(4);
  });

  it('should return the first element matching the predicate', function() {
    $data = [4,33,6];
    $first = pipe($data)->reduce(Reducers::first(function($x) { return $x % 2 !== 0; }));
    expect($first)->toEqual(33);
  });
