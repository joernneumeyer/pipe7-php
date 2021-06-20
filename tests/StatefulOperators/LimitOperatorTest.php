<?php

  use Neu\Pipe7\Filters;
  use Neu\Pipe7\Sources;
  use function Neu\Pipe7\pipe;

  it('should limit the supplied primes to 10', function() {
    $p = pipe(Sources::primes())->filter(Filters::limit(10))->toArray();
    $firstPrimes = [2,3,5,7,11,13,17,19,23,29];
    expect($p)->toMatchArray($firstPrimes);
  });
