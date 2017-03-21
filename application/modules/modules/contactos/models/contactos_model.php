<?php
class Contactos_model extends CI_Model
{

	public function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Conteo de los contactos existentes
	 *
	 * @return [array] [description]
	 */
        
    function get_uuid_nombre_comercial($clause=array())
    {
            if(empty($clause)){
                    return false;
            }

            $result = $this->db->select()
                    ->distinct()
                    ->from('cl_cliente_sociedades_contactos')
                    ->where($clause)
                    ->get()
                    ->result_array();
            return $result;
    }
    
    
    function contar_contactos($clause)
    {
        $fields = array(
            "con.id_contacto"
        );
        $result = $this->db->select($fields)
            ->distinct()
            ->from('con_contactos AS con')
            ->join('cl_cliente_sociedades_contactos AS cl_soc_con', 'cl_soc_con.uuid_contacto = con.uuid_contacto', 'LEFT')
            ->join('cl_clientes_sociedades AS cl_soc', 'cl_soc_con.uuid_sociedad = cl_soc.uuid_sociedad', 'LEFT')
            ->join('cl_clientes AS cl', 'cl.uuid_cliente = cl_soc_con.uuid_cliente', 'LEFT')
            ->join('usuarios', 'usuarios.id_usuario = con.id_asignado', 'LEFT')
            ->join('con_contactos_cat', 'con_contactos_cat.id_cat = con.id_toma_contacto', 'LEFT')
            ->join('act_actividades as act', 'con.uuid_contacto = act.uuid_contacto', 'LEFT')
            ->join('act_actividades as act2', 'act2.uuid_contacto = act.uuid_contacto AND act.id_actividad<act2.id_actividad', 'LEFT')
            ->where($clause)
            ->get()
            ->result_array();

        return $result;
    }
    
        /**
	 * [list_contactos description]
	 *
	 * @param integer $sidx
	 *        	[description]
	 * @param integer $sord
	 *        	[description]
	 * @param integer $limit
	 *        	[description]
	 * @param integer $start
	 *        	[description]
	 * @return [array] [description]
	 */
	function listar_contactos($clause, $sidx = 1, $sord = 1, $limit = 0, $start = 0)
	{   
		$i = 0;
		$fields = array (
            "con.id_contacto",
            "con.imagen_archivo",
             "HEX(con.uuid_contacto) AS uuid_contacto",
            "cl.nombre as cliente",
            "CONCAT(con.nombre,' ',con.apellido) AS nombre",
			"con.cargo",
            "datediff(act.fecha,NOW()) as ultimocontacto",
            "act.fecha as ultimo_contacto",
			"con.email",
			"con.telefono",
			"con.principal",
            "con_contactos_cat.etiqueta AS toma_contacto",
            "COALESCE(HEX(cl.uuid_cliente),0) AS uuid_cliente"
        );

        $clause["act2.id_actividad"]=null;

        $this->db->select($fields)
            ->distinct()->from('con_contactos AS con')
            ->join('cl_cliente_sociedades_contactos AS cl_soc_con', 'cl_soc_con.uuid_contacto = con.uuid_contacto', 'LEFT')
            ->join('cl_clientes_sociedades AS cl_soc', 'cl_soc_con.uuid_sociedad = cl_soc.uuid_sociedad', 'LEFT')
            ->join('cl_clientes AS cl', 'cl.uuid_cliente = cl_soc_con.uuid_cliente', 'LEFT')
            ->join('usuarios', 'usuarios.id_usuario = con.id_asignado', 'LEFT')
            ->join('con_contactos_cat', 'con_contactos_cat.id_cat = con.id_toma_contacto', 'LEFT')
            ->join('act_actividades as act', 'con.uuid_contacto = act.uuid_contacto', 'LEFT')
            ->join('act_actividades as act2', 'act2.uuid_contacto = act.uuid_contacto AND act.id_actividad<act2.id_actividad', 'LEFT')
            ->where($clause)
        	->group_by('con.uuid_contacto');
        
            if($sidx!=1){
                 $this->db->order_by($sidx, $sord);
            }

            if($limit!=0){
                $this->db->limit($limit, $start);
            }
				$result = $this->db->get()
				->result_array();
				 
				
				
 				
//       $query = Util::set_placeholder($result);
         $query =  $result;
        
       
        if(!empty($query)){
        	foreach($query as $row){
        		
        	 	$uuid = $row['uuid_contacto'];
        		list($fecha, $hace) = $this->actividades_model->seleccionar_ultimo_contacto(
        				array (
        						"act.uuid_contacto = UNHEX('$uuid')" => NULL,
        						"act.completada = 1" => NULL
        				)
        		); 
        		$clientes = $this->seleccionar_clientes_del_contacto( $row['uuid_contacto'] );
        		 
        		$result[$i]['id_contacto'] 		= $row['id_contacto'];
                $result[$i]['uuid_contacto'] 	= $row['uuid_contacto'];
                $result[$i]['uuid_cliente'] 	= $row['uuid_cliente'];
        		$result[$i]['cliente'] 			= $clientes;
        		$result[$i]['nombre'] 			= $row['nombre'];
        		$result[$i]['cargo'] 			= $row['cargo'];
        		$result[$i]['ultimo_contacto'] 	= $hace;
        		$result[$i]['email'] 			= $row['email'];
        		$result[$i]['telefono'] 		= $row['telefono'];
        		$result[$i]['toma_contacto'] 	= $row['toma_contacto'];
        		$result[$i]['imagen_archivo'] 	= $row['imagen_archivo'];
        		
        		++$i;
        	}
        }
        
		return $result;
	}

    function seleccionar_informacion_de_contacto($id_contacto=NULL)
    {
        if(empty($id_contacto)){
            $id_contacto = $this->input->post("id_contacto",true);
            if(empty($id_contacto)){
                return false;
            }
        }

        $result = array();

        $fields = array(
            "id_contacto",
            "con.imagen_archivo",
            "con.nombre",
            "con.apellido",
            "con.apellido_materno",
            "con.apellido_casada",
            "con.cargo",
            "con.celular",
            "con.email",
            "con.telefono",
            "con.id_toma_contacto",
            "con.direccion",
            "HEX(con.id_asignado) AS id_asignado",
            "con.comentarios",
        );
        $clause = array(
            "con.uuid_contacto = UNHEX('$id_contacto')" => NULL
        );
        $contacto = $this->db
            ->select($fields)
            ->distinct()
            ->from('con_contactos as con')
            ->where ($clause)
            ->get()
            ->result_array();

        if(!empty($contacto))
        {
            $result["imagen_archivo"] = !empty($contacto[0]["imagen_archivo"]) ? $contacto[0]["imagen_archivo"] : "";
            $result["imagen"] = !empty($contacto[0]["imagen_archivo"]) ? base_url( "public/uploads/". $contacto[0]["imagen_archivo"]) : "";
            $result["nombre"] = !empty($contacto[0]["nombre"]) ? $contacto[0]["nombre"] : "";
            $result["apellido"] = !empty($contacto[0]["apellido"]) ? $contacto[0]["apellido"] : "";
            $result["apellido_materno"] = !empty($contacto[0]["apellido_materno"]) ? $contacto[0]["apellido_materno"] : "";
            $result["apellido_casada"] = !empty($contacto[0]["apellido_casada"]) ? $contacto[0]["apellido_casada"] : "";
            $result["cargo"] = !empty($contacto[0]["cargo"]) ? $contacto[0]["cargo"] : "";
            $result["celular"] = !empty($contacto[0]["celular"]) ? $contacto[0]["celular"] : "";
            $result["email"] = !empty($contacto[0]["email"]) ? $contacto[0]["email"] : "";
            $result["nombre_cliente"] = !empty($contacto[0]["nombre_cliente"]) ? $contacto[0]["nombre_cliente"] : "";
            $result["uuid_sociedad"] = !empty($contacto[0]["uuid_sociedad"]) ? $contacto[0]["uuid_sociedad"] : "";
            $result["telefono"] = !empty($contacto[0]["telefono"]) ? $contacto[0]["telefono"] : "";
            $result["id_toma_contacto"] = !empty($contacto[0]["id_toma_contacto"]) ? $contacto[0]["id_toma_contacto"] : "";
            $result["direccion"] = !empty($contacto[0]["nombre"]) ? $contacto[0]["direccion"] : "";
            $result["id_asignado"] = !empty($contacto[0]["id_asignado"]) ? $contacto[0]["id_asignado"] : "";
            $result["comentarios"] = !empty($contacto[0]["comentarios"]) ? $contacto[0]["comentarios"] : "";

            $fields = array(
                "clsocon.id",
            	"ccat.valor AS tipo_cliente",
            	"HEX(clsocon.uuid_cliente) AS uuid_cliente",
            	"HEX(clsocon.uuid_sociedad) AS uuid_sociedad"
            );
            $clause = array(
            	"clsocon.uuid_contacto = UNHEX('$id_contacto')" => NULL
            );
            $clientes = $this->db->select($fields)
                ->distinct()
                ->from('cl_cliente_sociedades_contactos AS clsocon')
                ->join('cl_clientes AS cl', 'cl.uuid_cliente = clsocon.uuid_cliente', 'LEFT')
                ->join('cl_clientes_cat AS ccat', 'ccat.id_cat = cl.id_tipo_cliente', 'LEFT')
                ->where($clause)
                ->get()
                ->result_array();

            if(!empty($clientes))
            {
                $counter = 0;
                foreach($clientes AS $cliente)
                {
                	$index_exist = Util::multiarray_buscar_valor($cliente["uuid_cliente"], "uuid_cliente", (!empty($result["clientes"]) ? $result["clientes"] : array()));

                	//Verificar si ya el uuid_cliente ya existe
                	//en el arreglo, para no duplicarlo.
                	if(!empty($result["clientes"]) && is_numeric($index_exist) && $index_exist >= 0){
                		if(!empty($cliente["uuid_sociedad"])){
                			$result["clientes"][$index_exist]["nombre_comercial"][] = $cliente["uuid_sociedad"];
                		}
                	}else{
                		$result["clientes"][$counter]["id"] = $cliente["id"];
                		$result["clientes"][$counter]["uuid_cliente"] = $cliente["uuid_cliente"];
                		$result["clientes"][$counter]["tipo_cliente"] = $cliente["tipo_cliente"];
                		$result["clientes"][$counter]["nombre_comercial"][] = $cliente["uuid_sociedad"];
                		
                		$counter++;
                	}
                }
            }
        }
        return $result;

    }

 /* Por ahora solo se usa para formar la cadena, cliente, cliente, etc, cambiable*/ 
    public function seleccionar_clientes_del_contacto($uud_contacto){
    	$cadena = '';
    	$result = array();
    	$fields = array(
     			"cl.nombre",
    			"HEX(cl.uuid_cliente) AS uuid_cliente",
     	);
    	$clause = array(
    			"csc.uuid_contacto = UNHEX('$uud_contacto')" => NULL
    	);
    	$clientes = $this->db->select($fields)
    	->distinct()
    	->from('cl_cliente_sociedades_contactos AS csc')
    	->join('cl_clientes AS cl', 'cl.uuid_cliente = csc.uuid_cliente', 'LEFT')
     	->where($clause)
    	->get()
    	->result_array();
     	if(!empty($clientes))
    	{
    		$counter = 0;
    		foreach($clientes AS $cliente)
    		{
     				$result["clientes"][$counter]["id"] = $cliente["nombre"];
    				$result["clientes"][$counter]["uuid_cliente"] = $cliente["uuid_cliente"];
    				$cadena .= $cliente["nombre"].',';
    				$counter++;
    			 
    		}
    	}
    	return rtrim($cadena, ',');
    	//return $result; 
    }
    
    public function seleccionar_todos_contactos()
    {
    	$result = array();
    	$fields = array(
    			"con.id_contacto",
    			"HEX(con.uuid_contacto) AS uuid_contacto",
    			"concat(con.nombre, ' ', con.apellido) as nombre",
    			"con.apellido" 
     	);
    	 
    	$result = $this->db->select($fields)
    	->distinct()
    	->from('con_contactos AS con')
     	->get()
    	->result_array();
    
    	 
    	return $result;
    }
    /**
     * Seleccionar informacion de los
     * clientes y nombres comerciales desde un contacto
     *
     * @param string $uuid_contacto
     * @return array
     */
    function seleccionar_nombres_comerciales($uuid_cliente=NULL, $uuid_contacto=NULL)
    {
    	if($uuid_cliente==NULL){
    		return false;
    	}
    
    	$fields = array (
    			"soc.nombre_comercial AS nombre_comercial",
    			"HEX(clon.uuid_sociedad) AS uuid_sociedad",
    	);
    	$clause = array (
    			"clon.uuid_contacto = UNHEX('$uuid_contacto')" => NULL,
    			"clon.uuid_cliente = UNHEX('$uuid_cliente')" => NULL,
    	);
    	$resultado =  $this->db->select($fields)
    	->distinct()
    	->from("cl_clientes_sociedades AS soc")
    	->join('cl_cliente_sociedades_contactos AS clon', 'clon.uuid_sociedad = soc.uuid_sociedad', 'LEFT')
    	->where($clause)
    	->get()
    	->result_array();
    	//echo $this->db->last_query();
    	return $resultado;
    	 
    }
    public function seleccionar_contacto_info($id_contacto)
    {
        $result = array();
        $fields = array(
            "con.id_contacto",
            "con.nombre",
            "con.apellido",
            "con.telefono",
            "con.email",
        );
        $clause = array(
            "con.uuid_contacto = UNHEX('$id_contacto')" => NULL
        );
        $contactoINFO = $this->db->select($fields)
            ->distinct()
            ->from('con_contactos AS con')
            ->where($clause)
            ->get()
            ->result_array();

        if(!empty($contactoINFO))
        {
            $result['id_contacto'] = $contactoINFO[0]['id_contacto'];
            $result['nombre']     = $contactoINFO[0]['nombre'];
            $result['apellido']   = $contactoINFO[0]['apellido'];
            $result['telefono']   = $contactoINFO[0]['telefono'];
            $result['email']      = $contactoINFO[0]['email'];
        }

        return $result;
    }

    function guardar_contacto($contacto)
    {
        if(isset($contacto["contacto"])){
            $contacto["campo"]=$contacto["contacto"];
            $files = $_FILES["contacto"];

        }
        else{
            $files = $_FILES;
        }

        $uuid_cliente = !empty($contacto["campo"]["uuid_cliente"]) ? $contacto["campo"]["uuid_cliente"] : "";
        $nombre_comercial = !empty($contacto["campo"]["nombre_comercial"]) ? $contacto["campo"]["nombre_comercial"] : "";
        
        unset($contacto["campo"]["nombre_comercial"]);
        unset($contacto["campo"]["uuid_cliente"]);

        if(Util::is_array_empty($contacto)){
            return false;
        }

        //Init Fieldset variable
        $fieldset = array();

        //Recorrer arreglo e insertar los valores que no estan vacios
        //en el fieldset
        foreach ($contacto["campo"] AS $fieldname => $fieldvalue) {
            if(empty($fieldvalue)){
                continue;
            }

            //check if is an array
            if(is_array($fieldvalue)){
                foreach ($fieldvalue AS $name => $value) {
                	if(preg_match("/id_asignado/i", $name) || preg_match("/uuid_/i", $name)){
                		$fieldset["$name = UNHEX('$value')"] = NULL;
                			
                	}
                	else if(preg_match("/fecha/i", $name)){
                		//Darle mformato a la fecha
                		$fieldset[$name] = date("Y-m-d", strtotime($value));
                	}
                	else{
                        $fieldset[$name] = $this->security->xss_clean($value);
                    }
                }
            }else{
            	if(preg_match("/id_asignado/i", $fieldname) || preg_match("/uuid_/i", $fieldname)){
					
					$this->db->set($fieldname, "UNHEX('$fieldvalue')", FALSE);
				}
				else if(preg_match("/fecha/i", $fieldname)){
					//Darle mformato a la fecha
					$fieldset[$fieldname] = date("Y-m-d", strtotime($fieldvalue));
				}
				else{
					$fieldset[$fieldname] = $fieldvalue;
				}
            }
        }

        //Si el $fieldset es vacio
        if(Util::is_array_empty($fieldset)){
            return false;
        }

        //Subir foto de contacto
        if(!empty($files['campo']))
        {
            $filename = $files['campo']['name']['imagen_archivo'];
            $filename = str_replace(" ","_", $filename);
            $filename = str_replace("-","_", $filename);
            $file_name = "cl_". $filename;
            $config['upload_path']      = './public/uploads/contactos/';
            $config['file_name']        = $file_name;
            $config['allowed_types']    = '*';

            $extension = ".".end(explode('.', $files['campo']['name']['imagen_archivo']));

            $_FILES['campo']['name']     = $files['campo']['name']['imagen_archivo'];
            $_FILES['campo']['type']     = $files['campo']['type']['imagen_archivo'];
            $_FILES['campo']['tmp_name'] = $files['campo']['tmp_name']['imagen_archivo'];
            $_FILES['campo']['error']    = $files['campo']['error']['imagen_archivo'];
            $_FILES['campo']['size']     = $files['campo']['size']['imagen_archivo'];
            $_FILES['campo']['filename'] = $config['file_name']. $extension;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if($this->upload->do_upload("campo"))
            {
                $fileINFO = $this->upload->data();

                //Guardar datos de los archivos
                $fieldset['imagen_archivo'] ="contactos/".$fileINFO["file_name"];

            }
        }
        
        unset($fieldset['guardar']);
        
        //
        // Begin Transaction
        // docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
        //
        $this->db->trans_start();

        //Campos adicionales
        $this->db->set('uuid_contacto', 'ORDER_UUID(uuid())', FALSE);
        $fieldset["creado_por"] = $this->session->userdata('id_usuario');
        $fieldset["fecha_creacion"] = date('Y-m-d H-i-s');
        
        //Guardar Contacto
        $this->db->insert('con_contactos', $fieldset);
        $idContacto = $this->db->insert_id();
        
        /**
         * Sacar el UUID del Contacto.
         */
        $contactoINFO = $this->db->select("HEX(uuid_contacto) AS uuid_contacto")
	        ->distinct()
	        ->from('con_contactos')
	        ->where("id_contacto", $idContacto)
	        ->get()
	        ->result_array();
        $uuid_contacto = !empty($contactoINFO[0]["uuid_contacto"]) ? $contactoINFO[0]["uuid_contacto"] : "";
        
        //Si la informacion del contacto ya se ha guardado
        //y ya tenemos el ID del cliente,
        if(!empty($idContacto))
        {
        	//--------------------
        	// GUARDAR CLIENTES Y SUS SOCIEDADES
        	// RELACIONADOS A ESTE CONTACTO
        	//--------------------
        	if(!empty($_POST["clientes"])){
        		foreach ($_POST["clientes"] AS $cliente)
        		{
        			//Si es vacio continuar al siguiente cliente
        			if(empty($cliente)){
        				continue;
        			}
        			
        			$uuid_cliente = !empty($cliente["uuid_cliente"]) ? $cliente["uuid_cliente"] : "";
        			
        			$fieldset = array();
        			
        			//Verificar si son varias sociedades
        			//relacionadas al contacto
        			if(!empty($cliente["nombre_comercial"]))
        			{
        				foreach($cliente["nombre_comercial"] AS $uuid_sociedad)
        				{
        					$uuid_sociedad = !empty($uuid_sociedad) ? $uuid_sociedad : "";
        					
        					//Guardar datos
        					$this->db->set("uuid_cliente", "UNHEX('$uuid_cliente')", FALSE);
        					$this->db->set("uuid_sociedad", "UNHEX('$uuid_sociedad')", FALSE);
        					$this->db->set("uuid_contacto", "UNHEX('$uuid_contacto')", FALSE);
        					$this->db->insert('cl_cliente_sociedades_contactos', $fieldset);
        				}
        			}
        			else
        			{
        				//Guardar datos
        				$this->db->set("uuid_cliente", "UNHEX('$uuid_cliente')", FALSE);
        				
        				if(!empty($uuid_sociedad)){
        					$this->db->set("uuid_sociedad", "UNHEX('$uuid_sociedad')", FALSE);
        				}
        				
        				$this->db->set("uuid_contacto", "UNHEX('$uuid_contacto')", FALSE);
        				$this->db->insert('cl_cliente_sociedades_contactos', $fieldset);
        			}
        		}
        	}
        }
        
        //--------------------------
        // Esto creo que guarda los clientes
        // y sociedades relacionadas al contacto
        // creadas desde cliente juridico.
        //
        // Sociedades: desde el arreglo POST
        if(!empty($_POST["contacto"]["uuid_cliente"]))
        {
        	$fieldset = array();
        	$this->db->set("uuid_cliente", "UNHEX('$uuid_cliente')", FALSE);

            //Contacto: consulta a la base de datos con el último ID ingresado
            $contactoIngresado = $this->db->select("uuid_contacto")
				            ->from('con_contactos')
				            ->where("id_contacto", "$idContacto")
				            ->get()
				            ->result_array();
            
            $fieldset["uuid_contacto"] = $contactoIngresado[0]["uuid_contacto"];

            //Sociedad: uso de la función comp__seleccionar_sociedad
            $this->load->model('clientes/clientes_model');

            if(!empty($nombre_comercial))
            {
				foreach($nombre_comercial as $sociedad)
                {
                    $sociedadArray = $this->clientes_model->comp__seleccionar_sociedad(array("nombre_comercial"=>$sociedad));
                    $fieldset["uuid_sociedad"] = $sociedadArray[0]["uuid_sociedad"];
                	$this->db->insert('cl_cliente_sociedades_contactos', $fieldset);
				}
            }
            else
            {
            	$this->db->insert('cl_cliente_sociedades_contactos', $fieldset);
            }
        }

        $this->db->trans_complete();

        //---------------------------------------
        //End Transaction

        // Managing Errors
        if ($this->db->trans_status() === FALSE) {

            log_message("error", "MODULO: Contactos --> No se pudo guadar los datos del contacto en DB.");
            return false;

        } else {

            //guardar el id en variable de session
            $this->session->set_userdata('idContacto', $idContacto);
            return true;
        }
    }

    function editar_contacto($id_contacto=NULL)
    {
        //Verificar si el POST no es vacio
        if(Util::is_array_empty($_POST)){
            return false;
        }
        
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        die();*/

        //Luego verificar si el id_contacto no es vacio
        if(empty($id_contacto)){
            return false;
        }
        
        $uuid_cliente = !empty($_POST["campo"]["uuid_cliente"]) ? $_POST["campo"]["uuid_cliente"] : "";
        $nombre_comercial = !empty($_POST["campo"]["nombre_comercial"]) ? $_POST["campo"]["nombre_comercial"] : "";

        //Init Fieldset variable
        $fieldset = array();

        //Recorrer arreglo e insertar los valores que no estan vacios
        //en el fieldset
        foreach ($_POST["campo"] AS $fieldname => $fieldvalue)
        {
            if(empty($fieldvalue)){
                continue;
            }

            //check if is an array
            if(is_array($fieldvalue)){
                foreach ($fieldvalue AS $name => $value) {
                    if($value != ""){
                    	if(preg_match("/id_asignado/i", $name) || preg_match("/uuid_/i", $name)){
							$fieldset["$name = UNHEX('$value')"] = NULL;
							
						}
						else if(preg_match("/fecha/i", $name)){
							//Darle mformato a la fecha
							$fieldset[$name] = date("Y-m-d", strtotime($value));
						}
						else{
							$fieldset[$name] = $this->security->xss_clean($value);
						}
                    }
                }
            }else{
            	if(preg_match("/id_asignado/i", $fieldname) || preg_match("/uuid_/i", $fieldname)){
					
					$this->db->set($fieldname, "UNHEX('$fieldvalue')", FALSE);
				}
				else if(preg_match("/fecha/i", $fieldname)){
					//Darle mformato a la fecha
					$fieldset[$fieldname] = date("Y-m-d", strtotime($fieldvalue));
				}
				else{
					$fieldset[$fieldname] = $fieldvalue;
				}
            }
        }
        
        unset($fieldset['guardar']);

        //Si el $fieldset es vacio
        if(Util::is_array_empty($fieldset)){
            return false;
        }

        //
        // Begin Transaction
        // docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
        //
        $this->db->trans_start();

        $clause = array(
            "uuid_contacto = UNHEX('$id_contacto')" => NULL
        );
        $this->db->where($clause)
        		->update('con_contactos', $fieldset);
        
        //--------------------
        // GUARDAR CLIENTES Y SUS SOCIEDADES
        // RELACIONADOS A ESTE CONTACTO
        //--------------------
        if(!empty($_POST["clientes"])){
        	foreach ($_POST["clientes"] AS $cliente)
        	{
        		//Si es vacio continuar al siguiente cliente
        		if(empty($cliente)){
        			continue;
        		}
        
        		$uuid_cliente = !empty($cliente["uuid_cliente"]) ? $cliente["uuid_cliente"] : "";
        
        		$fieldset = array();
        
        		//Verificar si son varias sociedades
        		//relacionadas al contacto
        		if(!empty($cliente["nombre_comercial"]))
        		{
        			foreach($cliente["nombre_comercial"] AS $uuid_sociedad)
        			{
        				$uuid_sociedad = !empty($uuid_sociedad) ? $uuid_sociedad : "";
        				
        				//Verificar si el cliente y sociedad ya existe
        				$clause = array(
        					"uuid_cliente = UNHEX('$uuid_cliente')" => NULL,
        					"uuid_sociedad = UNHEX('$uuid_sociedad')" => NULL,
        					"uuid_contacto = UNHEX('$id_contacto')" => NULL,
        				);
        				$checkCliente = $this->db->select()
		        				->distinct()
		        				->from('cl_cliente_sociedades_contactos')
		        				->where($clause)
		        				->get()
		        				->result_array();
        				
        				if(empty($checkCliente))
        				{
	        				//Guardar datos
	        				$this->db->set("uuid_cliente", "UNHEX('$uuid_cliente')", FALSE);
	        				$this->db->set("uuid_sociedad", "UNHEX('$uuid_sociedad')", FALSE);
	        				$this->db->set("uuid_contacto", "UNHEX('$id_contacto')", FALSE);
	        				$this->db->insert('cl_cliente_sociedades_contactos', $fieldset);
        				}
        			}
        		}
        		else
        		{
        			//Verificar si el cliente y sociedad ya existe
        			$clause = array(
	        			"uuid_cliente = UNHEX('$uuid_cliente')" => NULL,
	        			"uuid_contacto = UNHEX('$id_contacto')" => NULL,
        			);
        			$checkCliente = $this->db->select()
		        			->distinct()
		        			->from('cl_cliente_sociedades_contactos')
		        			->where($clause)
		        			->get()
		        			->result_array();
        			
        			if(empty($checkCliente))
        			{
	        			//Guardar datos
	        			$this->db->set("uuid_cliente", "UNHEX('$uuid_cliente')", FALSE);
	        			$this->db->set("uuid_contacto", "UNHEX('$id_contacto')", FALSE);
	        			$this->db->insert('cl_cliente_sociedades_contactos', $fieldset);
        			}
        		}
        	}
        }       
        
        //Subir Imagen de cliente
        $files = $_FILES;
        
        if(!empty($files['campo']['name']['imagen_archivo']))
        {
        	$clause = array(
        		"uuid_contacto = UNHEX('$id_contacto')" => NULL,
        	);
        	$checkCliente = $this->db->select("imagen_archivo")
	        	->distinct()
	        	->from('con_contactos')
	        	->where($clause)
	        	->get()
	        	->result_array();
        
        	if(!empty($checkCliente)){
        
        		$imagen_vieja = !empty($checkCliente[0]["imagen_archivo"]) ? realpath(str_replace("system/", "", BASEPATH). "public/uploads/". $checkCliente[0]["imagen_archivo"]) : "";
        
        		if(file_exists($imagen_vieja)) {
        
        			//Si existe la imagen BORRARLA
        			unlink($imagen_vieja);
        		}
        	}
        
        	$filename = $files['campo']['name']['imagen_archivo'];
        	$filename = str_replace(" ","_", $filename);
        	$filename = str_replace("-","_", $filename);
        	$file_name = "cl_". $filename;
        
        	$config['upload_path']      = './public/uploads/contactos/';
        	$config['file_name']        = $file_name;
        	$config['allowed_types']    = '*';
        
        	$extension = ".".end(explode('.', $files['campo']['name']['imagen_archivo']));
        
        	$_FILES['campo']['name']     = $files['campo']['name']['imagen_archivo'];
        	$_FILES['campo']['type']     = $files['campo']['type']['imagen_archivo'];
        	$_FILES['campo']['tmp_name'] = $files['campo']['tmp_name']['imagen_archivo'];
        	$_FILES['campo']['error']    = $files['campo']['error']['imagen_archivo'];
        	$_FILES['campo']['size']     = $files['campo']['size']['imagen_archivo'];
        	$_FILES['campo']['filename'] = $config['file_name']. $extension;
        
        	$this->load->library('upload', $config);
        	$this->upload->initialize($config);
        
        	if($this->upload->do_upload("campo"))
        	{
        		$fileINFO = $this->upload->data();
        
        		//Guardar datos de los archivos
        		$fieldset = array(
        			'imagen_archivo' => "contactos/".$fileINFO["file_name"]
        		);
        		$clause = array(
        			"uuid_contacto = UNHEX('$id_contacto')" => NULL,
        		);
        		$this->db->where($clause)->update('con_contactos', $fieldset);
        	}
        }
        
        //---------------------------------------
        //End Transaction
        $this->db->trans_complete();

        // Managing Errors
        if ($this->db->trans_status() === FALSE) {

            log_message("error", "MODULO: Contactos --> No se pudo actualizar los datos del contacto en DB.");
            return false;

        } else {

            //guardar el id en variable de session
            $this->session->set_userdata('actualizadoContacto', $id_contacto);

            return true;
        }
    }

    /**
     * Funcion para eliminar un nombre comercial
     * de la relacion de cliente contacto.
     * 
     * @author: jluispinilla
     * @return boolean|multitype:boolean string
     */
    function eliminar_cliente_nombre_comercial()
    {
    	$uuid_contacto = $this->input->post("uuid_contacto",true);
    	$uuid_sociedad = $this->input->post("uuid_sociedad",true);
    	
    	//Retorna false si $uuid_sociedad es vacio
    	if(empty($uuid_sociedad)){
    		return false;
    	}
    	
    	//
    	// Begin Transaction
    	// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
    	//
    	$this->db->trans_start();
    	
    	//Borrar el nombre comercial seleccionado
    	$clause = array(
    		"uuid_contacto = UNHEX('$uuid_contacto')" => NULL,
    		"uuid_sociedad = UNHEX('$uuid_sociedad')" => NULL
    	);
    	$this->db->where($clause)
    		->delete('cl_cliente_sociedades_contactos');

    	// ---------------------------------------
    	// End Transaction
    	$this->db->trans_complete();
    	
    	// Managing Errors
    	if ($this->db->trans_status() === FALSE) {
    	
    		log_message("error", "MODULO: Contacto --> No se pudo eliminar el Nombre Comercial seleccionado.");
    	
    		return array(
    			"respuesta" => false,
    			"mensaje" => "Hubo un error al tratar de eliminar el Nombre Comercial seleccionado."
    		);
    	
    	} else {
    		return array(
    			"respuesta" => true,
    			"mensaje" => "Se ha eliminado el Nombre Comercial satisfactoriamente."
    		);
    	}
    }
    
    /**
     * Funcion para eliminar un cliente
     * de la relacion de cliente contacto.
     * 
     * @author: jluispinilla
     * @return boolean|multitype:boolean string
     */
    function eliminar_cliente()
    {
    	$uuid_contacto = $this->input->post("uuid_contacto",true);
    	$uuid_cliente = $this->input->post("uuid_cliente",true);
    	 
    	//Retorna false si $uuid_sociedad es vacio
    	if(empty($uuid_cliente)){
    		return false;
    	}
    	 
    	//
    	// Begin Transaction
    	// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
    	//
    	$this->db->trans_start();
    	 
    	//Borrar el nombre comercial seleccionado
    	$clause = array(
    		"uuid_contacto = UNHEX('$uuid_contacto')" => NULL,
    		"uuid_cliente = UNHEX('$uuid_cliente')" => NULL
    	);
    	$this->db->where($clause)
    		->delete('cl_cliente_sociedades_contactos');
    	
    	// ---------------------------------------
    	// End Transaction
    	$this->db->trans_complete();
    	 
    	// Managing Errors
    	if ($this->db->trans_status() === FALSE) {
    		 
    		log_message("error", "MODULO: Contacto --> No se pudo eliminar el Cliente seleccionado.");
    		 
    		return array(
    			"respuesta" => false,
    			"mensaje" => "Hubo un error al tratar de eliminar el Cliente seleccionado."
    		);
    		 
    	} else {
    		return array(
    			"respuesta" => true,
    			"mensaje" => "Se ha eliminado el Cliente satisfactoriamente."
    		);
    	}
    }
    
    function asignar_contacto_principal()
    {
    	$uuid_cliente = $this->input->post('uuid_cliente', true);
    	$uuid_contacto = $this->input->post('uuid_contacto', true);
    	
    	//Si el $fieldset es vacio
    	if(empty($uuid_cliente) || empty($uuid_contacto)){
    		return false;
    	}
    	
    	//
    	// Begin Transaction
    	// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
    	//
    	$this->db->trans_start();
    	
    	//Seleccionar todos los contactos 
    	//relacionados al cliente actual.
    	$clause = array(
    		"uuid_cliente = UNHEX('$uuid_cliente')" => NULL
    	);
    	$contactos = $this->db->select("HEX(uuid_contacto) AS uuid_contacto")
		    	->distinct()
		    	->from('cl_cliente_sociedades_contactos')
		    	->where($clause)
		    	->get()
		    	->result_array();
    	
    	if(!empty($contactos))
    	{
    		foreach($contactos AS $contacto)
    		{
    			$uid_contacto = !empty($contacto["uuid_contacto"]) ? $contacto["uuid_contacto"] : "";
    			
    			//Quitarle el asignado
    			//a todos los contactos del cliente
    			$fieldset = array(
    				"principal" =>	0
    			);
    			$clause = array(
    				"uuid_contacto = UNHEX('$uid_contacto')" => NULL
    			);
    			//Actualizar Cliente Potencial
    			$this->db->where($clause)->update('con_contactos', $fieldset);
    		}	
    	}
    	
		$fieldset = array(
    		"principal" =>	1
    	);
    	$clause = array(
    		"uuid_contacto = UNHEX('$uuid_contacto')" => NULL
    	);
    	//Actualizar Cliente Potencial
    	$this->db->where($clause)->update('con_contactos', $fieldset);
    	
    	//---------------------------------------
    	//End Transaction
    	$this->db->trans_complete();
    	
    	// Managing Errors
    	if ($this->db->trans_status() === FALSE) {
    	
    		log_message("error", "MODULO: Contactos --> No se pudo seleccionar el contacto como principal en DB.");
    	
    		return array(
    				"respuesta" => false,
    				"mensaje" => "Hubo un error al tratar de seleccionar el contacto como principal."
    		);
    	
    	} else {
    	
    		return array(
    			"respuesta" => true,
    			"mensaje" => "Se ha seleccionar el contacto como principal satisfactoriamente."
    		);
    	}
    }
    
    /**
     * Guardar un contacto exportado desde 
     * Clientes Potenciales
     *
     * @param array $clause
     * @return array
     */
    function comp__guardar_contacto($fieldset=NULL)
    {
    	if($fieldset == NULL){
    		return false;
    	}
    	
    	//
    	// Begin Transaction
    	// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
    	//
    	$this->db->trans_start();
    	
    	$uuid_cliente = !empty($fieldset["uuid_cliente"]) ? $fieldset["uuid_cliente"] : "";
    	unset($fieldset["uuid_cliente"]);
    
    	$this->db->set('uuid_contacto', 'ORDER_UUID(uuid())', FALSE);
    	$this->db->insert('con_contactos', $fieldset);
        $id_contacto = $this->db->insert_id();
        
        //Seleccionar UUID del Contacto Ingresado
        $result = $this->db->select("uuid_contacto")
		        ->distinct()
		        ->from('con_contactos')
		        ->where("id_contacto", $id_contacto)
		        ->get()
		        ->result_array();
        $uuid_contacto = !empty($result[0]["uuid_contacto"]) ? $result[0]["uuid_contacto"] : "";
        
        //Guardar Relacion Cliente/Contacto
        $fieldset = array (
        	"uuid_cliente" => $uuid_cliente,
        	"uuid_contacto" => $uuid_contacto
        );
        $this->db->insert('cl_cliente_sociedades_contactos', $fieldset);
        
        //---------------------------------------
        //End Transaction
        $this->db->trans_complete();
        
        // Managing Errors
        if ($this->db->trans_status() === FALSE) {
        
        	log_message("error", "MODULO: Contacto --> No se pudo guadar los datos del contacto convertido en juridico en DB.");
        	return false;
        
        } else {	
        	return true;
        }
    }
    function generar_csv($id_contactos)
    {
    	$id_contactos = (!empty($id_contactos) ? implode(', ', array_map(function($id_contactos){
    		return "'".$id_contactos."'";;
    	}, $id_contactos)) : "");
    
    		$sql = "SELECT DISTINCT   CONCAT(con.nombre,' ',con.apellido) AS Nombre, 
    				`con`.`cargo` as Cargo,  
    				`cl`.`nombre` as Cliente,
    				 `con`.`email` as 'E-mail',
    				`con`.`telefono` as Telefono, 
    				 datediff(act.fecha,NOW()) as 'Ultimo Contacto',
    				 `con_contactos_cat`.`etiqueta` AS 'Toma de contacto'
			FROM (`con_contactos` AS con)
			LEFT JOIN `cl_cliente_sociedades_contactos` AS cl_soc_con ON `cl_soc_con`.`uuid_contacto` = `con`.`uuid_contacto`
			LEFT JOIN `cl_clientes_sociedades` AS cl_soc ON `cl_soc_con`.`uuid_sociedad` = `cl_soc`.`uuid_sociedad`
			LEFT JOIN `cl_clientes` AS cl ON `cl`.`uuid_cliente` = `cl_soc_con`.`uuid_cliente`
			LEFT JOIN `usuarios` ON `usuarios`.`id_usuario` = `con`.`id_asignado`
			LEFT JOIN `con_contactos_cat` ON `con_contactos_cat`.`id_cat` = `con`.`id_toma_contacto`
			LEFT JOIN `act_actividades` as act ON `con`.`uuid_contacto` = `act`.`uuid_contacto`
			LEFT JOIN `act_actividades` as act2 ON `act2`.`uuid_contacto` = `act`.`uuid_contacto` AND act.id_actividad
			WHERE HEX(con.uuid_contacto) IN(". $id_contactos .") Group by `con`.`id_contacto`";
    		$query = $this->db->query($sql);
    
    		return $this->dbutil->csv_from_result($query);
    }
}
?>