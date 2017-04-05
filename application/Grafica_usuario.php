<?php defined('BASEPATH') || exit('No direct script access allowed');



class Grafica_usuario{

  protected static  $ci;

  public function __construct(){
    self::$ci = &get_instance();
  }


  public static function usuarios_actividades($fecha,$subordinados){

     $sql_tipo_actividad = self::sql_tipo_actividad();
     $sql_count = self::count_actividades();
     $clause = self::condicion_fecha($fecha);
     $column = self::$ci->input->post('sidx');
     $order = self::$ci->input->post('sord');
  	if(empty($subordinados)){
  	$tipo_actividad = self::$ci->db->query("SELECT DISTINCT agente , $sql_count
    FROM (SELECT DISTINCT (SELECT DISTINCT CONCAT (nombre, ' ', apellido) FROM usuarios where uuid_usuario = uuid_asignado) AS agente, $sql_tipo_actividad uuid_asignado, creado_por, id_actividad,fecha_creacion
FROM act_actividades) AS t WHERE $clause GROUP BY agente ORDER BY $column $order")->result_array();
  	}else{
  		$uuid = "'".implode("','", $subordinados['uuid_usuario'])."'";

  		$tipo_actividad = self::$ci->db->query("SELECT DISTINCT  agente , $sql_count
  				FROM (SELECT DISTINCT (SELECT DISTINCT CONCAT (nombre, ' ', apellido) FROM usuarios where uuid_usuario = uuid_asignado) AS agente,
  				$sql_tipo_actividad uuid_asignado,creado_por, id_actividad,fecha_creacion
  				FROM act_actividades WHERE HEX(uuid_asignado) IN ($uuid)) AS t  WHERE $clause GROUP BY agente ORDER BY $column $order")->result_array();
  	}

  	$row = array();
  	$var = array();
  	$total_categoria = 0;
    $total = 0;
  	foreach($tipo_actividad as $key => $actividad){
        if(is_array($actividad)){
          foreach($actividad as $j => $value){
              if($j != 'agente'){
          		$total_categoria+= $value ;
          	  }
          }
       }
    }


  	foreach($tipo_actividad as $key => $categoria){
     $total = 0;
     $var = array();
      if(is_array($categoria)){

        foreach($categoria as $j => $value){
            if($j != 'agente'){
            	$total +=$value;
            }
            $var[] =$value;
        }
     }
  		//$total = $categoria['llamada'] + $categoria['reunion'] + $categoria['tarea'] + $categoria['presentacion'];

  		//$vars = array('<span class="pie">'."$total/$total_categoria".'</span>',$categoria['agente'],$categoria['llamada'],$categoria['reunion'], $categoria['tarea'],$categoria['presentacion'],$total);
       array_unshift($var,'<span class="pie">'."$total/$total_categoria".'</span>');
       array_push($var,$total);
      $row[] = array('id'=>$key, 'cell'=>$var);

  	}

  	return  array('rows' => $row,'records'=>count($tipo_actividad),'total'=> count($row), 'page'=> 1);

  }

  public static function popular_tabla_perfil(){


    $tipo_actividad= self::$ci->db->query("select nombre,etiqueta from act_tipo_actividades ")->result_array();
    $colnames = array();
    $colmodel = array();
    foreach($tipo_actividad as $j=>$col){
      $colnames[]= $col['nombre'];
      $colmodel[] = array('name'=>$col['nombre'], 'index'=>$col['etiqueta'], 'width'=>40,'formatter'=> "integer");

    }
    array_unshift($colnames, "", "Usuarios");
    array_unshift($colmodel, array('name'=>"vacio", 'index'=>"vacio", 'width'=>20, 'sortable'=> false), array('name'=>'Usuarios', 'index'=>"agente", 'width'=>60));
    array_push($colmodel,array('name'=>"Total", 'index'=>"Total", 'width'=>15,'formatter'=> "integer", 'sortable'=> false));
    array_push($colnames,"Total");

    return  array('colName' => $colnames,'colModel' => $colmodel);

  }

  public static function getMontoOportunidad($etapa, $usuario,$fecha){

  	$fields = array (
  			"IFNULL(SUM(opp.valor_oportunidad),0) AS monto_total",
  			"COUNT(opp.id_oportunidad) AS total_oportunidades"
  	);

  	self::$ci->db->select($fields);
  	self::$ci->db->distinct();
  	self::$ci->db->from('opp_oportunidades AS opp');
  	self::$ci->db->join('opp_oportunidades_cat AS ocat', 'ocat.id_cat = opp.id_etapa_venta', 'LEFT');
  	self::$ci->db->where('HEX(id_asignado)',$usuario);
  	switch($etapa){

  		case 'ganadas':
  			self::$ci->db->where('ocat.valor','vendido');
  	    break;
  		case 'perdidas':
  			self::$ci->db->where('ocat.valor','venta_perdida');
  		break;
  		case 'nuevas':
  			self::$ci->db->where_not_in('ocat.valor',array('vendido','venta_perdida'));
  		break;

  	}
    $clause = self::condicion_fecha($fecha);
  	if(!empty($clause)){
      self::$ci->db->where($clause);
  	}

   $result = self::$ci->db->get()->row_array();
   return $result;
  }

  private function sql_tipo_actividad(){
    self::$ci->db->select("HEX(uuid_tipo_actividad) AS uuid_tipo_actividad, act_tipo_actividades.etiqueta");
    self::$ci->db->distinct();
    self::$ci->db->from('act_tipo_actividades');
    $result = self::$ci->db->get();
    $query = $result->result_array();
    $sql="";
    foreach ($query as $value) {
      $sql.= "IF (HEX(uuid_tipo_actividad)='".$value['uuid_tipo_actividad']."',1 ,NULL) AS ".$value['etiqueta']." ,";
    }
    return $sql;
  }

  public static function actividades_completadas_agregadas($fecha=array(),$usuario){

    $clause = '';
    $clause_subordinados = '';
    $tipo_actividades = self::$ci->db->query("SELECT HEX(actt.uuid_tipo_actividad) AS uuid_tipo_actividad ,completada,nombre, icono FROM  act_tipo_actividades AS actt, act_actividades AS acta
WHERE  acta.uuid_tipo_actividad IN (SELECT uuid_tipo_actividad FROM act_tipo_actividades) GROUP BY completada, nombre, icono")->result_array();

   $clause = self::condicion_fecha($fecha);

    $actividades = array();
    foreach($tipo_actividades as  $tipo){
      $actividades[]= array('actividad' => self::getCountActividades($tipo['uuid_tipo_actividad'],$usuario,$clause,$tipo['completada']),'nombre'=>$tipo['nombre'] ,'completada'=>$tipo['completada'], 'icono'=> $tipo['icono']);
    }

    return  $actividades;
  }

  private function getCountActividades($uuid_tipo_actividad,$usuario,$clause,$completada ){

    self::$ci->db->select('COUNT(id_actividad) as actividad');
    self::$ci->db->distinct();
    self::$ci->db->from('act_actividades');
    self::$ci->db->where('HEX(uuid_tipo_actividad)',$uuid_tipo_actividad);
    self::$ci->db->where('completada',$completada);
    self::$ci->db->where("HEX(uuid_asignado)",$usuario);
    self::$ci->db->where($clause);

      $query = self::$ci->db->get();
      $actividad = $query->row_array();
      return $actividad['actividad'];
  }

  public static function clientes_total_average($fecha=array(),$uuid_usuarios = array()){

    $clause = self::condicion_fecha($fecha);
    if(empty($uuid_usuarios)){
      $query = self::$ci->db->query("SELECT IFNULL(ROUND(AVG(score)),0) AS score FROM(SELECT total_campos, total_scores, (total_scores / total_campos  * 100) AS score
      FROM cl_clientes_scores WHERE $clause GROUP BY uuid_cliente) AS t");
      $average = $query->row();
    }else{
      $uuid = "'".implode("','", $uuid_usuarios['uuid_usuario'])."'";
      $query = self::$ci->db->query("SELECT IFNULL(ROUND(AVG(score)),0) AS score FROM(SELECT total_campos, total_scores, (total_scores / total_campos  * 100) AS score  FROM cl_clientes_scores WHERE $clause AND HEX(uuid_asignado) IN($uuid)  GROUP BY uuid_cliente) AS t");
      $average = $query->row();
    }

    return $average;
  }

  public static function clienteScoreUsuario($fecha, $uuid_usuario){

    $clause = self::condicion_fecha($fecha);
    $query = self::$ci->db->query("SELECT IFNULL(ROUND(AVG(score)),0) AS score FROM(SELECT total_campos, total_scores, (total_scores / total_campos  * 100) AS score
    FROM cl_clientes_scores WHERE $clause AND HEX(uuid_asignado) IN('$uuid_usuario')  GROUP BY uuid_cliente) AS t");
    $average = $query->row();
    return $average->score;
  }

  public static function condicion_fecha($fecha = array() ){
    $clause = '';
    if(!empty($fecha)){
      switch(key($fecha)){
        case 'hoy':
          $clause ='fecha_creacion BETWEEN "'.$fecha['hoy'].' 00:00:00" AND "'.$fecha['hoy'].' 23:59:59"';
          break;
        case 'ayer':
          $clause = 'fecha_creacion BETWEEN "'.$fecha['ayer'].' 00:00:00" AND "'.$fecha['ayer'].' 23:59:59"';
          break;
        case 'esta_semana':
          $clause = 'YEARWEEK(fecha_creacion) = "'.$fecha['esta_semana'].'"';
          break;
        case 'ultima_semana':
          $clause = 'YEARWEEK(fecha_creacion) = "'.$fecha['ultima_semana'].'"';
          break;
        case 'este_mes':
          $clause = 'EXTRACT(YEAR_MONTH FROM fecha_creacion) = "'.$fecha['este_mes'].'"';
          break;
        case 'ultimo_mes':
          $clause = 'EXTRACT(YEAR_MONTH FROM fecha_creacion) = "'.$fecha['ultimo_mes'].'"';
          break;
        case 'ultimos_7_dias':
          $clause ='fecha_creacion >= "'.$fecha['ultimos_7_dias'].' 00:00:00 "';
        break;
        case 'ultimos_30_dias':
          $clause ='fecha_creacion >= "'.$fecha['ultimos_30_dias'].' 00:00:00 "';
        break;
      }

    }
    return $clause;
  }
  public static function count_actividades(){
    $count = '';

    $i=1;

    self::$ci->db->select("HEX(uuid_tipo_actividad) AS uuid_tipo_actividad, act_tipo_actividades.etiqueta");
    self::$ci->db->distinct();
    self::$ci->db->from('act_tipo_actividades');
    $result = self::$ci->db->get();
    $query = $result->result_array();
    $total = count($query);
    foreach ($query as  $value) {
      $count .= " COUNT(".$value['etiqueta'].")  AS ".$value['etiqueta'];
      if($i < $total) $count.=",";
      $i++;
    }

    //COUNT(llamada) AS llamada, COUNT(reunion) AS reunion, COUNT(tarea) AS tarea,COUNT(presentacion) AS presentacion
    return $count;
  }

}
