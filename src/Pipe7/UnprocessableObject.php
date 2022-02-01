<?php

  namespace Neu\Pipe7;

  use RuntimeException;

  /**
   * Is thrown, if a {@see CollectionPipe} with an invalid data source has been created.
   * @package Neu\Pipe7
   */
  class UnprocessableObject extends RuntimeException {
  }
