<?php

  use Neu\Pipe7\Filters;
  use function Neu\Pipe7\pipe;

  it('should work', function () {
    $data = [23, 54, 567, 45, 324, 234, 345, 456, 657, 78, 98, 789, 78, 76, 5, 5, 5, 6778, 78, 5643, 324];
    $expected = [4 => 324, 234, 345, 456, 657, 78, 98];
    $result = pipe($data)->filter(Filters::slice(4, 10))->toArray();
    expect($result)->toMatchArray($expected);
  });
