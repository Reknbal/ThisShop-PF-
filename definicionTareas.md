# Proyecto Final 'TuShop'

## Modelos
* Categorias
* Usuarios
* Negocios
* Membresia
* Redes

### Datos necesarios
*Categorias*
* `id` int,
* `nombreCategoria` varchar

*Usuarios*
* `id_usuario` int, 
* `nombre_usuario` varchar,
* `email` varchar,
*  `contraseña` varchar,
*  `membresia` int,
*  `dni_usuario` int,
*  `nombreCompleto` varchar,
*  `num_telefono` int

*Redes*

 * `id_redes` int,
 * `instagram` varchar,
 * `facebook` varchar,
 * `tiktok` varchar,
 * `redesNegocio` int,
### Metodos/Verbos HTTP

*Categorias*
- GET (MOSTRAR)

*Usuarios*
- GET (MOSTRAR)
- CREATE/PUT (CREAR UNO NUEVO)
- DELETE (ELIMINAR)
- PATCH (ACTUALIZAR)

*Negocios*
- GET (MOSTRAR)
- CREATE/PUT (CREAR UNO NUEVO)
- DELETE (ELIMINAR)
- PUT (ACTUALIZAR)

*Redes*
- GET (MOSTRAR)
- CREATE/PUT (CREAR UNO NUEVO)
- DELETE (ELIMINAR)
- PUT (ACTUALIZAR)

*Membresia*
- GET (MOSTRAR)
- DELETE (EQUIVALENTE A DAR DE BAJA)
- CREATE (EQUIVALENTE A SUSCRIBIRSE)


## Controladores

### Restricciones (Regex)
*Categorias*
_No necesita_

*Usuarios*
_completar_
