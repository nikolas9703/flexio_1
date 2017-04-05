<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Jobs{

  protected static $ci = '';

  public function __construct(){
    self::$ci = &get_instance();
  }

  public static function seleccionar_jobs($clause=array()){

    self::$ci->db->select('descripcion,id');
    self::$ci->db->from('jobs');
    self::$ci->db->where($clause);
    $results = self::$ci->db->get()->result_array();

    $descripcion = array();
    $roles = self::getRoles();

    foreach ($results as $value) {
       array_push($descripcion,array('id'=>$value['id'], 'descripcion'=>$value['descripcion']));
    }

    return array('jobs' => $descripcion, 'roles' => $roles);
  }

  public static function getRoles(){
    self::$ci->db->select('nombre_rol, id_rol');
    self::$ci->db->distinct();
    self::$ci->db->from('roles');
    self::$ci->db->where('status','activo');
    $query = self::$ci->db->get();
    $ret = $query->result_array();
    return $ret;
  }

  public static function mostrar_jobs($clause = array()){

    self::$ci->db->select('id,id_job,estado,tiempo_ejecucion, uuid_usuarios,tipo,id_rol');
    self::$ci->db->from('configuracion_notificaciones_reportes');
    self::$ci->db->where($clause);
    $results = self::$ci->db->get()->result_array();
    return $results;
  }


}
