<?php

include './src/models/produit_model.php';
include './src/controllers/produit.php';

use Slim\Http\Request;
use Slim\Http\Response;

// PRODUIT
$app->post('/api/produit/create',function(Request $request, Response $response){

    $cls = new  Produit();
    $infos = $cls->create_produit($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->put('/api/produit/update/{id}',function(Request $request, Response $response){

    $cls = new  Produit();
    $infos = $cls->create_update_produit($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});
$app->post('/api/produit/update-image/{id}',function(Request $request, Response $response){

    $cls = new  Produit();
    $infos = $cls->update_image($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->delete('/api/produit/delete/{id}',function(Request $request, Response $response){

    $cls = new  Produit();
    $infos = $cls->delete_produit($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->get('/api/produit/load',function(Request $request, Response $response){

    $cls = new  Produit();
    $infos = $cls->load_produit($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->get('/api/produit/load/{id}',function(Request $request, Response $response){

    $cls = new  Produit();
    $infos = $cls->load_produit($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});


// CATEGORIE
$app->post('/api/produit/categorie/create',function(Request $request, Response $response){

    $cls = new  Produit();
    $infos = $cls->create_categorie($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->put('/api/produit/categorie/update/{id}',function(Request $request, Response $response){

    $cls = new  Produit();
    $infos = $cls->create_update_categorie($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});

$app->delete('/api/produit/categorie/delete/{id}',function(Request $request, Response $response){

    $cls = new  Produit();
    $infos = $cls->create_delete($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});
$app->get('/api/produit/categorie/load',function(Request $request, Response $response){

    $cls = new  Produit();
    $infos = $cls->load_categorie($request);

    return $response
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
    ->withStatus($infos["status"]);
});