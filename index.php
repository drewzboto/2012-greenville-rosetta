<?php
require_once 'vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path'       => __DIR__.'/views'
    )
);

$app['http'] = $app->share(function() {
    return new Guzzle\Service\Client('https://api.github.com');
});

$app['anonymous'] = $app->share(function() use ($app) {
    $user = new GitHub\User($app['http']);
    $user->login('rosetta-anon', 'rosetta123');
    return $user;
});

function getHackdayResponse($content)
{
    $response = new Response();
    $response->headers->set('Content-Type', 'application/vnd.org.restfest.2012.hackday+xml');
    $response->setContent($content);
    return $response;
}

$app->get('/', function() use ($app) {
    return getHackDayResponse($app['twig']->render('entry.twig', array()));
});

$app->get('/rels/tickets', function() use ($app) {
    $github = new RestFest\GitHub($app['http'], $app['anonymous']);
    return $github->getIssues();
});

$app->run();
