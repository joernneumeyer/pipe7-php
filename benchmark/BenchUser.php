<?php

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
