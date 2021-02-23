<?php
  require dirname(__DIR__) . '/vendor/autoload.php';
  require __DIR__ . '/Benchmark.php';
  require __DIR__ . '/BenchUser.php';
  require __DIR__ . '/BenchmarkResult.php';

  ini_set('memory_limit', '2048M');
  ini_set('opcache.jit', 1215);

  function formatRam(int $bytes) {
    $suffix = '';
    if ($bytes > 1024) {
      $bytes /= 1024;
      $suffix = 'K';
    }
    if ($bytes > 1024) {
      $bytes /= 1024;
      $suffix = 'M';
    }
    if ($bytes > 1024) {
      $bytes /= 1024;
      $suffix = 'G';
    }
    $bytes = ((int)($bytes * 1000)) / 1000;
    return $bytes . $suffix . 'Bytes';
  }

  function someName() {
    static $names = ['Alice', 'Bob', 'Charlie', 'Dan', 'Emma'];
    return $names[rand(0, count($names) - 1)];
  }

  function someAge() {
    return rand(0, 80);
  }

  function b() {
    return new Benchmark();
  }

  function makeBenchArray(int $n): array {
    $result = [];
    for ($i = 1; $i <= $n; ++$i) {
      $result[] = new BenchUser($i, someName(), someAge());
    }
    return $result;
  }

  function makeBenchGenerator(int $n) {
    for ($i = 1; $i <= $n; ++$i) {
      yield new BenchUser($i, someName(), someAge());
    }
  }
