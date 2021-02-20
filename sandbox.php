<?php

  use Neu\Pipe7\Reducers;

  require __DIR__ . '/vendor/autoload.php';

  $a = new stdClass();
  $a->foo = 20;
  $b = new stdClass();
  $b->foo = 55;

  $colors = ['red', 'blue', 'green'];
  $sizes = ['34', '36', '38', '40', '42', '44'];
  $data = [];
  foreach ($colors as $color) {
    foreach ($sizes as $size) {
      $data[] = [
        'size' => $size,
        'color' => $color
      ];
    }
  }

  $grouped = pipe($data)->reduce(Reducers::groupBy('[color]'));
  print_r($grouped);
