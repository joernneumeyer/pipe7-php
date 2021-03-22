<?php

  class BenchmarkResult {
    private $name;
    private $benchRam;
    private $benchRuntime;
    private $benchRamPeak;

    public function __construct(string $name, array $memoryUsage, float $benchRuntime) {
      $this->name = $name;
      $this->benchRuntime = ((int)($benchRuntime * 1000)) / 1000;
      $this->benchRam = $memoryUsage;
      $this->benchRamPeak = array_reduce($memoryUsage, function($max, $x) { return max($max, $x); }, 0) - $memoryUsage[0];
    }

    /**
     * @return string
     */
    public function getName(): string {
      return $this->name;
    }

    /**
     * @return array
     */
    public function getBenchRam() {
      return $this->benchRam;
    }

    /**
     * @return float
     */
    public function getBenchRuntime(): float {
      return $this->benchRuntime;
    }

    public function comparedToBaseline(BenchmarkResult $baseline) {
      $runtimeDiff = ((int)($this->benchRuntime / $baseline->benchRuntime * 10000)) / 100;
      $ramPeakDiff = $baseline->benchRam ? (((int)($this->benchRamPeak / $baseline->benchRamPeak * 10000)) / 100 ?: '<1') : '100';
      $formattedRamPeak = formatRam($this->benchRamPeak);
      $formattedRamPeakBaseline = formatRam($baseline->benchRamPeak);
      $result = "## runtime diff: $runtimeDiff% ({$this->getBenchRuntime()}s vs. {$baseline->getBenchRuntime()}s); ram peak diff: $ramPeakDiff% ({$formattedRamPeak} vs. {$formattedRamPeakBaseline}) ##";
      $resultLength = strlen($result);
      $border = str_repeat('#', $resultLength);
      return "$border\r\n$result\r\n$border";
    }

    public function __toString(): string {
      return "$this->name:\r\n\r\n\r\n## benchmark time: $this->benchRuntime\r\n## benchmark RAM: {$this->getBenchUsedRam(true)}\r\n### leaked RAM: {$this->getBenchRam(true)}";
    }
  }
