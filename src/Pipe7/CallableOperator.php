<?php

  namespace Neu\Pipe7;

  /**
   * A wrapper around the {@see StatefulOperator} interface, implementing the __invoke and rewind methods.
   * @package Neu\Pipe7
   */
  abstract class CallableOperator implements StatefulOperator {
    public function __invoke(...$args) {
      return $this->apply(...$args);
    }

    public function rewind(): void {
    }
  }
