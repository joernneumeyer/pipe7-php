<?php

  namespace Neu\Pipe7;

  use Closure;
  use Iterator;

  /**
   * An iterable data processing unit.
   * @package Neu\Pipe7
   */
  class CollectionPipe implements Iterator {
    private $sourceIterator;
    private $op;
    /** @var Closure|StatefulOperator|null */
    private $cb;
    private $isValid = true;

    private const OP_NONE   = 0;
    private const OP_MAP    = 1;
    private const OP_FILTER = 2;

    /**
     * CollectionPipe constructor.
     * @param $collection Iterator|array
     * @param int $op
     * @param StatefulOperator|Closure|null $cb
     * @throws UnprocessableObject
     */
    private function __construct($collection, int $op = self::OP_NONE, $cb = null) {
      if (is_array($collection)) {
        $this->sourceIterator = new \ArrayIterator($collection);
      } else {
        if (is_a($collection, Iterator::class)) {
          $this->sourceIterator = $collection;
        } else {
          throw new UnprocessableObject();
        }
      }
      $this->op = $op;
      $this->cb = $cb;
      if ($cb) {
        self::isValidOperator($cb);
      }
    }

    /**
     * Creates a new CollectionPipe, which transforms each element with the supplied mapper, when it is traversed.
     *
     * $transformer signature: fn(mixed $currentItem, mixed $currentKey, CollectionPipe $pipeInstance) => mixed
     * @param StatefulOperator|Closure $transformer The transforming function to apply to each element.
     * @return CollectionPipe
     * @throws UnprocessableObject
     */
    public function map($transformer): CollectionPipe {
      self::isValidOperator($transformer);
      return new CollectionPipe($this, self::OP_MAP, $transformer);
    }

    /**
     * Creates a new CollectionPipe, which filters the elements available during traversal, based on the result of the supplied {@see $predicate}.
     *
     * $predicate signature: fn(mixed $currentItem, mixed $currentKey, CollectionPipe $pipeInstance) => bool
     * @param StatefulOperator|Closure $predicate The predicate to apply to an element, to check if it should be used.
     * @return CollectionPipe
     * @throws UnprocessableObject
     */
    public function filter($predicate): CollectionPipe {
      self::isValidOperator($predicate);
      return new CollectionPipe($this, self::OP_FILTER, $predicate);
    }

    /**
     *
     * @param StatefulOperator|Closure $reducer The function to apply.
     * @param mixed|null $initial
     * @param bool $returnAsPipe If {$returnAsPipe} is set to true, and the reduced value is a valid data source, this method returns a new CollectionPipe for the reduced value.
     * @return mixed|CollectionPipe
     * @throws UnprocessableObject
     */
    public function reduce($reducer, $initial = null, bool $returnAsPipe = false) {
      self::isValidOperator($reducer);
      $carry = $initial;
      foreach ($this as $key => $value) {
        $carry = $reducer($carry, $value, $key, $this);
      }
      if ($returnAsPipe) {
        return new CollectionPipe($carry);
      } else {
        return $carry;
      }
    }

    /**
     * Converts the CollectionPipe into an array.
     *
     * Array keys are preserved by default.
     * @param bool $preserveKeys Flag to determine, whether array keys should be preserved.
     * @return array
     */
    public function toArray(bool $preserveKeys = true): array {
      $arr = iterator_to_array($this);
      return $preserveKeys ? $arr : array_values($arr);
    }

    private static function isValidOperator($op): void {
      if (!(is_callable($op) || $op instanceof StatefulOperator)) {
        throw new \Exception('Invalid operator supplied! Make sure to pass either a \'callable\' or an instance of \'' . StatefulOperator::class . '\'!');
      }
    }

    /**
     * Factory method to create new CollectionPipe instances.
     * @param $collection array|Iterator The data source.
     * @return CollectionPipe
     * @throws UnprocessableObject
     */
    public static function from($collection): CollectionPipe {
      return new CollectionPipe($collection);
    }

    public function invalidate(): void {
      $this->isValid = false;
    }

    /**
     * {@inheritdoc}
     */
    public function current() {
      switch ($this->op) {
        case self::OP_MAP:
          $map = $this->cb instanceof StatefulOperator ? [$this->cb, 'apply'] : $this->cb;
          return $map($this->sourceIterator->current(), $this->sourceIterator->key(), $this);
        default:
          return $this->sourceIterator->current();
      }
    }

    /**
     * {@inheritdoc}
     */
    public function next() {
      $this->sourceIterator->next();
      if ($this->op === self::OP_FILTER) {
        $predicate = $this->cb;
        while ($this->sourceIterator->valid() && $this->isValid && !$predicate($this->sourceIterator->current(), $this->sourceIterator->key(), $this)) {
          $this->sourceIterator->next();
        }
      }
    }

    /**
     * {@inheritdoc}
     */
    public function key() {
      return $this->sourceIterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid() {
      return $this->sourceIterator->valid() && $this->isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind() {
      $this->sourceIterator->rewind();
      $this->isValid = true;
      if ($this->cb instanceof StatefulOperator) {
        $this->cb->rewind();
      }
    }
  }
