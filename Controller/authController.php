<?php
//ACÁ SE VAN A MANEJAR LOS TOKENS DEL INICIO DE SESIÓN PARA DESP USARLOS EN JS

use Firebase\JWT\JWT;
use Laminas\Diactoros\Response\JsonResponse;

require_once __DIR__ . '/../Models/Usuarios.php';

class AuthController {
    private $usuarios;
    private $secretKey;

    public function __construct($conexion) {
        $this->usuarios = new Usuarios($conexion);
        $this->secretKey = getenv("JWT_SECRET");
    }

    public function login($email, $password) {
        $user = $this->usuarios->getUserEmail($email);

        if (!$user) {
            return new JsonResponse(["error" => "Usuario o contraseña incorrectos"], 401);
        }
        if (!password_verify($password, $user['password'])) {
            return new JsonResponse(["error" => "Usuario o contraseña incorrectos"], 401);
        }

        // Cargo los datos para el front
        $payload = [
            "email" => $user["email"],
            "dni_usuario" => $user["dni_usuario"],
            "nombre_usuario" => $user["nombre_usuario"],
            "nombreCompleto" => $user["nombreCompleto"],
            "num_telefono" => $user["num_telefono"],
            "membresia" => $user["membresia"] ?? 0, 
            "exp" => time() + 3600 // expira en 1 hora
        ];

        $jwt = JWT::encode($payload, $this->secretKey, "HS256");

        // Devolvemos token y datos del usuario
        return new JsonResponse([
            "token" => $jwt,
            "usuario" => $payload
        ]);
    }
}