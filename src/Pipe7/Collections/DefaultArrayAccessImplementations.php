<?php


  namespace Neu\Pipe7\Collections;


  /**
   * @template T
   */
  trait DefaultArrayAccessImplementations {
    /** @var array<T> $data */
    protected $data = [];

    /**
     * @param string|int $offset
     * @return bool
     */
    public function offsetExists($offset): bool {
      return isset($this->data[$offset]);
    }

    /**
     * @param string|int $offset
     * @return mixed
     */
    public function offsetGet($offset) {
      return $this->data[$offset];
    }

    /**
     * @param string|int $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void {
      if (is_null($offset)) {
        $this->data[] = $value;
      } else {
        $this->data[$offset] = $value;
      }
    }

    /**
     * @param string|int $offset
     */
    public function offsetUnset($offset): void {
      unset($this->data[$offset]);
    }

    public function count(): int {
      return count($this->data);
    }
  }
