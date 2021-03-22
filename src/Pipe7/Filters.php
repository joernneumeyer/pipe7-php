<?php

  namespace Neu\Pipe7;

  use Neu\Pipe7\StatefulOperators\Limit;

  /**
   * A collection of filtering operations, used to limit the number of elements in the next processing step.
   * @package Neu\Pipe7
   */
  final class Filters {

    /**
     * @param $options
     * @return Limit
     */
    public static function limit($count) {
      return (new Limit())->maxLength($count);
    }
  }
