<?php

  namespace Neu\Pipe7\Collections;

  use ArrayIterator;

  /**
   * @template T
   * @implements Stack<T>
   */
  class ArrayStack implements Stack {
    /** @var array<T> */
    protected $data = [];
    /** @var int */
    protected $cursor = 0;
    /** @var int */
    protected $size = 0;

    public function getIterator(): \Iterator {
      return new ArrayIterator($this->data);
    }

    public function count(): int {
      if ($this->size === 0) return 0;
      return $this->cursor;
    }

    function push($item): void {
      if ($this->cursor === $this->size) {
        $this->cursor = ++$this->size;
        $this->data[] = $item;
      } else {
        $this->data[$this->cursor++] = $item;
      }
    }

    function pop() {
      if (--$this->cursor < 0) {
        throw new \RuntimeException('Cannot pop items from an empty stack!');
      }
      $result = $this->data[$this->cursor];
      unset($this->data[$this->cursor]);
      return $result;
    }
  }
