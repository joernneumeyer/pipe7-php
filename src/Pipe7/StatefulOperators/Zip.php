<?php

  namespace Neu\Pipe7\StatefulOperators;

  use Iterator;
  use Neu\Pipe7\StatefulOperator;
  use Neu\Pipe7\StatefulOperatorStubs;

  /**
   * Class Zip
   * @package Neu\Pipe7\StatefulOperators
   */
  class Zip implements StatefulOperator {
    use StatefulOperatorStubs;

    /** @var Iterator */
    private $zipSource;

    /**
     * Zip constructor.
     * @param Iterator $iterator
     */
    public function __construct(Iterator $iterator) {
      $this->zipSource = $iterator;
    }

    public function apply(...$args) {
      if (!$this->zipSource->valid()) {
        throw new \Exception("Zip iterator is already invalid!");
      }
      $result = [$args[0], $this->zipSource->current()];
      $this->zipSource->next();
      return $result;
    }

    public function rewind(): void {
      $this->zipSource->rewind();
    }
  }
