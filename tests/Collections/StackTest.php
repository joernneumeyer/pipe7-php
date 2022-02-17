<?php

  use Neu\Pipe7\Collections\Stack;

  $classesToTest = [
    \Neu\Pipe7\Collections\ArrayStack::class
  ];

//  it('should add elements in proper order', function($stackName) {
//    /** @var Stack $stack */
//    $stack = new $stackName();
//  })->with($classesToTest);

  it('should work like a stack', function($stackName) {
    /** @var Stack $stack */
    $stack = new $stackName();
    $stack->push(2);
    $stack->push(5);
    $stack->push(7);
    $stack->push(1);
    $stack->push(5);
    $stack->push(6);
    expect($stack->pop())->toEqual(6);
    expect($stack->pop())->toEqual(5);
    expect($stack->pop())->toEqual(1);
    expect($stack)->toHaveCount(3);
    expect(iterator_to_array($stack->getIterator()))->toMatchArray([2, 5, 7]);
    $stack->push('hello');
    $stack->push('world');
    $stack->push(false);
    expect($stack)->toHaveCount(6);
    expect($stack->pop())->toEqual(false);
    expect($stack->pop())->toEqual('world');
    expect($stack->pop())->toEqual('hello');
    expect($stack)->toHaveCount(3);
  })->with($classesToTest);
