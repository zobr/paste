<?php

namespace app;

use \MongoDB\Client as MongoClient;

class Mongo extends Singleton {

    public function __construct($container) {
        $dbname = $container->config->get('mongo.database');
        $this->client = new MongoClient();
        $this->db = $this->client->{$dbname};
    }

}
