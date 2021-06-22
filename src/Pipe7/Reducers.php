<?php

  namespace Neu\Pipe7;

  use Closure;
  use Neu\Pipe7\StatefulOperators\Average;

  /**
   * A collection of common reduction or special mapping operations.
   * @package Neu\Pipe7
   */
  final class Reducers {
    /** @var Closure|null */
    private static $_sum;
    /** @var StatefulOperator|null */
    private static $_average;
    /** @var Closure|null */
    private static $_product;

    /**
     * Calculates the sum of all incoming elements.
     * @param callable|null $selector A function to extract a value from the element which should currently be added to the sum.
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

    /**
     * Calculates the average of all incoming elements.
     * @param callable|null $selector A function to extract a value from the element which should currently be added to the average.
     * @return StatefulOperator
     */
    public static function average(?callable $selector = null): StatefulOperator {
      if ($selector) {
        return new Average($selector);
      }
      if (!self::$_average) {
        self::$_average = new Average();
      }
      return self::$_average;
    }

    /**
     * Calculates the product of all incoming elements.
     * @param callable|null $selector A function to extract a value from the element which should currently be added to the product.
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
     * Groups all incoming elements by a specified property.
     * @param callable $selector A function to extract the target property value.
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

    /**
     * Returns the first element available in the pipe.
     *
     * If a predicate is supplied, the first element matching that predicate will be returned.
     * @param callable|null $predicate A condition which has to be fulfilled by the element.
     * @return Closure
     */
    public static function first(?callable $predicate = null): Closure {
      return function($carry, $x, $key, CollectionPipe $pipe) use ($predicate) {
        if ($predicate === null) {
          $pipe->invalidate();
          return $x;
        } else if ($predicate($x, $key)) {
          $pipe->invalidate();
          return $x;
        }
      };
    }

    /**
     * A reducer to join a series of incoming strings, or objects representable as strings.
     * @param string $glue The character (group) used between the joined strings.
     * @return Closure
     */
    public static function join(string $glue = '') {
      return function($carry, $x) use ($glue) {
        return $carry ? $carry . $glue . $x : $x;
      };
    }
  }
