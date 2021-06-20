<?php

  use Neu\Pipe7\Mappers;
  use function Neu\Pipe7\pipe;

  it('should convert the original values to strings', function () {
    $data = [5,8,1];
    $mapped = pipe($data)->map(Mappers::toString())->toArray();
    expect($mapped)->toMatchArray(['5', '8', '1']);
  });

  it('should invoke the mapper as many times, as there are elements in the source collection', function() {
    $data = ['bar', 'foo', 'World', 'John'];
    $counter = 0;
    $expected = count($data);
    pipe($data)->map(function($x) use (&$counter) {
      ++$counter;
      return $x;
    })->toArray();
    expect($counter)->toEqual($expected);
  });
