<?php

  use Neu\Pipe7\Mappers;
  use function Neu\Pipe7\pipe;

  it('should convert the original values to strings', function () {
    $data = [5,8,1];
    $mapped = pipe($data)->map(Mappers::toString())->toArray();
    expect($mapped)->toMatchArray(['5', '8', '1']);
  });
