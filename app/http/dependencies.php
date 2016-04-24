<?php

namespace app;

use \Slim\Views\PhpRenderer;

$container = $app->getContainer();


// --------------------------------------------------------
//  External dependencies
// --------------------------------------------------------

// Renderer
$container['renderer'] = function ($container) {
    $template_path = $container->config->get('slim.renderer.template_path');
    return new PhpRenderer($template_path);
};


// --------------------------------------------------------
//  Internal dependencies
// --------------------------------------------------------

$container['config'] = Config::getFactory();
$container['mongo'] = Mongo::getFactory();
$container['pasteRepository'] = PasteRepository::getFactory();
