<?php

  use function Neu\Pipe7\pipe;

  it('should limit the result to 5 elements', function() {
    $result = pipe(\Neu\Pipe7\Sources::range(50))->filter(\Neu\Pipe7\Filters::limit(5))->toArray();
    expect(count($result))->toEqual(5);
  });
