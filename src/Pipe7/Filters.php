<?php

  namespace Neu\Pipe7;

  use Closure;
  use Neu\Pipe7\StatefulOperators\Limit;
  use Neu\Pipe7\StatefulOperators\Skip;
  use Neu\Pipe7\StatefulOperators\Slice;

  /**
   * A collection of filtering operations, used to limit the number of elements in the next processing step.
   * @package Neu\Pipe7
   */
  final class Filters {

    /**
     * Limits the amount of elements emitted after the filter to the number of elements specified.
     * @param int $count
     * @return Limit
     */
    public static function limit(int $count): Limit {
      return new Limit($count);
    }

    /**
     * @param int $count
     * @return Skip
     */
    public static function skip(int $count): Skip {
      return new Skip($count);
    }

    /**
     * Checks if an array or string contains the specified needle.
     * @param mixed $needle
     * @return Closure
     */
    public static function contains($needle): Closure {
      return function($value) use ($needle) {
        if (is_array($value)) {
          return in_array($needle, $value);
        } else if (is_string($needle)) {
          return strpos($value, $needle) !== false;
        } else {
          throw new \InvalidArgumentException('Cannot check if type "' . gettype($value) . '" contains a needle!');
        }
      };
    }

    /**
     * Checks if an array or string starts with the specified needle.
     *
     * If the value is an array, it is expected to have numerical indices, starting with 0.
     * @param mixed $needle
     * @return Closure
     */
    public static function startsWith($needle): Closure {
      return function($value) use ($needle) {
        if (is_array($value)) {
          return $value[0] === $needle;
        } else if (is_string($needle)) {
          return strpos($value, $needle) === 0;
        }
        else {
          $value_type = is_object($value) ? get_class($value) : gettype($value);
          throw new \InvalidArgumentException('Cannot check if type "' . $value_type . '" contains a needle!');
        }
      };
    }

    /**
     * Checks if an array or string ends with the specified needle.
     *
     * If the value is an array, it is expected to have numerical indices, starting with 0.
     * @param mixed $needle
     * @return Closure
     */
    public static function endsWith($needle) {
      return function($value) use ($needle) {
        if (is_array($value)) {
          return $value[count($value) - 1] === $needle;
        } else if (is_string($needle)) {
          $valueLength = strlen($value);
          $needleLength = strlen($needle);
          $needleIndex = strpos($value, $needle);
          return $valueLength - $needleLength === $needleIndex;
        }
        else {
          $value_type = is_object($value) ? get_class($value) : gettype($value);
          throw new \InvalidArgumentException('Cannot check if type "' . $value_type . '" contains a needle!');
        }
      };
    }

    public static function slice(int $offset, int $end) {
      return new Slice($offset, $end);
    }
  }
