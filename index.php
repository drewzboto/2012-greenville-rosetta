<?php
require_once 'vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->get('/', function() use ($app) {
    $res = new Response('<ticket>Stuff</ticket>', 200, [ 'Content-Type' => 'application/vnd.org.restfest.hackday2012+xml' ]);
    return $res;
});

$app->run();
