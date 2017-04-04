<?php

//require_once dirname(__FILE__) . '/Nice_date.php';

/**
 * Notifications
 *
 * Consulta las notificaciones a mostrar en el header del sistema.
 *
 * @package    PensaApp
 * @subpackage Library
 * @category   Libraries
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @version    1.0 - 22/09/2015
 *
 */
class Notificaciones
{
//	protected static $acl;
  protected static $ci;

  protected $id_usuario;
 	protected static $uuid_usuario;
	public $notifications = array();

	function __construct(){

		//Instancia de Codeigniter
		self::$ci =& get_instance();

 		self::$uuid_usuario =  CRM_Controller::$uuid_usuario;
 	}



  public static function guardar_notificaciones($datos=array(),$uuid_asignado,$creado){

    if(!empty($datos)){
      self::$ci->db->set('uuid_asignado',"UNHEX('". $uuid_asignado ."')", FALSE);
      self::$ci->db->set('uuid_creado_por',"UNHEX('". $creado ."')", FALSE);
      self::$ci->db->insert('notificaciones',$datos);
      return array('id' =>self::$ci->db->insert_id());
    }

  }

  public static function getNotificaciones($nodejs=''){

    $condicion = array('tipo'=> 0, 'leido' => 0, 'Hex(uuid_asignado)'=> self::$uuid_usuario);
    self::$ci->db->select('data, id, fecha_creacion');
    self::$ci->db->from('notificaciones');
    self::$ci->db->where($condicion);
    self::$ci->db->order_by('id','desc');
    $query = self::$ci->db->get();
    $results = $query->result_array();
    $notificaciones=array();
    foreach ($results as  $notificacion) {
      $oportunidad= json_decode($notificacion['data']);
      array_push($notificaciones,array('tipo' => $oportunidad->tipo,'nombre' => $oportunidad->nombre, 'id' => $notificacion['id'], 'fecha_creacion' =>$notificacion['fecha_creacion'] ));
    }
    $results = array('total' => empty($notificaciones)? 0 : count($notificaciones), 'notificaciones' => $notificaciones );
    return empty($nodejs)?$results: json_encode($results);
  }

  static function marcar_leido($id=''){
    $datos = array('leido' => 1);
	  self::$ci->db->where('id', $id);
		self::$ci->db->update('notificaciones',$datos);
		return true;
	}


}
