<?php

  namespace Neu\Pipe7;

  use Generator;
  use Iterator;
  use Neu\Pipe7\Source\Combine;
  use Neu\Pipe7\Source\CombineNull;

  /**
   * Class Sources
   * @package Neu\Pipe7
   */
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

    /**
     * Combines multiple Iterators into a new one.
     * The resulting Iterator is invalid, as soon as the first source Iterator is invalid.
     * @param array<Iterator> $sources
     * @return Combine
     */
    public static function combine(array $sources): Combine {
      return new Combine($sources);
    }

    /**
     * Combines multiple Iterators into a new one.
     * The resulting Iterator is invalid, as soon as all source Iterators are invalid.
     * Values for invalid source Iterators will be filled with null.
     * @param array $sources
     * @return CombineNull
     */
    public static function combineNull(array $sources): CombineNull {
      return new CombineNull($sources);
    }
  }
