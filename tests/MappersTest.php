<?php

  use Neu\Pipe7\Mappers;
  use Neu\Pipe7\Sources;
  use function Neu\Pipe7\pipe;

  it('should convert the original values to strings', function () {
    $data   = [5, 8, 1];
    $mapped = pipe($data)->map(Mappers::toString())->toArray();
    expect($mapped)->toMatchArray(['5', '8', '1']);
  });

  it('should invoke the mapper as many times, as there are elements in the source collection', function () {
    $data     = ['bar', 'foo', 'World', 'John'];
    $counter  = 0;
    $expected = count($data);
    pipe($data)->map(function ($x) use (&$counter) {
      ++$counter;
      return $x;
    })->toArray();
    expect($counter)->toEqual($expected);
  });

  it('should join the two iterators', function () {
    $even     = Sources::range(10, 0, 2);
    $odd      = Sources::range(10, 1, 2);
    $result   = pipe($even)->map(Mappers::zip($odd))->toArray();
    $expected = [[0, 1], [2, 3], [4, 5], [6, 7], [8, 9]];
    expect($result)->toMatchArray($expected);
  });

  it('should throw, if the zip source is exhausted before the pipe source', function () {
    $even = Sources::range(10, 0, 2);
    $odd  = Sources::range(6, 1, 2);
    pipe($even)->map(Mappers::zip($odd))->toArray();
  })->throws(Exception::class);
