<?php

  use Neu\Pipe7\Filters;
  use Neu\Pipe7\Sources;
  use function Neu\Pipe7\pipe;

  it('should limit the result to 5 elements', function () {
    $result = pipe(Sources::range(50))->filter(Filters::limit(5))->toArray();
    expect(count($result))->toEqual(5);
  });

  it('contains - should only return strings containing the specified string', function () {
    $data     = ['Hello', 'World', 'foo', 'bar', 'foobar', 'salut', 'mark'];
    $expected = [3 => 'bar', 4 => 'foobar', 6 => 'mark'];
    $result   = pipe($data)->filter(Filters::contains('ar'))->toArray();
    expect($result)->toMatchArray($expected);
  });

  it('contains - should only return arrays containing the specified item', function () {
    $data     = [[2, 3, 4], [7, 2, 6], [5, 5], [6], [3, 2, 6, 7]];
    $expected = [1 => [7, 2, 6], 3 => [6], 4 => [3, 2, 6, 7]];
    $result   = pipe($data)->filter(Filters::contains(6))->toArray();
    expect($result)->toMatchArray($expected);
  });

  it('contains - should throw if an unusable type shall be searched', function () {
    $data = [100, 324, 678];
    pipe($data)->filter(Filters::contains(1))->toArray();
  })->throws(InvalidArgumentException::class);

  it('startsWith - should only return string starting with the specified string', function () {
    $data     = ['a', 'funny', 'World', 'foo', 'bar', 'foobar', 'salut', 'mark'];
    $expected = [1 => 'funny', 3 => 'foo', 5 => 'foobar'];
    $result   = pipe($data)->filter(Filters::startsWith('f'))->toArray();
    expect($result)->toMatchArray($expected);
  });

  it('should invoke the predicate as many times, as there are elements in the source collection', function() {
    $data = ['bar', 'fooa', 'World', 'John'];
    $counter = 0;
    $expected = count($data);
    $result = pipe($data)->filter(function($x) use (&$counter) {
      ++$counter;
      return strlen($x) > 3;
    })->toArray(false);
    expect($result)->toMatchArray(array_slice($data, 1));
    expect($counter)->toEqual($expected);
  });

  it('startsWith - should only return arrays starting with the specified element', function () {
    $data     = [[4, 6], [7], [2, 6, 3], [4, 1, 2, 7], [5]];
    $expected = [0 => [4, 6], 3 => [4, 1, 2, 7]];
    $result   = pipe($data)->filter(Filters::startsWith(4))->toArray();
    expect($result)->toMatchArray($expected);
  });

  it('startsWith - should throw if an unusable type shall be searched', function () {
    $data = [100, 324, 678];
    pipe($data)->filter(Filters::startsWith(1))->toArray();
  })->throws(InvalidArgumentException::class);

  it('endsWith - should only return string starting with the specified string', function () {
    $data     = ['funny', 'World', 'foo', 'bar', 'foobar', 'salut', 'mark'];
    $expected = [3 => 'bar', 4 => 'foobar'];
    $result   = pipe($data)->filter(Filters::endsWith('bar'))->toArray();
    expect($result)->toMatchArray($expected);
  });

  it('endsWith - should only return arrays starting with the specified element', function () {
    $data     = [[4, 6], [7], [2, 6, 3], [4, 1, 2, 7], [5]];
    $expected = [1=>[7], 3 => [4, 1, 2, 7]];
    $result   = pipe($data)->filter(Filters::endsWith(7))->toArray();
    expect($result)->toMatchArray($expected);
  });

  it('endsWith - should throw if an unusable type shall be searched', function () {
    $data = [100, 324, 678];
    pipe($data)->filter(Filters::endsWith(1))->toArray();
  })->throws(InvalidArgumentException::class);

  test('skip - skips the first 3 elements', function() {
    $result = pipe([1,2,3,4,5])->filter(Filters::skip(3))->toArray();
    expect($result)->toMatchArray([3 => 4, 4 => 5]);
  });

  test('skip - throws if a number less than 1 is provided', function() {
    Filters::skip(0);
  })->throws(InvalidArgumentException::class);
