<?php

include './src/models/zones_model.php';
include './src/controllers/zones.php';

use Slim\Http\Request;
use Slim\Http\Response;


$app->post('/api/zone/create',function(Request $request, Response $response){

    $cls = new Zones();
    $infos = $cls->create($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->get('/api/zones/load',function(Request $request, Response $response){

    $cls = new Zones();
    $infos = $cls->load($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});