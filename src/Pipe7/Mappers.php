<?php

  namespace Neu\Pipe7;

  use Closure;

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
  }
