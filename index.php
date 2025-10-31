<?php
require_once 'Controller/categoriaControlador.php';
require_once 'Controller/tiendaController.php';
require_once 'Controller/usuarioController.php';

use MiladRahimi\PhpRouter\Router;
use Laminas\Diactoros\Response\JsonResponse;

//Agrego el código para el error de CORS
header("Access-Control-Allow-Origin: *"); // Usar "*" permite cualquier origen y puede exponer datos sensibles; en producción, reemplaza "*" por la URL específica para mayor seguridad
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$router = Router::create();

$router->get('/', function () {
    return new JsonResponse(['message' => 'ok']);
});

//Get tiendas
$router->get('/tiendas',[tiendasController::class,'getTiendas']);

$router->dispatch();