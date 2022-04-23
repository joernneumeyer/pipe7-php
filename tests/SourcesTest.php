<?php

  use Neu\Pipe7\Sources;
  use function Neu\Pipe7\pipe;

  it('should generate proper primes', function () {
    $correct    = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29];
    $calculated = pipe(Sources::primes())->filter(\Neu\Pipe7\Filters::limit(10))->toArray();
    expect($calculated)->toMatchArray($correct);
  });

  it('should combine properly', function () {
    $a        = new ArrayIterator([1, 2, 3]);
    $b        = new ArrayIterator([2, 3, 4]);
    $expected = [[1, 2], [2, 3], [3, 4]];
    $combined = Sources::combine([$a, $b]);
    $result   = iterator_to_array($combined);
    expect($result)->toMatchArray($expected);
  });

  it('should throw, if an invalid sources array is provided', function () {
    $a = new ArrayIterator([1, 2, 3]);
    $b = 24;
    expect(function () use ($a, $b) {
      Sources::combine([$a, $b]);
    })->toThrow(InvalidArgumentException::class);
  });

  it('should stop as soon as the first source is invalid', function () {
    $a        = new ArrayIterator(['hello', 2, 3]);
    $b        = new ArrayIterator([2, 'world', 4, 'foo']);
    $expected = [['hello', 2], [2, 'world'], [3, 4]];
    $combined = Sources::combine([$a, $b]);
    $result   = iterator_to_array($combined);
    expect($result)->toMatchArray($expected);
  });
