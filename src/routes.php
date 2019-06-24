<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });

    $app->group('api/', function() use ($app) {
        $app->get('/queries', 'elasticQueries');
        $app->post('/video/{id}', 'addVideoElasticData');
        $app->post('/user/{id}', 'addUserElasticData');
        $app->delete('/user/{id}', 'deleteUserElasticData');
        $app->delete('/video/{id}', 'deleteVideoElasticData');
        $app->put('/video/{id}', 'updateUserElasticData');
        $app->put('/video/{id}', 'updateVideoElasticData');
    });
};
