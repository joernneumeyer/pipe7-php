<?php


  use Neu\Pipe7\Collections\Stack;

  $classesToTest = [
    \Neu\Pipe7\Collections\ArrayQueue::class
  ];

//  it('should add elements in proper order', function($stackName) {
//    /** @var Stack $stack */
//    $stack = new $stackName();
//  })->with($classesToTest);

  it('should work like a queue', function ($queueName) {
    /** @var \Neu\Pipe7\Collections\Queue $queue */
    $queue = new $queueName();
    $queue->put(2);
    $queue->put(6);
    $queue->put(3);
    $queue->put(9);
    expect($queue)->toHaveCount(4);
    expect(iterator_to_array($queue->getIterator()))->toMatchArray([2, 6, 3, 9]);
    expect($queue->pop())->toEqual(2);
    expect($queue->pop())->toEqual(6);
    expect($queue->pop())->toEqual(3);
    expect($queue)->toHaveCount(1);
    $queue->put('world');
    $queue->put(42);
    $queue->put('Hello');
    expect($queue)->toHaveCount(4);
    expect(iterator_to_array($queue->getIterator()))->toMatchArray([9, 'world', 42, 'Hello']);
  })->with($classesToTest);
