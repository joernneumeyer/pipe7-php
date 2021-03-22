<?php

  use function Neu\Pipe7\pipe;

  it('should generate proper primes', function() {
    $correct = [2,3,5,7,11,13,17,19,23,29];
    $calculated = pipe(\Neu\Pipe7\Sources::primes())->filter(\Neu\Pipe7\Filters::limit(10))->toArray();
    expect($calculated)->toMatchArray($correct);
  });
