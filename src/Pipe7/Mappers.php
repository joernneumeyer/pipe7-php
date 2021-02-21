<?php

  namespace Neu\Pipe7;

  use Closure;

  /**
   * A collection of helpers for common data mapping operations.
   * @package Neu\Pipe7
   */
  final class Mappers {
    private static $_identity;
    private static $_toString;

    /**
     * Returns the input value.
     * @return Closure
     */
    public static function identity(): Closure {
      if (!self::$_identity) {
        self::$_identity = function($x) { return $x; };
      }
      return self::$_identity;
    }

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
