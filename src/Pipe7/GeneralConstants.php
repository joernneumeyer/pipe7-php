<?php


  namespace Neu\Pipe7;

  /**
   * Class GeneralConstants
   * @package Neu\Pipe7
   * @codeCoverageIgnore
   */
  final class GeneralConstants {
    public const SCALAR_TYPES = ['int', 'float', 'bool', 'string'];
    public const NON_CLASS_TYPES = [...self::SCALAR_TYPES, 'array', 'callable'];
  }
