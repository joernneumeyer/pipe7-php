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

  it('should accept valid values', function($type, $expected) {
    $arr = TypedArray::forType($type);
    foreach ($expected as $e) {
      $arr[] = $e;
    }
    expect($arr->unwrapped())->toMatchArray($expected);
  })->with($validCases);

  it('should not accept invalid values', function($type, $value) {
    $arr = TypedArray::forType($type);
    $arr[] = $value;
  })->with($invalidCases)->throws(InvalidArgumentException::class);

  it('should throw if an undefined type is passed', function() {
    TypedArray::forType('foobar123');
  })->throws(InvalidArgumentException::class);
