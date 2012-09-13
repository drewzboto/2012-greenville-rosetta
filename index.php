<?php
require_once 'vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['http'] = $app->share(function() {
    return new Guzzle\Service\Client('https://api.github.com');
});

$app['anonymous'] = $app->share(function() use ($app) {
    $user = new GitHub\User($app['http']);
    $user->login('rosetta-anon', 'rosetta123');
    return $user;
});

$app->get('/', function() use ($app) {
    $res = new Response('<ticket>Stuff</ticket>', 200, array('Content-Type' => 'application/vnd.org.restfest.hackday2012+xml'));
    return $res;
});

$app->get('/rels/tickets', function() use ($app) {
    $github = new RestFest\GitHub($app['http'], $app['anonymous']);
    return $github->getIssues();
});

$app->run();
