<?php

  namespace Neu\Pipe7\Collections;


  use Iterator;
  use Neu\Pipe7\Iterators\SequentialKeyArrayIterator;

  /**
   * @package Neu\Pipe7\Collections
   * @template T
   * @implements Queue<T>
   */
  class ArrayQueue implements Queue {
    /** @var T[] */
    protected $data = [];
    /** @var int */
    protected $cursor = 0;
    /** @var int */
    protected $count = 0;

    public function data(): array {
      return $this->data;
    }

    public function getIterator(): Iterator {
      return new SequentialKeyArrayIterator($this->data);
    }

    public function count(): int {
      return $this->count;
    }

    function put($item): void {
      $this->data[] = $item;
      ++$this->count;
    }

    function pop() {
      if ($this->count === 0) {
        throw new \RuntimeException('Cannot pop items from an empty queue!');
      }
      --$this->count;
      $result = $this->data[$this->cursor];
      unset($this->data[$this->cursor]);
      ++$this->cursor;
      return $result;
    }
  }
