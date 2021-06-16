<?php
require('./Helpers/General.php');
include_once './Routes/Request.php';
include_once './Routes/Router.php';
include './Controller/TestController.php';

$router = new Router(new Request);


$router->get('/data', [TestController::class, 'index']);
$router->get('/data/{id}', [TestController::class, 'detail']);
$router->get('/data2', [TestController::class, 'index']);
$router->get('/data3', [TestController::class, 'index']);
$router->get('/data', [TestController::class, 'index']);
