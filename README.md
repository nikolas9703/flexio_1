#FLEXIO

#URL DE PRUEBAS
http://162.209.57.159/clientes/bluleaf/

#BASE DE DATOS
"clientes_bluleaf"

#ESTRUCTURA BÁSICA DE FLEXIO - DESARROLLO
```
#!html
1.- Todos los modulos se encuentran dentro de la carpeta "application/modules"
2.- El nombre del directorio del modulo debe empezar en minuscula ej: "cheques"
3.- Estructura de un modulo (ej):
    -cheques
        -config
            config.php  -> Este documento contiente informacion unica de configuracin, permisos, etc.
            routes.php  -> Este documento contiene las rutas del modulo
        -controllers
            Cheques.php -> Es el controlador el modulo, acá se maneja toda la lógica de negocio. PD. Primera letra en mayuscula
        -models
            Cheques_orm.php -> Contiene el modelo y las relacines de la tabla principal del modulo
            ... Un modelo es básicamente una tabla en base de datos. En Flexio hay una tabla requerida para
            ... la creacion de un modulo que se compone por un sufijo y el nombre del modulo
            ... por ejemplo "che_cheques"
        -views
            ... Estan contenidas las vistas del modulo. Las principales el nombre debe coincidir con el
            ... modulo, por ejemplo "cheques/listar" va a buscar el archivo "listar.php"
4.- Con respecto a las vistas en flexio se puede trabajar de dos formas principalmente.
4.1.- Usando Template.php: hay una librería que requiere tablas en base de datos como por ejemplo:
        "che_cheques_campos"    -> contendrá todos los campos del formulario del modulo
        "che_cheches_cat"       -> contendrá los catálogos unicos para ese módulo.
        Adicional a esto se deben crear manualmente relaciones de registros en las siguientes tablas:
        - modulos: registros de todos los modulos del sistema
        - mod_vistas: registros de las vistas del sistema agrupadas por modulos
        - mod_pestanas: registros de las pestanas agrupadas por vistas
        - mod_formularios: registros de los formularios agrupados por pestanas
        - mod_paneles: registro de los paneles agrupados por formularios
        - mod_panel_campos: relaciona el panel con los campos del formulario del modulo
        PD: cuando se realiza algun cambio en esta estructura se debe limpiar el cache de la
        aplicaicon para poder visualizar los cambios "application/cache"
4.2.- Usando la forma tradicional quemando el html
5.- Esta mista estrutura tambien se sigue en "public/assets/js/modules" donde se encuentran ubicados
los archivos JavaScript.
```