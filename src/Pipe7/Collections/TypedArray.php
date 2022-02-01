<?php


  namespace Neu\Pipe7\Collections;


  use ArrayAccess;
  use ArrayIterator;
  use Countable;
  use Iterator;
  use Neu\Pipe7\GeneralConstants;

  /**
   * Class TypedArray
   * @package Neu\Pipe7\Collections
   */
  class TypedArray implements ArrayAccess, \IteratorAggregate, Countable {
    use DefaultArrayAccessImplementations;

    /** @var string $type */
    private $type;

    private function __construct(string $type) {
      if (!in_array($type, GeneralConstants::NON_CLASS_TYPES) && !class_exists($type)) {
        throw new \InvalidArgumentException("Cannot create TypedArray with invalid type {$type}!");
      }
      $this->type = $type;
    }

    public static function forType(string $type): TypedArray {
      return new TypedArray($type);
    }

    /**
     * @param string|int $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void {
      $type = get_debug_type($value);
      $value_is_scalar = in_array($type, GeneralConstants::NON_CLASS_TYPES);
      $value_is_valid = true;
      if ($value_is_scalar) {
        if ($type !== $this->type) {
          $value_is_valid = false;
        }
      } else if (!is_a($value, $this->type)) {
        $value_is_valid = false;
      }
      if (!$value_is_valid) {
        throw new \InvalidArgumentException("Cannot add item of type {$type} to TypedArray<{$this->type}>!");
      }
      if (is_null($offset)) {
        $this->data[] = $value;
      } else {
        $this->data[$offset] = $value;
      }
    }

    public function type(): string {
      return $this->type;
    }

    public function getIterator(): Iterator {
      return new ArrayIterator($this->data);
    }

    function unwrapped() {
      return $this->data;
    }
  }
