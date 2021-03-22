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
      (function($ds, $bench) {
        $bench(($ds)());
      })($this->warmupSource, $bench);
      $benchStartTime     = microtime(true);
      $ramUsedInBenchmark = (function($ds, $bench) {
        return iterator_to_array($bench(($ds)()));
      })($this->dataSource, $bench);
      $benchEndTime       = microtime(true);
      return new BenchmarkResult($this->name, $ramUsedInBenchmark, $benchEndTime - $benchStartTime);
    }
  }
