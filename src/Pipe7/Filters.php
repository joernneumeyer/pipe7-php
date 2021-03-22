<?php

  namespace Neu\Pipe7;

  use Neu\Pipe7\StatefulOperators\Limit;

  final class Filters {

    /**
     * @param $options
     * @return Limit
     */
    public static function limit($options) {
      return (new Limit)->maxLength($options['max'] ?? null);
    }
  }
