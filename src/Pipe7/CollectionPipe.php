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

    private const OP_NONE = 0;
    private const OP_MAP = 1;
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
      } else if (is_a($collection, Iterator::class)) {
        $this->sourceIterator = $collection;
      } else {
        throw new UnprocessableObject();
      }
      $this->op = $op;
      $this->cb = $cb;
    }

    /**
     * @param callable $cb
     * @return CollectionPipe
     * @throws UnprocessableObject
     */
    public function map(callable $cb): CollectionPipe {
      return new CollectionPipe($this, self::OP_MAP, $cb);
    }

    /**
     * @param callable $cb
     * @return CollectionPipe
     * @throws UnprocessableObject
     */
    public function filter(callable $cb): CollectionPipe {
      return new CollectionPipe($this, self::OP_FILTER, $cb);
    }

    /**
     * @param callable $cb
     * @param null $initial
     * @param bool $returnAsPipe
     * @return mixed|CollectionPipe
     */
    public function reduce(callable $cb, $initial = null, bool $returnAsPipe = false) {
      $carry = $initial;
      foreach ($this as $key => $value) {
        $carry = $cb($carry, $value, $key);
      }
      if ($returnAsPipe) {
        return new CollectionPipe($carry);
      } else {
        return $carry;
      }
    }

    /**
     * @param bool $preserveKeys
     * @return array
     */
    public function toArray(bool $preserveKeys = true): array {
      $arr = iterator_to_array($this);
      return $preserveKeys ? $arr : array_values($arr);
    }

    /**
     * @param $collection
     * @return CollectionPipe
     * @throws UnprocessableObject
     */
    public static function from($collection): CollectionPipe {
      return new CollectionPipe($collection);
    }

    /**
     * @inheritDoc
     */
    public function current() {
      switch ($this->op) {
        case self::OP_MAP: return ($this->cb)($this->sourceIterator->current(), $this->sourceIterator->key());
        default: return $this->sourceIterator->current();
      }
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function key() {
      return $this->sourceIterator->key();
    }

    /**
     * @inheritDoc
     */
    public function valid() {
     return $this->sourceIterator->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind() {
      $this->sourceIterator->rewind();
    }
  }
