<?php

  namespace Neu\Pipe7;

  use Iterator;

  /**
   * Class CollectionPipe
   * @package Neu\Pipe7
   */
  class CollectionPipe implements Iterator {
    private $sourceIterator;
    private $op;
    private $cb;

    private const OP_NONE   = 0;
    private const OP_MAP    = 1;
    private const OP_FILTER = 2;

    /**
     * CollectionPipe constructor.
     * @param $collection Iterator|array
     * @param int $op
     * @param callable|null $cb
     * @throws UnprocessableObject
     */
    private function __construct($collection, int $op = self::OP_NONE, ?callable $cb = null) {
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
    }

    /**
     * Creates a new CollectionPipe, which transforms each element with the supplied mapper, when it is traversed.
     * @param callable $transformer The transforming function to apply to each element.
     * @return CollectionPipe
     * @throws UnprocessableObject
     */
    public function map(callable $transformer): CollectionPipe {
      return new CollectionPipe($this, self::OP_MAP, $transformer);
    }

    /**
     * Creates a new CollectionPipe, which filters the elements available during traversal, based on the result of the supplied {@see $predicate}.
     * @param callable $predicate The predicate to apply to an element, to check if it should be used.
     * @return CollectionPipe
     * @throws UnprocessableObject
     */
    public function filter(callable $predicate): CollectionPipe {
      return new CollectionPipe($this, self::OP_FILTER, $predicate);
    }

    /**
     *
     * @param callable $reducer The function to apply.
     * @param mixed|null $initial
     * @param bool $returnAsPipe If {$returnAsPipe} is set to true, and the reduced value is a valid data source, this method returns a new CollectionPipe for the reduced value.
     * @return mixed|CollectionPipe
     * @throws UnprocessableObject
     */
    public function reduce(callable $reducer, $initial = null, bool $returnAsPipe = false) {
      $carry = $initial;
      foreach ($this as $key => $value) {
        $carry = $reducer($carry, $value, $key);
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

    /**
     * Factory method to create new CollectionPipe instances.
     * @param $collection array|Iterator The data source.
     * @return CollectionPipe
     * @throws UnprocessableObject
     */
    public static function from($collection): CollectionPipe {
      return new CollectionPipe($collection);
    }

    /**
     * {@inheritdoc}
     */
    public function current() {
      switch ($this->op) {
        case self::OP_MAP:
          return ($this->cb)($this->sourceIterator->current(), $this->sourceIterator->key());
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
        while ($this->sourceIterator->valid() && !$predicate($this->sourceIterator->current(), $this->sourceIterator->key())) {
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
      return $this->sourceIterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind() {
      $this->sourceIterator->rewind();
    }
  }
