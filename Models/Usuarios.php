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

    public function getAllUsers(){
        $query = 'SELECT * FROM usuarios';

        try {
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            
            if ($stmt->error){
                throw new Exception('Error al mostrar usuarios');
            }
            $res = $stmt->get_result();
            $data_arr = [];

            if($res->num_rows > 0){
                while($data = $res->fetch_assoc()){
                    array_push($data_arr,$data);
                }
                return $data_arr;
            }
            return $data_arr;
        } catch (\Throwable $th) {
            return ['message' => $th->getMessage()];
        }

    }

    //Registro de usuario
    public function createUser($data, $membresia = null)
    {
        $query = 'INSERT INTO usuarios (dni_usuario,nombre_usuario,nombreCompleto,num_telefono, email, password) VALUES (?,?,?)';
        
        try {
            $hashed_pass = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $this->con->prepare($query);
            $stmt->bind_param('sss',$data['nombre'],$data['email'],$hashed_pass);
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
    private function actMembresia($email){
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

    //Método pago
    public function pagoMembresia($membresia,$email,$data){
        //obtengo el usuario por email, primero
        $query1 = 'SELECT id_usuario FROM usuarios WHERE email = ?';
        $stmt = $this->con->prepare($query1);
        $stmt->bind_param('s',$email); 

        $res = $stmt->get_result();

        if($res->num_rows > 0){
            $fila = $res->fetch_assoc();
            $id_usu = $fila['id_usuario'];

            if($membresia = 1){
            $query = 'INSERT INTO pagos (fecha_pago, monto_pago, metodo_pago, id_usuario) VALUES (?,?,?,?)';
            
            try {
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('idsi',$data['fecha_pago'],$data['monto_pago'],$data['metodo_pago'],$id_usu);
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

    //Eliminar usuario
    public function delete($email)
    {
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
}