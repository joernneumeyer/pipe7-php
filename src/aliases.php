<?php

  use Neu\Pipe7\CollectionPipe;
  use Neu\Pipe7\UnprocessableObject;

  /**
   * A small helper to create {@see CollectionPipe}s for varying iterable types.
   * @param $data array|Iterator The data source for the {@see CollectionPipe}.
   * @return CollectionPipe
   * @throws UnprocessableObject
   */
  function pipe($data): CollectionPipe {
    return CollectionPipe::from($data);
  }
