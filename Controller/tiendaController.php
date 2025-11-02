<?php

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;

require_once __DIR__ . '/../Models/Tiendas.php';

class tiendasController{

    //Obtener las tiendas
    public function getTiendas($categoria = null){
        $tienda = new Tiendas;

        if(!is_null($categoria)){
            return new JsonResponse($tienda->getTiendas($categoria));
        }

        return new JsonResponse($tienda->getTiendas());
    }

    public function createTiendas(ServerRequest $request){
        $data = $request->getParsedBody();

        if(empty($data)){
            $json = $request->getBody()->getContents();
            $data = json_decode($json);
        }

        $nombre_negocio = $data->nombre_negocio;
        $descripcion = $data->descripcion;
        $imagen = $data->imagen;
        $direccion = $data->direccion;
        
        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u',$nombre_negocio)){
            return new JsonResponse('Error en el nombre del negocio');
        }
        if(!preg_match('/^.+$/s',$descripcion)){
            return new JsonResponse('Error en la descripción del negocio');
        }
        if(!preg_match('/^https?:\/\/[^\s]+\.(jpg|jpeg|png|gif|webp)$/i',$imagen)){
            return new JsonResponse('Error en la imagen ingresada');
        }
        if(!preg_match('/^[A-Za-zÀ-ÿ0-9\s\.,°º#\-]{5,100}$/',$direccion)){
            return new JsonResponse('Error en la dirección ingresada');
        }

        $data_arr = [
            'nombre_negocio' => $nombre_negocio,
            'descripcion' => $descripcion,
            'imagen' => $imagen,
            'direccion' => $direccion
        ];

        $tienda = new Tiendas;
        return new JsonResponse($tienda->create($data_arr));
    }

    public function updateTienda(ServerRequest $request, $id){
        $id_al = (int) $id;

        if(!is_int($id_al) || intval($id_al) < 1){
            return new JsonResponse(['message' => 'error en la consulta']);
        }

        $data = $request->getParsedBody();

        if(empty($data)){
            $json = $request->getBody()->getContents();
            $data = json_decode($json) ?? [];
        }
        $nombre_negocio = $data->nombre_negocio;
        $descripcion = $data->descripcion;
        $imagen = $data->imagen;
        $direccion = $data->direccion;
        
        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u',$nombre_negocio)){
            return new JsonResponse('Error en el nombre del negocio');
        }
        if(!preg_match('/^.+$/s',$descripcion)){
            return new JsonResponse('Error en la descripción del negocio');
        }
        if(!preg_match('/^https?:\/\/[^\s]+\.(jpg|jpeg|png|gif|webp)$/i',$imagen)){
            return new JsonResponse('Error en la imagen ingresada');
        }
        if(!preg_match('/^[A-Za-zÀ-ÿ0-9\s\.,°º#\-]{5,100}$/',$direccion)){
            return new JsonResponse('Error en la dirección ingresada');
        }

        $data_arr = [
            'nombre_negocio' => $nombre_negocio,
            'descripcion' => $descripcion,
            'imagen' => $imagen,
            'direccion' => $direccion
        ];

        $tienda = new Tiendas;
        return new JsonResponse($tienda->updateTienda($data_arr,$id_al));
    }

    public function deleteTienda($id){
        $id_al = (int) $id;
        if(!is_int($id_al) || intval($id_al < 1)){
            return new JsonResponse(['message' => 'Error al eliminar usuario']);
        }

        $tienda = new Tiendas;
        return new JsonResponse($tienda->deleteTienda($id_al));
    }
}