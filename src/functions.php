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
        for ($i = 0; $i < $n && $iterator->valid(); ++$i) {
          $result[] = $iterator->current();
          $iterator->next();
        }
        return $result;
      };
    } else {
      return take($iterator)($n);
    }
  }
