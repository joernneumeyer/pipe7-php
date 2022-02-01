<?php

  namespace Neu\Pipe7\StatefulOperators;

  use InvalidArgumentException;
  use Neu\Pipe7\StatefulOperator;

  class Skip implements StatefulOperator {
    private $nrOfItemsChecked = 0;
    private $nrOfItemsToSkip;

    public function __construct(int $nrOfItemsToSkip) {
      if ($nrOfItemsToSkip < 1) {
        throw new InvalidArgumentException('The number of items to skip must be at least 1!');
      }
      $this->nrOfItemsToSkip = $nrOfItemsToSkip;
    }

    function rewind(): void {
      $this->nrOfItemsChecked = 0;
    }

    function apply(...$args) {
      ++$this->nrOfItemsChecked;
      return $this->nrOfItemsChecked >= $this->nrOfItemsToSkip;
    }
  }
