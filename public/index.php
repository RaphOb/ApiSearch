<?php

use Elasticsearch\ClientBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
$dependencies = require __DIR__ . '/../src/dependencies.php';
$dependencies($app);

// Register middleware
$middleware = require __DIR__ . '/../src/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../src/routes.php';
$routes($app);


// Run app
$app->run();

/**
 * @param Request $request
 * @param Response $response
 * @return array
 */
function elasticQueries(Request $request, Response $response)
{
    $param = $request->getQueryParams();
    $querie = $param['queri'];
    $client = ClientBuilder::create()->build();
    $param = [
        'index' => 'youtube',
        'body' => [
            'query' => [
                'query_string' => [
                    'query' => $querie . '~'
                ]
            ]
        ]
    ];
    return error_log(print_r(($response = $client->search($param))));
}

/**
 * @param Request $request
 * @param Response $response
 */
function addVideoElasticData(Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $client = ClientBuilder::create()->build();

    $param = [
        'index' => 'youtube',
        'id' => $id,
        'body' => [
            'typeOf' => 'videos',
            'name' => $data['name']
        ]
    ];
    $response = $client->index($param);
}

/**
 * @param Request $request
 * @param Response $response
 */
function deleteVideoElasticData(Request $request, Response $response)
{
    $client = ClientBuilder::create()->build();
    $id = $request->getAttribute('id');
    $param = [
        'index' => 'youtube',
        'type' => 'videos',
        'id' => $id
    ];

    $response = $client->delete($param);
}

/**
 * @param Request $request
 * @param Response $response
 */
function addUserElasticData(Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $client = ClientBuilder::create()->build();
    $param = [
        'index' => 'youtube',
        'type' => 'videos',
        'id' => $id,
        'body' => [
            'type' => 'users',
            'username' => $data['username'],
            'email' => $data['email']
        ]
    ];

    $response = $client->index($param);

}

/**
 * @param Request $request
 * @param Response $response
 */
function deleteUserElasticData(Request $request, Response $response)
{
    $client = ClientBuilder::create()->build();
    $id = $request->getAttribute('id');
    $param = [
        'index' => 'youtube',
        'type' => 'videos',
        'id' => $id
    ];

    $response = $client->delete($param);
}

/**
 * @param Request $request
 * @param Response $response
 */
function updateUserElasticData(Request $request, Response $response)
{
    $client = ClientBuilder::create()->build();
    $id = $request->getAttribute('id');
    $data = $request->getParsedBody();
    $params = [
        'index' => 'youtube',
        'type' => 'users',
        'id' => $id,
        'body' => [
            'doc' => [
                //TODO
            ]
        ]
    ];

    $response = $client->update($params);
}

/**
 * @param Request $request
 * @param Response $response
 */
function updateVideoElasticData(Request $request, Response $response)
{
    $client = ClientBuilder::create()->build();
    $id = $request->getAttribute('id');
    $data = $request->getParsedBody();
    $params = [
        'index' => 'youtube',
        'type' => 'videos',
        'id' => $id,
        'body' => [
            'doc' => [
                //TODO
            ]
        ]
    ];

    $response = $client->update($params);
}

