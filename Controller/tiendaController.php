<?php

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\Uploader;


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
        $direccion = $data->direccion;
        
        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u',$nombre_negocio)){
            return new JsonResponse('Error en el nombre del negocio');
        }
        if(!preg_match('/^.+$/s',$descripcion)){
            return new JsonResponse('Error en la descripción del negocio');
        }
        if(!preg_match('/^[A-Za-zÀ-ÿ0-9\s\.,°º#\-]{5,100}$/',$direccion)){
            return new JsonResponse('Error en la dirección ingresada');
        }

        //Uso Cloudinary acá
        $uploadedFiles = $request->getUploadedFiles();
        $imagenFile = $uploadedFiles['imagen'] ?? null;
        $url_imagen = null;

        if ($imagenFile && $imagenFile->getError() === UPLOAD_ERR_OK) {
            require_once __DIR__ . '/../config/cloudinary.php';
            $tmpFilePath = $imagenFile->getStream()->getMetadata('uri');

            try {
                $uploadResult = Uploader::upload($tmpFilePath, [
                    "folder" => "negocios"
                ]);
                $url_imagen = $uploadResult['secure_url'];
            } catch (Exception $e) {
                return new JsonResponse(['error' => 'Error al subir la imagen: ' . $e->getMessage()]);
            }
        } else {
            return new JsonResponse(['error' => 'Debe enviar una imagen válida']);
        }


        //Guardo todo normal
        $data_arr = [
            'nombre_negocio' => $nombre_negocio,
            'descripcion' => $descripcion,
            'imagen' => $url_imagen,
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
        $direccion = $data->direccion;
        
        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u',$nombre_negocio)){
            return new JsonResponse('Error en el nombre del negocio');
        }
        if(!preg_match('/^.+$/s',$descripcion)){
            return new JsonResponse('Error en la descripción del negocio');
        }
        if(!preg_match('/^[A-Za-zÀ-ÿ0-9\s\.,°º#\-]{5,100}$/',$direccion)){
            return new JsonResponse('Error en la dirección ingresada');
        }

        //Traigo la tienda actual
        $tienda = new Tiendas;
        $tiendaAct = $tienda->getOne($id_al);

        if(empty($tiendaAct)){
            return new JsonResponse(['error' => 'Tienda no encontrada']);
        }

        //Vuelvo a hacer lo mismo de arriba
        $uploadedFiles = $request->getUploadedFiles();
        $imagenFile = $uploadedFiles['imagen'] ?? null;
        $url_imagen = $tiendaAct['imagen'];

        if ($imagenFile && $imagenFile->getError() === UPLOAD_ERR_OK) {
            require_once __DIR__ . '/../config/cloudinary.php';
            $tmpFilePath = $imagenFile->getStream()->getMetadata('uri');

            try {
                $uploadResult = Uploader::upload($tmpFilePath, [
                    "folder" => "negocios"
                ]);
                $url_imagen = $uploadResult['secure_url'];
            } catch (Exception $e) {
                return new JsonResponse(['error' => 'Error al reemplazar la imagen: ' . $e->getMessage()]);
            }
        }
        //Actualizo el array
        $data_arr = [
            'nombre_negocio' => $nombre_negocio,
            'descripcion' => $descripcion,
            'imagen' => $url_imagen,
            'direccion' => $direccion
        ];

        return new JsonResponse($tienda->updateTienda($data_arr, $id_al));
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