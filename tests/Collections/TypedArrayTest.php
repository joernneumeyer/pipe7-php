<?php

  use Neu\Pipe7\Collections\TypedArray;

  class SimpleEntity {
    /** @var string */
    public $name;

    function __construct(string $name) {
      $this->name = $name;
    }
  }

  class OtherEntity {
    /** @var int */
    public $age;

    function __construct(int $age) {
      $this->age = $age;
    }
  }

  $validCases = [
    ['int', [0, 1, 2, 3]],
    ['string', ['Hello', 'World', 'foo']],
    [SimpleEntity::class, [new SimpleEntity('John'), new SimpleEntity('Doe'), new SimpleEntity('PHP')]]
  ];

  $invalidCases = [
    ['int', 'Hello, World!'],
    [SimpleEntity::class, null],
    [SimpleEntity::class, new OtherEntity(20)],
  ];

  it('should accept valid values', function ($type, $expected) {
    $arr = TypedArray::forType($type);
    foreach ($expected as $e) {
      $arr[] = $e;
    }
    expect($arr->unwrapped())->toMatchArray($expected);
  })->with($validCases);

  it('should not accept invalid values', function ($type, $value) {
    $arr   = TypedArray::forType($type);
    $arr[] = $value;
  })->with($invalidCases)->throws(InvalidArgumentException::class);

  it('should throw if an undefined type is passed', function () {
    TypedArray::forType('foobar123');
  })->throws(InvalidArgumentException::class);

  it('should return the proper type', function () {
    $arr = TypedArray::forType('int');
    expect($arr->type())->toEqual('int');
  });

  it('should return a proper iterator', function () {
    $arr = TypedArray::forType('int');
    $arr[] = 42;
    $arr[] = 567;
    $arr[] = 21;
    $result = iterator_to_array($arr);
    expect($result)->toMatchArray([42, 567, 21]);
  });

  it('should push elements to their proper index', function() {
    $arr = TypedArray::forType('int');
    $arr['hello'] = 55;
    $arr['world'] = 65;
    $result = iterator_to_array($arr);
    expect($result)->toMatchArray(['hello' => 55, 'world' => 65]);
  });
