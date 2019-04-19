<?php

namespace App;

use \Exception;
use \DateTime;
use \SQLite3;

class PasteRepository extends Singleton {

  public function __construct($container) {
    $this->config = $container->get('config');
    $this->db = new SQLite3(
      __BASE__ . '/storage/paste.db',
      SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    $this->db->query("
      CREATE TABLE IF NOT EXISTS 'paste' (
        'id'  VARCHAR NOT NULL,
        'syntax'  VARCHAR,
        'text'  VARCHAR,
        'submitterIp' VARCHAR,
        'createdAt' DATETIME,
        'updatedAt' DATETIME,
        PRIMARY KEY('id')
      );
      CREATE INDEX IF NOT EXISTS 'createdAt' ON 'paste' (
        'createdAt' ASC
      );
    ");
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
    $query = $this->db->prepare("
      SELECT * FROM 'paste'
      ORDER BY createdAt DESC
    ");
    $result = $query->execute();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $items[] = new Paste($row);
    }
    return $items;
  }

  public function getById($id) {
    $query = $this->db->prepare("
      SELECT * FROM paste
      WHERE id = :id
      LIMIT 1
    ");
    $query->bindValue(':id', $id);
    $row = $query->execute()->fetchArray(SQLITE3_ASSOC);
    if (!$row) {
      return null;
    }
    return new Paste($row);
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
    if (!isset($paste->createdAt)) {
      $paste->createdAt = new DateTime();
    }
    $paste->submitterIp = $_SERVER['REMOTE_ADDR'];
    $existingPaste = $this->getById($paste->id);
    if ($existingPaste) {
      $query = $this->db->prepare("
        UPDATE paste
        SET syntax = :syntax, updatedAt = :updatedAt
        WHERE id = :id
      ");
    }
    else {
      $query = $this->db->prepare("
        INSERT INTO paste
        ('id', 'syntax', 'text', 'submitterIp', 'createdAt', 'updatedAt')
        VALUES (:id, :syntax, :text, :submitterIp, :createdAt, :updatedAt)
      ");
    }
    $createdAt = $paste->createdAt->format('Y-m-d H:i:s');
    $query->bindParam(':id', $paste->id);
    $query->bindParam(':syntax', $paste->syntax);
    $query->bindParam(':text', $paste->text);
    $query->bindParam(':submitterIp', $paste->submitterIp);
    $query->bindParam(':createdAt', $createdAt);
    $query->bindParam(':updatedAt', $updatedAt);
    $query->execute();
    return $paste;
  }

}
