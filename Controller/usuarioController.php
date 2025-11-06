<?php 
require_once __DIR__ . '/../Models/Usuarios.php';
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;

class UsuarioController{
//---------------------CONTROLES USUARIOS-------------
// GET
public function getByEmail($email){
      
        if(!preg_match("/^[a-zA-Z0-9](?:[a-zA-Z0-9._%+-]{0,63})@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/",$email))
        {
        return new JsonResponse(['Message'=>'Error no es un email válido']);
        }
            $usuario= new Usuarios;
            return new JsonResponse($usuario->getUserEmail($email));
}
//DELETE
public function delete($email){
     if(!preg_match("/^[a-zA-Z0-9](?:[a-zA-Z0-9._%+-]{0,63})@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/",$email))
    {   
        return new JsonResponse(['Message'=>'Error direccion de email inválida']);
    }
    $usuario=new Usuarios;
     return new JsonResponse($usuario->deleteUser($email));

}

// PUT - PERSONAL INFO
public function updatePersonalInfo(ServerRequest $request, $email){
    $data=$request->getParsedBody();
    if(empty($data)){
        $json=$request->getBody()->getContents();
        $data= json_decode($json) ?? [];
    }
    $dni_usuario= $data->dni_usuario;
    $nombreUser = $data->nombre;
    $nombreCompleto = $data->nombreCompleto;
    $num_Telefono = $data->num_Telefono;

    if(!preg_match("/^[a-zA-Z0-9](?:[a-zA-Z0-9._%+-]{0,63})@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/",$email))
    {   
        return new JsonResponse(['Message'=>'Error direccion de email inválida']);
    }
    //valido nombre user
    if(!preg_match("^[a-zA-Z-áéíóúÁÉÍÓÚñÑüÜ\s]+$^",$nombreUser))
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
        'dni'=>$dni_usuario,
        'nombre'=>$nombreUser,
        'nombreCompleto'=>$nombreCompleto,
        'Numerotelefono'=>$num_Telefono
    ];
    $usuario=new Usuarios;
   return new JsonResponse($usuario->updateUsuario($data_array,$email));
}
//POST 
public function CreateUser(ServerRequest $request,$membresia){
    $data=$request->getParsedBody();
    if(empty($data)){
        $json=$request->getBody()->getContents();
        $data= json_decode($json) ?? [];
    }
    
      
    $dni_usuario= $data->dni_usuario;
    $email= $data->email;
    $nombreUser = $data->nombre;
    $nombreCompleto = $data->nombreCompleto;
    $num_Telefono = $data->num_Telefono;
    $password = $data->password;

    //Validacion informacion del usuario
    //Validar email
    if(!preg_match("/^[a-zA-Z0-9](?:[a-zA-Z0-9._%+-]{0,63})@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/",$email))
    {   
        return new JsonResponse(['Message'=>'Error direccion de email inválida']);
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
    //valida contraseña
    if(!preg_match('/^[A-Za-z0-9!@#$%^&*(),.?":{}|<>]{8,}$/',$password))
    {   
        return new JsonResponse(['Message'=>'Error contraseña inválido']);
    }


     $data_array=[
        'dni'=>$dni_usuario,
        'nombre'=>$nombreUser,
        'email'=>$email,
        'nombreCompleto'=>$nombreCompleto,
        'Numerotelefono'=>$num_Telefono,
        'password'=>$password
    ];
    //validar y controlar estado de membresia
     if(is_int($membresia)&&$membresia==1){
       
     $usuario=new Usuarios;
    return new JsonResponse($usuario->createUser($data_array,$membresia));
    }
    $usuario=new Usuarios; 
     return new JsonResponse($usuario->CreateUser($data_array));
    
}
//PATH - PASS
public function updatePass($email,$newPass){
 //Validar email
    if(!preg_match("/^[a-zA-Z0-9](?:[a-zA-Z0-9._%+-]{0,63})@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/",$email))
    {   
        return new JsonResponse(['Message'=>'Error direccion de email inválida']);
    }
    //valida contraseña
    if(!preg_match('/^[A-Za-z0-9!@#$%^&*(),.?":{}|<>]{8,}$/',$newPass))
    {   
        return new JsonResponse(['Message'=>'Error contraseña inválido']);
    }
    $usuario=new Usuarios;
    return $usuario->updatePass($email,$newPass);
}
//----------------- CONTROLES DE PAGO----------------
//GET -> 
public function getPagos($id_p){
    //Validar id
    if(!preg_match('/^[1-9]\d*$/',$id_p))
        {
        return new JsonResponse(['Message'=>'Error no es un id válido']);
        }
     $id_pago= (int) $id_p;
      $pagos = new Usuarios; 
     return new JsonResponse($pagos->getPagoById($id_pago));
    }

//POST
public function createPago(ServerRequest $request,$email){
        $data = $request->getParsedBody();

        if(empty($data)){
            $json = $request->getBody()->getContents();
            $data = json_decode($json) ?? [];
        }

        $monto_pago = $data->monto_pago;
        $metodo_pago = $data->metodo_pago;
        //VALIDO EMAIL
        if(!preg_match("/^[a-zA-Z0-9](?:[a-zA-Z0-9._%+-]{0,63})@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/",$email))
            {   
                return new JsonResponse(['Message'=>'Error direccion de email inválida']);
            }
            //VALIDO MONTO
        if(!preg_match('/^[+-]?\d+(\.\d+)?$/',$monto_pago)){
            return new JsonResponse(['message' => 'Monto inválido']);
        }
        //VALIDO METODO
        if(!preg_match('/^.+$/s',$metodo_pago)){
            return new JsonResponse(['message' => 'Método de pago inválido']);
        }

        $data_arr = [
            'monto_pago' => $monto_pago,
            'metodo_pago' => $metodo_pago
        ];
        $pago = new Usuarios;
       return new JsonResponse($pago->pagoMembresia($email,$data_arr));
    }
    
//DELETE
public function deletePago($id_p,$email){
        
     //Validar id
    if(!preg_match('/^[1-9]\d*$/',$id_p))
        {
        return new JsonResponse(['Message'=>'Error no es un id válido']);
        }
        //Validar email
    if(!preg_match("/^[a-zA-Z0-9](?:[a-zA-Z0-9._%+-]{0,63})@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/",$email))
    {   
        return new JsonResponse(['Message'=>'Error direccion de email inválida']);
    }
        $id_pago= (int) $id_p;
            $pago= new Usuarios;
            return new JsonResponse($pago->deletePago($id_pago,$email));
    }

}
?>