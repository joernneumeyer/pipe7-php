<?php

  namespace Neu\Pipe7;

  use Closure;

  final class KeyMappers {
    public static function reset(): Closure {
      $i = 0;
      return function () use (&$i) {
        return $i++;
      };
    }

    public static function addOffset(int $offset): Closure {
      return function (int $k) use ($offset) {
        return $k + $offset;
      };
    }
  }
