<?php

namespace app;

use \Slim\Views\PhpRenderer;

$container = $app->getContainer();


// --------------------------------------------------------
//  External dependencies
// --------------------------------------------------------

// Renderer
$container['renderer'] = function ($app) {
    $settings = $app->get('settings')['renderer'];
    return new PhpRenderer($settings['template_path']);
};


// --------------------------------------------------------
//  Internal dependencies
// --------------------------------------------------------

$container['config'] = Config::getFactory();
$container['mongo'] = Mongo::getFactory();
$container['pasteRepository'] = PasteRepository::getFactory();
