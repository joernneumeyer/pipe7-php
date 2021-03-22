<?php
  require __DIR__ . '/bootstrap.php';

  define('BENCH_SET_SIZE', $argc > 1 ? (int)$argv[1] : 5000000);
  define('WARMUP_SET_SIZE', $argc > 2 ? (int)$argv[2] : 10000);

  echo 'Benchmark data set size: ' . BENCH_SET_SIZE . ' entries' . PHP_EOL;
  echo 'Warm-up data set size: ' . WARMUP_SET_SIZE . ' entries' . PHP_EOL;
  echo PHP_EOL;

  function warmupSource() {
    return makeBenchArray(WARMUP_SET_SIZE);
  }

  function dataSource() {
    return makeBenchArray(BENCH_SET_SIZE);
  }

  function generatorWarmupSource() {
    return makeBenchGenerator(WARMUP_SET_SIZE);
  }

  function generatorDataSource() {
    return makeBenchGenerator(BENCH_SET_SIZE);
  }

  function someTask(...$args) {

  }

  /**
   * @var $benchmarks Benchmark[]
   */
  $benchmarks = [
    b()->withName('array_filter')->withWarmupSource('warmupSource')->withDataSource('dataSource')->withBenchmarkLogic(function ($data) {
      $initialRam     = memory_get_usage();
      $olderThanFifty = array_filter($data, function (BenchUser $u) {
        return $u->getAge() >= 50;
      });
      is_array($olderThanFifty);
      return memory_get_usage() - $initialRam;
    }),
    b()->withName('pipe7 filter')->withWarmupSource('warmupSource')->withDataSource('dataSource')->withBenchmarkLogic(function ($data) {
      $initialRam     = memory_get_usage();
      $olderThanFifty = pipe($data)->filter(function (BenchUser $u) {
        return $u->getAge() >= 50;
      })->toArray();
      is_array($olderThanFifty);
      return memory_get_usage() - $initialRam;
    }),
    b()->withName('array_map')->withWarmupSource('warmupSource')->withDataSource('dataSource')->withBenchmarkLogic(function ($data) {
      $initialRam = memory_get_usage();
      $names      = array_map(function (BenchUser $u) {
        return $u->getUsername();
      }, $data);
      is_array($names);
      return memory_get_usage() - $initialRam;
    }),
    b()->withName('pipe7 map')->withWarmupSource('warmupSource')->withDataSource('dataSource')->withBenchmarkLogic(function ($data) {
      $initialRam = memory_get_usage();
      $names      = pipe($data)->map(function (BenchUser $u) {
        return $u->getUsername();
      })->toArray();
      is_array($names);
      return memory_get_usage() - $initialRam;
    }),
    b()->withName('array_reduce')->withWarmupSource('warmupSource')->withDataSource('dataSource')->withBenchmarkLogic(function ($data) {
      $initialRam = memory_get_usage();
      $names      = array_reduce($data, function ($carry, BenchUser $u) {
        $firstLetter = $u->getUsername()[0];
        if (isset($carry[$firstLetter])) {
          ++$carry[$firstLetter];
        } else {
          $carry[$firstLetter] = 1;
        }
        return $carry;
      }, []);
      is_array($names);
      return memory_get_usage() - $initialRam;
    }),
    b()->withName('pipe7 reduce')->withWarmupSource('warmupSource')->withDataSource('dataSource')->withBenchmarkLogic(function ($data) {
      $initialRam = memory_get_usage();
      $names      = pipe($data)->reduce(function ($carry, BenchUser $u) {
        $firstLetter = $u->getUsername()[0];
        if (isset($carry[$firstLetter])) {
          ++$carry[$firstLetter];
        } else {
          $carry[$firstLetter] = 1;
        }
        return $carry;
      }, []);
      is_array($names);
      return memory_get_usage() - $initialRam;
    }),
    b()->withName('array_filter into array_reduce')->withWarmupSource('warmupSource')->withDataSource('dataSource')->withBenchmarkLogic(function ($data) {
      $initialRam       = memory_get_usage();
      $olderThanFifty   = array_filter($data, function (BenchUser $u) {
        return $u->getAge() >= 50;
      });
      $youngerThanFifty = array_filter($data, function (BenchUser $u) {
        return $u->getAge() < 50;
      });
      $names            = array_reduce($olderThanFifty, function ($carry, BenchUser $u) {
        $firstLetter = $u->getUsername()[0];
        if (isset($carry[$firstLetter])) {
          ++$carry[$firstLetter];
        } else {
          $carry[$firstLetter] = 1;
        }
        return $carry;
      }, []);
      is_array($names);
      $names = array_reduce($youngerThanFifty, function ($carry, BenchUser $u) {
        $firstLetter = $u->getUsername()[0];
        if (isset($carry[$firstLetter])) {
          ++$carry[$firstLetter];
        } else {
          $carry[$firstLetter] = 1;
        }
        return $carry;
      }, []);
      is_array($names);
      return memory_get_usage() - $initialRam;
    }),
    b()->withName('pipe7 filter into reduce')->withWarmupSource('warmupSource')->withDataSource('dataSource')->withBenchmarkLogic(function ($data) {
      $initialRam = memory_get_usage();
      $names      = pipe($data)->filter(function (BenchUser $u) {
        return $u->getAge() >= 50;
      })->reduce(function ($carry, BenchUser $u) {
        $firstLetter = $u->getUsername()[0];
        if (isset($carry[$firstLetter])) {
          ++$carry[$firstLetter];
        } else {
          $carry[$firstLetter] = 1;
        }
        return $carry;
      }, []);
      is_array($names);
      $names      = pipe($data)->filter(function (BenchUser $u) {
        return $u->getAge() < 50;
      })->reduce(function ($carry, BenchUser $u) {
        $firstLetter = $u->getUsername()[0];
        if (isset($carry[$firstLetter])) {
          ++$carry[$firstLetter];
        } else {
          $carry[$firstLetter] = 1;
        }
        return $carry;
      }, []);
      is_array($names);
      return memory_get_usage() - $initialRam;
    }),
    b()->withName('array_map from generator')->withWarmupSource('generatorWarmupSource')->withDataSource('generatorDataSource')->withBenchmarkLogic(function($generator) {
      $data = iterator_to_array($generator);
      $initialRam = memory_get_usage();
      $names      = array_map(function (BenchUser $u) {
        return $u->getUsername();
      }, $data);
      foreach ($names as $name) {
        someTask($name);
      }
      return memory_get_usage() - $initialRam;
    }),
    b()->withName('pipe7 map from generator')->withWarmupSource('generatorWarmupSource')->withDataSource('generatorDataSource')->withBenchmarkLogic(function($generator) {
      $initialRam = memory_get_usage();
      $names      = pipe($generator)->map(function (BenchUser $u) {
        return $u->getUsername();
      });
      foreach ($names as $name) {
        someTask($name);
      }
      return memory_get_usage() - $initialRam;
    })
  ];

  $results = [];

  foreach ($benchmarks as $benchmark) {
    echo 'Running benchmark: ' . $benchmark->getName() . PHP_EOL;
    $results[] = $benchmark->run();
  }

  $length = count($results) / 2;
  echo PHP_EOL;

  for ($i = 0; $i < $length; ++$i) {
    $baseline = $results[$i * 2];
    $pipe7    = $results[$i * 2 + 1];
    echo "{$pipe7->getName()} -> {$baseline->getName()}" . PHP_EOL;
    echo $pipe7->comparedToBaseline($baseline) . PHP_EOL . PHP_EOL;
  }
