<?php

namespace app;

class Config extends Singleton {

    public $config;

    public function __construct() {
        // Set environment key
        $config_dir = __DIR__ . '/../config';
        $this->config = require $config_dir . '/default.php';

        // Set environment key
        $env = getenv('APP_ENV') ? getenv('APP_ENV') : 'local';
        $this->config['env'] = $env;

        // Override options by merging with another config
        if (file_exists($config_dir . '/' . $env . '.php')) {
            $env_config = require $config_dir . '/' . $env . '.php';
            foreach ($env_config as $i => $value) {
                $this->config[$i] = $value;
            }
        }
    }

    public function get($path = null) {
        if (!$path) {
            return $this->config;
        }
        $levels = explode('.', $path);
        $entry = $this->config;
        foreach ($levels as $level) {
            if (!isset($entry[$level])) {
                return null;
            }
            $entry = $entry[$level];
        }
        return $entry;
    }

}
