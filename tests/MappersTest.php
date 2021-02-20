<?php

  use Neu\Pipe7\Mappers;

  it('just returns the value which is passed in', function () {
    $data   = [4, 8, 1, 9, 322];
    $mapped = pipe($data)->map(Mappers::identity())->toArray();
    expect($mapped)->toMatchArray($data);
  });

  it('should convert the original values to strings', function () {
    $data = [5,8,1];
    $mapped = pipe($data)->map(Mappers::toString())->toArray();
    expect($mapped)->toMatchArray(['5', '8', '1']);
  });
