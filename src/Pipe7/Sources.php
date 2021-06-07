<?php

  namespace Neu\Pipe7;

  use Generator;

  final class Sources {
    /**
     * @return Generator<int>
     */
    public static function primes(): Generator {
      $primes = [];
      for ($i = 2; ; ++$i) {
        $isPrime = true;
        foreach ($primes as $p) {
          if ($i % $p === 0) {
            $isPrime = false;
            break;
          }
        }
        if ($isPrime) {
          $primes[] = $i;
          yield $i;
        }
      }
    }

    /**
     * @param int $n
     * @param int $offset
     * @param int $step
     * @return Generator<int>
     */
    public static function range(int $n, int $offset = 0, int $step = 1): Generator {
      if ($offset > $n) {
        $step = -abs($step);
      }
      for ($i = $offset; $i < $n; $i += $step) {
        yield $i;
      }
    }
  }
