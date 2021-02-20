<?php

  if (!class_exists('User')) {
    class User {

      /**
       * @var int
       */
      private $id;
      /**
       * @var string
       */
      private $username;
      /**
       * @var string
       */
      private $firstName;
      /**
       * @var string
       */
      private $lastName;
      /**
       * @var int
       */
      private $age;

      public function __construct(int $id, string $username, string $firstName, string $lastName, int $age) {
        $this->id        = $id;
        $this->username  = $username;
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
        $this->age       = $age;
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
       * @return string
       */
      public function getFirstName(): string {
        return $this->firstName;
      }

      /**
       * @return string
       */
      public function getLastName(): string {
        return $this->lastName;
      }

      /**
       * @return int
       */
      public function getAge(): int {
        return $this->age;
      }
    }
  }

  $data = [new User(1, 'foobar', 'John', 'Doe', 42), new User(2, 'example', 'Max', 'Mustermann', 33),
    new User(3, 'Matrixx', 'Alice', 'Wonderland', 66), new User(4, 'neo', 'Keanu', 'Reeves', 45),
    new User(5, 'rick', 'Rick', 'Sanchez', 70), new User(6, 'morty', 'Morty', 'Smith', 14)];

  return $data;
