<?php

  namespace Neu\Pipe7\StatefulOperators;

  use Neu\Pipe7\StatefulOperator;

  /**
   * @package Neu\Pipe7\StatefulOperators
   */
  class Slice implements StatefulOperator {
    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $end;

    /**
     * @var int
     */
    private $currentIndex = 0;

    public function __construct(int $offset, int $end) {
      $this->offset = $offset;
      $this->end = $end;
    }

    function rewind(): void {
      $this->currentIndex = 0;
    }

    function apply(...$args) {
      $res = $this->currentIndex >= $this->offset && $this->currentIndex <= $this->end;
      ++$this->currentIndex;
      return $res;
    }
  }
