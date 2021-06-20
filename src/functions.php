<?php

  namespace Neu\Pipe7;

  use Closure;
  use Iterator;

  /**
   * A small helper to create {@see CollectionPipe}s for varying iterable types.
   * @param array<mixed>|Iterator<mixed> $data The data source for the {@see CollectionPipe}.
   * @return CollectionPipe
   * @throws UnprocessableObject
   * @package Neu\Pipe7
   */
  function pipe($data): CollectionPipe {
    return CollectionPipe::from($data);
  }

  /**
   * A function that consumes a specified number of items from a given iterator.
   * If no number of items is specified, a function will be returned,
   * which accepts the number of items it shall return, until the iterator is invalid.
   * @param Iterator<mixed> $iterator
   * @param int|null $n
   * @return Closure|mixed
   * @package Neu\Pipe7
   */
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
