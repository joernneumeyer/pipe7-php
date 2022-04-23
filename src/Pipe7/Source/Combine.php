<?php

  namespace Neu\Pipe7\Source;

  use Iterator;

  class Combine implements Iterator {
    private $sources;
    private $key = 0;
    /**
     * @param array<Iterator> $sources
     */
    public function __construct(array $sources) {
      foreach ($sources as $s) {
        if (!($s instanceof Iterator)) {
          throw new \InvalidArgumentException('Please provide an array that only consists of Iterators!');
        }
      }
      $this->sources = $sources;
    }

    public function current() {
      return array_map(function (Iterator $i) {
        return $i->current();
      }, $this->sources);
    }

    public function next() {
      foreach ($this->sources as $s) {
        $s->next();
      }
      ++$this->key;
    }

    public function key() {
      return $this->key;
    }

    public function valid(): bool {
      return array_reduce($this->sources, function (bool $carry, Iterator $i) {
        return $carry && $i->valid();
      }, true);
    }

    public function rewind() {
      foreach ($this->sources as $s) {
        $s->rewind();
      }
      $this->key = 0;
    }
  }