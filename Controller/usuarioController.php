<?php 
require_once __DIR__ . '/../Models/Usuarios.php';
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;

class UsuarioController{
public function get($email){
//CORREGIR. NO ES POR ID ES POR EMAIL.
//agregar preg y listo   
    
if(!preg_match('',$email))
{
return new JsonResponse(['Message'=>'Error no es un email válido']);
}
    $usuario= new Usuarios;
    return new JsonResponse($usuario->getUserEmail($email));
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
public function update(ServerRequest $request, $id){
    //completar
    $data=$request->getParsedBody();
    if(empty($data)){
        $json=$request->getBody()->getContents();
        $data= json_decode($json) ?? [];
    }
    $nombre = $data->nombre;
    $dni_usuario= $data->dni_usuario;
    $nombreCompleto = $data->nombreCompleto;
    $num_Telefono = $data->num_Telefono;

    // controlo id
    if(!preg_match('/^[1-9]\d*$/',$id))
    {   
        return new JsonResponse(['Message'=>'Error id inválido']);
    }
    //valido nombre user
    if(!preg_match('',$nombre))
    {   
        return new JsonResponse(['Message'=>'Error nombre de usuario inválido']);
    }
    //valido dni
    if(!preg_match('',$dni_usuario))
    {   
        return new JsonResponse(['Message'=>'Error DNI inválido']);
    }
    //VALIDO NOMBRE COMPLETO REAL
    if(!preg_match('',$nombreCompleto))
    {   
        return new JsonResponse(['Message'=>'Error id inválido']);
    }
    //VALIDO NUM CELULAR
    if(!preg_match('',$num_Telefono))
    {   
        return new JsonResponse(['Message'=>'Error numero de telefono inválido']);
    }
    
    $id_ent= (int) $id;
    $usuario=new Usuarios;
     //return new JsonResponse($usuario->delete($id_ent));

}
}



?>