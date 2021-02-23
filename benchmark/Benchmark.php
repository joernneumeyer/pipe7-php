<?php


  class Benchmark {
    private $name;
    private $dataSource;
    private $warmupSource;
    private $benchmarkLogic;

    public function withName(string $name) {
      $this->name = $name;
      return $this;
    }

    public function withDataSource($dataSource) {
      $this->dataSource = $dataSource;
      return $this;
    }

    public function withWarmupSource($warmupSource) {
      $this->warmupSource = $warmupSource;
      return $this;
    }

    public function withBenchmarkLogic(Closure $closure) {
      $this->benchmarkLogic = $closure;
      return $this;
    }

    /**
     * @return mixed
     */
    public function getName() {
      return $this->name;
    }



    public function run() {
      $bench           = $this->benchmarkLogic;
      $ramBeforeWarmup = memory_get_usage();
      $warmupStartTime = microtime(true);
      (function($ds, $bench) {
        $bench(($ds)());
      })($this->warmupSource, $bench);
      $warmupEndTime      = microtime(true);
      $ramAfterWarmup     = memory_get_usage();
      $ramBeforeBenchmark = memory_get_usage();
      $benchStartTime     = microtime(true);
      $ramUsedInBenchmark = (function($ds, $bench) {
        return $bench(($ds)());
      })($this->dataSource, $bench);
      $benchEndTime       = microtime(true);
      $ramAfterBenchmark  = memory_get_usage();
      return new BenchmarkResult($this->name, $ramAfterWarmup - $ramBeforeWarmup, $ramAfterBenchmark - $ramBeforeBenchmark, $ramUsedInBenchmark, $warmupEndTime - $warmupStartTime, $benchEndTime - $benchStartTime);
    }
  }
