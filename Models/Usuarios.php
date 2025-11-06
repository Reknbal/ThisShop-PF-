<?php

use Laminas\Diactoros\Response\JsonResponse;
require_once __DIR__ . '/../Settings/db.php';

class Usuarios{
    protected $con;

    public function __construct()
    {
        $this->con = Database::connect();
    }

    //Métodos get
    public function getUserEmail($email)
    {
        $query = 'SELECT * FROM usuarios WHERE email = ? LIMIT 1';
        
        try {
            $stmt = $this->con->prepare($query);
            $stmt->bind_param('s',$email);
            $stmt->execute();
            if ($stmt->error){
            throw new Exception('Error al obtener el usuario');
            }
            $res = $stmt->get_result();

            $data = $res->fetch_assoc();
            return $data ?: [];

        } catch (\Throwable $th) {
            return ['message' => $th->getMessage()];
        }
    }

    //Registro de usuario
    public function createUser($data, $membresia = null)
    {
        $query = 'INSERT INTO usuarios (dni_usuario,nombre_usuario,nombreCompleto,num_telefono, email, password) VALUES (?,?,?,?,?,?)';
        
        try {
            $hashed_pass = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $this->con->prepare($query);
            $stmt->bind_param('isssss',$data['nombre'],$data['email'],$hashed_pass);
            $stmt->execute();

            if($stmt->error){
                return new Exception('Error en la creación del usuario');
            }

            //En caso de querer activar la membresía
            if($membresia == true){
                $this->actMembresia($data['email']);
            }
            return $stmt->insert_id;
            
        } catch (\Throwable $th) {
            return ['message' => $th->getMessage()];
        }
    }
   
    //Método update: contraseña
    public function updatePass($email,$newPass){
        //Verifico si el usuario existe primero
        $user = $this->getUserEmail($email);
        if(empty($user)){
            return ['message' => 'Usuario no encontrado o no existente'];
        }
        //hasheo la nueva contra primero
        $hashed_pass = password_hash($newPass, PASSWORD_DEFAULT);
        $query = 'UPDATE usuarios SET password = ? WHERE email = ?';

        try {
            $stmt = $this->con->prepare($query);
            $stmt->bind_param('ss',$hashed_pass,$email);
            $stmt->execute();

            if($stmt->error){
                throw new Exception('Error al actualizar la contraseña');
            }
            return $stmt->affected_rows;
        } catch (\Throwable $th) {
            return ['message' => $th->getMessage()];
        }
    }

    //Update datos generales
    public function updateUsuario($data,$email){
        //Verifico si el usuario existe

        $user = $this->getUserEmail($email);
        if(empty($user)){
            return ['message' => 'Usuario no encontrado o no existente'];
        }
        $query = 'UPDATE usuarios SET dni_usuario = ?, nombre_usuario = ?,nombreCompleto = ?, num_telefono = ? WHERE usuarios email = ?';

        try {
            $stmt = $this->con->prepare($query);
            $stmt->bind_param('issss',$data['dni_usuario'],$data['nombre_usuario'],$data['nombreCompleto'],$data['num_telefono'],$email);
            $stmt->execute();

            if($stmt->error){
                throw new Exception('Error al actualizar datos');
            }
            return $stmt->affected_rows;
        } catch (\Throwable $th) {
            return ['message' => $th->getMessage()];
        }

        return $stmt->affected_rows;

    }

    //Eliminar usuario
    public function deleteUser($email)
    {
        //Verifico si el usuario existe
        $user = $this->getUserEmail($email);
        if(empty($user)){
            return ['message' => 'Usuario no encontrado o no existente'];
        }
        
        $query = 'DELETE FROM usuarios WHERE email = ?';

        try {
            $stmt = $this->con->prepare($query);
            $stmt->bind_param('s',$email);
            $stmt->execute();

            if ($stmt->error){
                throw new Exception('Error al eliminar usuario');
            }
            return ['message' => 'Usuario eliminado satisfactoriamente'];
        } catch (\Throwable $th) {
            return ['message' => $th->getMessage()];
        }
    }
    //------------------MEMBRESIA----------
 public function actMembresia($email){
        $query = 'UPDATE usuarios SET membresia = 1 WHERE email = ?';
        try {
            $stmt = $this->con->prepare($query);
            $stmt->bind_param('s',$email);
            $stmt->execute();

            if($stmt->error){
                throw new Exception('Error al activar la membresía');
            }
        } catch (\Throwable $th) {
            return ['message' => $th->getMessage()];
        }
    }
    // -------------------PAGOS--------------
    // METODO PAGO MOSTRAR
    public function getPagoById($id_pago)
    {
        $query = 'SELECT * FROM pagos WHERE id_usuario = ? LIMIT 1';
        
        try {
            $stmt = $this->con->prepare($query);
            $stmt->bind_param('i',$id_pago);
            $stmt->execute();
            if ($stmt->error){
            throw new Exception('Error al obtener los datos de pago');
            }
            $res = $stmt->get_result();

            $data = $res->fetch_assoc();
            return $data ?: [];

        } catch (\Throwable $th) {
            return ['message' => $th->getMessage()];
        }
    }
    //Método pago CARGAR 
    public function pagoMembresia($email,$data){
        //obtengo el usuario por email, primero
        $query1 = 'SELECT membresia, id_usuario FROM usuarios WHERE email = ?';
        $stmt = $this->con->prepare($query1);
        $stmt->bind_param('s',$email); 

        $res = $stmt->get_result();

        if($res->num_rows > 0){
            $fila = $res->fetch_assoc();
            $membresia = $fila['membresia'];
            $id_usu=$fila['id_usuario'];

            if($membresia == 1){
            $query = 'INSERT INTO pagos (monto_pago, metodo_pago, id_usuario) VALUES (?,?,?)';
            
            try {
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('dsi',$data['monto_pago'],$data['metodo_pago'],$id_usu);
                $stmt->execute();

                if($stmt->error){
                    throw new Exception('Error al cargar el pago');
                }
            } catch (\Throwable $th) {
                return ['message' => $th->getMessage()];
            }
        }
        }
        
    }
    //DELETE pago
    public function deletePago($id_pago,$email){
        $query1='UPDATE usuarios SET membresia = 0 WHERE email = ?';  
        try{
            $stmt = $this->con->prepare($query1);
            $stmt->bind_param('s',$email);
            $stmt->execute();

            if ($stmt->error){
                throw new Exception('Error al actualizar datos del usuario');
            }  
            $query = 'DELETE FROM pagos WHERE id_pago = ?';
      
            $stmt = $this->con->prepare($query);
            $stmt->bind_param('i',$id_pago);
            $stmt->execute();

            if ($stmt->error){
                throw new Exception('Error al dar de baja');
            }
            return ['message' => 'Membresia dado de baja exitosamente'];
        } catch (\Throwable $th) {
            return ['message' => $th->getMessage()];
        }
    }
    }