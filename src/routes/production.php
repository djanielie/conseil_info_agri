<?php

include './src/models/production_model.php';
include './src/controllers/production.php';

use Slim\Http\Request;
use Slim\Http\Response;


$app->post('/api/production/create',function(Request $request, Response $response){

    $cls = new Production();
    $infos = $cls->create_account($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->put('/api/production/update/{id}',function(Request $request, Response $response){

    $cls = new Production();
    $infos = $cls->create_update($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->get('/api/production/load',function(Request $request, Response $response){

    $cls = new Production();
    $infos = $cls->load_production($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->get('/api/production/load/{id}',function(Request $request, Response $response){

    $cls = new Production();
    $infos = $cls->load_by_id($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->delete('/api/production/delete/{id}',function(Request $request, Response $response){

    $cls = new Production();
    $infos = $cls->delete_production($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});
$app->put('/api/production/validate',function(Request $request, Response $response){

    $cls = new Production();
    $infos = $cls->ActiveProduction($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});
