<?php
class Tablero_indicadores_model extends CI_Model 
{
	public function __construct() {
		parent::__construct ();

	}
	
	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */
	function contar_agentes($clause)
	{
		$fields = array (
			"agt.id_agente"
		);
		$result = $this->db->select($fields)
			->distinct()
			->from('agt_agentes AS agt')
			->where($clause)
			->get()
			->result_array();
		return $result;
	}
	
	function listar_subordinados()
	{
		/**
		* Array con los uuid de usuarios
		* que el usario actual puede ver.
        */
       	$ver_usuarios = @CRM_Controller::andrea_ACL();
		
		//Remover el usuario loguiado, para veriicar
		//Si en el arreglo existen de verdad usuarios subordinados
		$usuarios = array_diff($ver_usuarios["uuid_usuario"], array(CRM_Controller::$uuid_usuario));
		
		//Verificar si arreglo no es vacio
		if(empty($usuarios)){
			return false;
		}
		
		//Si existen subordinados
		//Volver a agregar al usuario actual.
		$usuarios[] = CRM_Controller::$uuid_usuario;
		
		$fields = array (
			"HEX(uuid_usuario) AS uuid_usuario",
			"CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre",
		);
		$result = $this->db->select($fields)
			->distinct()
			->from('usuarios')
			->where_in("HEX(uuid_usuario)", $usuarios)
			->get()
			->result_array();

		return $result;
	}
	
	/**
	 * Seleccionar los ultimas acciones de los modulos
	 * de (Propiedad, Actibidad y Oportunidades)
	 *
	 * 
	 */
	function seleccionar_historial_modulos()
	{
		$notificaciones = array();
		
		//datos provienen de filtro busqueda
		$fecha_inicio 	= $this->input->post('fecha_inicio', true);
		$fecha_fin 		= $this->input->post('fecha_fin', true);
		$uuid_usuario 	= $this->input->post('uuid_usuario', true);
		
		/**
		* Array con los uuid de usuarios
		* que el usario actual puede ver.
        */
       	$ver_usuarios = @CRM_Controller::andrea_ACL();
		
		//-------------------------------
		// Notificaciones Oportunidades
		//-------------------------------
		//Permiso Default: ver las asignadas o las que ha creado
		$oportunidad_clause = "opp.nombre <> ''";
		
		/**
		 * verificar array $ver_usuarios
		 */
		//if(!empty($ver_usuarios["uuid_usuario"]) && CRM_Controller::$categoria_usuario_key != "admin"){
		if(!empty($ver_usuarios["uuid_usuario"])){
			$usuarios = $ver_usuarios["uuid_usuario"];
			$usuarios = (!empty($usuarios) ? implode(", ", array_map(function($usuarios){ return "'".$usuarios."'"; }, $usuarios)) : "");
				
			$oportunidad_clause = "HEX(opp.id_asignado) IN(". $usuarios .")";
		}
		
		//Si existe busqueda por el usuario
		//No aplicar condicion de subordinado
		if(!empty($uuid_usuario)){
			$oportunidad_clause = "(HEX(opp.id_asignado) IN('". $uuid_usuario ."')";
		}

		//Colocar filtro por fecha
		if( !empty($fecha_inicio)){
			$fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_inicio));
			$oportunidad_clause .= " AND opp.fecha_creacion >= '$fecha_inicio'";
		}
		if( !empty($fecha_fin)){
			$fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_fin));
			$oportunidad_clause .= " AND opp.fecha_creacion <= '$fecha_fin'";
		}

		$sql_oportunidad = "SELECT DISTINCT HEX(opp.uuid_oportunidad) AS uuid, opp.nombre, 'oportunidad' AS 'modulo', opp.fecha_creacion AS fecha,
				CONCAT_WS(' ', IF(usr2.nombre != '', usr2.nombre, ''), IF(usr2.apellido != '', usr2.apellido, '')) AS nombre_usuario
				FROM (`opp_oportunidades` AS opp) 
				LEFT JOIN `cl_clientes` AS cl ON `cl`.`uuid_cliente` = `opp`.`uuid_cliente` 
				LEFT JOIN `cp_clientes_potenciales` AS clp ON `clp`.`uuid_cliente_potencial` = `opp`.`uuid_cliente_potencial` 
				LEFT JOIN `cl_cliente_sociedades_contactos` AS clcon ON `clcon`.`uuid_cliente` = `cl`.`uuid_cliente` 
				LEFT JOIN `con_contactos` AS con ON `con`.`uuid_contacto` = `clcon`.`uuid_contacto` 
				LEFT JOIN `opp_oportunidades_cat` AS ocat ON `ocat`.`id_cat` = `opp`.`id_etapa_venta` 
				LEFT JOIN `usuarios` AS usr1 ON `usr1`.`uuid_usuario` = `opp`.`id_asignado` 
				LEFT JOIN `usuarios` AS usr2 ON `usr2`.`uuid_usuario` = `opp`.`creado_por` 
				LEFT JOIN `cl_clientes_cat` AS ccat ON `ccat`.`id_cat` = `cl`.`id_tipo_cliente` 
				LEFT JOIN `rpo_propiedades` AS prop ON `prop`.`uuid_propiedad` = `opp`.`uuid_propiedad` 
				WHERE ocat.etiqueta NOT IN('Ganado','Perdido') AND $oportunidad_clause";
		
		//-------------------------------
		// Notificaciones de Actividades
		//-------------------------------
		$actividad_clause = "act.cancelado = 0 AND act.completada = 0 ";
		if(!empty($ver_usuarios["uuid_usuario"])){
			$usuarios = $ver_usuarios["uuid_usuario"];
			$usuarios = (!empty($usuarios) ? implode(", ", array_map(function($usuarios){ return "'".$usuarios."'"; }, $usuarios)) : "");
		
			$actividad_clause = "HEX(usr.uuid_usuario) IN(". $usuarios .")";
		}
		
		//Si existe busqueda por el usuario
		//No aplicar condicion de subordinado
		if(!empty($uuid_usuario)){
			$actividad_clause = "(HEX(usr.uuid_usuario) IN('". $uuid_usuario ."'))";
		}
		
		//Colocar filtro por fecha
		if( !empty($fecha_inicio)){
			$fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_inicio));
			$actividad_clause .= " AND act.fecha_creacion >= '$fecha_inicio'";
		}
		if( !empty($fecha_fin)){
			$fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_fin));
			$actividad_clause .= " AND act.fecha_creacion <= '$fecha_fin'";
		}

		$sql_actividad = "SELECT DISTINCT HEX(act.uuid_actividad) AS uuid, act.asunto AS nombre, 'actividad' AS 'modulo', act.fecha_creacion AS fecha,
						CONCAT_WS(' ', IF(usr.nombre != '', usr.nombre, ''), IF(usr.apellido != '', usr.apellido, '')) AS nombre_usuario
						FROM (`act_actividades` AS act) 
						LEFT JOIN `act_tipo_actividades` AS tact ON `tact`.`uuid_tipo_actividad` = `act`.`uuid_tipo_actividad` 
						LEFT JOIN `usuarios` AS usr ON `usr`.`uuid_usuario` = `act`.`uuid_asignado` 
						LEFT JOIN `cl_clientes` AS cl ON `act`.`uuid_cliente` = `cl`.`uuid_cliente` 
						LEFT JOIN `cl_clientes_sociedades` AS csoc ON `csoc`.`uuid_sociedad` = `act`.`uuid_sociedad` 
						LEFT JOIN `con_contactos` AS con ON `con`.`uuid_contacto` = `act`.`uuid_contacto` 
						LEFT JOIN `act_actividades_cat` AS acat ON `act`.`relacionado_con` = `acat`.`id_cat` 
						WHERE $actividad_clause";
		
		//-------------------------------
		// Notificaciones de Propiedaes
		//-------------------------------
		/*if(CRM_Controller::$categoria_usuario_key == 'admin'){
			$propiedades_clause = "rpo.nombre <> ''";
		}else{*/
			$propiedades_clause = "HEX(rpo.uuid_categoria) = 'HEX(". CRM_Controller::$uuid_categoria_usuario .")'";
		//}
		
		//Colocar filtro por fecha
		if( !empty($fecha_inicio)){
			$fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_inicio));
			$actividad_clause .= " AND rpo.fecha_creacion >= '$fecha_inicio'";
		}
		if( !empty($fecha_fin)){
			$fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_fin));
			$actividad_clause .= " AND rpo.fecha_creacion <= '$fecha_fin'";
		}
		
		$sql_propiedad = "SELECT DISTINCT HEX(`rpo`.`uuid_propiedad`) AS uuid, `rpo`.`nombre` AS nombre, 'propiedad' AS 'modulo', rpo.fecha_creacion AS fecha, 
				CONCAT_WS(' ', IF(usr.nombre != '', usr.nombre, ''), IF(usr.apellido != '', usr.apellido, '')) AS nombre_usuario 
				FROM (`rpo_propiedades` AS rpo) 
				LEFT JOIN `rpo_propiedad_proyecto` AS prop_proy ON `prop_proy`.`uuid_propiedad` = `rpo`.`uuid_propiedad` 
				LEFT JOIN `proy_proyectos` AS pry ON `pry`.`uuid_proyecto` = `prop_proy`.`uuid_proyecto` 
				LEFT JOIN `rpo_propiedades_cat` AS cat ON `cat`.`id_cat` = `rpo`.`id_tipo_propiedad` 
				LEFT JOIN `rpo_propiedades_cat` AS cat2 ON `cat2`.`id_cat` = `rpo`.`id_estado_propiedad` 
				LEFT JOIN `usuarios` AS usr ON `usr`.`id_usuario` = `rpo`.`creado_por` 
				WHERE $propiedades_clause";
		
		//-------------------------------
		// Notificaciones de Proyectos
		//-------------------------------
		/*$fields = array (
			"pry.id_proyecto",
			"pry.nombre",
			"pry.fecha_creacion",
			"ccat.etiqueta as tipo",
			"HEX(pry.uuid_proyecto) AS uuid_proyecto",
			"CONCAT_WS(' ', IF(usr.nombre != '', usr.nombre, ''), IF(usr.apellido != '', usr.apellido, '')) AS creado_por",
		);
		$proyectos = $this->db->select($fields)
				->distinct()
				->from('proy_proyectos AS pry')
				->join('proy_proyectos_cat AS ccat', 'ccat.id_cat = pry.id_tipo', 'LEFT')
				->join('proy_proyectos_cat AS ccat2', 'ccat2.id_cat = pry.id_fase', 'LEFT')
				->join('usuarios AS usr', 'usr.uuid_usuario = pry.creado_por', 'LEFT')
				->join('rpo_propiedad_proyecto AS rpo_pry', 'rpo_pry.uuid_proyecto = pry.uuid_proyecto', 'LEFT')
				->join('rpo_propiedades AS rpo', 'rpo.uuid_propiedad = rpo_pry.uuid_propiedad', 'LEFT')
				->join('opp_oportunidades AS opp', 'opp.uuid_propiedad = rpo.uuid_propiedad', 'LEFT')
				//->where($clause)
				->get()
				->result_array();
		
		if(!empty($proyectos))
		{
			foreach($proyectos AS $proyecto)
			{
				$creado_por = !empty($proyecto) && !empty($proyecto["creado_por"]) ? $proyecto["creado_por"] : "";
				$nombre_proyecto = !empty($proyecto) && !empty($proyecto["nombre"]) ? $proyecto["nombre"] : "";
				$fecha_creacion = !empty($proyecto) && !empty($proyecto["fecha_creacion"]) ? $proyecto["fecha_creacion"] : "";
				$tiempo_transcurrido = !empty($proyecto) && !empty($proyecto["fecha_creacion"]) ? Util::timeago($proyecto["fecha_creacion"]) : "";
		
				$notificaciones[$i]["mensaje"] = $creado_por. ' ha creado el proyecto '. $nombre_proyecto;
				$notificaciones[$i]["tiempo_transcurrido"] = $tiempo_transcurrido;
				$notificaciones[$i]["fecha"] = ($fecha_creacion != "" && $fecha_creacion != "0000-00-00 00:00:00" ? Util::translate_date_to_spanish(date('l d \d\e F Y \d\e Y - h:i a', strtotime($fecha_creacion))) : "");
				$i++;
			}
		}*/
		
		//Cantidad de registros a mostrar
		$limit = !empty($_POST["limit"]) ? (int)$this->input->post('limit', true) : 5;
		
		$tables = "";
		if(Auth::has_permission('acceso', 'oportunidades/listar-oportunidades')){
			$tables .= "($sql_oportunidad)";
		}
		if(Auth::has_permission('acceso', 'propiedades/listar-propiedades')){
			$tables .= $tables != "" ? " UNION ($sql_propiedad)" : "($sql_propiedad)";
		}
		if(Auth::has_permission('acceso', 'actividades/listar-actividades')){
			$tables .= $tables != "" ? " UNION ($sql_actividad)" : "($sql_actividad)";
		}
		
		/*$sql = "SELECT td.* FROM (
		($sql_oportunidad)
		UNION
		($sql_actividad)
		UNION
		($sql_propiedad)) AS td ORDER BY td.fecha DESC
		LIMIT 0, %s";*/
		
		if(empty($tables)){
			return false;
		}
		
		/*echo $tables;
		die();
		*/
		$sql = "SELECT td.* FROM ($tables) AS td ORDER BY td.fecha DESC LIMIT 0, %s";
		$sql = sprintf($sql, $limit);
		$results = $this->db->query($sql)->result_array();
		
		$i=0;
		if(!empty($results))
		{
			foreach($results AS $result)
			{
				$nombre_usuario = !empty($result) && !empty($result["nombre_usuario"]) ? $result["nombre_usuario"] : "";
				$nombre_notificacion = !empty($result) && !empty($result["nombre"]) ? $result["nombre"] : "";
				$tipo_notificacion = !empty($result) && !empty($result["modulo"]) ? $result["modulo"] : "";
				$fecha_creacion = !empty($result) && !empty($result["fecha"]) ? $result["fecha"] : "";
				$tiempo_transcurrido = !empty($fecha_creacion) && !empty($fecha_creacion) ? Util::timeago($fecha_creacion) : "";
		
				$notificaciones[$i]["mensaje"] = $nombre_usuario. ' ha creado la '. $tipo_notificacion.' '. $nombre_notificacion;
				$notificaciones[$i]["tiempo_transcurrido"] = $tiempo_transcurrido;
				$notificaciones[$i]["fecha"] = ($fecha_creacion != "" && $fecha_creacion != "0000-00-00 00:00:00" ? Util::translate_date_to_spanish(date('l d \d\e F Y \d\e Y - h:i a', strtotime($fecha_creacion))) : "");
				$i++;
			}
		}
		
		return $notificaciones;
	}
	
}
