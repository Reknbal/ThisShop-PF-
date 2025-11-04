<?php
//ACÁ SE VAN A MANEJAR LOS TOKENS DEL INICIO DE SESIÓN PARA DESP USARLOS EN JS
use Firebase\JWT\JWT;
require_once __DIR__ . '/../Models/Usuarios.php';

class AuthController {
    private $usuarios;
    private $secretKey;

    public function __construct($conexion) {
        $this->usuarios = new Usuarios($conexion);
        $this->secretKey = getenv("JWT_SECRET"); // cargamos del .env
    }

    public function login($email, $password) {
        $user = $this->usuarios->getUserEmail($email);

        if (!$user || $user['password'] !== $password) {
            return ["error" => "Usuario o contraseña incorrectos"];
        }

        $payload = [
            "id" => $user["id"],
            "nombre" => $user["nombre"],
            "email" => $user["email"],
            "exp" => time() + 3600
        ];

        $jwt = JWT::encode($payload, $this->secretKey, "HS256");

        return [
            "token" => $jwt,
            "usuario" => [
                "id" => $user["id"],
                "nombre" => $user["nombre"],
                "email" => $user["email"]
            ]
        ];
    }
}
