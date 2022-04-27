<?php

  namespace Neu\Pipe7\Source;

  use Iterator;

  /**
   * @package Neu\Source
   */
  class Combine implements Iterator {

    /**
     * @var Iterator[]
     */
    private $sources;

    /**
     * @var int
     */
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

    public function current(): array {
      return array_map(function (Iterator $i) {
        return $i->current();
      }, $this->sources);
    }

    public function next(): void {
      foreach ($this->sources as $s) {
        $s->next();
      }
      ++$this->key;
    }

    public function key(): int {
      return $this->key;
    }

    public function valid(): bool {
      return array_reduce($this->sources, function (bool $carry, Iterator $i) {
        return $carry && $i->valid();
      }, true);
    }

    public function rewind(): void {
      foreach ($this->sources as $s) {
        $s->rewind();
      }
      $this->key = 0;
    }
  }
