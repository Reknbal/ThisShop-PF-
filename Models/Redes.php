<?php
require_once __DIR__ . "/../Settings/db.php";
use Laminas\Diactoros\Response\JsonResponse;
class Redes{
protected $con;

public function __construct()
{
    $this->con=Database::connect();
}
public function getOne($id_Negocio){
  $query='SELECT * FROM redes WHERE id_redes=?';
    try{
        $stmt=$this->con->prepare($query);
        $stmt->bind_param('i',$id_Negocio);
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

    public function create($data,$id_Negocio){
    $query="INSERT INTO (instagram, facebook, tiktok, redesNegocio) VALUES (?,?,?,?)";
    try{
        $stmt=$this->con->prepare($query);
        $stmt->bind_param('sssi',$data['instagram'],$data['facebook'],$data['tiktok'],$id_Negocio);
        $stmt->execute();

        if($stmt->error){
            throw new Exception('Error al almacenar datos');
    }
    return ['Message'=>'Datos almacenados correctamente'];
    }
 catch(\Throwable $th){
        return new JsonResponse(['Message'=>$th->getMessage()]);
    }
}

public function update($id_Negocio,$data){
       $query="UPDATE redes SET  instagram= ?, facebook = ?, tiktok = ?  WHERE redesNegocio=?";
    try{
        $stmt=$this->con->prepare($query);
        $stmt->bind_param('sssi',$data['instagram'],$data['facebook'],$data['tiktok'],$id_Negocio);
        $stmt->execute();

        if($stmt->error){
            throw new Exception('Error al actualizar los datos');
    }
    return ['Message'=>'Datos actualizados correctamente'];
}
    catch(\Throwable $th){
        return new JsonResponse(['Message'=>$th->getMessage()]);
    }
}
}
?>