<?php

  namespace Neu\Pipe7;

  use ArrayAccess;
  use Neu\Pipe7\Collections\DefaultArrayAccessImplementations;

  class ArrayPipe implements Pipe, ArrayAccess {
    use DefaultArrayAccessImplementations;

    public function __construct(array $source) {
      $this->data = $source;
    }

    /**
     * @param mixed $op
     */
    private static function isValidOperator($op): void {
      if (!(is_callable($op) || $op instanceof StatefulOperator)) {
        throw new InvalidOperator('Invalid operator supplied! Make sure to pass either a \'callable\' or an instance of \'' . StatefulOperator::class . '\'!');
      }
    }

    function invalidate(): void {
    }

    function toArray(): array {
      return $this->data;
    }

    public function reduce($reducer, $initial = null, bool $returnAsPipe = false) {
      self::isValidOperator($reducer);
      $intermediate = $initial;
      foreach ($this->data as $key => $value) {
        $intermediate = $reducer($intermediate, $value, $key, $this);
      }
      return $returnAsPipe ? new ArrayPipe($intermediate) : $intermediate;
    }

    public function mapKeys($transformer): Pipe {
      self::isValidOperator($transformer);
      $newSource = [];
      foreach ($this->data as $key => $value) {
        $newKey = $transformer($value, $key);
        $newSource[$newKey] = $value;
      }
      return new ArrayPipe($newSource);
    }

    public function filter($predicate): Pipe {
      self::isValidOperator($predicate);
      $newSource = [];
      foreach ($this->data as $key => $value) {
        if ($predicate($value, $key)) {
          $newSource[$key] = $value;
        }
      }
      return new ArrayPipe($newSource);
    }

    public function map($transformer): Pipe {
      self::isValidOperator($transformer);
      $newSource = [];
      foreach ($this->data as $key => $value) {
        $newSource[$key] = $transformer($value, $key, $this);
      }
      return new ArrayPipe($newSource);
    }

    public function forEach(callable $cb): void {
      foreach ($this->data as $key => $value) {
        $cb($value, $key);
      }
    }
  }
