<?php

  namespace Neu\Pipe7\Collections;

  use Countable;
  use IteratorAggregate;

  /**
   * @template T
   */
  interface Queue extends Countable, IteratorAggregate {
    /**
     * @param T $item
     * @return void
     */
    function put($item): void;

    /**
     * @return T
     */
    function pop();
  }
