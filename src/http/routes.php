<?php

namespace App;

use \Exception;

$app->get('/', function ($req, $res, $args) {
  return $this->renderer->render($res, 'paste-create.phtml', [
    'title' => 'Create new paste!',
  ]);
});

$app->post('/', function ($req, $res, $args) {
  $body = $req->getParsedBody();
  $text = $body['text'];
  $syntax = $body['syntax'];
  if (strlen($text) < 2) {
    return $res->withStatus(301)->withHeader('Location', '/');
  }
  $paste = new Paste([
    'text' => $text,
    'syntax' => $syntax,
  ]);
  $this->pasteRepository->save($paste);
  return $res->withStatus(301)->withHeader('Location', '/' . $paste->id);
});

$app->get('/show/last', function ($req, $res, $args) {
  $paste = $this->pasteRepository->getLast();
  return $this->renderer->render($res, 'paste-show.phtml', [
    'title' => "View paste {$paste->id}",
    'paste' => $paste,
  ]);
});

$app->get('/show/all', function ($req, $res, $args) {
  $pastes = $this->pasteRepository->getAll();
  return $this->renderer->render($res, 'paste-list.phtml', [
    'title' => 'View all pastes',
    'pastes' => $pastes,
  ]);
});

$app->get('/{id}', function ($req, $res, $args) {
  $paste = $this->pasteRepository->getById($args['id']);
  if (!$paste) {
    return $res->withStatus(301)->withHeader('Location', '/');
  }
  return $this->renderer->render($res, 'paste-show.phtml', [
    'title' => "View paste {$paste->id}",
    'description' => "Paste {$paste->id} in {$paste->syntax}.",
    'paste' => $paste,
  ]);
});
