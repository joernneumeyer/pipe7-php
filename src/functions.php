<?php

  namespace Neu\Pipe7;

  use Iterator;

  /**
   * A small helper to create {@see CollectionPipe}s for varying iterable types.
   * @param $data array|Iterator The data source for the {@see CollectionPipe}.
   * @return CollectionPipe
   * @throws UnprocessableObject
   * @package Neu\Pipe7
   */
  function pipe($data): CollectionPipe {
    return CollectionPipe::from($data);
  }

  function take(Iterator $iterator, ?int $n = null) {
    if ($n === null) {
      return function (int $n) use ($iterator) {
        $result = [];
        foreach ($iterator as $i) {
          if (--$n > -1) {
            $result[] = $i;
          } else {
            break;
          }
        }
        return $result;
      };
    } else {
      return take($iterator)($n);
    }
  }
