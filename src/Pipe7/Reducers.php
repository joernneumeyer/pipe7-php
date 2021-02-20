<?php

  namespace Neu\Pipe7;

  use Closure;
  use Symfony\Component\PropertyAccess\PropertyAccessor;

  final class Reducers {
    private static $_sum;
    private static $_average;
    private static $_product;

    /**
     * @param callable|null $selector
     * @return Closure
     */
    public static function sum(?callable $selector = null): Closure {
      if ($selector) {
        return function ($carry, $i) use ($selector) {
          return $carry + $selector($i);
        };
      }
      if (!self::$_sum) {
        self::$_sum = function ($carry, $i) {
          return $carry + $i;
        };
      }
      return self::$_sum;
    }

    private static function addNumberToAverage(float $average, float $newElement, int $index) {
      return ($average * $index + $newElement) / ($index + 1);
    }

    /**
     * @param callable|null $selector
     * @return Closure
     */
    public static function average(?callable $selector = null): Closure {
      if ($selector) {
        return function ($carry, $item, $index) use ($selector) {
          return self::addNumberToAverage($carry, $selector($item), $index);
        };
      }
      if (!self::$_average) {
        self::$_average = function ($carry, $item, $index) {
          return self::addNumberToAverage($carry, $item, $index);
        };
      }
      return self::$_average;
    }

    /**
     * @param callable|null $selector
     * @return Closure
     */
    public static function product(?callable $selector = null): Closure {
      if ($selector) {
        return function ($carry, $item) use ($selector) {
          return $carry * $selector($item);
        };
      }
      if (!self::$_product) {
        self::$_product = function ($carry, $i) {
          return $carry * $i;
        };
      }
      return self::$_product;
    }

    /**
     * @param callable $selector
     * @return Closure
     */
    public static function groupBy(callable $selector): Closure {
      return function(&$carry, $value, $originalKey) use ($selector) {
        $key = $selector($value, $originalKey);
        if (!isset($carry[$key])) {
          $carry[$key] = [];
        }
        $carry[$key][] = $value;
        return $carry;
      };
    }
  }
