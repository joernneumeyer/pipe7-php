<?php

  namespace Neu\Pipe7\StatefulOperators;

  use Neu\Pipe7\CallableOperator;
  use Neu\Pipe7\CollectionPipe;
  use Neu\Pipe7\StatefulOperator;

  class Limit extends CallableOperator {
    /** @var int */
    private $i = 0;
    /** @var int */
    private $maxLength = 0;

    /**
     * @param int|null $value
     * @return Limit|int
     */
    public function maxLength(?int $value = null) {
      if (is_null($value)) {
        return $this->maxLength;
      } else {
        $this->maxLength = $value - 1;
        return $this;
      }
    }

//    public function minLength() {
//
//    }

    function apply(...$args) {
      /** @var CollectionPipe $pipe */
      $pipe = $args[2];
      $shallContinue = ++$this->i <= $this->maxLength;
      if (!$shallContinue) {
        $pipe->invalidate();
      }
      return $shallContinue;
    }

    function rewind(): void {
      $this->i = 0;
    }
  }
