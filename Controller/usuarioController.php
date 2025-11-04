<?php 
require_once __DIR__ . '/../Models/Usuarios.php';
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;

class UsuarioController{
public function get($email){
//CORREGIR. NO ES POR ID ES POR EMAIL.
//agregar preg y listo   
    
if(!preg_match("/^[a-zA-Z0-9](?:[a-zA-Z0-9._%+-]{0,63})@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/",$email))
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
    $nombreUser = $data->nombre;
    $dni_usuario= $data->dni_usuario;
    $nombreCompleto = $data->nombreCompleto;
    $num_Telefono = $data->num_Telefono;

    // controlo id
    if(!preg_match('/^[1-9]\d*$/',$id))
    {   
        return new JsonResponse(['Message'=>'Error id inválido']);
    }
    //valido nombre user
    if(!preg_match('^[a-zA-Z-áéíóúÁÉÍÓÚñÑüÜ\s]+$^',$nombreUser))
    {   
        return new JsonResponse(['Message'=>'Error nombre de usuario inválido']);
    }
    //valido dni
    if(!preg_match('/^[1-9][0-9]{7}$/',$dni_usuario))
    {   
        return new JsonResponse(['Message'=>'Error DNI inválido']);
    }
    //VALIDO NOMBRE COMPLETO REAL
    if(!preg_match('^[a-zA-Z-áéíóúÁÉÍÓÚñÑüÜ\s]+$^',$nombreCompleto))
    {   
        return new JsonResponse(['Message'=>'Error id inválido']);
    }
    //VALIDO NUM CELULAR
    if(!preg_match("/^(?:\d{6}|\d{8}|\d{10})$/",$num_Telefono))
    {   
        return new JsonResponse(['Message'=>'Error numero de telefono inválido']);
    }
    $data_array=[
        'nombre'=>$nombreUser,
        'dni'=>$dni_usuario,
        'nombreCompleto'=>$nombreCompleto,
        'Numerotelefono'=>$num_Telefono
    ];
    $id_ent= (int) $id;
    $usuario=new Usuarios;
   // return new JsonResponse($usuario->update);
}
}



?>