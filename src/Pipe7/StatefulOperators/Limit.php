<?php

  namespace Neu\Pipe7\StatefulOperators;

  use Neu\Pipe7\CollectionPipe;
  use Neu\Pipe7\StatefulOperator;
  use Neu\Pipe7\StatefulOperatorStubs;

  class Limit implements StatefulOperator {
    use StatefulOperatorStubs;

    /** @var int */
    private $i = 0;
    /** @var int */
    private $maxLength;

    /**
     * Limits the number of items emitted my the {@see CollectionPipe}.
     * @param int $maxLength The number of items which shall be passed to the next pipe.
     */
    public function __construct(int $maxLength) {
      $this->maxLength = $maxLength - 1;
    }

    function apply(...$args) {
      $shallContinue = ++$this->i <= $this->maxLength;
      if (!$shallContinue) {
        /** @var CollectionPipe $pipe */
        $pipe = $args[2];
        $pipe->invalidate();
      }
      return $shallContinue;
    }

    function rewind(): void {
      $this->i = 0;
    }
  }
