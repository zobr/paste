<?php

namespace app;

class Config extends Singleton {

    private $configs;

    // The special 'no_entry' value
    private $no_entry;

    public function __construct() {
        $config_dir = __DIR__ . '/../config';
        $env = $this->env();

        // Setup the special 'no_entry' value
        $this->no_entry = uniqid('config_no_entry_');

        // Load up the default config
        $this->configs = [
            'default' => require $config_dir . '/default.php',
        ];

        // Add an environment config
        $env_config = $config_dir . '/' . $env . '.php';
        if (file_exists($env_config)) {
            $this->configs[$env] = require $env_config;
        }
    }

    // Returns current environment
    public function env() {
        return getenv('APP_ENV') ? getenv('APP_ENV') : 'local';
    }

    // Returns a config entry. Path is a dot-delimited reference to the
    // config entry. E.g.: 'slim.renderer.template_path'.
    public function get($path = null, $namespace = null) {
        // Default to current environment namespace
        if ($namespace === null) {
            $namespace = $this->env();
        }
        // Default to non-existing entry (use a special value)
        $entry = $this->no_entry;
        // Namespace config is not empty
        if (isset($this->configs[$namespace])) {
            // Assign the config
            $config = $this->configs[$namespace];
            // If no path is specified, whole config is the default.
            $entry = $config;
            // Path was specified
            if ($path !== null) {
                // Traverse the namespace config
                $levels = explode('.', $path);
                foreach ($levels as $level) {
                    // Nothing to see here
                    if (!isset($entry[$level])) {
                        $entry = $this->no_entry;
                        break;
                    }
                    // Go deeper into the config
                    $entry = $entry[$level];
                }
            }
        }
        // Entry not found
        if ($entry === $this->no_entry) {
            // Nothing was found
            if ($namespace === 'default') {
                throw new Error("Path {$path} does not exist in config!");
            }
            // Try with default namespace
            return $this->get($path, 'default');
        }
        return $entry;
    }

}
