<?php

use Laminas\Diactoros\Response\JsonResponse;

require_once __DIR__ . '/../Models/Tiendas.php';

class tiendasController{
    protected $con;

    public function __construct()
    {
        $this->con = Database::connect();
    }

    public function getTiendas($categoria = null){
        try {
            if (!is_null($categoria)){
                $query = 'SELECT * FROM tiendas WHERE categorias = ? ORDER BY membresia DESC';
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('s',$categoria);
            }else{
                $query = 'SELECT * FROM tiendas ORDER BY membresia DESC';
            }
            $stmt->execute();

            if($stmt->error){
                throw new Exception('Error al mostrar tiendas');
            }

            $res = $stmt->get_result();
            $data_arr = [];

            if ($res->num_rows > 0){
                while($data = $res->fetch_assoc()){
                    array_push($data_arr,$data);
                }
                return $data_arr;
            }
            return $data_arr;
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()]);
        }
    }
}