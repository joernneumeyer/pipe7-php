<?php


  namespace Neu\Pipe7;


  trait StatefulOperatorStubs {
    /**
     * @param array<mixed> ...$args
     * @return mixed
     */
    public function __invoke(...$args) {
      return $this->apply(...$args);
    }

    public function rewind(): void {
    }
  }
