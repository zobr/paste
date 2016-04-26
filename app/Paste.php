<?php

namespace app;

class Paste extends Entity {

    public function __construct($data) {
        parent::__construct($data);
        // Ensure compatibility with earlier versions of pastes
        if ($this->version < 3) {
            if (isset($data['user']['ip'])) {
                unset($this->user);
                $this->submitterIp = $data['user']['ip'];
            }
            if (isset($data['dateCreated'])) {
                unset($this->dateCreated);
                $this->createdAt = $data['dateCreated']->toDateTime();
            }
            $this->version = 3;
        }
        if (!isset($this->uid)) {
            $this->stripWhitespace();
            $this->generateUid();
        }
    }

    public function generateUid($length = 7) {
        $hash = base64_encode(sha1($this->text, true));
        $hash = str_replace('+', '', $hash);
        $hash = str_replace('/', '', $hash);
        $hash = str_replace('=', '', $hash);
        $hash = substr($hash, 0, $length);
        $this->uid = $hash;
        return $this;
    }

    public function toShortLine($base_uri = '') {
        // Format the date
        $date = $this->createdAt->format('Y-m-d H:i:s');
        // Create shortline
        $shortline = $base_uri . '/' . $this->uid
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
        $min_indent = null;
        foreach ($lines as $i => $line) {
            preg_match('/^(\s+)/', $line, $matches);
            $indent = strlen($matches[0]);
            if ($min_indent === null || $indent < $min_indent) {
                $min_indent = $indent;
            }
        }
        foreach ($lines as $i => $line) {
            $lines[$i] = substr($line, $min_indent);
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
