<?php


  namespace Neu\Pipe7\StatefulOperators;


  use Neu\Pipe7\StatefulOperator;
  use Neu\Pipe7\StatefulOperatorStubs;

  class Average implements StatefulOperator {
    use StatefulOperatorStubs;

    /** @var int */
    private $numberOfItems = 0;
    /** @var callable|null */
    private $selector;

    public function __construct(?callable $selector = null) {
      $this->selector = $selector;
    }

    public function apply(...$args) {
      $carry = $args[0];
      $item = $args[1];
      if ($this->selector) {
        $item = ($this->selector)($item);
      }
      $carry = ($carry * $this->numberOfItems + $item) / ($this->numberOfItems + 1);
      ++$this->numberOfItems;
      return $carry;
    }

    public function rewind(): void {
      $this->numberOfItems = 0;
    }
  }
