<?php
  require __DIR__ . '/bootstrap.php';

  use function Neu\Pipe7\pipe;

  function makeLogarithmicScale(int $topLimit) {
    $result = [];
    $last = 0;
    for ($i = 100; $last < $topLimit; $i *= 10) {
      for ($k = 1; $k < 10 && $last < $topLimit; ++$k) {
        $result[] = $last = $i * $k;
      }
    }
    if ($result[count($result) - 1] !== $topLimit) {
      $result[] = $topLimit;
    }
    return $result;
  }

  $benchSetSizes = makeLogarithmicScale(1000000);

  $benchSetSize = 1000;
  $warmupSetSize = 10000;

  $warmupSource = function() use (&$warmupSetSize) { return makeBenchArray($warmupSetSize); };

  $dataSource = function() use (&$benchSetSize) { return makeBenchArray($benchSetSize); };

  $generatorWarmupSource = function() use (&$warmupSetSize) { return makeBenchGenerator($warmupSetSize); };

  $generatorDataSource = function() use (&$benchSetSize) { return makeBenchGenerator($benchSetSize); };

  function someTask(...$args) {

  }

  function arrayToCsv($arr) {
    $result = [];
    $result[] = join(';', array_keys($arr[0]));
    foreach ($arr as $a) {
      $result[] = join(';', $a);
    }
    return join("\r\n", $result);
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
      b()->withName('loop map from generator')->withWarmupSource($generatorWarmupSource)->withDataSource($generatorDataSource)->withBenchmarkLogic(function ($generator) {
        yield memory_get_usage();
        $names = [];
        foreach ($generator as $user) {
          $names[] = $user->getUsername();
        }
        is_array($names);
        yield memory_get_usage();
      }),
      b()->withName('array_map from generator')->withWarmupSource($generatorWarmupSource)->withDataSource($generatorDataSource)->withBenchmarkLogic(function ($generator) {
        yield memory_get_usage();
        $data  = iterator_to_array($generator);
        $names = array_map(function (BenchUser $u) {
          return $u->getUsername();
        }, $data);
        is_array($names);
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
      }),
      b()->withName('pipe7 map from generator with buffer')->withWarmupSource($generatorWarmupSource)->withDataSource($generatorDataSource)->withBenchmarkLogic(function ($generator) {
        yield memory_get_usage();
        $names = pipe($generator)->enableIntermediateResults()->map(function (BenchUser $u) {
          return $u->getUsername();
        });
        foreach ($names as $name) {
          yield memory_get_usage();
          someTask($name);
        }
        yield memory_get_usage();
      }),
    ],
  ];

  $results = [];
  $csv = [];

  foreach ($benchSetSizes as $benchSetSize) {
    $benchmarkSetCount = count($benchmarkSets);
    $currentBenchmarkSetNr = 0;
    $benchSetSizeFormat = number_format($benchSetSize);
  
    echo PHP_EOL . 'Benchmark data set size: ' . $benchSetSizeFormat . ' entries' . PHP_EOL;
    echo 'Warm-up data set size: ' . number_format($warmupSetSize) . ' entries' . PHP_EOL;
    echo PHP_EOL;

    foreach ($benchmarkSets as $benchmarkSet) {
      $baseline = $benchmarkSet[0];
      echo '  Running benchmark set ' . (++$currentBenchmarkSetNr) . '/' . $benchmarkSetCount . PHP_EOL;
      echo '  Running baseline benchmark: ' . $baseline->getName() . PHP_EOL;
      $baselineResult   = $baseline->run();
      $benchmarkSetSize = count($benchmarkSet);

      $baselineResultArray              = $baselineResult->toArray($baselineResult);
      $baselineResultArray['setSize']   = $benchSetSize;
      $baselineResultArray['name']      = $baselineResult->getName();
      $csv[] = $baselineResultArray;

      // $results[] = '######################################' . PHP_EOL;
      // $results[] = '######################################' . PHP_EOL . PHP_EOL;
      for ($i = 1; $i < $benchmarkSetSize; ++$i) {
        $bench = $benchmarkSet[$i];
        echo '  Running benchmark: ' . $bench->getName() . PHP_EOL;
        $benchmarkResult = $bench->run();
        $benchName                      = "{$benchmarkResult->getName()} -> {$baseline->getName()}";
        $baselineResultArray            = $benchmarkResult->toArray($baselineResult);
        $baselineResultArray['setSize'] = $benchSetSize;
        $baselineResultArray['name']    = $benchName;
        $csv[]                          = $baselineResultArray;
        $results[]                      = "{$benchName} ({$benchSetSizeFormat} items)\r\n{$benchmarkResult->comparedToBaseline($baselineResult)}\r\n\r\n";
      }
    }
  }

  // echo PHP_EOL . PHP_EOL . join('', $results);

  $csvContent = arrayToCsv($csv);
  file_put_contents('benchmark-result-' . time() . '.txt', $csvContent);
