<?php
  require __DIR__ . '/bootstrap.php';

  use function Neu\Pipe7\pipe;

  define('BENCH_SET_SIZE', $argc > 1 ? (int)$argv[1] : 5000000);
  define('WARMUP_SET_SIZE', $argc > 2 ? (int)$argv[2] : 10000);

  echo 'Benchmark data set size: ' . BENCH_SET_SIZE . ' entries' . PHP_EOL;
  echo 'Warm-up data set size: ' . WARMUP_SET_SIZE . ' entries' . PHP_EOL;
  echo PHP_EOL;

  $warmupSource = function () {
    return makeBenchArray(WARMUP_SET_SIZE);
  };

  $dataSource = function () {
    return makeBenchArray(BENCH_SET_SIZE);
  };

  $generatorWarmupSource = function () {
    return makeBenchGenerator(WARMUP_SET_SIZE);
  };

  $generatorDataSource = function () {
    return makeBenchGenerator(BENCH_SET_SIZE);
  };

  function someTask(...$args) {

  }

  /**
   * @var $benchmarkSets Benchmark[][]
   */
  $benchmarkSets = [
    [
      b()->withName('loop filter')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $olderThanFifty = [];
        foreach ($data as $user) {
          if ($user->getAge() >= 50) {
            $olderThanFifty[] = $user;
          }
        }
        is_array($olderThanFifty);
        yield memory_get_usage();
      }),
      b()->withName('array_filter')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $olderThanFifty = array_filter($data, function (BenchUser $u) {
          return $u->getAge() >= 50;
        });
        is_array($olderThanFifty);
        yield memory_get_usage();
      }),
      b()->withName('pipe7 filter')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $olderThanFifty = pipe($data)->filter(function (BenchUser $u) {
          return $u->getAge() >= 50;
        })->toArray();
        is_array($olderThanFifty);
        yield memory_get_usage();
      }),
      b()->withName('pipe7 filter with buffer')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $olderThanFifty = pipe($data)->enableIntermediateResults()->filter(function (BenchUser $u) {
          return $u->getAge() >= 50;
        })->toArray();
        is_array($olderThanFifty);
        yield memory_get_usage();
      })
    ], [
      b()->withName('loop map')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $names = [];
        foreach ($data as $user) {
          $names[] = $user->getUsername();
        }
        is_array($names);
        yield memory_get_usage();
      }),
      b()->withName('array_map')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $names = array_map(function (BenchUser $u) {
          return $u->getUsername();
        }, $data);
        is_array($names);
        yield memory_get_usage();
      }),
      b()->withName('pipe7 map')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $names = pipe($data)->map(function (BenchUser $u) {
          return $u->getUsername();
        })->toArray();
        is_array($names);
        yield memory_get_usage();
      }),
      b()->withName('pipe7 map with buffer')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $names = pipe($data)->enableIntermediateResults()->map(function (BenchUser $u) {
          return $u->getUsername();
        })->toArray();
        is_array($names);
        yield memory_get_usage();
      }),
    ], [
      b()->withName('loop reduce')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $names = [];
        foreach ($data as $user) {
          $firstLetter = $user->getUsername()[0];
          if (isset($names[$firstLetter])) {
            ++$names[$firstLetter];
          } else {
            $names[$firstLetter] = 1;
          }
        }
        is_array($names);
        yield memory_get_usage();
      }),b()->withName('array_reduce')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $names = array_reduce($data, function ($carry, BenchUser $u) {
          $firstLetter = $u->getUsername()[0];
          if (isset($carry[$firstLetter])) {
            ++$carry[$firstLetter];
          } else {
            $carry[$firstLetter] = 1;
          }
          return $carry;
        }, []);
        is_array($names);
        yield memory_get_usage();
      }),
      b()->withName('pipe7 reduce')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $names = pipe($data)->reduce(function ($carry, BenchUser $u) {
          $firstLetter = $u->getUsername()[0];
          if (isset($carry[$firstLetter])) {
            ++$carry[$firstLetter];
          } else {
            $carry[$firstLetter] = 1;
          }
          return $carry;
        }, []);
        is_array($names);
        yield memory_get_usage();
      }),
      b()->withName('pipe7 reduce with buffer')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $names = pipe($data)->enableIntermediateResults()->reduce(function ($carry, BenchUser $u) {
          $firstLetter = $u->getUsername()[0];
          if (isset($carry[$firstLetter])) {
            ++$carry[$firstLetter];
          } else {
            $carry[$firstLetter] = 1;
          }
          return $carry;
        }, []);
        is_array($names);
        yield memory_get_usage();
      }),
    ], [
      b()->withName('loop filter and reduce')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
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
        yield memory_get_usage();
      }),b()->withName('array_filter into array_reduce')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
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
        yield memory_get_usage();
      }),
      b()->withName('pipe7 filter into reduce')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $source = pipe($data);
        $names = $source->filter(function (BenchUser $u) {
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
        $names = $source->filter(function (BenchUser $u) {
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
        yield memory_get_usage();
      }),
      b()->withName('pipe7 filter into reduce with buffer')->withWarmupSource($warmupSource)->withDataSource($dataSource)->withBenchmarkLogic(function ($data) {
        yield memory_get_usage();
        $source = pipe($data)->enableIntermediateResults();
        $names = $source->filter(function (BenchUser $u) {
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
        $names = $source->filter(function (BenchUser $u) {
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
        yield memory_get_usage();
      }),
    ], [
      b()->withName('array_map from generator')->withWarmupSource($generatorWarmupSource)->withDataSource($generatorDataSource)->withBenchmarkLogic(function ($generator) {
        yield memory_get_usage();
        $data  = iterator_to_array($generator);
        $names = array_map(function (BenchUser $u) {
          return $u->getUsername();
        }, $data);
        foreach ($names as $name) {
          yield memory_get_usage();
          someTask($name);
        }
        yield memory_get_usage();
      }),
      b()->withName('pipe7 map from generator')->withWarmupSource($generatorWarmupSource)->withDataSource($generatorDataSource)->withBenchmarkLogic(function ($generator) {
        yield memory_get_usage();
        $names = pipe($generator)->map(function (BenchUser $u) {
          return $u->getUsername();
        });
        foreach ($names as $name) {
          yield memory_get_usage();
          someTask($name);
        }
        yield memory_get_usage();
      })
    ]
  ];

  $results = [];

  foreach ($benchmarkSets as $benchmarkSet) {
    $baseline = $benchmarkSet[0];
    echo 'Running baseline benchmark: ' . $baseline->getName() . PHP_EOL;
    $baselineResult   = $baseline->run();
    $benchmarkSetSize = count($benchmarkSet);
    for ($i = 1; $i < $benchmarkSetSize; ++$i) {
      $bench = $benchmarkSet[$i];
      echo 'Running benchmark: ' . $bench->getName() . PHP_EOL;
      $benchmarkResult = $bench->run();
      $results[]       = "{$benchmarkResult->getName()} -> {$baseline->getName()}" . PHP_EOL . $benchmarkResult->comparedToBaseline($baselineResult) . PHP_EOL . PHP_EOL;
    }
  }

  echo PHP_EOL . PHP_EOL . join('', $results);
