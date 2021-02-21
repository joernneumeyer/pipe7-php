<?php

  require __DIR__ . '/vendor/autoload.php';

  class BenchUser {
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $username;
    /**
     * @var int
     */
    private $age;

    public function __construct(int $id, string $username, int $age) {
      $this->id       = $id;
      $this->username = $username;
      $this->age      = $age;
    }

    /**
     * @return int
     */
    public function getId(): int {
      return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string {
      return $this->username;
    }

    /**
     * @return int
     */
    public function getAge(): int {
      return $this->age;
    }
  }

  function someName() {
    static $names = ['Alice', 'Bob', 'Charlie', 'Dan', 'Emma'];
    return $names[rand(0, count($names) - 1)];
  }

  function someAge() {
    return rand(0, 80);
  }

  ini_set('memory_limit', '2048M');
  ini_set('opcache.jit', 1215);

  echo 'mapping, filtering, and reducing on 5,000,000 users, with a warmup of 10,000 users' . PHP_EOL;
  echo '# Regular array functions:' . PHP_EOL;
  (function () {
    echo '## Generating test data' . PHP_EOL;
    $warmupUsers = [];
    $benchUsers  = [];
    for ($i = 1; $i <= 10000; ++$i) {
      $warmupUsers[] = new BenchUser($i, someName(), someAge());
    }
    for ($i = 1; $i <= 5000000; ++$i) {
      $benchUsers[] = new BenchUser($i, someName(), someAge());
    }
    function benchArray($data) {
      $overFifty           = array_filter($data, function (BenchUser $u) {
        return $u->getAge() >= 50;
      });
      $names               = array_map(function (BenchUser $u) {
        return $u->getUsername();
      }, $overFifty);
      array_reduce($names, function ($carry, string $name) {
        if (isset($carry[$name[0]])) {
          ++$carry[$name[0]];
        } else {
          $carry[$name[0]] = 1;
        }
        return $carry;
      }, []);
      return memory_get_usage();
    }

    // most common start letter over 50
    echo '## Warming up..' . PHP_EOL;
    benchArray($warmupUsers);
    echo '## Starting benchmark..' . PHP_EOL;
    $tStart = microtime(true);
    $usage = benchArray($benchUsers);
    $tEnd  = microtime(true);
    $tDiff = $tEnd - $tStart;
    $tDiff = ((int)($tDiff * 1000)) / 1000;
    echo '## Time Elapsed: ' . $tDiff . 's; Memory Usage:' . (((int)($usage / (1024 * 1024) * 100)) / 100) . 'MBytes' . PHP_EOL;
  })();

  echo PHP_EOL;

  echo '# pipe7:' . PHP_EOL;
  (function () {
    echo '## Generating test data' . PHP_EOL;
    $warmupUsers = [];
    $benchUsers  = [];
    for ($i = 1; $i <= 10000; ++$i) {
      $warmupUsers[] = new BenchUser($i, someName(), someAge());
    }
    for ($i = 1; $i <= 5000000; ++$i) {
      $benchUsers[] = new BenchUser($i, someName(), someAge());
    }
    function benchPipe7($data) {
      pipe($data)->filter(function (BenchUser $u) {
        return $u->getAge() >= 50;
      })->map(function (BenchUser $u) {
        return $u->getUsername();
      })->reduce(function ($carry, string $name) {
        if (isset($carry[$name[0]])) {
          ++$carry[$name[0]];
        } else {
          $carry[$name[0]] = 1;
        }
        return $carry;
      }, []);
      return memory_get_usage();
    }

    // most common start letter over 50
    echo '## Warming up..' . PHP_EOL;
    benchPipe7($warmupUsers);
    echo '## Starting benchmark..' . PHP_EOL;
    $tStart = microtime(true);
    $usage = benchPipe7($benchUsers);
    $tEnd  = microtime(true);
    $tDiff = $tEnd - $tStart;
    $tDiff = ((int)($tDiff * 1000)) / 1000;
    echo '## Time Elapsed: ' . $tDiff . 's; Memory Usage:' . (((int)($usage / (1024 * 1024) * 100)) / 100) . 'MBytes' . PHP_EOL;
  })();

  echo PHP_EOL;

  echo '# pipe7 using a generator as a data source:' . PHP_EOL;
  (function () {
    echo '## Generating test data' . PHP_EOL;
    function makeSampleUsers($n) {
      for ($i = 1; $i <= $n; ++$i) {
        yield new BenchUser($i, someName(), someAge());
      }
    }
    function benchPipe7Generator($data) {
      pipe($data)->filter(function (BenchUser $u) {
        return $u->getAge() >= 50;
      })->map(function (BenchUser $u) {
        return $u->getUsername();
      })->reduce(function ($carry, string $name) {
        if (isset($carry[$name[0]])) {
          ++$carry[$name[0]];
        } else {
          $carry[$name[0]] = 1;
        }
        return $carry;
      }, []);
      return memory_get_usage();
    }

    // most common start letter over 50
    echo '## Warming up..' . PHP_EOL;
    benchPipe7Generator(makeSampleUsers(10000));
    echo '## Starting benchmark..' . PHP_EOL;
    $tStart = microtime(true);
    $usage = benchPipe7Generator(makeSampleUsers(5000000));
    $tEnd  = microtime(true);
    $tDiff = $tEnd - $tStart;
    $tDiff = ((int)($tDiff * 1000)) / 1000;
    echo '## Time Elapsed: ' . $tDiff . 's; Memory Usage:' . (((int)($usage / (1024 * 1024) * 100)) / 100) . 'MBytes' . PHP_EOL;
  })();

