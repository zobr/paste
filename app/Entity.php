<?php

namespace app;

use \DateTime;
use \MongoDB\BSON\UTCDateTime;

class Entity {

    public function __construct($data = []) {
        if (is_object($data)) {
            $data = (array) $data;
        }
        if (is_array($data)) {
            foreach ($data as $i => $value) {
                if ($value instanceof UTCDateTime) {
                    $this->{$i} = $value->toDateTime();
                    continue;
                }
                $this->{$i} = $value;
            }
        }
    }

    public function toMongoDocument() {
        $data = (array) $this;
        foreach ($data as $i => $value) {
            if ($value instanceof DateTime) {
                $data[$i] = new UTCDateTime($value->getTimestamp() . '000');
            }
        }
        return $data;
    }

    public function toArray() {
        return (array) $this;
    }

}
