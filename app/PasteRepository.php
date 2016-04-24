<?php

namespace app;

use \Exception;
use \DateTime;

class PasteRepository extends Singleton {

    public function __construct($container) {
        $this->config = $container->config;
        $collection = $this->config->get('paste.collection');
        $this->collection = $container->mongo->db->{$collection};
    }

    public function rebuildIndex() {
        $this->collection->createIndex([ 'uid' => 1 ]);
    }

    public function isSyntaxAvailable($syntax) {
        $syntax_list = $this->config->get('paste.syntax_list');
        return array_key_exists($syntax, $syntax_list);
    }

    public function getSortedSyntaxList() {
        $syntax_list = $this->config->get('paste.syntax_list');
        $sorted_list = [];
        foreach ($syntax_list as $i => $aliases) {
            foreach ($aliases as $alias) {
                $sorted_list[] = [
                    'key' => $i,
                    'name' => $alias,
                ];
            }
        }
        uasort($sorted_list, function ($a, $b) {
            if ($a['key'] === 'plain') {
                return -1;
            }
            if ($b['key'] === 'plain') {
                return 1;
            }
            return strcmp($a['name'], $b['name']);
        });
        return $sorted_list;
    }

    public function getAll() {
        $cursor = $this->collection->find([], [
            'sort' => [ '_id' => -1 ],
            'limit' => 1000,
        ]);
        $items = [];
        foreach ($cursor as $document) {
            $items[] = new Paste($document);
        }
        return $items;
    }

    public function getByUid($uid) {
        $document = $this->collection->findOne([
            'uid' => $uid,
        ]);
        if (!$document) {
            return null;
        }
        return new Paste($document);
    }

    public function getLast() {
        $document = $this->collection->findOne([], [
            'sort' => [ '_id' => -1 ],
        ]);
        if (!$document) {
            return null;
        }
        return new Paste($document);
    }

    public function save(Paste $paste) {
        if (!$this->isSyntaxAvailable($paste->syntax)) {
            $paste->syntax = $this->config->get('paste.syntax_default');
        }
        if (!isset($paste->dateCreated)) {
            $paste->createdAt = new DateTime();
        }
        $paste->updatedAt = new DateTime();
        $paste->submitterIp = $_SERVER['REMOTE_ADDR'];
        $paste->version = $this->config->get('paste.version');
        $status = $this->collection->replaceOne([
            'uid' => $paste->uid,
        ], $paste->toMongoDocument(), [
            'upsert' => true,
        ]);
        $this->rebuildIndex();
        if (!$status) {
            throw new Exception('paste.save');
        }
        return $this;
    }

}
