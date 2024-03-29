<?php

  namespace Neu\Pipe7\Iterators;

  use ArrayIterator;

  /**
   * @package Neu\Pipe7\Collections
   */
  class SequentialKeyArrayIterator extends ArrayIterator {
    /** @var int */
    private $currentIndex = 0;

    public function key(): int {
      return $this->currentIndex;
    }

    public function next(): void {
      parent::next();
      ++$this->currentIndex;
    }

    public function rewind(): void {
      parent::rewind();
      $this->currentIndex = 0;
    }
  }
