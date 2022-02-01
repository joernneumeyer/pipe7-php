<?php

  use Neu\Pipe7\Filters;
  use Neu\Pipe7\KeyMappers;
  use Neu\Pipe7\Sources;
  use function Neu\Pipe7\pipe;

  it('should skip the first 5 items', function() {
    $skipped = pipe(Sources::range(30))->filter(Filters::skip(10))->toArray();
    $expected = pipe(Sources::range(30, 10))->mapKeys(KeyMappers::addOffset(10))->toArray();
    expect($skipped)->toMatchArray($expected);
  });
