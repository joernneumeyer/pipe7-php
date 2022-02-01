<?php

  namespace Neu\Pipe7;

  use Closure;
  use Iterator;
  use Exception;

  /**
   * An iterable data processing unit.
   * @implements Iterator<mixed>
   * @package Neu\Pipe7
   */
  class CollectionPipe implements Iterator {
    private const CHUNK_SIZE = 10000;

    /** @var bool */
    private $useIntermediateResults = false;
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
    /** @var array<mixed> */
    private $buffer = [];
    /** @var array<string> */
    private $bufferKeys = [];
    /** @var int */
    private $bufferKeyIndex = -1;
    /** @var int */
    private $bufferSize = 0;
    /** @var bool */
    private $firstItemAfterRewind = true;

    private const OP_NONE    = 0;
    private const OP_MAP     = 1;
    private const OP_FILTER  = 2;
    private const OP_MAP_KEY = 3;

    /**
     * CollectionPipe constructor.
     * @param Iterator<mixed>|array<mixed> $collection
     * @param int $op
     * @param StatefulOperator|Closure|null $cb
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
        if ($cb instanceof StatefulOperator) {
          $this->cbOp = Closure::fromCallable([$cb, 'apply']);
        } else {
          $this->cbOp = $cb;
        }
      }
    }

    /**
     * @return $this
     */
    public function enableIntermediateResults(): CollectionPipe {
      $this->useIntermediateResults = true;
      return $this;
    }

    /**
     * Creates a new CollectionPipe, which transforms each element with the supplied mapper, when it is traversed.
     *
     * $transformer signature: fn(mixed $currentItem, mixed $currentKey, CollectionPipe $pipeInstance) => mixed
     * @param StatefulOperator|Closure $transformer The transforming function to apply to each element.
     * @return CollectionPipe
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
     */
    public function filter($predicate): CollectionPipe {
      self::isValidOperator($predicate);
      return new CollectionPipe($this, self::OP_FILTER, $predicate);
    }

    /**
     * Creates a new CollectionPipe, which transforms each key with the supplied mapper, when it is traversed.
     *
     * $transformer signature: fn(mixed $currentKey, mixed $currentItem, CollectionPipe $pipeInstance) => mixed
     * @param StatefulOperator|Closure $transformer The transforming function to apply to each element.
     * @return CollectionPipe
     */
    public function mapKeys($transformer): CollectionPipe {
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

    private function populateBuffer(): bool {
      if ($this->buffer === [] || $this->bufferKeyIndex >= $this->bufferSize) {
        if ($this->sourceIterator->valid()) {
          $this->bufferKeyIndex = 0;
          for ($i = 0; $i < self::CHUNK_SIZE && $this->sourceIterator->valid(); ++$i) {
            $this->buffer[$this->sourceIterator->key()] = $this->sourceIterator->current();
            $this->sourceIterator->next();
          }
          $this->bufferKeys     = array_keys($this->buffer);
          $this->bufferSize     = count($this->buffer);
          $this->bufferKeyIndex = -1;
        }
//        else {
//          throw new \Exception('foobar');
//        }
      }
      return $this->buffer !== [];
    }

    /**
     * {@inheritdoc}
     */
    public function current() {
      if ($this->firstItemAfterRewind) {
        $this->firstItemAfterRewind = false;
        if ($this->op === self::OP_FILTER) {
          $this->next(true);
        }
      }
      if ($this->useIntermediateResults) {
        if (!$this->populateBuffer()) {
          return null;
        }
        $key   = $this->bufferKeys[++$this->bufferKeyIndex];
        $value = $this->buffer[$key];
      } else {
        $value = $this->sourceIterator->current();
        $key   = $this->sourceIterator->key();
      }

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
    public function next(bool $skipInitialNext = false) {
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
     */
    public function key() {
      if ($this->op === self::OP_MAP_KEY) {
        return ($this->cbOp)($this->sourceIterator->key(), $this->current(), $this);
      }
      return $this->sourceIterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid() {
      $dataAvailable     = $this->sourceIterator->valid() || $this->buffer !== [];
      $bufferNotExceeded = $this->bufferKeyIndex < $this->bufferSize - 1;
      $iteratorIsValid   = $dataAvailable && $this->isValid;
      if ($this->bufferSize > 0) {
        return $iteratorIsValid && $bufferNotExceeded;
      } else {
        return $iteratorIsValid;
      }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind() {
      $this->sourceIterator->rewind();
      $this->isValid        = true;
      $this->buffer         = [];
      $this->bufferKeyIndex = -1;
      $this->bufferKeys     = [];
      $this->bufferSize     = 0;
      if ($this->cb instanceof StatefulOperator) {
        $this->cb->rewind();
      }
//      $this->next(false);
      $this->firstItemAfterRewind = true;
    }
  }
