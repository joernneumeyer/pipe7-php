<?php

  namespace Neu\Pipe7;

  use RuntimeException;

  /**
   * Is thrown, if a {@see CollectionPipe} is about to be created, with an invalid processing operator.
   * @package Neu\Pipe7
   */
  class InvalidOperator extends RuntimeException {
  }
