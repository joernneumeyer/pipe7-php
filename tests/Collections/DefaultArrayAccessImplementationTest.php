<?php

  use Neu\Pipe7\Collections\DefaultArrayAccessImplementations;

  /**
   * @implements ArrayAccess<mixed, mixed>
   */
  class CollectionWithArrayAccess implements ArrayAccess, Countable {
    use DefaultArrayAccessImplementations;
    /**
     * @var mixed[] $data
     */
  }

  it('works', function() {
    $arr = new CollectionWithArrayAccess();
    $arr['f'] = 1;
    if (isset($arr['f'])) {
      unset($arr['f']);
    }
    $arr[] = 'gg';
    $arr[] = 'wp';
    expect($arr[0])->toEqual('gg');
    expect($arr[1])->toEqual('wp');
    expect(count($arr))->toEqual(2);
  });
