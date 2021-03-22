<?php

  namespace Neu\Pipe7\Iterators;

  use Iterator;

  /**
   * An iterator which iterates an array in reverse order.
   * @package Neu\Pipe7\Iterators
   */
  class ReverseArrayIterator implements Iterator {
    public const PRESERVE_KEYS = 1;

    private $source;
    private $keys;
    private $keysIndex;
    private $length;
    private $options;

    public function __construct(array $source, int $options = 0) {
      $this->source = $source;
      $this->keys = array_keys($source);
      $this->length = count($this->keys);
      $this->options = $options;
      $this->rewind();
    }

    /**
     * Returns the current element.
     * @return mixed
     */
    public function current() {
      return $this->source[$this->keys[$this->keysIndex]];
    }

    /**
     * Moves the internal pointer to the next element.
     */
    public function next() {
      --$this->keysIndex;
    }

    /**
     * Returns the current key.
     * @return string|int
     */
    public function key() {
      if ($this->options & self::PRESERVE_KEYS) {
        return $this->keys[$this->keysIndex];
      } else {
        return $this->length - ($this->keysIndex + 1);
      }
    }

    /**
     * Returns the iterators validity.
     * @return bool
     */
    public function valid() {
      return $this->keysIndex >= 0;
    }

    /**
     * Resets the iterator.
     */
    public function rewind() {
      $this->keysIndex = $this->length - 1;
    }
  }
