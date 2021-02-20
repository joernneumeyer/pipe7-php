<?php

  namespace Neu\Pipe7;

  use Closure;
  use Symfony\Component\PropertyAccess\PropertyAccessor;

  final class Mappers {
    private static $_identity;
    private static $_toString;

    /**
     * @return Closure
     */
    public static function identity(): Closure {
      if (!self::$_identity) {
        self::$_identity = function($x) { return $x; };
      }
      return self::$_identity;
    }

    /**
     * @return Closure
     */
    public static function toString(): Closure {
      if (!self::$_toString) {
        self::$_toString = function($x) { return (string)$x; };
      }
      return self::$_toString;
    }
  }
