<?php

  namespace Neu\Pipe7;

  use Iterator;

  /**
   * @template T
   */
  interface Pipe {
    function invalidate(): void;
    function toArray(): array;
    /**
     *
     * @param StatefulOperator|callable $reducer The function to apply.
     * @param mixed|null $initial
     * @param bool $returnAsPipe If {$returnAsPipe} is set to true, and the reduced value is a valid data source, this method returns a new Pipe for the reduced value.
     * @return mixed|Pipe
     */
    public function reduce($reducer, $initial = null, bool $returnAsPipe = false);
    /**
     * Returns a Pipe, which transforms each key with the supplied mapper, when it is traversed.
     *
     * $transformer signature: fn(mixed $currentKey, mixed $currentItem, Pipe $pipeInstance) => mixed
     * @param StatefulOperator|callable $transformer The transforming function to apply to each element.
     * @return Pipe
     */
    public function mapKeys($transformer): Pipe;
    /**
     * Returns a Pipe, which filters the elements available during traversal, based on the result of the supplied {@see $predicate}.
     *
     * $predicate signature: fn(mixed $currentItem, mixed $currentKey, Pipe $pipeInstance) => bool
     * @param StatefulOperator|callable $predicate The predicate to apply to an element, to check if it should be used.
     * @return Pipe
     */
    public function filter($predicate): Pipe;
    /**
     * Returns a Pipe, which transforms each element with the supplied mapper, when it is traversed.
     *
     * $transformer signature: fn(mixed $currentItem, mixed $currentKey, Pipe $pipeInstance) => mixed
     * @param StatefulOperator|callable $transformer The transforming function to apply to each element.
     * @return Pipe
     */
    public function map($transformer): Pipe;
    /**
     * Iterate over each element and apply the callback.
     * @param callable $cb The function to apply.
     * @return void
     */
    public function forEach(callable $cb): void;
  }
