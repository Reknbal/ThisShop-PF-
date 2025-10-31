<?php 
require_once __DIR__ . '/../Models/Usuarios.php';
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;

class UsuarioController{
public function get($id){
$id_ent= (int) $id;

if($id_ent>0){
    $usuario= new Usuarios;
    return new JsonResponse($usuario->get($id));
}
return new JsonResponse(['Message'=>'Error identificador invalido']);
}

public function delete($id){
    if(!preg_match('/^[1-9]\d*$/',$id))
    {   
        return new JsonResponse(['Message'=>'Error id inválido']);
    }
    $id_ent= (int) $id;
    $usuario=new Usuarios;
     return new JsonResponse($usuario->delete($id_ent));

}
}



?>