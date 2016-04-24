<?php

namespace app;

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

// IP filtering middleware
$app->add(function ($req, $res, $next) {
    $real_ip = $_SERVER['REMOTE_ADDR'];
    $blacklist = $this->config->get('ip.blacklist');
    $whitelist = $this->config->get('ip.whitelist');
    foreach ($whitelist as $ip) {
        $match = Helper::matchIpAddr($ip, $real_ip);
        if ($match) {
            return $next($req, $res);
        }
    }
    foreach ($blacklist as $ip) {
        $match = Helper::matchIpAddr($ip, $real_ip);
        if ($match) {
            $res->withStatus(403);
            echo '<pre>You were blacklisted.' . "\n"
                . 'If it was by mistake, please send me an email.</pre>';
            exit;
        }
    }
    return $next($req, $res);
});
