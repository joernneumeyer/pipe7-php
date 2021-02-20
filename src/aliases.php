<?php

  use Neu\Pipe7\CollectionPipe;
  use Neu\Pipe7\UnprocessableObject;

  if (!function_exists('pipe')) {
    /**
     * A small helper to create {@see DataPipe}s for varying iterable types.
     * @param $data array|Iterator
     * @return CollectionPipe
     * @throws UnprocessableObject
     */
    function pipe($data): CollectionPipe {
      return CollectionPipe::from($data);
    }
  }
