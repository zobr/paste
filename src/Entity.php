<?php

namespace App;

use \DateTimeImmutable;

class Entity {

  public function __construct($data = []) {
    if (is_object($data)) {
      $data = (array) $data;
    }
    if (is_array($data)) {
      foreach ($data as $i => $value) {
        // Convert date-looking strings to DateTime
        if (preg_match('/^\d{4}-\d{2}-\d{2}\s.+$/', $value)) {
          $this->{$i} = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s", $value);
          continue;
        }
        $this->{$i} = $value;
      }
    }
  }

  public function toArray() {
    return (array) $this;
  }

}
