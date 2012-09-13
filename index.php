<?php
require_once 'vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path' => 'views'
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

$app['github'] = $app->share(function() use ($app) {
    return new RestFest\GitHub($app['http'], $app['anonymous']);
});

function getHackdayResponse($content)
{
    $response = new Response();
    $response->headers->set('Content-Type', 'application/vnd.org.restfest.2012.hackday+xml');
    $response->setContent($content);
    return $response;
}

$app->get('/', function() use ($app) {
    return getHackdayResponse($app['twig']->render('entry.twig', array('self' => "http://{$_SERVER['HTTP_HOST']}")));
});

$app->get('/tickets/', function() use ($app) {
    return $app['github']->getIssues();
});

$app->post('/tickets/', function(Request $r) use ($app) {
    try {
        if ($number = $app['github']->createIssue($r->getContent())) {
            return new Response('', 201, array('Location' => "http://{$_SERVER['HTTP_HOST']}/tickets/$number"));
        }
    } catch (\InvalidArgumentException $e) {
        return new Response('Invalid payload', 400);
    }

    return new Response('', 500);
});

$app->get('/tickets/{id}', function($id) use ($app) {
    return getHackdayResponse($app['github']->getIssue($id));
});

$app->put('/tickets/{id}', function(Request $r, $id) use ($app) {
    if ($app['github']->updateIssue($id, $r->getContent())) {
        return new Response('', 201);
    }

    return new Response('', 500);
});

$app->run();
