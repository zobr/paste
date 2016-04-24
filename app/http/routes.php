<?php

namespace app;

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
    try {
        $paste = new Paste([
            'text' => $text,
            'syntax' => $syntax,
        ]);
        $this->pasteRepository->save($paste);
    } catch (Exception $e) {
        var_export($e);
        exit;
        return $res->withStatus(301)->withHeader('Location', '/');
    }
    return $res->withStatus(301)->withHeader('Location', '/' . $paste->uid);
});

$app->get('/show/last', function ($req, $res, $args) {
    $paste = $this->pasteRepository->getLast();
    return $this->renderer->render($res, 'paste-show.phtml', [
        'title' => "View paste {$paste->uid}",
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

$app->get('/{uid}', function ($req, $res, $args) {
    $paste = $this->pasteRepository->getByUid($args['uid']);
    if (!$paste) {
        return $res->withStatus(301)->withHeader('Location', '/');
    }
    return $this->renderer->render($res, 'paste-show.phtml', [
        'title' => "View paste {$paste->uid}",
        'description' => "Paste {$paste->uid} in {$paste->syntax}",
        'paste' => $paste,
    ]);
});

// $app->get('/login', function ($req, $res, $args) {
//     return $this->renderer->render($res, 'login.phtml', []);
// });

// $app->post('/login', function ($req, $res, $args) {
//     $body = $req->getParsedBody();
//     $user = $this->session->login($body['email'], $body['password']);
//     if ($user) {
//         return $res->withStatus(301)->withHeader('Location', '/');
//     }
//     return $this->renderer->render($res, 'login.phtml', [
//         'error' => 'Invalid email or password',
//     ]);
// });

// $app->get('/logout', function ($req, $res, $args) {
//     $this->session->logout();
//     return $res->withStatus(301)->withHeader('Location', '/login');
// });

// $app->get('/image/upload', function ($req, $res, $args) {
//     return $this->renderer->render($res, 'image_upload.phtml', []);
// });

// $app->post('/image/upload', function ($req, $res, $args) {
//     $files = $req->getUploadedFiles('file');
//     $this->imageRepository->bulkCreate($files['file']);
//     $this->session->addFlashMessage('Upload successful!');
//     return $res->withStatus(301)->withHeader('Location', '/image/upload');
// });
