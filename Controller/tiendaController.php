<?php

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

require_once __DIR__ . '/../Settings/cloudinary.php';
require_once __DIR__ . '/../Models/Tiendas.php';
require_once __DIR__ . '/../Models/Usuarios.php';

class tiendasController{

    //Obtener las tiendas
    public function getOne($categoria = null, $id_negocio = null, $nombre_negocio = null){
        $tienda = new Tiendas;

        if(!is_null($categoria)){
            return new JsonResponse($tienda->getAll($categoria));
        }
        if(!is_null($id_negocio)){
            return new JsonResponse($tienda->getOne($id_negocio));
        }
        if(!is_null($nombre_negocio)){
            return new JsonResponse($tienda->getOne($nombre_negocio));
        }

        return new JsonResponse($tienda->getAll());
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
            require_once __DIR__ . '/../Settings/cloudinary.php';
            $tmpFilePath = $imagenFile->getStream()->getMetadata('uri');

            try {
                $uploadResult = (new UploadApi())->upload($tmpFilePath, [
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

        //EXPLICACIÓN CLOUDINARY
        //Obtener todos los archivos enviados desde el formulario
        $uploadedFiles = $request->getUploadedFiles();
        // Extrae el archivo específico del campo "imagen", 
        // Si no se subió nada, guarda null (por el operador ??).
        $imagenFile = $uploadedFiles['imagen'] ?? null;
        // Guarda temporalmente la URL de la imagen actual en caso de que no se suba una nueva
        $url_imagen = $tiendaAct['imagen'];

        
        //Verifico que el usuario haya subido una imagen y controlo que no haya errores
        if ($imagenFile && $imagenFile->getError() === UPLOAD_ERR_OK) {
            require_once __DIR__ . '/../Settings/cloudinary.php';
            // Obtiene la ruta temporal del archivo subido (en el servidor) para poder enviarlo a Cloudinary
            $tmpFilePath = $imagenFile->getStream()->getMetadata('uri');

            try {
                // Sube el archivo temporal a Cloudinary, dentro de la carpeta "negocios"
                // La variable $uploadResult devuelve varios datos, entre ellos la URL segura

                $uploadResult = (new UploadApi())->upload($tmpFilePath, [
                "folder" => "negocios"
                ]);
                // Guarda la URL pública y segura que genera Cloudinary (para guardarla después en la base de datos)
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

        return new JsonResponse($tienda->update($data_arr, $id_al));
    }

    public function deleteTienda($id){
        $id_al = (int) $id;
        if(!is_int($id_al) || intval($id_al < 1)){
            return new JsonResponse(['message' => 'Error al eliminar usuario']);
        }

        $tienda = new Tiendas;
        return new JsonResponse($tienda->delete($id_al));
    }

    //REDES
    public function redesTiendas(ServerRequest $request,$membresia,$id_negocio,$email){
        //Verifico si el negocio existe
        $negocio = $this->getTiendas($id_negocio);
        if(empty($negocio)){
            return ['message' => 'Negocio no encontrado o no existente'];
        }
        
        $membresia = new Usuarios;
        $membresia->actMembresia($email);

        if($membresia && $membresia = 1){
            $data = $request->getParsedBody();
            if(empty($data)){
                $json = $request->getBody()->getContents();
                $data = json_decode($json) ?? [];   
            }

            $instagram = $data->instagram;
            $facebook = $data->facebook;
            $tiktok = $data->tiktok;

            if(!preg_match('^https?:\/\/(www\.)?facebook\.com\/[A-Za-z0-9\.]+\/?$',$instagram)){
                return new JsonResponse(['message' => 'Error al ingresar la cuenta de Instagram']);
            }
            if(!preg_match('^https?:\/\/(www\.)?instagram\.com\/[A-Za-z0-9._]+\/?$',$facebook)){
                return new JsonResponse(['message' => 'Error al ingresar la cuenta de Facebook']);
            }
            if(!preg_match('^https?:\/\/(www\.)?tiktok\.com\/@[\w\.-]+\/?$',$tiktok)){
                return new JsonResponse(['message' => 'Error al ingresar la cuenta de Tiktok']);
            }

            $data_arr = [
                'instagram' => $instagram,
                'facebook' => $facebook,
                'tiktok' => $tiktok
            ];
            $tienda = new Tiendas;
            return new JsonResponse($tienda->create($data_arr,$id_negocio));
        }
    }

    public function redesUpdate(ServerRequest $request, $id){
        $id_al = (int) $id;

        if(!is_int($id_al) || intval($id_al) < 1){
            return new JsonResponse(['message' => 'error en la consulta']);
        }

        $data = $request->getParsedBody();

        if(empty($data)){
            $json = $request->getBody()->getContents();
            $data = json_decode($json) ?? [];
        }

        $instagram = $data->instagram;
        $facebook = $data->facebook;
        $tiktok = $data->tiktok;

        if(!preg_match('^https?:\/\/(www\.)?facebook\.com\/[A-Za-z0-9\.]+\/?$',$instagram)){
            return new JsonResponse(['message' => 'Error al ingresar la cuenta de Instagram']);
        }
        if(!preg_match('^https?:\/\/(www\.)?instagram\.com\/[A-Za-z0-9._]+\/?$',$facebook)){
            return new JsonResponse(['message' => 'Error al ingresar la cuenta de Facebook']);
        }
        if(!preg_match('^https?:\/\/(www\.)?tiktok\.com\/@[\w\.-]+\/?$',$tiktok)){
            return new JsonResponse(['message' => 'Error al ingresar la cuenta de Tiktok']);
        }

        $data_arr = [
            'instagram' => $instagram,
            'facebook' => $facebook,
            'tiktok' => $tiktok
        ];
        $tienda = new Tiendas;
        return new JsonResponse($tienda->update($data_arr,$data));
    }


    public function getRedes()
    {
        $redes = new Tiendas;
        return new JsonResponse($redes->getOne($id_negocio));
    }
}