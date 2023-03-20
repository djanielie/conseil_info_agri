<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST,GET,DELETE,PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
use Middlware\Secure;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Slim\Http\Request;
use Slim\Http\Response;
use Tuupola\Middleware\JwtAuthentication;

require './vendor/autoload.php';
include './config/declare.php';

$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);
$container = $app->getContainer();

$logger = new Logger("slim");
$rotating = new RotatingFileHandler(URL_LOG_FILE_SLIM, 0, Logger::DEBUG);
$logger->pushHandler($rotating);

$app->add(new JwtAuthentication([
    'path' => '/api',
    'ignore' => [
        "/api/account/create",
        "/api/account/verify",
        "/api/account/login",
        "/api/account/categorie/load",
        "/api/zones/load",
        "/api/produit/categorie/load",
        "/api/production/load",
        "/api/account/recover"
    ],
    "secure" => false,
    "relaxed" => ["http://localhost:5000"],
    'attribute' => 'decoded_token_data',
    'secret' => JWT_SECRET,
    'algorithm' => JWT_ALGORITHM,
    "logger" => $logger,
    'error' => function ($response, $arguments) {
        $data['status'] = 402;
        $data['message'] = $arguments['message'];

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
            ->withStatus(401);
    }
]));

//config
include './config/Vars.php';
include './config/Dbo.php';
include './config/Sms_keccel.php';

// include src
include './src/index.php';
 
$app->get("/",function (Request $request,Response $response){

    $infos = ["message"=>"success", "status"=>200];
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($infos, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))->withStatus($infos["status"]);
});

$app->run();