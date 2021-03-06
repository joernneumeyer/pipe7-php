# pipe7
pipe7 is an `Iterator`-based data processing library.
It aims to tackle some issues one may encounter while using regular higher-order functions built in to PHP (like `array_filter`, `array_map`, and `array_reduce`).

## PHPDoc
PHPDocumentor generated docs can be found here: [https://docs.pipe7.joern-neumeyer.de/](https://docs.pipe7.joern-neumeyer.de/).

## Installation
`composer require neu/pipe7`

## Problem statement
PHP already offers some functions to perform data transformations on arrays.
However, the given APIs are not consistent and only work on arrays.
If one wants to (or has to) use a `Generator` or `Iterator`, they cannot use the built-in functions for these data sources.

On top, since functions like `array_map` return an immediate result, even though more operations may be performed on the array.
They may end up using more memory than necessary, because the intermediate arrays are not used, except in the next step of the processing chain.

## Solution
pipe7 offers a consistent and predictable API to work with, so your code is easier to understand.
It also supports `Iterator`s as data sources (which includes `Generator`s).

In fact, the entire processing mechanism is built on the basis of `Iterator`s.
This allows pipe7 to have a low memory footprint, if you are performing a long chain of transformations on your source data structure.
That also helps not just with the calculation of a final result set (like a conversion to an array), but it also allows you to only trigger
a computation on the elements you really need in something like a `foreach` loop.
If you just need to process the first five elements from your result set, pipe7 will only request enough elements from your original data source, to deliver these five result elements.
So no element will be put through a callback (like in `array_map`), if it is not necessary.

### Also...
There are a lot of common operations which you may want to perform on you data (like average calculation, grouping elements, or converting them to strings).
For such common use-cases, pipe7 provides a collection of helpers available in the classes `Mappers` and `Reducers`.

To see all available helpers, please visit the [documentation](https://docs.pipe7.joern-neumeyer.de/) or build it yourself using [phpDocumentor](https://phpdoc.org/).

## Performance
pipe7 is a trade-off. It performs worse than the standard array functions, when it comes down to CPU time.
But that's the point. CPU time is exchanged for memory efficiency.

This repository contains a [benchmark](./benchmark.php), which show performance differences between pipe7 and the regular array functions.
As a reference, the array functions are used as a baseline for measurement.

<table>
  <thead>
    <tr>
      <th>use-case</th>
      <th>CPU (time compared to baseline)</th>
      <th>RAM (required/used memory to compute the result, compared to baseline)</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>when applying one transformation</td>
      <td>&cong;250%</td>
      <td>&cong;100%</td>
    </tr>
    <tr>
      <td>when applying multiple transformation</td>
      <td>&cong;250%</td>
      <td>&lt;1%</td>
    </tr>
  </tbody>
</table>

The values given in the table are only approximations.
However, it becomes clear, that pipe7 takes its toll on CPU efficiency, but it is more efficient RAM-wise.
As can be seen, the RAM usage is especially low, when using a `Generator` as a data source.

Since `Generator`s (or `Iterator`s in general) are not directly compatible with functions like `array_map`, it would either
first have to be converted into an array, or an additional data processing mechanism would be necessary.

So pipe7 really shines, when it can be used in combination with `Generator`s.

## Usage
Create a new `CollectionPipe`:
```php
use Neu\Pipe7\CollectionPipe;

$data = [1,2,3];
$p = CollectionPipe::from($data);
// or with the shorthand
$p = pipe($data);
```

Transforming elements:
```php
$doubled = $p->map(function($x){ return $x * 2; })->toArray();
// if you're using php7.4 or later,
// better use arrow functions for more concise code
$doubled = $p->map(fn($x) => $x * 2)->toArray();
```

Filtering elements:
```php
$even = $p->filter(fn($x) => $x % 2 === 0)->toArray();
```

Combining Elements:
```php
$total = $p->reduce(fn($carry, $x) => $carry + $x, 0);
```

As shown above, the methods `map` and `filter` always return a new `CollectionPipe`, from which the result has to be collected (e.g. via the `toArray` method).
It would also be possible to iterate the new `CollectionPipe` instance in a `foreach` loop.

In contrast, `reduce` only returns a new `CollectionPipe` if its third parameter is set to `true` and the carry of the reduce operation is an `Iterator`.
If the third parameter is `false`, or not set, a reduced `array`/`Iterator` would just be returned.
If the third parameter is `true`, and the reduced value is not an `array`/`Iterator`, an `UnprocessableObject` exception will be thrown.

For more information, please have a look at the [documentation](https://docs.pipe7.joern-neumeyer.de/).

## Stateful operators
Some operations cannot easily be expressed in a single function.
Such an operation could, for example, be a limitation on the items being processed in a given pipe.
So, if you need to implement such an operation, create a `class` which implements the `interface` `Neu\Pipe7\StatefulOperator`.

If you want to save yourself a bit of boilerplate code, consider extending the `class` `Neu\Pipe7\CallableOperator`.
By doing so, you get an implementation for the `__invoke` method, and a dummy implementation for the `rewind` method, if your operation does not require any specific resetting.

Stateful operators can be passed as arguments to pipes in the same way you would otherwise pass a `Closure`.

## How will the passed operations be evaluated?
Depending on the task a particular pipe has to fulfill, it may be important to know how a pipe's logic will be executed.

### Mappers
Signature: `function(mixed $value, mixed $key, Neu\Pipe7\CollectionPipe $this)`

If you created a pipe, which should transform incoming elements, that logic will be executed when the `current` method on the pipe is called.

### Filters
Signature: `function(mixed $value, mixed $key, Neu\Pipe7\CollectionPipe $this)`

Filters are called, when the `next` method on the pipe is called, as they determine, whether an element shall be emitted by the pipe or not.

### Reducers
Signature: `function(mixed #carry, mixed $value, mixed $key, Neu\Pipe7\CollectionPipe $this)`

Reducers have another signature than mappers and filters, since it is their job to create a single new value from the elements provided to them.
So, they may be used to calculate sums or similar results.

## License
pipe7 is available under the terms of the [GNU Lesser General Public License in version 3.0 or later](./LICENSE).
