<?php

  use Neu\Pipe7\KeyMappers;
  use function Neu\Pipe7\pipe;

  it('should reset the array keys', function () {
    $arr = pipe([2 => 3, 7 => 4, 9 => 5])->mapKeys(KeyMappers::reset())->toArray();
    expect($arr)->toMatchArray([3, 4, 5]);
  });

  it('should add an offset', function () {
    $arr = pipe([2 => 3, 7 => 4, 9 => 5])->mapKeys(KeyMappers::addOffset(2))->toArray();
    expect($arr)->toMatchArray([4 => 3, 9 => 4, 11 => 5]);
  });
