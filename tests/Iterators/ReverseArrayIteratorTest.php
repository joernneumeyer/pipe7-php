<?php

  use Neu\Pipe7\Iterators\ReverseArrayIterator;

  function pr($v) {
    $ob = ob_get_contents();
    ob_end_clean();
    echo $v;
    ob_start();
    echo $ob;
  }

  $sampleTestSet = [
    [
      [1, 2, 3],
      [3, 2, 1],
    ],
    [
      ['foo', 4, 'bar', 'baz', 42],
      [42, 'baz', 'bar', 4, 'foo'],
    ],
    [
      ['John', false, null, 'v'],
      ['v', null, false, 'John'],
    ],
  ];

  it('should return the array elements in reverse order', function ($input, $result) {
    $iter = new ReverseArrayIterator($input);
    $arr  = iterator_to_array($iter);
    expect($arr)->toMatchArray($result);
  })->with($sampleTestSet);

  it('should return the array elements in reverse order with their original keys', function ($input, $result) {
    $iter   = new ReverseArrayIterator($input, ReverseArrayIterator::PRESERVE_KEYS);
    $arr    = iterator_to_array($iter);
    $result = array_combine(array_reverse(array_keys($input)), $result);
    expect($arr)->toMatchArray($result);
  })->with($sampleTestSet);
