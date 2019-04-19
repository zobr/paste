<?php

namespace App;

class Paste extends Entity {

  public function __construct($data) {
    parent::__construct($data);
    if (!isset($this->id)) {
      $this->stripWhitespace();
      $this->generateId();
    }
  }

  public function generateId($length = 7) {
    $hash = base64_encode(sha1($this->text, true));
    $hash = str_replace('+', '', $hash);
    $hash = str_replace('/', '', $hash);
    $hash = str_replace('=', '', $hash);
    $hash = substr($hash, 0, $length);
    $this->id = $hash;
    return $this;
  }

  public function toShortLine($base_uri = '') {
    // Format the date
    $date = $this->createdAt->format('Y-m-d H:i:s');
    // Create shortline
    $shortline = $base_uri . '/' . $this->id
      . ' | ' . self::pad($this->syntax, 10)
      . ' | ' . self::pad($date, 19)
      . ' | ' . self::pad($this->submitterIp, 15)
      . ' | ' . self::pad($this->text, 40);
    return $shortline;
  }

  public function stripWhitespace() {
    $text = $this->text;
    $text = str_replace("\r", '', $text);
    $text = str_replace("\t", '  ', $text);
    $lines = explode("\n", $text);
    $minIndent = null;
    foreach ($lines as $i => $line) {
      if (strlen($line) === 0) {
        continue;
      }
      preg_match('/^(\s+)/', $line, $matches);
      $indent = strlen($matches[0] ?? '');
      if ($minIndent === null || $indent < $minIndent) {
        $minIndent = $indent;
      }
    }
    if ($minIndent === null) {
      $minIndent = 0;
    }
    foreach ($lines as $i => $line) {
      $lines[$i] = substr($line, $minIndent);
    }
    $text = implode("\n", $lines);
    $this->text = trim($text);
    return $this;
  }

  // Paste-specific helper function for string padding
  public static function pad($text, $n) {
    $text = str_replace("\n", '', $text);
    $text = str_replace("\r", '', $text);
    $text = str_replace("\t", '', $text);
    $text = mb_substr($text, 0, $n);
    $text = str_pad($text, $n, ' ');
    return $text;
  }

}
