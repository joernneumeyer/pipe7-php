<?php

  use Neu\Pipe7\Iterators\SequentialKeyArrayIterator;

  it('should return sequential array indicies, despite their actual indicies', function () {
    $arr = ['hello' => 'world', 6 => false, 5 => 5, 'John' => 'Doe'];
    $iter = new SequentialKeyArrayIterator($arr);
    $result = iterator_to_array($iter);
    expect($result)->toMatchArray(['world', false, 5, 'Doe']);
  });
