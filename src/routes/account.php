<?php

include './src/models/account_model.php';
include './src/controllers/account.php';

use Slim\Http\Request;
use Slim\Http\Response;


$app->post('/api/account/create',function(Request $request, Response $response){

    $cls = new Account();
    $infos = $cls->create_account($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->post('/api/account/recover',function(Request $request, Response $response){

    $cls = new Account();
    $infos = $cls->recover_account($request);

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
        ->withStatus($infos["status"]);
});

$app->post('/api/account/login',function(Request $request, Response $response){

    $cls = new Account();
    $infos = $cls->login($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->put('/api/account/verify',function(Request $request, Response $response){

    $cls = new Account();
    $infos = $cls->verify($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->get('/api/account/categorie/load',function(Request $request, Response $response){

    $cls = new Account();
    $infos = $cls->load_categories($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});