<?php

  namespace Neu\Pipe7\Collections;

  use Exception;
  use Traversable;

  /**
   * @template T
   */
  class ArrayStack implements Stack {
    protected $data = [];
    protected $cursor = 0;
    protected $size = 0;

    public function getIterator() {
      return new \ArrayIterator($this->data);
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
        // TODO stack is empty
        return null;
      }
      $result = $this->data[$this->cursor];
      unset($this->data[$this->cursor]);
      return $result;
    }
  }
