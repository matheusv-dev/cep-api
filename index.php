<?php
require_once "vendor/autoload.php";

use CoffeeCode\Router\Router;

header("Access-Control-Allow-Origin: *");

$router = new Router('/');
$router->namespace("App\Controller");
$router->get("/{cep}/", "CEP:Search", "CEP.Search");

$router->dispatch();

if ($router->error()) {
  print_r('<br> Has error: ' . $router->error());
}
