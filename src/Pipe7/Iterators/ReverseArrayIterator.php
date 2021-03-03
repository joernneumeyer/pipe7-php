<?php

  namespace Neu\Pipe7\Iterators;

  use Iterator;

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

    public function current() {
      return $this->source[$this->keys[$this->keysIndex]];
    }

    public function next() {
      --$this->keysIndex;
    }

    public function key() {
      if ($this->options & self::PRESERVE_KEYS) {
        return $this->keys[$this->keysIndex];
      } else {
        return $this->length - ($this->keysIndex + 1);
      }
    }

    public function valid() {
      return $this->keysIndex >= 0;
    }

    public function rewind() {
      $this->keysIndex = $this->length - 1;
    }
  }
