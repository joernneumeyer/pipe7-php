<?php

  namespace Neu\Pipe7;

  /**
   * An operator which needs to maintain some internal state in order to perform its function.
   * @package Neu\Pipe7
   */
  interface StatefulOperator {
    /**
     * This method is invoked, when the entire {@see CollectionPipe} is rewound.
     */
    function rewind(): void;

    /**
     * This method shall contain the logic, which would usually be contained in the callback of one of the transform methods.
     * @param mixed ...$args
     * @return mixed
     */
    function apply(...$args);
  }
