<?php
require_once './vendor/autoload.php';
require_once 'Controller/categoriaControlador.php';
require_once 'Controller/tiendaController.php';
require_once 'Controller/usuarioController.php';
require_once 'Controller/authController.php';

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
//EndPoints para categoria
$router->get('/tiendas',[CategoriasController::class,'mostrar']);

//Endpoints para los emprendimientos
//GETs
$router->get('/tiendas',[tiendasController::class,'getTiendas']);//normal sin parametros
$router->get('/tiendas/{id_negocio}',[tiendasController::class,'getTiendas']);//por id
$router->get('/tiendas/{categoria}',[tiendasController::class,'getTiendas']);//categoria
$router->get('/tiendas/{nombre_negocio}',[tiendasController::class,'getTiendas']);//nombre

$router->post('/tiendas',[tiendasController::class,'createTiendas']);
$router->patch('/tiendas',[tiendasController::class,'updateTienda']);
$router->delete('/tiendas',[tiendasController::class,'deleteTienda']);

//EndPoint Redes terminarlo
$router->get('/tiendas{id}',[tiendasController::class,'getRedes']);
$router->post('/tiendas',[tiendasController::class,'createRedesTiendas']);
$router->patch('/tiendas/{id}',[tiendasController::class,'redesUpdate']);
$router->delete('/tiendas/{id}',[tiendasController::class,'deleteRedes']);

//Endpoint para el pago
//get
$router->get('/tiendas/{id_P}',[UsuarioController::class,'getPagos']);
//delete
$router->delete('/tiendas/{id_p}',[UsuarioController::class,'deletePago']);
//post
$router->post('/tiendas/{email}',[UsuarioController::class,'createPago']);

//Endpoints para los usuarios
//get
$router->get('/tiendas',[UsuarioController::class,'getByEmail']);
//delete
$router->delete('/tiendas/{email}',[UsuarioController::class,'delete']);
//post
$router->post('/tiendas',[UsuarioController::class,'CreateUser']);
//put
$router->put('/tiendas/{email}',[UsuarioController::class,'updatePersonalInfo']);
//path - pass
$router->patch('/tiendas/{email}',[UsuarioController::class,'updatePass']);


//ENDPOINT INCIO DE SESION (LOGIN)
$router->post('/auth/login', function($request) use ($conexion) {
    $data = json_decode($request->getBody()->getContents(), true);

    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    $authController = new AuthController($conexion);
    return $authController->login($email,$password);
});

$router->dispatch();