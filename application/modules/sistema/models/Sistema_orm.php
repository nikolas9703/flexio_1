<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
class Sistema_orm extends Model{
  protected $table = 'configuracion_sistema';
  protected $primaryKey = "id_configuracion";
  public $timestamps = false;
  protected $guarded = ['id_configuracion'];


 public static function seleccionar_politicas(){

   /*$politicas = self::find(1)->first();
   $politica = array();
   $result = $politicas->toArray();

     $politica['usuario']['long_minima_usuario'] = $result['usu_long_minima_usuario'];
     $politica['usuario']['long_maxima_usuario'] = $result['usu_long_maxima_usuario'];
     $politica['usuario']['uso_correo']   		  = $result['usu_uso_correo'];
     $politica['usuario']['editar_perfil']    	  = $result['usu_editar_perfil'];

     $politica['contrasena']['long_minima_contrasena']   	= $result['contr_long_minima_contrasena'];
     $politica['contrasena']['expira_despues_dias']   		=$result['contr_expira_despues_dias'];
     $politica['contrasena']['contr_notificar_antes_dias']   		=$result['contr_notificar_antes_dias'];
     $politica['contrasena']['configuracion_avanzada'] 	= $result['contr_configuracion_avanzada'];
     $politica['contrasena']['notificacion_usuarios_expiracion']  	= $result['contr_notificacion_usuarios_expiracion'];
     $politica['contrasena']['minima_cantidad_letras']  	= $result['contr_minima_cantidad_letras'];
     $politica['contrasena']['minima_cantidad_numeros']  	= $result['contr_minima_cantidad_numeros'];
     $politica['contrasena']['minima_cantidad_caracteres'] = $result['contr_minima_cantidad_caracteres'];
     $politica['contrasena']['restringir_contrasena_vieja']= $result['contr_restringir_contrasena_vieja'];
   return $politica;*/
 }

}
