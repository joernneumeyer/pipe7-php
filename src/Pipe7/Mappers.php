<?php

  namespace Neu\Pipe7;

  use Closure;
  use Iterator;
  use Neu\Pipe7\StatefulOperators\Zip;

  /**
   * A collection of helpers for common data mapping operations.
   * @package Neu\Pipe7
   */
  final class Mappers {
    /** @var Closure|null */
    private static $_toString;

    /**
     * Converts the input to a string.
     * @return Closure
     */
    public static function toString(): Closure {
      if (!self::$_toString) {
        self::$_toString = function($x) { return (string)$x; };
      }
      return self::$_toString;
    }

    /**
     * Joins elements coming from the previous pipe with elements coming from the supplied iterator.
     * The items from the pipe source will be located at index 0, and the items from the zip source will be located at index 1.
     * @param Iterator $iterator
     * @return StatefulOperator
     */
    public static function zip(Iterator $iterator): StatefulOperator {
      return new Zip($iterator);
    }
  }
