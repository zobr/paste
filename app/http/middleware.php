<?php

namespace app;

use \Psr7Middlewares\Middleware;

// Authorization middleware
$app->add(function ($req, $res, $next) {
    $users = $this->config->get('auth.users');
    $routes = $this->config->get('auth.routes');
    $route = $req->getUri()->getPath();
    if (in_array($route, $routes)) {
        $auth_user = @$_SERVER['PHP_AUTH_USER'];
        $auth_password = @$_SERVER['PHP_AUTH_PW'];
        if ($auth_user && isset($users[$auth_user])) {
            $valid = password_verify($auth_password, $users[$auth_user]);
            if ($valid) {
                return $next($req, $res);
            }
        }
        // Show a basic auth dialog
        return $res->withStatus(401)
            ->withHeader('WWW-Authenticate', 'Basic realm="Restricted area"');
        echo '<pre>Sorry, you are not allowed to be here! :c</pre>';
    }
    return $next($req, $res);
});
