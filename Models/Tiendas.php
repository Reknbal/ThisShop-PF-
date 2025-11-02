<?php
require_once __DIR__ . "/../Settings/db.php";
use Laminas\Diactoros\Response\JsonResponse;

class Tiendas{
protected $con;

public function __construct()
{
    $this->con=Database::connect();
}

public function getAll()
{$query='SELECT * FROM negocio';
    try{
        $stmt=$this->con->prepare($query);
        $stmt->execute();

        if($stmt->error){
            throw new Exception('Error al obtener los datos');

        }

        $res=$stmt->get_result();
        $data_array=[];

        if($res->num_rows>0)
        {
            while($array=$res->fetch_assoc())
            {
                array_push($data_array,$array);
            }

            return $data_array;
        }
        $data_array;

    }

    catch(\Throwable $th){
        return new JsonResponse(['Message'=>$th->getMessage()]);
    }

}

public function getOne($id){
  $query='SELECT * FROM negocio WHERE id_negocio=?';
    try{
        $stmt=$this->con->prepare($query);
        $stmt->bind_param('i',$id);
        $stmt->execute();

        if($stmt->error){
            throw new Exception('Error al obtener los datos');

        }

        $res=$stmt->get_result();
        $data_array=[];

        if($res->num_rows>0)
        {
            while($array=$res->fetch_assoc())
            {
                array_push($data_array,$array);
            }

            return $data_array;
        }
        $data_array;

    }

    catch(\Throwable $th){
        return new JsonResponse(['Message'=>$th->getMessage()]);
    }
  
}
public function create($data){


}
}
?>