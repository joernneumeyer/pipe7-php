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
