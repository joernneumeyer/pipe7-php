<?php


  namespace Neu\Pipe7;


  trait StatefulOperatorStubs {
    public function __invoke(...$args) {
      return $this->apply(...$args);
    }

    public function rewind(): void {
    }

    function onPipeInvalid() {
    }
  }
