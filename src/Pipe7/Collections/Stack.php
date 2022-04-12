<?php

  namespace Neu\Pipe7\Collections;

  use Countable;
  use IteratorAggregate;

  /**
   * @package Neu\Pipe7\Collections
   * @template T
   */
  interface Stack extends Countable, IteratorAggregate {
    /**
     * @param T $item
     * @return void
     */
    function push($item): void;

    /**
     * @return T
     */
    function pop();
  }
