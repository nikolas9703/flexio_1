<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 02:30 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Agentes\Models\Agentes;
use Flexio\Modulo\Agentes\Models\AgentesCatalogo as AgentesCatalogoModel;
use Flexio\Modulo\ComisionesSeguros\Repository\ComisionesSegurosRepository;
use Flexio\Modulo\HonorariosSeguros\Repository\HonorariosSegurosRepository;
use Flexio\Modulo\HonorariosSeguros\Models\HonorariosSeguros;
use Flexio\Modulo\HonorariosSeguros\Models\SegHonorariosPart;
use Flexio\Modulo\Contabilidad\Models\Cuentas;
use Flexio\Modulo\ComisionesSeguros\Models\SegComisionesParticipacion;
use League\Csv\Writer as Writer;
use Dompdf\Dompdf;
use Flexio\Modulo\Pagos\Models\Pagos;

class Honorarios_seguros extends CRM_Controller
{

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;
	protected $aseguradoras;
	protected $agentes;
	protected $AgentesCatalogoModel;
	protected $ComisionesSegurosRepository;
	protected $HonorariosSegurosRepository;
	protected $HonorariosSeguros;
	protected $Pagos;
	protected $SegHonorariosPart;
	protected $Cuentas;

    function __construct()
    {
        parent::__construct();

        $this->load->helper(array('file', 'string', 'util'));
        //$this->load->model('remesas/Remesas_orm');


        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_empresa = $this->empresaObj->id;


        $uuid_usuario = $this->session->userdata("huuid_usuario");
        $usuario = Usuarios::findByUuid($uuid_usuario);
        $this->usuario_id = $usuario->id;
		
		$this->aseguradoras= new Aseguradoras();
		$this->agentes=new Agentes();
		$this->AgentesCatalogoModel=new AgentesCatalogoModel();
		$this->ComisionesSegurosRepository= new ComisionesSegurosRepository();
		$this->HonorariosSegurosRepository=new HonorariosSegurosRepository();
		$this->HonorariosSeguros=new HonorariosSeguros();
		$this->Pagos=new Pagos();
		$this->SegHonorariosPart=new SegHonorariosPart();
		$this->Cuentas=new Cuentas();
    }

    public function listar(){
		
        if (is_null($this->session->flashdata('mensaje')) ) {
           $mensaje = []; 
        } else {
            $mensaje = $this->session->flashdata('mensaje');
        }

        $this->_css();
        $this->_js();

        $data = array();
		$data['mensaje'] = $mensaje;
       /* if (!$this->auth->has_permission('acceso', 'honorarios_seguros/listar') == true) {
            $acceso = 0;
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para ingresar a honorarios', 'titulo' => 'Honorarios ');
            $this->session->set_flashdata('mensaje', $mensaje);

            redirect(base_url(''));
        }*/

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Honorarios',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Honorarios</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );
		$this->assets->agregar_js(array(
			'public/assets/js/modules/honorarios_seguros/listar.js',
        ));

        $breadcrumb["menu"] = array(
            "url" => 'honorarios_seguros/crear',
            "clase" => 'modalOpcionesCrear',
            "nombre" => "Crear"
        );
		
		
		$this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));
        
		$data['agentes'] = $this->agentes->where(['id_empresa' =>$this->id_empresa])->get();
		
		$data['usuarios']=Usuarios::join('usuarios_has_roles', 'usuario_id', '=', 'usuarios.id')
        ->where('usuarios_has_roles.empresa_id', '=', $this->id_empresa)
        ->where('usuarios.estado', '=', 'Activo')
        ->select('usuarios.id', 'nombre','apellido')
        ->groupBy('usuarios.id')
        ->get();
        
        $menuOpciones["#exportarBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Listado de Honorarios');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }

    public function ajax_listar_honorarios()
    {
        //Just Allow ajax request

        if(!$this->input->is_ajax_request()){
            return false;
        }
		
		$clause = array(
    		"empresa_id" =>  $this->id_empresa
    	);
        
		$no_honorario= $this->input->post('no_honorario', true);
		$agente= $this->input->post('agente', true);
		$inicio_fecha= $this->input->post('inicio_fecha', true);
		$fin_fecha= $this->input->post('fin_fecha', true);
		$usuario= $this->input->post('usuario', true);
		$estado= $this->input->post('estado', true);
		
		if(!empty($no_honorario)){
    		$clause["no_honorario"] = array('LIKE', "%$no_honorario%");
    	}
		if(!empty($agente)){
    		$clause["agente_id"] = $agente;
    	}
		if(!empty($inicio_fecha)){
			$fecha1=date('Y-m-d', strtotime($inicio_fecha));
    		$clause["fecha1"] = $fecha1;
    	}
		if(!empty($fin_fecha)){
			$fecha2=date('Y-m-d', strtotime($fin_fecha));
    		$clause["fecha2"] = $fecha2;
    	}
		if(!empty($usuario)){
    		$clause["usuario_id"] = $usuario;
    	}
		if(!empty($estado)){
    		$clause["seg_honorarios.estado"] = $estado;
    	}
		
		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		
    	$count = $this->HonorariosSegurosRepository->listar($clause, NULL, NULL, NULL, NULL)->count();
    		
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    		 
    	$rows = $this->HonorariosSegurosRepository->listar($clause, $sidx, $sord, $limit, $start);
    	
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;

        if(!empty($rows)){
            foreach ($rows as  $row){
                $tituloBoton = ($row['estado']!=1)?'Habilitar':'Deshabilitar';
                $hidden_options = "";
				
				if($row->estado=='pagada')
				{
					$clase_estado='background-color: #5cb85c';
					$estado='Pagada';
				}
				else if($row->estado=='por_pagar')
				{
					$clase_estado='background-color: #5bc0de';
					$estado='Por pagar';
				}
				else if($row->estado=='en_proceso')
				{
					$clase_estado='background-color: #F8AD46';
					$estado='En proceso';
				}
				
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				$hidden_options = '<a href="'. base_url('honorarios_seguros/editar/'. bin2hex($row->uuid_honorario)) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
			
				if($row['monto_total']>=0)
					$estilomonto='totales-success';
				else
					$estilomonto='totales-danger';
				
                $level = substr_count($row['nombre'],".");
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'no_honorario'=> '<a href="'.base_url('honorarios_seguros/editar/'.bin2hex($row->uuid_honorario)).'">'.$row['no_honorario']."</a>",
                    'comisiones_pagadas' => $row['comisiones_pagadas'],
                    'agente_id' => $row['nom_agente'],
                    'monto_total' => '<label class="'.$estilomonto.'">'.number_format($row['monto_total'], 2, '.', ',').'</label>',
					'fecha' => $row['created_at'],
                    'usuario_id' => $row->nom_usuario." ".$row->ape_usuario,
					'estado' => '<span style="color:white; '.$clase_estado.'" class="btn btn-xs btn-block estadoSolicitudes">'.$estado.'</span>',
					'link' => $link_option,
                    'options'=>$hidden_options 
                ) );
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }
	
	public function ocultotabla()
    {

        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/honorarios_seguros/tabla.js',
        ));

        $this->load->view('tabla');
    }
	
	public function crear(){
		
		/*if (!$this->auth->has_permission('acceso', 'remesas_entrantes/crear')) {
			// No, tiene permiso, redireccionarlo.
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para crear', 'titulo' => 'Remesas entrantes');
			$this->session->set_flashdata('mensaje', $mensaje);
			redirect(base_url('remesas_entrantes/listar'));
		} else {
			$mensaje = [];
		}*/

        $this->_css();
        $this->_js();

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/honorarios_seguros/crear.vue.js',
			'public/assets/js/modules/honorarios_seguros/plugins.js'
        ));
		
		$this->assets->agregar_var_js(array(
			"vista" => 'crear',
			"fecha_desde" => '',
			"fecha_hasta" => '',
			"agente_id" => '',
			"usuario_id" => '',    
			"codigo" => '',
			"estado_honorario"=>'',
			"no_pago"=>''
		));

        $data['agentes'] = $this->agentes->where(['id_empresa' =>$this->id_empresa])->get();

        $clause = array('empresa_id' => $this->id_empresa);

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Honorarios: crear',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
				1 => array("nombre" => '<b>Honorarios</b>', "activo" => true, "url" => 'honorarios_seguros/listar'),
				2 => array("nombre" => '<b>Crear</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );

        $breadcrumb["menu"] = array(
            "url" => '',
            "clase" => 'modalOpcionesCrear',
            "nombre" => "Acción"
        );
		
        $menuOpciones["#exportarLnk"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;


        $this->template->agregar_titulo_header('Crear Honorario');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }
	
	public function editar($uuid=NULL)
	{
		
		/*if( !$this->auth->has_permission('acceso', 'remesas_entrantes/ver/(:any)') && !$this->auth->has_permission('acceso', 'remesas_entrantes/editar/(:any)') ) { 
			// No, tiene permiso, redireccionarlo.
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para ver detalle de la remesa', 'titulo' => 'Remesas entrantes');
			$this->session->set_flashdata('mensaje', $mensaje);
			redirect(base_url('remesas_entrantes/listar'));
		}
		elseif($this->auth->has_permission('acceso', 'remesas_entrantes/editar/(:any)')) 
		{
				$ver = 0;
				$mensaje = [];
		}
		else
		{
				$ver = 1;
				$mensaje = [];
		}*/

		$this->assets->agregar_js(array(
			'public/assets/js/modules/honorarios_seguros/crear.vue.js',
			'public/assets/js/modules/honorarios_seguros/plugins.js'
		));

		$data = array();
		$data['vista'] = "editar";

		$honorario = $this->HonorariosSeguros->where(['uuid_honorario' => hex2bin(strtolower($uuid))])->first();
		$codigo = $honorario->no_honorario;

		$this->_js();
		$this->_css();
		
		$fecha_desde='';
		if($honorario->fecha_desde!='0000-00-00' && $honorario->fecha_desde!=null && $honorario->fecha_desde!='')
			$fecha_desde = date('m/d/Y', strtotime($honorario->fecha_desde));
		
		$fecha_hasta='';
		if($honorario->fecha_hasta!='0000-00-00' && $honorario->fecha_hasta!=null && $honorario->fecha_hasta!='')
			$fecha_hasta = date('m/d/Y', strtotime($honorario->fecha_hasta));  

		$codigohono='';
		if($honorario->id_pago!="")
		{
			$codigohono=$honorario->datosPago->codigo;
		}
			
		$this->assets->agregar_var_js(array(
			"vista" => 'editar',
			"fecha_desde" => $fecha_desde ,
			"fecha_hasta" => $fecha_hasta,
			"agente_id" => $honorario->agente_id,
			"usuario_id" => $honorario->usuario_id,    
			"codigo" => $codigo,
			"estado_honorario"=>$honorario->estado,
			"no_pago"=>$codigohono
		));
		 $data['agentes'] = $this->agentes->where(['id_empresa' =>$this->id_empresa])->get();

        $clause = array('empresa_id' => $this->id_empresa);
		
		$breadcrumb = array(
			"titulo" => '<i class="fa fa-archive"></i> Honorarios: '.$codigo,
			"ruta" => array(
				0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
				1 => array("nombre" => '<b>Honorarios</b>', "activo" => true, "url" => 'honorarios_seguros/listar'),
				2 => array("nombre" => '<b>Detalle</b>', "activo" => true)
				),
			"filtro" => false,
			"menu" => array()
			);

		$breadcrumb["menu"] = array(
			"url" => '',
			"clase" => '',
			"nombre" => "Acción"
			);

		$menuOpciones["#imprimirHonorarioBtn"] = "Imprimir";
		$breadcrumb["menu"]["opciones"] = $menuOpciones;

		$this->template->agregar_titulo_header('Ver honorario');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar($breadcrumb);
	}
	
	
	function ajax_get_datos_agente(){
		
		$agente=$this->agentes->find($_POST['id_agente']);
		$identificacion = $agente->identificacion;
		
		$response = array();
		
		if ($agente->tipo_identificacion=="natural") 
		{
			if($agente->letra == '0' || empty(($agente->letra) || !isset($agente['letra']))){
				list($provincia, $tomo, $asiento) =  explode("-", $identificacion);
				$response['letra']= "0";
				$response['tomo']= $tomo;
				$response['asiento']= $asiento;
				$response['tipo_identificacion']= "natural";
			}
			else if(($agente->letra == 'N'))
			{
				list($provincia, $tomo, $asiento) =  explode("-", $identificacion);
				$provincia = str_replace("N","",$provincia);
				//$response['provincia']= $provincia;
				$response['letra']= 'N';
				$response['tomo'] = $tomo;
				$response['asiento']= $asiento;
				$response['tipo_identificacion']= "natural";
			}
			else if($agente->letra == 'PE')
			{
				list($provincia, $tomo, $asiento) =  explode("-", $identificacion);
				$provincia = str_replace("PE","",$provincia);
				//$response['provincia']= $provincia;
				$response['letra']= 'PE';
				$response['tomo'] = $tomo;
				$response['asiento']= $asiento;
				$response['tipo_identificacion']= "natural";
			}
			else if($agente->letra == 'E')
			{
				list($provincia, $tomo, $asiento) =  explode("-", $identificacion);
				$provincia = str_replace("E","",$provincia);
				//$response['provincia']= $provincia;
				$response['letra']= 'E';
				$response['tomo'] = $tomo;
				$response['asiento']= $asiento;
				$response['tipo_identificacion']= "natural";
			}
			else if(($agente->letra == 'PI'))
			{
				list($provincia, $tomo, $asiento) =  explode("-", $identificacion);
				$provincia = str_replace("PI","",$provincia);
				//$response['provincia']= $provincia;
				$response['letra']= "PI";
				$response['tomo']= $tomo;
				$response['asiento']= $asiento;
				$response['tipo_identificacion']= "natural";
			}
			
			if($provincia!="")
				$provincia=$this->AgentesCatalogoModel->where('key',$provincia)->first()->etiqueta;
			else
				$provincia='';
			$response['provincia']= html_entity_decode(utf8_decode($provincia));
		}else if ($agente->tipo_identificacion=="juridico") {
			list($tomo_ruc, $folio, $asiento_ruc, $digito) =  explode("-", $identificacion); 
			$response['tomo_ruc']=$tomo_ruc;	
			$response['folio_ruc']=$folio;	
			$response['asiento_ruc']=$asiento_ruc;	
			$response['digito_ruc']=$digito;	
			$response['tipo_identificacion']="juridico";
		}else if ($agente['tipo_identificacion'] == "pasaporte") {
			$response['letra_pas']='PAS';	
			$response['pasaporte']= $identificacion;
			$response['tipo_identificacion']="pasaporte";
		}
		
		$response['telefono']=$agente->telefono;
		$response['correo']=$agente->correo;
		
		 echo json_encode($response);
		
	}
	
	public function tabla_comisiones(){

        $this->assets->agregar_js(array(
            'public/assets/js/modules/honorarios_seguros/crear.vue.js'
        ));

    
        $this->load->view('tabla_comisiones');
    }
	
	public function ajax_get_comisiones() {

        $clause = array(
    		"empresa_id" =>  $this->id_empresa
    	);
		
		$id_agente = $_POST['id_agente'];
        $fecha_inicial = $_POST['fecha_inicio']; 
        $fecha_final = $_POST['fecha_final'];

		if($fecha_inicial!="")
		{
			$clause['fecha1']=date('Y-m-d', strtotime($fecha_inicial));
		}
		if($fecha_final!="")
		{
			$clause['fecha2']=date('Y-m-d', strtotime($fecha_final));
		}
		
		$response = new stdClass();
        $response->inter = array();
		$monto_total=0;
		$total_comisiones=0;
		
		$honorarioguardadoid='';
		
		if($_POST['codigo']!="")
		{
			$honorarioguardado=$this->HonorariosSeguros->where('no_honorario',$_POST['codigo'])->first();
			$idcomisiones=$this->SegHonorariosPart->where('id_honorario',$honorarioguardado->id)->get();
			
			$honorarioguardadoid=$honorarioguardado->id;
			
			$comiId=array();
			foreach($idcomisiones as $comi)
			{
				$comiId[]=$comi->id_comision_part;
			}
			$comisionesguardadas=$this->ComisionesSegurosRepository->getComisionesAgentesGuardadas($comiId,$id_agente, $fecha_inicial,$fecha_final);
			
			foreach($comisionesguardadas as $key => $value)
			{
				$link_comision=base_url('comisiones_seguros/ver/'.bin2hex($value->datosComision->uuid_comision));
				$link_cliente=base_url('clientes/ver/'.bin2hex($value->datosComision->cliente->uuid_cliente));
				$link_aseguradora=base_url('aseguradoras/editar/'.bin2hex($value->datosComision->datosAseguradora->uuid_aseguradora));
				$link_poliza=base_url('polizas/editar/'.bin2hex($value->datosComision->polizas->uuid_poliza));
				
				array_push($response->inter, array("id"=>$value->comision_id,"link_comision"=>$link_comision,"link_cliente"=>$link_cliente,"link_aseguradora"=>$link_aseguradora,"link_poliza"=>$link_poliza,"no_comision"=>$value->datosComision->no_comision,"fecha_comision"=>date_format(date_create($value->datosComision->created_at),'Y-m-d'),"no_recibo" => $value->datosComision->datosCobro->codigo, "cliente"=>$value->datosComision->cliente->nombre,"aseguradora"=>$value->datosComision->datosAseguradora->nombre,"ramo"=>$value->datosComision->datosRamos->nombre,"poliza"=>$value->datosComision->polizas->numero,"prima_neta"=>number_format($value->datosComision->facturasComisiones->subtotal,2),"pago"=>number_format($value->datosComision->pago_sobre_prima,2),"porcentaje_comision" => $value->porcentaje, "monto_comision" => $value->monto,"total"=>'',"total_com"=>'','estilos' => 'font-weight: normal' ));
				
				$monto_total+=$value->monto;
				$total_comisiones++;
			}
		}
		
		if($honorarioguardadoid=='')
		{
			$comisionesagentes = $this->ComisionesSegurosRepository->getComisionesAgentes($id_agente,$this->id_empresa,$fecha_inicial,$fecha_final);
        
			foreach ($comisionesagentes as $key => $value) 
			{
				$link_comision=base_url('comisiones_seguros/ver/'.bin2hex($value->datosComision->uuid_comision));
				$link_cliente=base_url('clientes/ver/'.bin2hex($value->datosComision->cliente->uuid_cliente));
				$link_aseguradora=base_url('aseguradoras/editar/'.bin2hex($value->datosComision->datosAseguradora->uuid_aseguradora));
				$link_poliza=base_url('polizas/editar/'.bin2hex($value->datosComision->polizas->uuid_poliza));
				
				array_push($response->inter, array("id"=>$value->comision_id,"link_comision"=>$link_comision,"link_cliente"=>$link_cliente,"link_aseguradora"=>$link_aseguradora,"link_poliza"=>$link_poliza,"no_comision"=>$value->datosComision->no_comision,"fecha_comision"=>date_format(date_create($value->datosComision->created_at),'Y-m-d'),"no_recibo" => $value->datosComision->datosCobro->codigo, "cliente"=>$value->datosComision->cliente->nombre,"aseguradora"=>$value->datosComision->datosAseguradora->nombre,"ramo"=>$value->datosComision->datosRamos->nombre,"poliza"=>$value->datosComision->polizas->numero,"prima_neta"=>number_format($value->datosComision->facturasComisiones->subtotal,2),"pago"=>number_format($value->datosComision->pago_sobre_prima,2),"porcentaje_comision" => $value->porcentaje, "monto_comision" => $value->monto,"total"=>'',"total_com"=>'','estilos' => 'font-weight: normal' ));
				
				$monto_total+=$value->monto;
				$total_comisiones++;
			}
		}
		
		array_push($response->inter, array("id"=>'',"link_comision"=>'',"link_cliente"=>'',"link_aseguradora"=>'',"link_poliza"=>'',"no_comision"=>'',"fecha_comision"=>'',"no_recibo" => '', "cliente"=>'',"aseguradora"=>'',"ramo"=>'',"poliza"=>'',"prima_neta"=>'',"pago"=>'',"porcentaje_comision" => '', "monto_comision" => '',"total"=>number_format($monto_total,2),"total_com"=>$total_comisiones,'estilos' => 'font-weight: bold' ));
			
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit;       
    }
	
	public function guardar_honorario_proceso()
	{
		$id_agente = $_POST['id_agente'];
        $fecha_inicial = $_POST['fecha_inicio']; 
        $fecha_final = $_POST['fecha_final'];
		$comisiones_par=$_POST['comisionespar_id'];
		$monto=$_POST['monto'];
		$comisiones=$_POST['comisiones'];
		
		if($_POST['codigo']=='')
		{
			//crear el honorario
			$campo=array();
			$count = $this->HonorariosSeguros->where('empresa_id',$this->id_empresa)->count();
			$codigo = Util::generar_codigo('HON'.$this->id_empresa, ($count+1) );
			$campo["no_honorario"] = $codigo;
			$campo["uuid_honorario"] = Capsule::raw("ORDER_UUID(uuid())");
			$campo['monto_total']=$monto;
			$campo['comisiones_pagadas']=$comisiones;
			$campo['estado']='en_proceso';
			$campo['agente_id']=$id_agente;
			if($_POST['fecha_inicio']!="")
				$campo["fecha_desde"] =date('Y-m-d', strtotime($_POST['fecha_inicio']));
			if($_POST['fecha_final']!="")
				$campo["fecha_hasta"] =date('Y-m-d', strtotime($_POST['fecha_final']));
			$campo['empresa_id']=$this->id_empresa;
			$campo['usuario_id']=$this->usuario_id;
			$campo['created_at']=date('Y-m-d H:i:s');
			$campo['updated_at']=date('Y-m-d H:i:s');
			
			$crear_honorario=$this->HonorariosSeguros->create($campo);
			
			foreach($comisiones_par as $compar)
			{
				$campohonpar=array();
				$campohonpar['id_honorario']=$crear_honorario->id;
				$campohonpar['id_comision_part']=$compar;
				
				$honorariopart=$this->SegHonorariosPart->create($campohonpar);
			}
		}
		
		else
		{
			//buscar el honorario existente
			$honorario_existente=$this->HonorariosSeguros->where('no_honorario',$_POST['codigo'])->first();
			
			$this->SegHonorariosPart->where('id_honorario',$honorario_existente->id)->delete();
			
			foreach($comisiones_par as $compar)
			{
				$exitepar=$this->SegHonorariosPart->where('id_honorario',$honorario_existente->id)
				->where('id_comision_part',$compar)->count();
				
				if($exitepar==0)
				{
					$campohonpar=array();
					$campohonpar['id_honorario']=$honorario_existente->id;
					$campohonpar['id_comision_part']=$compar;
				
					$honorariopart=$this->SegHonorariosPart->create($campohonpar);
				}
				else
				{
					$actpar['updated_at']=date('Y-m-d H:i:s');
					
					$exitepar=$this->SegHonorariosPart->where('id_honorario',$honorario_existente->id)
					->where('id_comision_part',$compar)->update($actpar);
				}
				
			}
		}
		
		$response['exito']='si';
		
		echo json_encode($response);
	}
	
	public function guardar_honorario_por_pagar()
	{
		$id_agente = $_POST['id_agente'];
        $fecha_inicial = $_POST['fecha_inicio']; 
        $fecha_final = $_POST['fecha_final'];
		$comisiones_par=$_POST['comisionespar_id'];
		$monto=$_POST['monto'];
		$comisiones=$_POST['comisiones'];
		
		if($_POST['codigo']=='')
		{
			//crear el honorario
			$campo=array();
			$count = $this->HonorariosSeguros->where('empresa_id',$this->id_empresa)->count();
			$codigo = Util::generar_codigo('HON'.$this->id_empresa, ($count+1) );
			$campo["no_honorario"] = $codigo;
			$campo["uuid_honorario"] = Capsule::raw("ORDER_UUID(uuid())");
			$campo['monto_total']=$monto;
			$campo['comisiones_pagadas']=$comisiones;
			$campo['estado']='por_pagar';
			$campo['agente_id']=$id_agente;
			if($_POST['fecha_inicio']!="")
				$campo["fecha_desde"] =date('Y-m-d', strtotime($_POST['fecha_inicio']));
			if($_POST['fecha_final']!="")
				$campo["fecha_hasta"] =date('Y-m-d', strtotime($_POST['fecha_final']));
			$campo['empresa_id']=$this->id_empresa;
			$campo['usuario_id']=$this->usuario_id;
			$campo['created_at']=date('Y-m-d H:i:s');
			$campo['updated_at']=date('Y-m-d H:i:s');
			
			$crear_honorario=$this->HonorariosSeguros->create($campo);
			
			foreach($comisiones_par as $compar)
			{
				$campohonpar=array();
				$campohonpar['id_honorario']=$crear_honorario->id;
				$campohonpar['id_comision_part']=$compar;
				
				$honorariopart=$this->SegHonorariosPart->create($campohonpar);
			}
		}
		else
		{
			//buscar el honorario existente
			$campo=array();
			$campo['estado']='por_pagar';
			$actualizar_hono=$this->HonorariosSeguros->where('no_honorario',$_POST['codigo'])->first()->update($campo);
			
			$crear_honorario=$this->HonorariosSeguros->where('no_honorario',$_POST['codigo'])->first();
			
			$this->SegHonorariosPart->where('id_honorario',$crear_honorario->id)->delete();
			
			$id_par_com_pagas=array();
			foreach($comisiones_par as $compar)
			{
				$exitepar=$this->SegHonorariosPart->where('id_honorario',$crear_honorario->id)
				->where('id_comision_part',$compar)->count();
				
				if($exitepar==0)
				{
					$campohonpar=array();
					$campohonpar['id_honorario']=$crear_honorario->id;
					$campohonpar['id_comision_part']=$compar;
				
					$honorariopart=$this->SegHonorariosPart->create($campohonpar);
				}
				else
				{
					$actpar['updated_at']=date('Y-m-d H:i:s');
					
					$exitepar=$this->SegHonorariosPart->where('id_honorario',$crear_honorario->id)
					->where('id_comision_part',$compar)->update($actpar);
				}
				
			}
		}
		
		//crear el pago por cada honorario creado
		$banco=$this->Cuentas->where('empresa_id',$this->id_empresa)->where('estado',1)->first();
		$campo['campo']["uuid_pago"] = Capsule::raw("ORDER_UUID(uuid())");
		$campo['campo']["fecha_pago"] = date('d/m/Y');
		$campo['campo']["proveedor_id"] = $id_agente;
		$campo['campo']["monto_pagado"] = $monto;
		$campo['campo']["cuenta_id"] = 0;
		$campo['campo']["empresa_id"] = $this->id_empresa;
		$campo['campo']["depositable_type"] = 'banco';
		$campo['campo']["depositable_id"] = $banco->id;
		$campo['campo']['empezable_type'] = 'participacion';
        $campo['campo']['empezable_id'] = $crear_honorario->id;
		$campo['campo']['estado'] = 'por_aplicar';
		$campo['campo']['formulario'] = 'honorario';
		
		$metodo_pago_reg['tipo_pago']='efectivo';
		$metodo_pago_reg['total_pagado']=$monto;
		
		$pagables=$comisiones_par;
		
		$formGuardar = new Flexio\Modulo\Pagos\FormRequest\GuardarPagos();
		
		$guardado=$formGuardar->save($campo,$metodo_pago_reg,$pagables);
		
		$acthono['id_pago']=$guardado->id;
		
		$actpagohonorario=$this->HonorariosSeguros->find($crear_honorario->id)->update($acthono);
		
		//$actparpago['no_recibo']=$guardado->id;
		//$actpagopart=SegComisionesParticipacion::where('agente_id',$id_agente)->whereIn('comision_id',$comisiones_par)->update($actparpago);
		
		$response['exito']='si';
		
		echo json_encode($response);
		
	}
	
	public function exportar() {
    	if(empty($_POST)){
    		exit();
    	}
    	$ids =  $this->input->post('ids', true);
		$id = explode(",", $ids);

		if(empty($id)){
			return false;
		}
		$csv = array();
		
        $clause['id'] = $id;
                
		$honorarios = $this->HonorariosSegurosRepository->exportar($clause);
		if(empty($honorarios)){
			return false;
		}
		$i=0;
		foreach ($honorarios AS $row)
		{
			if($row->estado=='en_proceso')
				$estado='En proceso';
			else if($row->estado=='pagada')
				$estado='Pagada';
			if($row->estado=='por_pagar')
				$estado='Por pagar';
			
			$csvdata[$i]['no_honorario'] = $row->no_honorario;
			$csvdata[$i]["comisiones_pagadas"] = $row->comisiones_pagadas;
			$csvdata[$i]["agente"] = utf8_decode(Util::verificar_valor($row->nom_agente));
			$csvdata[$i]["monto_total"] = $row->monto_total;
			$csvdata[$i]["created_at"] = $row->created_at;
			$csvdata[$i]["usuario"] = $row->nom_usuario." ".$row->ape_usuario;
			$csvdata[$i]["estado"] = $estado;
			$i++;
		}
		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			utf8_decode('N. Honorario'),
			'Comisiones pagadas',
			'Agente',
			utf8_decode('Monto'),
			'Fecha',
			'Usuario',
			'Estado'
		]);
		$csv->insertAll($csvdata);
		$csv->output("Honorario-". date('ymd') .".csv");
		exit();
    }
	
	public function imprimirHonorarios($codigo = null, $id_agente = null , $fecha_inicial = null, $fecha_final = null)
	{
		$clause = array(
    		"empresa_id" =>  $this->id_empresa
    	);

		if($fecha_inicial!="")
		{
			$clause['fecha1']=date('Y-m-d', strtotime($fecha_inicial));
		}
		if($fecha_final!="")
		{
			$clause['fecha2']=date('Y-m-d', strtotime($fecha_final));
		}
		
		$datosAgente=Agentes::find($id_agente);
		
		$response = new stdClass();
        $datosHonorario= array();
		$monto_total=0;
		$total_comisiones=0;
		
		$honorarioguardadoid='';
		
		if($codigo)
		{
			$honorarioguardado=$this->HonorariosSeguros->where('no_honorario',$codigo)->first();
			$idcomisiones=$this->SegHonorariosPart->where('id_honorario',$honorarioguardado->id)->get();
			
			$honorarioguardadoid=$honorarioguardado->id;
			
			$comiId=array();
			foreach($idcomisiones as $comi)
			{
				$comiId[]=$comi->id_comision_part;
			}
			$comisionesguardadas=$this->ComisionesSegurosRepository->getComisionesAgentesGuardadas($comiId,$id_agente);
			
			foreach($comisionesguardadas as $key => $value)
			{
				$link_comision=base_url('comisiones_seguros/ver/'.bin2hex($value->datosComision->uuid_comision));
				$link_cliente=base_url('clientes/ver/'.bin2hex($value->datosComision->cliente->uuid_cliente));
				$link_aseguradora=base_url('aseguradoras/editar/'.bin2hex($value->datosComision->datosAseguradora->uuid_aseguradora));
				$link_poliza=base_url('polizas/editar/'.bin2hex($value->datosComision->polizas->uuid_poliza));
				
				array_push($datosHonorario, array("id"=>$value->comision_id,"link_comision"=>$link_comision,"link_cliente"=>$link_cliente,"link_aseguradora"=>$link_aseguradora,"link_poliza"=>$link_poliza,"no_comision"=>$value->datosComision->no_comision,"fecha_comision"=>date_format(date_create($value->datosComision->created_at),'Y-m-d'),"no_recibo" => $value->datosComision->datosCobro->codigo, "cliente"=>$value->datosComision->cliente->nombre,"aseguradora"=>$value->datosComision->datosAseguradora->nombre,"ramo"=>$value->datosComision->datosRamos->nombre,"poliza"=>$value->datosComision->polizas->numero,"prima_neta"=>number_format($value->datosComision->facturasComisiones->subtotal,2),"pago"=>number_format($value->datosComision->pago_sobre_prima,2),"porcentaje_comision" => $value->datosComision->comision, "monto_comision" => number_format($value->datosComision->monto_comision,2),"total"=>'',"total_com"=>'','estilos' => 'font-weight: normal' ));
				
				$monto_total+=$value->datosComision->monto_comision;
				$total_comisiones++;
			}
		}
		
		if($honorarioguardadoid=='')
		{
			$comisionesagentes = $this->ComisionesSegurosRepository->getComisionesAgentes($id_agente,$this->id_empresa,$fecha_inicial,$fecha_final);
        
			foreach ($comisionesagentes as $key => $value) 
			{
				$link_comision=base_url('comisiones_seguros/ver/'.bin2hex($value->datosComision->uuid_comision));
				$link_cliente=base_url('clientes/ver/'.bin2hex($value->datosComision->cliente->uuid_cliente));
				$link_aseguradora=base_url('aseguradoras/editar/'.bin2hex($value->datosComision->datosAseguradora->uuid_aseguradora));
				$link_poliza=base_url('polizas/editar/'.bin2hex($value->datosComision->polizas->uuid_poliza));
				
				array_push($datosHonorario, array("id"=>$value->comision_id,"link_comision"=>$link_comision,"link_cliente"=>$link_cliente,"link_aseguradora"=>$link_aseguradora,"link_poliza"=>$link_poliza,"no_comision"=>$value->datosComision->no_comision,"fecha_comision"=>date_format(date_create($value->datosComision->created_at),'Y-m-d'),"no_recibo" => $value->datosComision->datosCobro->codigo, "cliente"=>$value->datosComision->cliente->nombre,"aseguradora"=>$value->datosComision->datosAseguradora->nombre,"ramo"=>$value->datosComision->datosRamos->nombre,"poliza"=>$value->datosComision->polizas->numero,"prima_neta"=>number_format($value->datosComision->facturasComisiones->subtotal,2),"pago"=>number_format($value->datosComision->pago_sobre_prima,2),"porcentaje_comision" => $value->datosComision->comision, "monto_comision" => number_format($value->datosComision->monto_comision,2),"total"=>'',"total_com"=>'','estilos' => 'font-weight: normal' ));
				
				$monto_total+=$value->datosComision->monto_comision;
				$total_comisiones++;
			}
		}
		
		if($honorarioguardado->estado=='en_proceso')
		{
			$formulario='formularioEnProceso';
		}
		else
		{
			$formulario='formularioProcesada';
		}
		
		array_push($datosHonorario, array("id"=>'',"link_comision"=>'',"link_cliente"=>'',"link_aseguradora"=>'',"link_poliza"=>'',"no_comision"=>'',"fecha_comision"=>'',"no_recibo" => '', "cliente"=>'',"aseguradora"=>'',"ramo"=>'',"poliza"=>'',"prima_neta"=>'',"pago"=>'',"porcentaje_comision" => '', "monto_comision" => '',"total"=>number_format($monto_total,2),"total_com"=>$total_comisiones,'estilos' => 'font-weight: bold' ));
		
        $data = ['datosagente'=>$datosAgente,'honorario'=>$honorarioguardado,'fecha_inicial' => $fecha_inicial, 'fecha_final' => $fecha_final, 'datosHonorario' => $datosHonorario];
		$dompdf = new Dompdf();
		$html = $this->load->view('pdf/' . $formulario, $data,true);
		$dompdf->loadHtml($html);
		$dompdf->set_paper ('A4','landscape'); 
		//$dompdf->setPaper('A4', 'landscape');
		$dompdf->render();
		$dompdf->stream($nombre, array("Attachment" => false));
		exit(0);
	}

     private function _css() {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            //'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css',
            //'public/assets/css/plugins/jquery/toastr.min.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css',
            //'public/assets/css/modules/stylesheets/remesasentrantes.css',
			//'public/assets/css/modules/stylesheets/cobros.css',
			'public/assets/css/modules/stylesheets/honorariosseguros.css'
        ));
    }

    private function _js() {
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/default/subir_documento_modulo.js',
                //'public/assets/js/default/grid.js',
        ));
    }
    
}