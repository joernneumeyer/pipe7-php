<?php

  use Neu\Pipe7\Sources;
  use function Neu\Pipe7\take;

  it('should consume the first 10 elements of the generator', function () {
    $expected = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
    $result   = take(Sources::range(20), 10);
    expect($result)->toMatchArray($expected);
  });

  it('should return a function which fives back elements bit for bit', function () {
    $consumer = take(Sources::range(20));
    expect($consumer(2))->toMatchArray([0, 1]);
    expect($consumer(4))->toMatchArray([2, 3, 4, 5]);
  });
