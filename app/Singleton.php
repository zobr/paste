<?php

namespace app;

class Singleton {

    protected static $instances;

    protected function __construct() {}

    final private function __clone() {}

    public static function getInstance() {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
    }

    public static function getFactory() {
        $class = get_called_class();
        return function ($container) use ($class) {
            return new $class($container);
        };
    }

}
