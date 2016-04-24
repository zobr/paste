<?php

namespace app;

class Paste extends Entity {

    public function __construct($data) {
        parent::__construct($data);
        // Legacy stuff
        if ($this->version < 3) {
            if (isset($data['user']['ip'])) {
                unset($this->user);
                $this->submitterIp = $data['user']['ip'];
            }
            if (isset($data['dateCreated'])) {
                unset($this->dateCreated);
                $this->createdAt = $data['dateCreated'];
            }
        }
        if (!isset($this->uid)) {
            $this->uid = $this->hash();
        }
    }

    public function hash($length = 7) {
        if (isset($this->uid)) {
            return $this->uid;
        }
        $hash = base64_encode(sha1($this->text, true));
        $hash = str_replace('+', '', $hash);
        $hash = str_replace('/', '', $hash);
        $hash = str_replace('=', '', $hash);
        $hash = substr($hash, 0, $length);
        return $hash;
    }

    public function toShortLine() {
        // String padding function
        $pad = function ($text, $n) {
            $text = str_replace("\n", '', $text);
            $text = str_replace("\r", '', $text);
            $text = str_replace("\t", '', $text);
            $text = mb_substr($text, 0, $n);
            $text = str_pad($text, $n, ' ');
            return $text;
        };
        // TODO: Move base_url to config. How?
        $config = Config::getInstance();
        $base_uri = $config->get('base_uri');
        $date = $this->createdAt->format('Y-m-d H:i:s');
        // Create shortline
        $shortline = $base_uri . '/' . $this->uid
            . ' | ' . $pad($this->syntax, 10)
            . ' | ' . $pad($date, 19)
            . ' | ' . $pad($this->submitterIp, 15)
            . ' | ' . $pad($this->text, 40);
        return $shortline;
    }

}
