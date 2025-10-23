<?php
use Laminas\Diactoros\Response\JsonResponse;

require_once __DIR__ . '/../Models/Categorias.php';
Class CategoriasController{

    public function mostrar(){
        $catergorias=new Categorias;
        return new JsonResponse($catergorias->get());
    }

}

?>