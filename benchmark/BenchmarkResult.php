<?php

  class BenchmarkResult {
    private string $name;
    private int    $warmupRam;
    private int    $benchRam;
    private float  $warmupRuntime;
    private float  $benchRuntime;
    private int    $benchUsedRam;

    public function __construct(string $name, int $warmupRam, int $benchRam, int $benchUsedRam, float $warmupRuntime, float $benchRuntime) {
      $this->name = $name;
      $this->warmupRam = $warmupRam;
      $this->benchRam = $benchRam;
      $this->warmupRuntime = ((int)($warmupRuntime * 1000)) / 1000;
      $this->benchRuntime = ((int)($benchRuntime * 1000)) / 1000;
      $this->benchUsedRam = $benchUsedRam;
    }

    /**
     * @return string
     */
    public function getName(): string {
      return $this->name;
    }

    /**
     * @param bool $formatted
     * @return int
     */
    public function getWarmupRam(bool $formatted = false) {
      if ($formatted) {
        return formatRam($this->warmupRam);
      }
      return $this->warmupRam;
    }

    /**
     * @param bool $formatted
     * @return int
     */
    public function getBenchRam(bool $formatted = false) {
      if ($formatted) {
        return formatRam($this->benchRam);
      }
      return $this->benchRam;
    }

    /**
     * @return float
     */
    public function getWarmupRuntime(): float {
      return $this->warmupRuntime;
    }

    /**
     * @return float
     */
    public function getBenchRuntime(): float {
      return $this->benchRuntime;
    }

    /**
     * @param bool $formatted
     * @return int
     */
    public function getBenchUsedRam(bool $formatted = false) {
      if ($formatted) {
        return formatRam($this->benchUsedRam);
      }
      return $this->benchUsedRam;
    }

    public function comparedToBaseline(BenchmarkResult $baseline) {
      $runtimeDiff = ((int)($this->benchRuntime / $baseline->benchRuntime * 10000)) / 100;
      $ramDiff = $baseline->benchUsedRam ? (((int)($this->benchUsedRam / $baseline->benchUsedRam * 10000)) / 100 ?: '<1') : '100';
      $ramLeakDiff = $baseline->benchRam ? (((int)($this->benchRam / $baseline->benchRam * 10000)) / 100 ?: '<1') : '100';
      $result = "## runtime diff: $runtimeDiff% ({$this->getBenchRuntime()}s vs. {$baseline->getBenchRuntime()}s); ram diff: $ramDiff% ({$this->getBenchUsedRam(true)} vs. {$baseline->getBenchUsedRam(true)}); ram leak diff: $ramLeakDiff% ({$this->getBenchRam(true)} vs. {$baseline->getBenchRam(true)}) ##";
      $resultLength = strlen($result);
      $border = str_repeat('#', $resultLength);
      return "$border\r\n$result\r\n$border";
    }

    public function __toString(): string {
      return "$this->name:\r\n## warmup time: $this->warmupRuntime\r\n## warmup RAM: {$this->getWarmupRam(true)}\r\n## benchmark time: $this->benchRuntime\r\n## benchmark RAM: {$this->getBenchUsedRam(true)}\r\n### leaked RAM: {$this->getBenchRam(true)}";
    }
  }
