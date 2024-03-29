<?php

  namespace Neu\Pipe7;

  use Closure;
  use Iterator;
  use Exception;
  use Traversable;

  /**
   * An iterable data processing unit.
   * @implements Pipe<mixed>
   * @implements Iterator<mixed>
   * @package Neu\Pipe7
   */
  class CollectionPipe implements Pipe, Iterator {
    /** @var Iterator<mixed> */
    private $sourceIterator;
    /** @var int */
    private $op;
    /** @var Closure|StatefulOperator|null */
    private $cb;
    /** @var Closure|StatefulOperator */
    private $cbOp;
    /** @var bool */
    private $isValid = true;
    /** @var bool */
    private $firstItemAfterRewind = true;

    private const OP_NONE    = 0;
    private const OP_MAP     = 1;
    private const OP_FILTER  = 2;
    private const OP_MAP_KEY = 3;

    /**
     * CollectionPipe constructor.
     * @param Traversable<mixed>|array<mixed> $collection
     * @param int $op
     * @param StatefulOperator|callable|null $cb
     */
    private function __construct($collection, int $op = self::OP_NONE, $cb = null) {
      if (is_array($collection)) {
        $this->sourceIterator = new \ArrayIterator($collection);
      } else {
        if ($collection instanceof \IteratorAggregate) {
          try {
            $collection = $collection->getIterator();
          } catch (\Throwable $e) {
            throw new UnprocessableObject($e->getMessage(), $e->getCode(), $e);
          }
        }
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
        if ($cb instanceof StatefulOperator) {
          $this->cbOp = Closure::fromCallable([$cb, 'apply']);
        } else if ($cb instanceof Closure) {
          $this->cbOp = $cb;
        } else {
          $this->cbOp = Closure::fromCallable($cb);
        }
      }
    }

    /**
     * Creates a new CollectionPipe, which transforms each element with the supplied mapper, when it is traversed.
     *
     * $transformer signature: fn(mixed $currentItem, mixed $currentKey, CollectionPipe $pipeInstance) => mixed
     * @param StatefulOperator|callable $transformer The transforming function to apply to each element.
     * @return CollectionPipe
     */
    public function map($transformer): Pipe {
      self::isValidOperator($transformer);
      return new CollectionPipe($this, self::OP_MAP, $transformer);
    }

    /**
     * Creates a new CollectionPipe, which filters the elements available during traversal, based on the result of the supplied {@see $predicate}.
     *
     * $predicate signature: fn(mixed $currentItem, mixed $currentKey, CollectionPipe $pipeInstance) => bool
     * @param StatefulOperator|callable $predicate The predicate to apply to an element, to check if it should be used.
     * @return CollectionPipe
     */
    public function filter($predicate): Pipe {
      self::isValidOperator($predicate);
      return new CollectionPipe($this, self::OP_FILTER, $predicate);
    }

    /**
     * Creates a new CollectionPipe, which transforms each key with the supplied mapper, when it is traversed.
     *
     * $transformer signature: fn(mixed $currentKey, mixed $currentItem, CollectionPipe $pipeInstance) => mixed
     * @param StatefulOperator|callable $transformer The transforming function to apply to each element.
     * @return CollectionPipe
     */
    public function mapKeys($transformer): Pipe {
      self::isValidOperator($transformer);
      return new CollectionPipe($this, self::OP_MAP_KEY, $transformer);
    }

    /**
     *
     * @param StatefulOperator|Closure $reducer The function to apply.
     * @param mixed|null $initial
     * @param bool $returnAsPipe If {$returnAsPipe} is set to true, and the reduced value is a valid data source, this method returns a new CollectionPipe for the reduced value.
     * @return mixed|CollectionPipe
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
     * @return array<mixed>
     */
    public function toArray(bool $preserveKeys = true): array {
      $arr = iterator_to_array($this);
      return $preserveKeys ? $arr : array_values($arr);
    }

    /**
     * @param mixed $op
     */
    private static function isValidOperator($op): void {
      if (!(is_callable($op) || $op instanceof StatefulOperator)) {
        throw new InvalidOperator('Invalid operator supplied! Make sure to pass either a \'callable\' or an instance of \'' . StatefulOperator::class . '\'!');
      }
    }

    /**
     * Factory method to create new CollectionPipe instances.
     * @param array<mixed>|Iterator<mixed> $collection The data source.
     * @return CollectionPipe
     */
    public static function from($collection): CollectionPipe {
      return new CollectionPipe($collection);
    }

    public function invalidate(): void {
      $this->isValid = false;
    }

    /**
     * {@inheritdoc}
     * @retrun mixed
     */
    #[\ReturnTypeWillChange]
    public function current() {
      if ($this->firstItemAfterRewind) {
        $this->firstItemAfterRewind = false;
        if ($this->op === self::OP_FILTER) {
          $this->next(true);
        }
      }
      $value = $this->sourceIterator->current();
      $key   = $this->sourceIterator->key();

      switch ($this->op) {
        case self::OP_MAP:
          return ($this->cbOp)($value, $key, $this);
        default:
          return $value;
      }
    }

    /**
     * {@inheritdoc}
     */
    public function next(bool $skipInitialNext = false): void {
      if (!$skipInitialNext) {
        $this->sourceIterator->next();
      }
      if ($this->op === self::OP_FILTER) {
        $predicate = $this->cbOp;
        while ($this->sourceIterator->valid() && $this->isValid && !$predicate($this->sourceIterator->current(), $this->sourceIterator->key(), $this)) {
          $this->sourceIterator->next();
        }
      }
    }

    /**
     * {@inheritdoc}
     * @retrun mixed
     */
    #[\ReturnTypeWillChange]
    public function key() {
      if ($this->op === self::OP_MAP_KEY) {
        return ($this->cbOp)($this->sourceIterator->key(), $this->current(), $this);
      }
      return $this->sourceIterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool {
      $dataAvailable     = $this->sourceIterator->valid();
      return $dataAvailable && $this->isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void {
      $this->sourceIterator->rewind();
      $this->isValid        = true;
      if ($this->cb instanceof StatefulOperator) {
        $this->cb->rewind();
      }
//      $this->next(false);
      $this->firstItemAfterRewind = true;
    }

    /**
     * Iterate over each element and apply the callback.
     * @param callable $cb The function to apply.
     * @return void
     */
    public function forEach(callable $cb): void {
      foreach ($this as $k => $v) {
        ($cb)($v, $k);
      }
    }
  }
