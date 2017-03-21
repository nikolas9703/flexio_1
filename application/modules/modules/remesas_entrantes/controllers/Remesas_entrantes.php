<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 02:30 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\RemesasEntrantes\Models\RemesasEntrantes as RemesasEntrantes;
use Flexio\Modulo\RemesasEntrantes\Models\RemesasEntrantesFacturas as RemesasEntrantesFacturas;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Ramos\Repository\RamoRepository as RamoRepository;
use Flexio\Modulo\Usuarios\Models\RolesUsuario;
use Flexio\Modulo\Ramos\Models\RamosUsuarios;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\FacturasSeguros\Repository\FacturaSeguroRepository;
use Flexio\Modulo\RemesasEntrantes\Repository\RemesasEntrantesRepository as RemesasEntrantesRepository;
use Flexio\Modulo\Ramos\Models\Ramos as Ramos;
use League\Csv\Writer as Writer;
use Dompdf\Dompdf;
use Flexio\Modulo\Cobros_seguros\HttpRequest\FormGuardar;
use Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros as cobrosSeguros;
use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\ComisionesSeguros\Models\ComisionesSeguros as ComisionesSeguros;
use Flexio\Modulo\ComisionesSeguros\Models\ComisionesSegurosRemesas as ComisionesSegurosRemesas;
use Flexio\Modulo\ComisionesSeguros\Repository\ComisionesSegurosRepository as ComisionesSegurosRepository;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\ComisionesSeguros\Models\SegComisionesParticipacion as SegComisionesParticipacion;
use Flexio\Modulo\Polizas\Models\SegPolizasAgentePrin as agentePrincipal;
use Flexio\Modulo\Polizas\Models\PolizasParticipacion as agenteParticipacion;

class Remesas_entrantes extends CRM_Controller
{

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;
    protected $ramoRepository;
	protected $aseguradoras;
	protected $RemesasEntrantes;
	protected $RemesasEntrantesRepository;
	protected $FacturaSeguroRepository;
	protected $Ramos;
	protected $RemesasEntrantesFacturas;
	protected $cobrosSeguros;
	protected $ComisionesSeguros;
	protected $COmisionesSegurosRemesas;
	protected $COmisionesSegurosRepository;
	protected $SegComisionesParticipacion;
	protected $agentePrincipal;
	protected $agenteParticipacion;


    function __construct()
    {
        parent::__construct();

        $this->load->helper(array('file', 'string', 'util'));
        $this->load->model('movimiento_monetario/Movimiento_monetario_orm');
		$this->load->model('movimiento_monetario/Items_recibos_orm');

        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_empresa = $this->empresaObj->id;


        $uuid_usuario = $this->session->userdata("huuid_usuario");
        $usuario = Usuarios::findByUuid($uuid_usuario);
        $this->usuario_id = $usuario->id;
        
        $this->ramoRepository = new RamoRepository();
		
		$this->aseguradoras= new Aseguradoras();
		$this->RemesasEntrantes=new RemesasEntrantes();
		$this->RemesasEntrantesRepository= new RemesasEntrantesRepository();
		$this->FacturaSeguroRepository=new FacturaSeguroRepository();
		$this->Ramos= new Ramos();
		$this->RemesasEntrantesFacturas=new RemesasEntrantesFacturas();
		$this->cobrosSeguros = new cobrosSeguros();
		$this->ComisionesSeguros=new ComisionesSeguros();
		$this->COmisionesSegurosRemesas=new COmisionesSegurosRemesas();
		$this->COmisionesSegurosRepository=new COmisionesSegurosRepository();
		$this->SegComisionesParticipacion=new SegComisionesParticipacion();
		$this->agentePrincipal= new agentePrincipal();
		$this->agenteParticipacion=new agenteParticipacion();

    }

    public function ocultotabla($id_cliente = NULL)
    {

        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/remesas_entrantes/tabla.js',
        ));

        if (!empty($id_cliente)) {

            // Agregra variables PHP como variables JS
            $this->assets->agregar_var_js(array(
                "id_cliente" => $id_cliente
            ));

        }

        $this->load->view('tabla');
    }
	
	public function tablatabremesasentrantes($id_cliente = NULL)
    {

        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/remesas_entrantes/tablatab.js',
        ));

        $this->load->view('tabla');
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
        if (!$this->auth->has_permission('acceso', 'remesas_entrantes/listar') == true) {
            $acceso = 0;
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para ingresar a remesas entrantes', 'titulo' => 'Remesas Entrantes ');
            $this->session->set_flashdata('mensaje', $mensaje);

            redirect(base_url(''));
        }

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Remesas Entrantes',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Remesas Entrantes</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );
		$this->assets->agregar_js(array(
			'public/assets/js/modules/remesas_entrantes/listar.js',
        ));

        $breadcrumb["menu"] = array(
            "url" => 'remesas_entrantes/crear',
            "clase" => 'modalOpcionesCrear',
            "nombre" => "Crear"
        );
		
		
		$this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));
        
		$data['aseguradoras']=$this->aseguradoras->select('id','nombre')->where('empresa_id','=',$this->id_empresa)->get();
		$data['usuarios']=Usuarios::join('usuarios_has_roles', 'usuario_id', '=', 'usuarios.id')
        ->where('usuarios_has_roles.empresa_id', '=', $this->id_empresa)
        ->where('usuarios.estado', '=', 'Activo')
        ->select('usuarios.id', 'nombre','apellido')
        ->groupBy('usuarios.id')
        ->get();
        
        $menuOpciones["#exportarBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Listado de Remesas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }

    public function ajax_listar_remesas()
    {
        //Just Allow ajax request

        if(!$this->input->is_ajax_request()){
            return false;
        }
		
		$clause = array(
    		"empresa_id" =>  $this->id_empresa
    	);
        
		$no_remesa= $this->input->post('no_remesa', true);
		$nombre_aseguradora= $this->input->post('nombre_aseguradora', true);
		$inicio_fecha= $this->input->post('inicio_fecha', true);
		$fin_fecha= $this->input->post('fin_fecha', true);
		$usuario= $this->input->post('usuario', true);
		$estado= $this->input->post('estado', true);
		$pagos_remesados= $this->input->post('pagos_remesados', true);
		$aseguradora= $this->input->post('aseguradora_id', true);
		$monto= $this->input->post('seg_remesas_entrantes_monto', true);
		$fecha= $this->input->post('seg_remesas_entrantes_fecha', true);
		$usuario_nombre=$this->input->post('usuario_id', true);		
		$estado = $this->input->post('estado', true);
		
		$uuid_aseguradora=$this->input->post('uuid_aseguradora', true);
		
		if(!empty($no_remesa)){
    		$clause["no_remesa"] = array('LIKE', "%$no_remesa%");
    	}
		if(!empty($nombre_aseguradora)){
    		$clause["aseguradora_id"] = $nombre_aseguradora;
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
    		$clause["seg_remesas_entrantes.estado"] = $estado;
    	}
		
		if(!empty($uuid_aseguradora)){
			$aseguradora_id=$this->aseguradoras->where('uuid_aseguradora',hex2bin($uuid_aseguradora))->first()->id;
    		$clause["seg_remesas_entrantes.aseguradora_id"] = $aseguradora_id;
    	}
		
		if(!empty($pagos_remesados)){
			$clause["seg_remesas_entrantes.pagos_remesados"] = $pagos_remesados;
    	}
		
		if(!empty($aseguradora)){
			$clause["seg_aseguradoras.nombre"] = array('LIKE','%'.$aseguradora.'%');
    	}
		
		if(!empty($monto)){
			$clause["seg_remesas_entrantes.monto"] = $monto;
    	}
		
		if(!empty($fecha)){
			$fecha1=date('Y-m-d', strtotime($fecha));
    		$clause["fecha1"] = $fecha1;
    	}
		
		if(!empty($usuario_nombre)){
    		$clause["usuarios.nombre"] = array('LIKE','%'.$usuario_nombre.'%');
    	}
		
		if(!empty($estado)){
    		$clause["seg_remesas_entrantes.estado"] = $estado;
    	}
		
		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		
    	$count = $this->RemesasEntrantesRepository->listar($clause, NULL, NULL, NULL, NULL)->count();
    		
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    		 
    	$rows = $this->RemesasEntrantesRepository->listar($clause, $sidx, $sord, $limit, $start);
    	
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
				
				if($row->estado=='liquidada')
				{
					$clase_estado='background-color: #5cb85c';
					$estado='Liquidada';
				}
				else if($row->estado=='por_liquidar')
				{
					$clase_estado='background-color: #5bc0de';
					$estado='Por liquidar';
				}
				else if($row->estado=='en_proceso')
				{
					$clase_estado='background-color: #F8AD46';
					$estado='En proceso';
				}
				else
				{
					$clase_estado='label-danger';
					$estado='Anulada';
				}
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				$hidden_options = '<a href="'. base_url('remesas_entrantes/editar/'. bin2hex($row->uuid_remesa_entrante)) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
			
				if($row['monto']>=0)
					$estilomonto='totales-success';
				else
					$estilomonto='totales-danger';
                $level = substr_count($row['nombre'],".");
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'no_remesa'=> '<a href="'.base_url('remesas_entrantes/editar/'.bin2hex($row->uuid_remesa_entrante)).'">'.$row['no_remesa']."</a>",
                    'pagos_remesados' => $row['pagos_remesados'],
                    'aseguradora_id' => $row['nom_aseguradora'],
                    'monto' => '<label class="'.$estilomonto.'">'.number_format($row['monto'], 2, '.', ',').'</label>',
					'fecha' => $row['fecha'],
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

    public function crear(){
		
		if (!$this->auth->has_permission('acceso', 'remesas_entrantes/crear')) {
			// No, tiene permiso, redireccionarlo.
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para crear', 'titulo' => 'Remesas entrantes');
			$this->session->set_flashdata('mensaje', $mensaje);
			redirect(base_url('remesas_entrantes/listar'));
		} else {
			$mensaje = [];
		}

        $this->_css();
        $this->_js();

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/remesas_entrantes/plugins.js'
        ));
		
		$this->assets->agregar_var_js(array(
			"vista" => 'crear',
			"fecha_desde" => '' ,
			"fecha_hasta" => '',
			"ramos_id" => '',
			"aseguradora_id" => '',    
			"codigo" => '',
			"borrador"=>'',
			"estado_remesa"=>'',
			"ver"=>'',
			"no_recibo"=>'',
			"monto_recibo"=>'',
			"nombre_recibo"=>''
		));

        $data['aseguradoras'] = Aseguradoras::where(['empresa_id' =>$this->id_empresa])->get();

        $clause = array('empresa_id' => $this->id_empresa);
        $data['menu_crear'] = $this->ramoRepository->listar_cuentas($clause);

        $ramosRoles = RolesUsuario::with(array('ramos'))->where(['usuario_id' => $this->usuario_id, 'empresa_id' => $this->id_empresa])->get();
        $ramosUsuario = RamosUsuarios::where(['id_usuario' => $this->usuario_id])->get();

        $data['rolesArray'] = array();
        $data['usuariosArray'] = array();
        $i = 0;
        foreach ($ramosRoles AS $value) {
            foreach ($value->ramos AS $valuee) {
                $data['rolesArray'][$i] = $valuee->id_ramo;
                $i++;
            }
        }
        $i = 0;
        foreach ($ramosUsuario AS $value) {
            $data['usuariosArray'][$i] = $value['id_ramo'];
            $i++;
        }

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Remesas entrantes: crear',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
				1 => array("nombre" => '<b>Remesas entrantes</b>', "activo" => true, "url" => 'remesas_entrantes/listar'),
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
		
        $menuOpciones["#exportarSolicitudesLnk"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;


        $this->template->agregar_titulo_header('Crer Remesa entrante');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }
	
	function editar($uuid=null){
		if( !$this->auth->has_permission('acceso', 'remesas_entrantes/ver/(:any)') && !$this->auth->has_permission('acceso', 'remesas_entrantes/editar/(:any)') ) { 
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
		}

		$this->assets->agregar_js(array(
			'public/assets/js/modules/remesas_entrantes/plugins.js',
			//'public/assets/js/modules/remesas_entrantes/crear.vue.js',
		));

		$data = array();
		$data['vista'] = "editar";
		//$data['ver'] = $ver;

		$Remesas = $this->RemesasEntrantes->where(['uuid_remesa_entrante' => hex2bin(strtolower($uuid))])->first();
		$codigo = $Remesas->no_remesa;
		
		if($Remesas->estado=='en_proceso')
			$borrador='si';
		else
			$borrador='no';

		$this->_js();
		$this->_css();
		
		$fecha_desde='';
		if($Remesas->fecha_desde!='0000-00-00' && $Remesas->fecha_desde!=null && $Remesas->fecha_desde!='')
			$fecha_desde = date('m/d/Y', strtotime($Remesas->fecha_desde));
		
		$fecha_hasta='';
		if($Remesas->fecha_hasta!='0000-00-00' && $Remesas->fecha_hasta!=null && $Remesas->fecha_hasta!='')
			$fecha_hasta = date('m/d/Y', strtotime($Remesas->fecha_hasta));  

		$no_recibo='';
		$monto_recibo='';
		$nombre_recibo='';
		if($Remesas->estado=='por_liquidar')
		{
			if($Remesas->id_recibo!='')
			{
				$no_recibo=$Remesas->id_recibo;
				
				$datosrecibo=Movimiento_monetario_orm::find($no_recibo);
				$montorecibo=Items_recibos_orm::where('id_recibo',$no_recibo)->sum('credito');
				
				$monto_recibo=$montorecibo;
				$nombre_recibo=$datosrecibo->codigo.' '.$datosrecibo->narracion;
			}
		}
		$this->assets->agregar_var_js(array(
			"vista" => 'editar',
			"fecha_desde" => $fecha_desde ,
			"fecha_hasta" => $fecha_hasta,
			"ramos_id" => $Remesas->ramos_id,
			"aseguradora_id" => $Remesas->aseguradora_id,    
			"codigo" => $codigo,
			"borrador" => $borrador,
			"estado_remesa"=>$Remesas->estado,
			"ver"=>$ver,
			"no_recibo"=>$no_recibo,
			"monto_recibo"=>$monto_recibo,
			"nombre_recibo"=>$nombre_recibo,
		));
		$data['aseguradoras'] = Aseguradoras::where(['empresa_id' =>$this->id_empresa])->get();
		
		$data['recibos'] = Movimiento_monetario_orm::select('mov_recibo_dinero.*')->leftJoin("seg_remesas_entrantes", "seg_remesas_entrantes.id_recibo", "=", "mov_recibo_dinero.id")->where(['mov_recibo_dinero.empresa_id' =>$this->id_empresa])
		->whereNull('seg_remesas_entrantes.id_recibo')
		->orWhere('seg_remesas_entrantes.id',$Remesas->id)->get();

        $clause = array('empresa_id' => $this->id_empresa);
        $data['menu_crear'] = $this->ramoRepository->listar_cuentas($clause);

        $ramosRoles = RolesUsuario::with(array('ramos'))->where(['usuario_id' => $this->usuario_id, 'empresa_id' => $this->id_empresa])->get();
        $ramosUsuario = RamosUsuarios::where(['id_usuario' => $this->usuario_id])->get();

        $data['rolesArray'] = array();
        $data['usuariosArray'] = array();
        $i = 0;
        foreach ($ramosRoles AS $value) {
            foreach ($value->ramos AS $valuee) {
                $data['rolesArray'][$i] = $valuee->id_ramo;
                $i++;
            }
        }
        $i = 0;
        foreach ($ramosUsuario AS $value) {
            $data['usuariosArray'][$i] = $value['id_ramo'];
            $i++;
        }

		$breadcrumb = array(
			"titulo" => '<i class="fa fa-archive"></i> Remesas entrantes: '.$codigo,
			"ruta" => array(
				0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
				1 => array("nombre" => '<b>Remesas entrantes</b>', "activo" => true, "url" => 'remesas_entrantes/listar'),
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

		$menuOpciones["#imprimirRemesaBtn"] = "Imprimir";
		
		if($Remesas->estado=='por_liquidar')
		{
			$menuOpciones["#eliminarRemesaBtn"] = "Eliminar";
		}
		
		$breadcrumb["menu"]["opciones"] = $menuOpciones;

		$this->template->agregar_titulo_header('Ver remesa entrante');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar($breadcrumb);
	}
	
	public function tabla_remesas(){

        $this->assets->agregar_js(array(
            'public/assets/js/modules/remesas_entrantes/crear.vue.js'
        ));

    
        $this->load->view('tabla_remesas');
    }
	
	public function tabla_remesas_procesadas(){

        $this->assets->agregar_js(array(
            'public/assets/js/modules/remesas_entrantes/crear.vue.js'
        ));

    
        $this->load->view('tabla_remesas_procesadas');
    }
	
	public function ajax_get_remesa_entrantes() {

        $clause = array(
    		"fac_facturas.empresa_id" =>  $this->id_empresa
    	);
		
		$id_asegurado = $_POST['id_aseguradora'];
        $fecha_inicial = $_POST['fecha_inicio']; 
        $fecha_final = $_POST['fecha_final'];
        $id_ramos = $_POST['id_ramos'];

		if($id_asegurado!="")
		{
			$clause['pol_polizas.aseguradora_id']=$id_asegurado;
		}
		if($fecha_inicial!="")
		{
			$clause['fecha1']=date('Y-m-d', strtotime($fecha_inicial));
		}
		if($fecha_final!="")
		{
			$clause['fecha2']=date('Y-m-d', strtotime($fecha_final));
		}
		if($id_ramos!="")
		{
			if(!in_array('todos',$id_ramos))
			{
				$clause['ramo_id']=$id_ramos;
			}
				
		}

		if($_POST['codigo_remesa']=='')
		{
			$facturas = $this->FacturaSeguroRepository->getFacturas($clause);
			$remesa_existe='no';
		}
			
		else
		{
			$id_remesa=$this->RemesasEntrantes->where('no_remesa',$_POST['codigo_remesa'])->first();
			$clause1 = array();
			$clause1['remesa_entrante_id']=$id_remesa->id;
			$facturas = $this->FacturaSeguroRepository->getFacturasRemesas($clause,$clause1);
			$remesa_existe='si';
		}
			
        $response = new stdClass();
        $response->inter = array();
		$var=0;
		$monto_total_final=0;
        foreach ($facturas as $key => $value) {
			
			if($remesa_existe=="si")
			{
				$monto_factura=$this->RemesasEntrantesFacturas->where('factura_id',$value->id)
				->where('remesa_entrante_id',$id_remesa->id)->count();
				
				if($monto_factura>0)
				{
					$monto_factura=$this->RemesasEntrantesFacturas->where('factura_id',$value->id)
					->where('remesa_entrante_id',$id_remesa->id)->first();
					$mont_pag_factura=$monto_factura->mont_pag_factura;
				}
				else
				{
					$mont_pag_factura='';
				}
			}
			else
			{
				$mont_pag_factura='';
			}
			
			if($mont_pag_factura!="")
			{
				$monto=$mont_pag_factura;
				$monto1=$mont_pag_factura;
				$mont_pag_factura='si';
			}
			else
			{
				if($value->estado!='cobrado_completo')
				{
					$monto=0;
					$monto1=0;
					$monto1=number_format($monto1,2);
				}
				else
				{
					$monto=$value->total;
					$monto1=0;
					$monto1=number_format($monto1,2);
				}
				$mont_pag_factura='no';
			}
			
			/*if($value->estado=='cobrado_completo')
			{
				$monto_total_final+=$value->total;
			}
			else
			{
				$monto_total_final+=$monto;
			}*/
			
			$monto_total_final+=number_format($monto1,2);
				
			//var_dump($monto);
			if($var==0)
			{
				$ramo_anterior=$value->ramo_id;
				array_push($response->inter, array("saldo"=>"","chequeada"=>"","id" => '',"monto_total_final"=>"","link_factura"=>"","link_poliza"=>"","mont_pag_factura"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => $value->polizas->datosRamos->nombre, 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => '', 'monto' => '' ,'estado' => '', 'estilos' => 'font-weight: bold; background-color:#efefef;' ));
			}
			$var=$var+1;
			
			if($ramo_anterior!=$value->ramo_id)
			{
				$ramo_anterior=$value->ramo_id;
				array_push($response->inter, array("saldo"=>"","chequeada"=>"","id" => '', "monto_total_final"=>"","link_factura"=>"",""=>"link_poliza","mont_pag_factura"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => $value->polizas->datosRamos->nombre, 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => '', 'monto' => '' ,'estado' => '', 'estilos' => 'font-weight: bold; background-color:#efefef;' ));
			}
			
			$url_poliza=base_url('polizas/editar/'.bin2hex($value->polizas->uuid_polizas));
			$url_factura=base_url('facturas_seguros/editar/'.$value->uuid_factura);
			
            array_push($response->inter, array("saldo"=>$value->saldo,"chequeada"=>$value->chequeada,"id" => $value->id, "monto_total_final"=>"","link_factura"=>$url_factura,"link_poliza"=>$url_poliza,"mont_pag_factura"=>$mont_pag_factura,"uuid_poliza"=>bin2hex($value->polizas->uuid_polizas),"uuid_factura"=>bin2hex($value->uuid_factura),"numero_factura" => $value->codigo, "numero_poliza" => $value->polizas->numero, 'ramo_id'=>$value->ramo_id,'nombre_ramo' => $value->polizas->datosRamos->nombre, 'inicio_vigencia' => date($value->fecha_desde), 'fin_vigencia' => date($value->fecha_hasta), 'nombre_cliente' => $value->cliente->nombre, 'fecha_factura' => date($value->fecha_desde), 'monto' => number_format($monto,2) ,'estado' => $value->estado, 'estilos' => 'font-weight: normal' ));
        }
		
		array_push($response->inter, array("chequeada"=>"","id" => '', "monto_total_final"=>number_format($monto_total_final,2),"link_factura"=>"",""=>"link_poliza","mont_pag_factura"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => '', 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => 'Total prima pagada', 'monto' => '' ,'estado' => '', 'estilos' => 'font-weight: bold; background-color:#cccccc;' ));
			
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit;       
    }
	
	public function ajax_get_remesa_entrantes_procesar() {
		
		$response = new stdClass();
        $response->inter = array();
		
		$facturas_id=array();
		$montos=array();
		$id_montos=array();
		$facturas_chequeadas=array();
		if(isset($_POST['facturas_id']))
			$facturas_id=$_POST['facturas_id'];
		if(isset($_POST['montos']))
			$montos=$_POST['montos'];
		if(isset($_POST['facturas_chequeadas']))
			$facturas_chequeadas=$_POST['facturas_chequeadas'];
		if(isset($_POST['id_monto']))
			$id_montos=$_POST['id_monto'];
		
		$aseguradora_id=$_POST['aseguradora_id'];
		
        $clause = array(
    		"fac_facturas.empresa_id" =>  $this->id_empresa
    	);
		
		$arreglototal=array_merge($facturas_id,$facturas_chequeadas);
		
		if(count($arreglototal))
		{
			$clause['fac_facturas.id']=$arreglototal;
		}
		
		if($_POST['codigo_remesa']=='')
		{
			$count = $this->RemesasEntrantes->where('empresa_id',$this->id_empresa)->count();
			$codigo = Util::generar_codigo('REN'.$this->id_empresa, ($count+1) );
			$campo["uuid_remesa_entrante"] = Capsule::raw("ORDER_UUID(uuid())");
			$campo["pagos_remesados"] =count($arreglototal);
			$campo["aseguradora_id"] =$aseguradora_id;
			$campo["no_remesa"] =$codigo;
			$campo["fecha"] =date('Y-m-d');
			$campo["usuario_id"] =$this->usuario_id;
			$campo["estado"] ='por_liquidar';
			$campo["empresa_id"] =$this->id_empresa;
			$campo["created_at"] =date('Y-m-d H:i:s');
			$campo["updated_at"] =date('Y-m-d H:i:s');
			if($_POST['fecha_desde']!="")
				$campo["fecha_desde"] =date('Y-m-d', strtotime($_POST['fecha_desde']));
			if($_POST['fecha_hasta']!="")
				$campo["fecha_hasta"] =date('Y-m-d', strtotime($_POST['fecha_hasta']));
			$campo["ramos_id"] =$arrayRamos = implode(",", $_POST['ramos_id']);
			
			//se crea la remesa entrante
			$insertar_remesa=$this->RemesasEntrantes->create($campo);
			
			$id_remesa=$insertar_remesa->id;
			$id_remesa_entrante=$id_remesa;
			$estado_remesa=$insertar_remesa->estado;
			$codigo_remesa=$insertar_remesa->no_remesa;
			
			$facturas = $this->FacturaSeguroRepository->getFacturasPrtocesadas($clause);
			
			foreach($facturas as $key => $value)
			{
				if(count($montos)>0)
				{
					if(in_array($value->id,$id_montos))
					{
						$key = array_keys($id_montos,$value->id);
						$valor=$montos[$key[0]];
					}
					else
					{
						$valor=0;
					}
					if($value->estado=='cobrado_completo')
					{
						$valor_real=$value->total;
					}
					else
					{
						$valor_real=$valor;
					}
				}
				else
				{
					$valor_real=0;
				}
				
				if($value->estado!='cobrado_completo')
				{
					//Se inicializa el cobro
					$accion = new FormGuardar();
					$cobro=array();
					//Se recolectan los datos para el cobro
					$cobro['uuid_cobro']=Capsule::raw("ORDER_UUID(uuid())");
					$cobro['empezable_id'] = $value->polizas->id;
					$cobro['empezable_type'] = 'polizas';
					$cobro['depositable_type'] = 'banco';
					$cobro['depositable_id'] = 4915;
					$cobro['num_remesa_entrante']=$codigo_remesa;
					$cobro['codigo'] = $accion->getLastCodigo();
					$cobro['estado'] = 'aplicado';
					$cobro['formulario'] = 'seguros';
					$cobro['monto_pagado'] = $valor_real;
					$cobro['empresa_id'] = $this->id_empresa;
					$cobro['cliente_id'] = $value->cliente->id;
					$cobro['fecha_pago'] = date('d/m/Y');
					
					//$facturas_cobros['factura_id']=$value->id;
					$facturas_cobros=array();
					$facturas_cobros[0]['monto_pagado']=$valor_real;
					$facturas_cobros[0]['empresa_id'] = $this->id_empresa;
					$facturas_cobros[0]['cobrable_id'] = $value->id;
					$facturas_cobros[0]['cobrable_type'] = 'factura';
					$facturas_cobros[0]['empezable'] = 'factura';
					$facturas_cobros[0]['id_ramo'] = $value->ramo_id;
					
					$facturas_cobros = collect(FormRequest::array_filter_dos_dimenciones($facturas_cobros));
					
					//metodo de pago para el cobro
					$metodo_cobro=array();
					$metodo_cobro[0]['tipo_pago']='remesa';
					$metodo_cobro[0]['total_pagado']=$valor_real;
					
					//creo el cobro
					$accionguardar=$accion->crear($cobro,$facturas_cobros,$metodo_cobro);
					
					//Generar la comision
					
					$comision['uuid_comision']=Capsule::raw("ORDER_UUID(uuid())");
					$countcomision = $this->ComisionesSeguros->where('id_empresa',$this->id_empresa)->count();
					$codigo = Util::generar_codigo('COM'.$this->id_empresa, ($countcomision+1) );
			
					$comision['no_comision']=$codigo;
					$comision['id_cobro']=$accionguardar->id;
					$comision['fecha']=date('Y-m-d');
					$comision['monto_recibo']=$valor_real;
					$comision['id_factura']=$value->id;
					$comision['id_aseguradora']=$aseguradora_id;
					$comision['id_poliza']=$value->polizas->id;
					$comision['id_cliente']=$value->cliente->id;
					$comision['id_ramo']=$value->ramo_id;
					$comision['comision']=$value->polizas->comision;
					$comision['impuesto']=$value->porcentaje_impuesto;
					$comision['impuesto_pago']=($valor_real*($value->porcentaje_impuesto/100));
					$comision['pago_sobre_prima']=$comision['monto_recibo']-$comision['impuesto_pago'];
					$comision['monto_comision']=($comision['pago_sobre_prima']*($value->polizas->comision/100));
					$comision['sobre_comision']=$value->polizas->porcentaje_sobre_comision;
					$comision['monto_scomision']=($comision['pago_sobre_prima']*($value->polizas->porcentaje_sobre_comision/100));
					//$comision['comision_pendiente']=0;
					$comision['id_remesa']=$id_remesa;
					$comision['lugar_pago']=$value->polizas->primafk->sitio_pago;
					$comision['estado']='por_liquidar';
					$comision['created_at']=date('Y-m-d H:i:s');
					$comision['updated_at']=date('Y-m-d H:i:s');
					$comision['id_empresa']=$this->id_empresa;
					
					if($value->polizas->desc_comision=='si')
					{
						$comision['comision_descontada']=($comision['pago_sobre_prima']*($value->polizas->comision/100));
						$comision['scomision_descontada']=($comision['pago_sobre_prima']*($value->polizas->porcentaje_sobre_comision/100));
					}
					else
					{
						$comision['comision_descontada']=0;
						$comision['scomision_descontada']=0;
					}
					
					$comision['comision_pagada']=($comision['monto_comision']-$comision['comision_descontada'])+($comision['monto_scomision']-$comision['scomision_descontada']);
					
					$comision['comision_pendiente']=($comision['monto_comision']-$comision['comision_descontada'])+($comision['monto_scomision']-$comision['scomision_descontada']);
					
					$comision_creada=$this->ComisionesSeguros->create($comision);
					
					$comisionremesa['id_remesa']=$id_remesa;
					$comisionremesa['id_comision']=$comision_creada->id;
					
					$comision_remesa_creada=$this->COmisionesSegurosRemesas->create($comisionremesa);
					
					//obtener los agentes de participacion de la poliza y crearlos para la comision
					$agentespolizas=$this->agenteParticipacion->where('id_poliza',$value->polizas->id)->get();
					$totalmontoagentes=0;
					foreach($agentespolizas as $agente)
					{
						$datosagentecomision=array();
						$datosagentecomision['agente_id']=$agente->agente_id;
						$datosagentecomision['porcentaje']=$agente->porcentaje_participacion;
						$datosagentecomision['comision_id']=$comision_creada->id;
						$datosagentecomision['created_at']=date('Y-m-d H:i:s');
						$datosagentecomision['updated_at']=date('Y-m-d H:i:s');
						
						if($comision_creada->comision_pagada>0)
							$datosagentecomision['monto']=$comision_creada->comision_pagada*($agente->porcentaje_participacion/100);
						else
							$datosagentecomision['monto']=($comision_creada->comision_descontada+$comision_creada->monto_scomision)*($agente->porcentaje_participacion/100);
						
						$partcomisioncreada=$this->SegComisionesParticipacion->create($datosagentecomision);
						
						$totalmontoagentes+=$partcomisioncreada->monto;
					}
					
					//obtener el porcentaje del agente principal y guardarlo en la comision
					$totalagentespolizasprincipal=$this->agentePrincipal->where('poliza_id',$value->polizas->id)->count();
					
					if($totalagentespolizasprincipal>0)
					{
						$agentespolizasprincipal=$this->agentePrincipal->where('poliza_id',$value->polizas->id)->first();
					
						$datosagentepcomision=array();
						$datosagentepcomision['agente_id']=$agentespolizasprincipal->agente_id;
						$datosagentepcomision['porcentaje']=$agentespolizasprincipal->comision;
						$datosagentepcomision['comision_id']=$comision_creada->id;
						$datosagentepcomision['created_at']=date('Y-m-d H:i:s');
						$datosagentepcomision['updated_at']=date('Y-m-d H:i:s');
						
						if($comision_creada->comision_pagada>0)
							$datosagentepcomision['monto']=$comision_creada->comision_pagada*($agentespolizasprincipal->comision/100);
						else
							$datosagentepcomision['monto']=($comision_creada->comision_descontada+$comision_creada->monto_scomision)*($agentespolizasprincipal->comision/100);
						
						$partcomisioncreadap=$this->SegComisionesParticipacion->create($datosagentepcomision);
						
						$totalmontoagentes+=$partcomisioncreadap->monto;
						
						
						if(number_format($totalmontoagentes,2)>(number_format(($comision_creada->comision_descontada+$comision_creada->monto_scomision + $comision_creada->comision_pagada),2)) || number_format($totalmontoagentes,2)<(number_format(($comision_creada->comision_descontada+$comision_creada->monto_scomision + $comision_creada->comision_pagada),2)))
						{
							$totalfinalcom=number_format($totalmontoagentes,2) - (number_format(($comision_creada->comision_descontada+$comision_creada->monto_scomision + $comision_creada->comision_pagada),2));
							
							if($totalfinalcom<0)
							{
								$final=$partcomisioncreadap->monto - $totalfinalcom;
							}
							else
							{
								$final=$partcomisioncreadap->monto + $totalfinalcom;
							}
							
							$datosactu=array();
							$datosactu['monto']=$final;
							$partcomisionactp=$this->SegComisionesParticipacion->find($partcomisioncreadap->id)->update($datosactu);
						}
					}
				}
				
				if($value->estado=='cobrado_completo')
				{
					$chequeada=1;
				}
				else
				{
					$chequeada=0;
				}
				
				$valores['uuid_remesa_entrante_factura']=Capsule::raw("ORDER_UUID(uuid())");
				$valores['remesa_entrante_id']=$id_remesa_entrante;
				$valores['factura_id']=$value->id;
				$valores['mont_pag_factura']=(float)$valor_real;
				$valores['chequeada']=$chequeada;
				$valores["created_at"] =date('Y-m-d H:i:s');
				$valores["update_at"] =date('Y-m-d H:i:s');
				
				$comision_remesa_guardada_total=$this->RemesasEntrantesFacturas->where(['remesa_entrante_id' => $id_remesa_entrante])
				->where('factura_id',$value->id)->count();
				
				if($comision_remesa_guardada_total>0)
				{
					$insertar_remesa_factura=$comision_remesa_guardada->update($valores);
				}
				else
				{
					$insertar_remesa_factura=$this->RemesasEntrantesFacturas->create($valores);
				}
			}
		}
		else
		{
			$id_remesa=$this->RemesasEntrantes->where('no_remesa',$_POST['codigo_remesa'])->first();
			$id_remesa_entrante=$id_remesa->id;
			$campoupdate["updated_at"] =date('Y-m-d H:i:s');
			if($_POST['fecha_desde']!="")
				$campoupdate["fecha_desde"] =date('Y-m-d', strtotime($_POST['fecha_desde']));
			if($_POST['fecha_hasta']!="")
				$campoupdate["fecha_hasta"] =date('Y-m-d', strtotime($_POST['fecha_hasta']));
			$actalizar_remesa=$this->RemesasEntrantes->where(['id' => $id_remesa_entrante])->update($campoupdate);
			$estado_remesa=$id_remesa->estado;
		}
		
		if($estado_remesa=='por_liquidar')
		{
			$consultaComisionestotal=$this->COmisionesSegurosRepository->consultarComisionesProcesar($id_remesa_entrante,$aseguradora_id,$_POST['ramos_id'],$_POST['fecha_desde'],$_POST['fecha_hasta'],$this->id_empresa)->count();
			
			$consultaComisiones=$this->COmisionesSegurosRepository->consultarComisionesProcesar($id_remesa_entrante,$aseguradora_id,$_POST['ramos_id'],$_POST['fecha_desde'],$_POST['fecha_hasta'],$this->id_empresa)->get();
			
		}
		else
		{
			$consultaComisionestotal=$this->COmisionesSegurosRepository->consultarComisionesLiquidada($id_remesa_entrante,$aseguradora_id,$_POST['ramos_id'],$_POST['fecha_desde'],$_POST['fecha_hasta'],$this->id_empresa)->count();
			
			$consultaComisiones=$this->COmisionesSegurosRepository->consultarComisionesLiquidada($id_remesa_entrante,$aseguradora_id,$_POST['ramos_id'],$_POST['fecha_desde'],$_POST['fecha_hasta'],$this->id_empresa)->get();
		}
		
		$var=0;
		$total= $consultaComisionestotal;
		$comision_pagada_total=0;
		$total_com_esperada=0;
		$total_com_descontada=0;
		$total_sob_esperada=0;
		$total_sob_descontada=0;
		$valor_real=0;
		$comision_descontado=0;
		$sobcomision_descontada=0;
		$comision_esperada=0;
		$sobcomision_esperada=0;
		$comision_pagada=0;
		$prima_neta_final=0;
		$pago_final=0;
		$com_esp_final=0;
		$com_des_final=0;
		$scom_esp_final=0;
		$scom_des_final=0;
		$com_paga_final=0;
		$sumamonto=0;
		$ramo='';
		$comision_esperada_comparar_total=0;
		
        foreach ($consultaComisiones as $key => $value) {			
			//$prima_neta_final+=$value->facturasComisiones->subtotal;
			$prima_neta_final+=$value->pago_sobre_prima;
			
			$url_poliza=base_url('polizas/editar/'.bin2hex($value->polizas->uuid_polizas));
			$url_factura=base_url('comisiones_seguros/ver/'.bin2hex($value->facturasComisiones->uuid_comision));
			
			$valor_real=$value->monto_recibo;
			
			$sumamonto+=$valor_real;
			
			$pago_final+=$valor_real;
			$comision_esperada=$value->monto_comision;
			
			//$com_esp_final+=$comision_esperada;
			
			$sobcomision_esperada=$value->monto_scomision;
			
			$scom_esp_final+=$sobcomision_esperada;
			
			$comision_descontado=$value->comision_descontada;
			$sobcomision_descontada=$value->scomision_descontada;
			
			$com_des_final+=$comision_descontado;
			$scom_des_final+=$sobcomision_descontada;
			
			$comision_esperada_comparar_total+=$value->monto_comision-$value->comision_descontada+$value->monto_scomision-$value->scomision_descontada;
			
			$com_esp_final+=$value->monto_comision-$value->comision_descontada+$value->monto_scomision-$value->scomision_descontada;
			
			$url_poliza=base_url('polizas/editar/'.bin2hex($value->polizas->uuid_polizas));
			$url_factura=base_url('comisiones_seguros/ver/'.bin2hex($value->uuid_comision));
            
			if($var==0)
			{
				$total_com_esperada+=$comision_esperada;
				$total_com_descontada+=$comision_descontado;
				$total_sob_esperada+=$sobcomision_esperada;
				$total_sob_descontada+=$sobcomision_descontada;
				
				$comision_pagada=$value->comision_pagada;
					
				$comision_pagada_total+=$comision_pagada;
				$ramo_anterior=$value->id_ramo;
				
				//$com_paga_final+=$comision_pagada;
				
				$comision_esperada_comparar=$value->monto_comision-$value->comision_descontada+$value->monto_scomision-$value->scomision_descontada;
				
				if($comision_esperada_comparar!=$comision_pagada)
				{
					$estilo='font-weight: normal; color:#ff0000;';
				}
				else
					$estilo='font-weight: normal';
				
				array_push($response->inter, array("remesa_creada"=>"","aseguradora_id"=>"","id" => $value->id, "final"=>0,"link_poliza"=>$url_poliza,"link_factura"=>$url_factura,"prima_neta_final"=>"","pago_final"=>"","com_esp_final"=>"","com_des_final"=>"","scom_esp_final"=>"","scom_des_final"=>"","com_paga_final"=>"","total_sob_descontada"=>number_format($total_sob_descontada, 2),"total_sob_esperada"=>number_format($total_sob_esperada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_esperada"=>number_format($comision_esperada_comparar_total,4),"comision_pagada_total"=>number_format($comision_pagada_total, 2),"comision_pagada"=>number_format($comision_pagada, 2),"sobcomision_descontada"=>number_format($sobcomision_descontada, 2),"sobcomision_esperada"=>number_format($sobcomision_esperada, 2),"porcentaje_sobre_comision"=>$value->sobre_comision,"comision_descontado"=>number_format($comision_descontado, 2),"comision_esperada"=>number_format($comision_esperada,4),"porcentaje_comision"=>$value->comision,"prima_neta"=>number_format($value->pago_sobre_prima,2),"fecha_operacion"=>date('Y-m-d'),"uuid_poliza"=>bin2hex($value->polizas->uuid_polizas),"uuid_factura"=>bin2hex($value->uuid_comision),"numero_factura" => $value->no_comision, "numero_poliza" => $value->polizas->numero , 'ramo_id'=>$value->id_ramo,'nombre_ramo' => $value->datosRamos->nombre, 'inicio_vigencia' => date($value->facturasComisiones->fecha_desde), 'fin_vigencia' => date($value->facturasComisiones->fecha_hasta), 'nombre_cliente' => $value->cliente->nombre, 'fecha_factura' => date($value->facturasComisiones->fecha_desde), 'monto' =>number_format($valor_real, 2),'estado' => $value->facturasComisiones->estado, 'estilos' => $estilo ));
			}
			$nombre_ramo_anterior=$this->Ramos->find($ramo_anterior)->nombre;
			if($ramo_anterior!=$value->id_ramo)
			{
				if($comision_esperada_comparar_total!=$comision_pagada_total)
				{
					$estilo='font-weight: bold; background-color:#efefef; color:#ff0000;';
				}
				else
					$estilo='font-weight: bold; background-color:#efefef;';
				
				array_push($response->inter, array("remesa_creada"=>"","aseguradora_id"=>"","link_poliza"=>"","link_factura"=>"","id" => '', "final"=>0,"prima_neta_final"=>"","pago_final"=>"","com_esp_final"=>"","com_des_final"=>"","scom_esp_final"=>"","scom_des_final"=>"","com_paga_final"=>"","total_sob_descontada"=>number_format($total_sob_descontada, 2),"total_sob_esperada"=>number_format($total_sob_esperada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_esperada"=>number_format($comision_esperada_comparar_total,4),"comision_pagada_total"=>number_format($comision_pagada_total, 2),"comision_pagada"=>number_format($comision_pagada, 2),"sobcomision_descontada"=>"","sobcomision_esperada"=>"","porcentaje_sobre_comision"=>"","comision_descontado"=>"","comision_esperada"=>"","porcentaje_comision"=>"","prima_neta"=>"","fecha_operacion"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => $nombre_ramo_anterior, 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => '', 'monto' => '' ,'estado' => '', 'estilos' => $estilo ));
				
				$total_com_esperada=0;
				$total_com_descontada=0;
				$total_sob_esperada=0;
				$total_sob_descontada=0;
				$comision_pagada_total=0;
				$ramo_anterior=$value->id_ramo;
				$comision_esperada_comparar_total=0;
			}
			
			if($var!=0)
			{
				$total_com_esperada+=$comision_esperada;
				$total_com_descontada+=$comision_descontado;
				$total_sob_esperada+=$sobcomision_esperada;
				$total_sob_descontada+=$sobcomision_descontada;
				
				$comision_pagada=$value->comision_pagada;
				
				$comision_pagada_total+=$comision_pagada;
				
				$comision_esperada_comparar=$value->monto_comision-$value->comision_descontada+$value->monto_scomision-$value->scomision_descontada;
				
				if($comision_esperada_comparar!=$comision_pagada)
				{
					$estilo='font-weight: normal; color:#ff0000;';
				}
				else
					$estilo='font-weight: normal;';

				array_push($response->inter, array("remesa_creada"=>"","aseguradora_id"=>"","id" => $value->id, "link_poliza"=>$url_poliza,"link_factura"=>$url_factura,"final"=>0,"prima_neta_final"=>"","pago_final"=>"","com_esp_final"=>"","com_des_final"=>"","scom_esp_final"=>"","scom_des_final"=>"","com_paga_final"=>"","total_sob_descontada"=>number_format($total_sob_descontada, 2),"total_sob_esperada"=>number_format($total_sob_esperada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_esperada"=>number_format($comision_esperada_comparar_total,4),"comision_pagada_total"=>number_format($comision_pagada_total, 2),"comision_pagada"=>number_format($comision_pagada, 2),"sobcomision_descontada"=>number_format($sobcomision_descontada, 2),"sobcomision_esperada"=>number_format($sobcomision_esperada, 2),"porcentaje_sobre_comision"=>$value->sobre_comision,"comision_descontado"=>number_format($comision_descontado, 2),"comision_esperada"=>number_format($comision_esperada,4),"porcentaje_comision"=>$value->comision,"prima_neta"=>number_format($value->pago_sobre_prima,2),"fecha_operacion"=>date('Y-m-d'),"uuid_poliza"=>bin2hex($value->polizas->uuid_polizas),"uuid_factura"=>bin2hex($value->uuid_comision),"numero_factura" => $value->no_comision, "numero_poliza" => $value->polizas->numero , 'ramo_id'=>$value->id_ramo,'nombre_ramo' => $value->datosRamos->nombre, 'inicio_vigencia' => date($value->fecha_pago), 'fin_vigencia' => date($value->fecha_pago), 'nombre_cliente' => $value->cliente->nombre, 'fecha_factura' => date($value->facturasComisiones->fecha_desde), 'monto' => number_format($valor_real, 2) ,'estado' => $value->facturasComisiones->estado, 'estilos' => $estilo ));
			}
			
			$com_paga_final+=$comision_pagada;
			
			$var=$var+1;
			
			$ramo=$value->id_ramo;
		}
		
		$nombre_ramo_anterior='';
		if($ramo!="")
			$nombre_ramo_anterior=$this->Ramos->find($ramo)->nombre;
		
		if($comision_esperada_comparar_total!=$comision_pagada_total)
			$estilo='font-weight: bold; background-color:#efefef; color:#ff0000;';
		else
			$estilo='font-weight: bold; background-color:#efefef;';
		
		array_push($response->inter, array("remesa_creada"=>"","aseguradora_id"=>"","link_poliza"=>"","link_factura"=>"","id" => '', "final"=>0,"prima_neta_final"=>"","pago_final"=>"","com_esp_final"=>"","com_des_final"=>"","scom_esp_final"=>"","scom_des_final"=>"","com_paga_final"=>"", "total_sob_descontada"=>number_format($total_sob_descontada, 2),"total_sob_esperada"=>number_format($total_sob_esperada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_esperada"=>number_format($comision_esperada_comparar_total,4),"comision_pagada_total"=>number_format($comision_pagada_total, 2),"comision_pagada"=>number_format($comision_pagada_total, 2),"sobcomision_descontada"=>"","sobcomision_esperada"=>"","porcentaje_sobre_comision"=>"","comision_descontado"=>"","comision_esperada"=>"","porcentaje_comision"=>"","prima_neta"=>"","fecha_operacion"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => $nombre_ramo_anterior, 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => '', 'monto' => '' ,'estado' => '', 'estilos' => $estilo));
		
		if($com_paga_final!=$com_esp_final)
			$estilo='font-weight: bold; background-color:#cccccc; color:#ff0000;';
		else
			$estilo='font-weight: bold; background-color:#cccccc;';
		
		$campoupdate["monto"] =$sumamonto;
		$campoupdate["updated_at"] =date('Y-m-d H:i:s');
		$actalizar_remesa=$this->RemesasEntrantes->where(['id' => $id_remesa_entrante])->update($campoupdate);
		
		$remesa=$this->RemesasEntrantes->find($id_remesa_entrante);
		
		$uuid_remesa=bin2hex($remesa->uuid_remesa_entrante);
		
		$response->uuid=$uuid_remesa;
		
		array_push($response->inter, array("remesa_creada"=>$uuid_remesa,"aseguradora_id"=>$aseguradora_id,"link_poliza"=>"","link_factura"=>"","id" => '', "final"=>1,"prima_neta_final"=>number_format($prima_neta_final, 2),"pago_final"=>number_format($pago_final, 2),"com_esp_final"=>number_format($com_esp_final, 4),"com_des_final"=>number_format($com_des_final, 2),"scom_esp_final"=>number_format($scom_esp_final, 2),"scom_des_final"=>number_format($scom_des_final, 2),"com_paga_final"=>number_format($com_paga_final, 2),"total_sob_descontada"=>"","total_sob_esperada"=>"","total_com_descontada"=>"","total_com_descontada"=>"","total_com_esperada"=>"","comision_pagada_total"=>"","comision_pagada"=>"","sobcomision_descontada"=>"","sobcomision_esperada"=>"","porcentaje_sobre_comision"=>"","comision_descontado"=>"","comision_esperada"=>"","porcentaje_comision"=>"","prima_neta"=>"","fecha_operacion"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => $nombre_ramo_anterior, 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => '', 'monto' => '' ,'estado' => '', 'estilos' => $estilo ));
				
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        
		exit;       
    }

	public function ajax_get_remesa_entrantes_borrador() {
		
		$facturas_id=array();
		$montos=array();
		$id_montos=array();
		$facturas_chequeadas=array();
		$aseguradora_id=$_POST['aseguradora_id'];
		if(isset($_POST['facturas_id']))
			$facturas_id=$_POST['facturas_id'];
		if(isset($_POST['montos']))
			$montos=$_POST['montos'];
		if(isset($_POST['facturas_chequeadas']))
			$facturas_chequeadas=$_POST['facturas_chequeadas'];
		if(isset($_POST['id_monto']))
			$id_montos=$_POST['id_monto'];
		
        $clause = array(
    		"fac_facturas.empresa_id" =>  $this->id_empresa
    	);
		
		
		$arreglototal=array_merge($facturas_id,$facturas_chequeadas);

		if(count($arreglototal))
		{
			$clause['fac_facturas.id']=$arreglototal;
		}
		
		$facturas = $this->FacturaSeguroRepository->getFacturasPrtocesadas($clause);
		
		if($_POST['codigo_remesa']=='')
		{
			$count = $this->RemesasEntrantes->where('empresa_id',$this->id_empresa)->count();
			$codigo = Util::generar_codigo('REN'.$this->id_empresa, ($count+1) );
			$campo["uuid_remesa_entrante"] = Capsule::raw("ORDER_UUID(uuid())");
			$campo["pagos_remesados"] =count($facturas);
			$campo["aseguradora_id"] =$aseguradora_id;
			$campo["no_remesa"] =$codigo;
			$campo["fecha"] =date('Y-m-d');
			$campo["usuario_id"] =$this->usuario_id;
			$campo["estado"] ='en_proceso';
			$campo["empresa_id"] =$this->id_empresa;
			$campo["created_at"] =date('Y-m-d H:i:s');
			$campo["updated_at"] =date('Y-m-d H:i:s');
			if($_POST['fecha_desde']!="")
				$campo["fecha_desde"] =date('Y-m-d', strtotime($_POST['fecha_desde']));
			if($_POST['fecha_hasta']!="")
				$campo["fecha_hasta"] =date('Y-m-d', strtotime($_POST['fecha_hasta']));
			$campo["ramos_id"] =$arrayRamos = implode(",", $_POST['ramos_id']);
			
			$insertar_remesa=$this->RemesasEntrantes->create($campo);
			$id_remesa=$insertar_remesa->id;
		}
		else
		{
			$id_remesa=$this->RemesasEntrantes->where('no_remesa',$_POST['codigo_remesa'])->first();
			$campo["pagos_remesados"] =count($facturas);
			$campo["fecha"] =date('Y-m-d');
			$campo["usuario_id"] =$this->usuario_id;
			$campo["estado"] ='en_proceso';
			$campo["empresa_id"] =$this->id_empresa;
			$campo["updated_at"] =date('Y-m-d H:i:s');
			if($_POST['fecha_desde']!="")
				$campo["fecha_desde"] =date('Y-m-d', strtotime($_POST['fecha_desde']));
			if($_POST['fecha_hasta']!="")
				$campo["fecha_hasta"] =date('Y-m-d', strtotime($_POST['fecha_hasta']));
			$campo["ramos_id"] =$arrayRamos = implode(",", $_POST['ramos_id']);
			
			$insertar_remesa=$this->RemesasEntrantes->update($campo);
			$id_remesa=$id_remesa->id;
			
			$eliminar_facturas_remesas=$this->RemesasEntrantesFacturas->where('remesa_entrante_id',$id_remesa)->delete();

		}
		
		$sumamonto=0;
        foreach ($facturas as $key => $value) {
			
			if(count($montos)>0)
			{
				if(in_array($value->id,$id_montos))
				{
					$key = array_keys($id_montos,$value->id);
					$valor=$montos[$key[0]];
				}
				else
				{
					$valor=0;
				}
				if($value->estado=='cobrado_completo')
				{
					$valor_real=$value->subtotal;
					$chequeada=1;
				}
				else
				{
					$valor_real=$valor;
					$chequeada=0;
				}
			}
			$sumamonto+=$valor_real;
			
			$valores['uuid_remesa_entrante_factura']=Capsule::raw("ORDER_UUID(uuid())");
			$valores['remesa_entrante_id']=$id_remesa;
			$valores['factura_id']=$value->id;
			$valores['mont_pag_factura']=(float)$valor_real;
			$valores['chequeada']=$chequeada;
			$valores["created_at"] =date('Y-m-d H:i:s');
			$valores["update_at"] =date('Y-m-d H:i:s');
			
			$insertar_remesa_factura=$this->RemesasEntrantesFacturas->create($valores);
		}
		$campoupdate["monto"] =0.00;
		$campoupdate["updated_at"] =date('Y-m-d H:i:s');
		$actalizar_remesa=$this->RemesasEntrantes->where(['id' => $id_remesa])->update($campoupdate);
		
		$response = new stdClass();
        $response->inter = array();
		
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit;       
    }
	
	public function guardar() {
		
		$aseguradora_id=$_POST['aseguradora_id'];
		$facturas=$_POST['facturas_id'];
		$montos=$_POST['monto_final'];
		$com_pagada=$_POST['com_pagada'];
		$boton=$_POST['guardar'];
		$no_recibo=$_POST['no_recibo_guardar'];
		
		if($_POST['remesa_creada']!='')
		{
			$codigo_remesa=$this->RemesasEntrantesRepository->findByUuid($_POST['remesa_creada'])->no_remesa;
		}
		else
		{
			$codigo_remesa=$_POST['codigo_remesa_procesado'];
		}
		
		$remesa_entrante = $this->RemesasEntrantes->where('no_remesa',$codigo_remesa)->first();

		$campo["pagos_remesados"] =count($facturas);
		$campo["aseguradora_id"] =$aseguradora_id;
		$campo["fecha"] =date('Y-m-d');
		$campo["usuario_id"] =$this->usuario_id;
		$campo["estado"] ='por_liquidar';
		$campo["id_recibo"] =$no_recibo;
		$campo["empresa_id"] =$this->id_empresa;
		$campo["updated_at"] =date('Y-m-d H:i:s');
		
		$id_remesa=$remesa_entrante->id;
		$insertar_remesa=$this->RemesasEntrantes->where(['id' => $id_remesa])->update($campo);
		//$eliminar_facturas_remesas=$this->RemesasEntrantesFacturas->where('remesa_entrante_id',$id_remesa)->delete();
		
		$sumamonto=0;
		$total_pago=0;
		foreach ($facturas as $key => $value) {
		
			$sumamonto+=$montos[$key];
			
			$comision=$this->ComisionesSeguros->find($value);
			$comision_pagada=$comision->monto_comision-$comision->comision_descontada+$comision->monto_scomision-$comision->monto_scomision;
			
			$comisionact['comision_pagada']=$com_pagada[$key];
			$comisionact['comision_pendiente']=$com_pagada[$key]-$comision_pagada;
			$comisionact['updated_at'] =date('Y-m-d H:i:s');
			if(isset($_POST['liquidar']))
			{
				$valorcero=0;
				if(number_format($comisionact['comision_pendiente'],2)==number_format($valorcero,2))
					$comisionact['estado']='liquidada';
				else
					$comisionact['estado']='con_diferencia';
			}
			
			$total_pago+=$comisionact['comision_pagada'];
			$actualizar=$this->ComisionesSeguros->find($value)->update($comisionact);
		}
		if(isset($_POST['liquidar']))
		{
			$campoupdate["estado"] ='liquidada';
			$campoupdate["fecha_liquidada"] =date('y-m-d H:i:s');
		}
		
		$campoupdate["monto"] =$total_pago;
		$campoupdate["updated_at"] =date('Y-m-d H:i:s');
		$actalizar_remesa=$this->RemesasEntrantes->where(['id' => $id_remesa])->update($campoupdate);
		
		if(!is_null($insertar_remesa)){   
		$mensaje = array('tipo'=>"success", 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente' ,'titulo'=>'Remesa entrante '. $codigo);	

		}
		else{
			$mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡Error!</b> Su solicitud no fue procesada' ,'titulo'=>'Remesa entrante '.$codigo);	
		}
		
		$this->session->set_flashdata('mensaje', $mensaje);
		redirect(base_url('remesas_entrantes/listar')); 
     
    }
	
	public function imprimirRemesasProcesadas($codigo_remesa = null, $id_aseguradora = null , $fecha_inicial = null, $fecha_final = null){
		
        $clause = array(
    		"fac_facturas.empresa_id" =>  $this->id_empresa
    	);

		if($id_aseguradora!="")
		{
			$clause['pol_polizas.aseguradora_id']=$id_aseguradora;
		}
		if($fecha_inicial!="")
		{
			$clause['fecha1']=date('Y-m-d', strtotime($fecha_inicial));
		}
		if($fecha_final!="")
		{
			$clause['fecha2']=date('Y-m-d', strtotime($fecha_final));
		}
		
		
		$id_remesa=$this->RemesasEntrantes->where('no_remesa',$codigo_remesa)->first();
		$id_ramos = explode(",", $id_remesa->ramos_id);
		if($id_ramos!="")
		{
			if(!in_array('todos',$id_ramos))
			{
				$clause['ramo_id']=$id_ramos;
			}
		}
		
		if($id_remesa->estado=='en_proceso')
		{
			$clause1 = array();
			$clause1['remesa_entrante_id']=$id_remesa->id;
			$facturas = $this->FacturaSeguroRepository->getFacturasRemesas($clause,$clause1);
			$remesa_existe='si';
			$datosRemesa= array();
			$var=0;
			$monto_total_final=0;
			foreach ($facturas as $key => $value) {
				
				if($remesa_existe=="si")
				{
					$monto_factura=$this->RemesasEntrantesFacturas->where('factura_id',$value->id)
					->where('remesa_entrante_id',$id_remesa->id)->count();
					
					if($monto_factura>0)
					{
						$monto_factura=$this->RemesasEntrantesFacturas->where('factura_id',$value->id)
						->where('remesa_entrante_id',$id_remesa->id)->first();
						$mont_pag_factura=$monto_factura->mont_pag_factura;
					}
					else
					{
						$mont_pag_factura='';
					}
				}
				else
				{
					$mont_pag_factura='';
				}
				
				if($mont_pag_factura!="")
				{
					$monto=$mont_pag_factura;
					$mont_pag_factura='si';
				}
				else
				{
					$monto=$value->total;
					$mont_pag_factura='no';
				}
				
				if($value->estado=='cobrado_completo')
				{
					$monto_total_final+=$value->total;
				}
				else
				{
					$monto_total_final+=$monto;
				}
					
				//var_dump($monto);
				if($var==0)
				{
					$ramo_anterior=$value->ramo_id;
					array_push($datosRemesa, array("saldo"=>"","chequeada"=>"","id" => '',"monto_total_final"=>"","link_factura"=>"","link_poliza"=>"","mont_pag_factura"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => $value->polizas->datosRamos->nombre, 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => '', 'monto' => '' ,'estado' => '', 'estilos' => 'font-weight: bold; background-color:#efefef;' ));
				}
				$var=$var+1;
				
				if($ramo_anterior!=$value->ramo_id)
				{
					$ramo_anterior=$value->ramo_id;
					array_push($datosRemesa, array("saldo"=>"","chequeada"=>"","id" => '', "monto_total_final"=>"","link_factura"=>"",""=>"link_poliza","mont_pag_factura"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => $value->polizas->datosRamos->nombre, 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => '', 'monto' => '' ,'estado' => '', 'estilos' => 'font-weight: bold; background-color:#efefef;' ));
				}
				
				$url_poliza=base_url('polizas/editar/'.bin2hex($value->polizas->uuid_polizas));
				$url_factura=base_url('facturas_seguros/editar/'.$value->uuid_factura);
				
				array_push($datosRemesa, array("saldo"=>$value->saldo,"chequeada"=>$value->chequeada,"id" => $value->id, "monto_total_final"=>"","link_factura"=>$url_factura,"link_poliza"=>$url_poliza,"mont_pag_factura"=>$mont_pag_factura,"uuid_poliza"=>bin2hex($value->polizas->uuid_polizas),"uuid_factura"=>bin2hex($value->uuid_factura),"numero_factura" => $value->codigo, "numero_poliza" => $value->polizas->numero, 'ramo_id'=>$value->ramo_id,'nombre_ramo' => $value->polizas->datosRamos->nombre, 'inicio_vigencia' => date($value->fecha_desde), 'fin_vigencia' => date($value->fecha_hasta), 'nombre_cliente' => $value->cliente->nombre, 'fecha_factura' => date($value->fecha_desde), 'monto' => $monto ,'estado' => $value->estado, 'estilos' => 'font-weight: normal' ));
			}
			
			array_push($datosRemesa, array("chequeada"=>"","id" => '', "monto_total_final"=>$monto_total_final,"link_factura"=>"",""=>"link_poliza","mont_pag_factura"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => '', 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => 'Total prima pagada', 'monto' => '' ,'estado' => '', 'estilos' => 'font-weight: bold; background-color:#cccccc;' ));
			
			$formulario = "formularioEnProceso";
		}
		else
		{
			$id_ramos = explode(",", $id_remesa->ramos_id);
		
			if($id_remesa->estado=='por_liquidar')
			{
				$consultaComisionestotal=$this->COmisionesSegurosRepository->consultarComisionesProcesar($id_remesa->id,$id_aseguradora,$id_ramos,$fecha_inicial,$fecha_final,$this->id_empresa)->count();
				
				$consultaComisiones=$this->COmisionesSegurosRepository->consultarComisionesProcesar($id_remesa->id,$id_aseguradora,$id_ramos,$fecha_final,$fecha_inicial,$this->id_empresa)->get();
				
			}
			else
			{
				$consultaComisionestotal=$this->COmisionesSegurosRepository->consultarComisionesLiquidada($id_remesa->id,$id_aseguradora,$id_ramos,$fecha_inicial,$fecha_final,$this->id_empresa)->count();
				
				$consultaComisiones=$this->COmisionesSegurosRepository->consultarComisionesLiquidada($id_remesa->id,$id_aseguradora,$id_ramos,$fecha_inicial,$fecha_final,$this->id_empresa)->get();
			}
			$clause=array();
			
			$datosRemesa = array();
			$var=0;
			$total= $consultaComisionestotal;
			$comision_pagada_total=0;
			$total_com_esperada=0;
			$total_com_descontada=0;
			$total_sob_esperada=0;
			$total_sob_descontada=0;
			$valor_real=0;
			$comision_descontado=0;
			$sobcomision_descontada=0;
			$comision_esperada=0;
			$sobcomision_esperada=0;
			$comision_pagada=0;
			$prima_neta_final=0;
			$pago_final=0;
			$com_esp_final=0;
			$com_des_final=0;
			$scom_esp_final=0;
			$scom_des_final=0;
			$com_paga_final=0;
			$sumamonto=0;
			$ramo='';
			$comision_esperada_comparar_total=0;
			
			foreach ($consultaComisiones as $key => $value) {			
				//$prima_neta_final+=$value->facturasComisiones->subtotal;
				$prima_neta_final+=$value->pago_sobre_prima;
				
				$url_poliza=base_url('polizas/editar/'.bin2hex($value->polizas->uuid_polizas));
				$url_factura=base_url('comisiones_seguros/ver/'.bin2hex($value->facturasComisiones->uuid_comision));
				
				$valor_real=$value->monto_recibo;
				
				$sumamonto+=$valor_real;
				
				$pago_final+=$valor_real;
				$comision_esperada=$value->monto_comision;
				
				//$com_esp_final+=$comision_esperada;
				
				$sobcomision_esperada=$value->monto_scomision;
				
				$scom_esp_final+=$sobcomision_esperada;
				
				$comision_descontado=$value->comision_descontada;
				$sobcomision_descontada=$value->scomision_descontada;
				
				$com_des_final+=$comision_descontado;
				$scom_des_final+=$sobcomision_descontada;
				
				$comision_esperada_comparar_total+=$value->monto_comision-$value->comision_descontada+$value->monto_scomision-$value->scomision_descontada;
				
				$com_esp_final+=$value->monto_comision-$value->comision_descontada+$value->monto_scomision-$value->scomision_descontada;
				
				$url_poliza=base_url('polizas/editar/'.bin2hex($value->polizas->uuid_polizas));
				$url_factura=base_url('comisiones_seguros/ver/'.bin2hex($value->uuid_comision));
				
				if($var==0)
				{
					$total_com_esperada+=$comision_esperada;
					$total_com_descontada+=$comision_descontado;
					$total_sob_esperada+=$sobcomision_esperada;
					$total_sob_descontada+=$sobcomision_descontada;
					
					$comision_pagada=$value->comision_pagada;
						
					$comision_pagada_total+=$comision_pagada;
					$ramo_anterior=$value->id_ramo;
					
					//$com_paga_final+=$comision_pagada;
					
					$comision_esperada_comparar=$value->monto_comision-$value->comision_descontada+$value->monto_scomision-$value->scomision_descontada;
					
					if($comision_esperada_comparar!=$comision_pagada)
					{
						$estilo='font-weight: normal; color:#ff0000;';
					}
					else
						$estilo='font-weight: normal';
					
					array_push($datosRemesa, array("remesa_creada"=>"","aseguradora_id"=>"","id" => $value->id, "final"=>0,"link_poliza"=>$url_poliza,"link_factura"=>$url_factura,"prima_neta_final"=>"","pago_final"=>"","com_esp_final"=>"","com_des_final"=>"","scom_esp_final"=>"","scom_des_final"=>"","com_paga_final"=>"","total_sob_descontada"=>number_format($total_sob_descontada, 2),"total_sob_esperada"=>number_format($total_sob_esperada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_esperada"=>number_format($comision_esperada_comparar_total,4),"comision_pagada_total"=>number_format($comision_pagada_total, 2),"comision_pagada"=>number_format($comision_pagada, 2),"sobcomision_descontada"=>number_format($sobcomision_descontada, 2),"sobcomision_esperada"=>number_format($sobcomision_esperada, 2),"porcentaje_sobre_comision"=>$value->sobre_comision,"comision_descontado"=>number_format($comision_descontado, 2),"comision_esperada"=>number_format($comision_esperada,4),"porcentaje_comision"=>$value->comision,"prima_neta"=>number_format($value->pago_sobre_prima,2),"fecha_operacion"=>date('Y-m-d'),"uuid_poliza"=>bin2hex($value->polizas->uuid_polizas),"uuid_factura"=>bin2hex($value->uuid_comision),"numero_factura" => $value->no_comision, "numero_poliza" => $value->polizas->numero , 'ramo_id'=>$value->id_ramo,'nombre_ramo' => $value->datosRamos->nombre, 'inicio_vigencia' => date($value->facturasComisiones->fecha_desde), 'fin_vigencia' => date($value->facturasComisiones->fecha_hasta), 'nombre_cliente' => $value->cliente->nombre, 'fecha_factura' => date($value->facturasComisiones->fecha_desde), 'monto' =>number_format($valor_real, 2),'estado' => $value->facturasComisiones->estado, 'estilos' => $estilo ));
				}
				$nombre_ramo_anterior=$this->Ramos->find($ramo_anterior)->nombre;
				if($ramo_anterior!=$value->id_ramo)
				{
					if($comision_esperada_comparar_total!=$comision_pagada_total)
					{
						$estilo='font-weight: bold; background-color:#efefef; color:#ff0000;';
					}
					else
						$estilo='font-weight: bold; background-color:#efefef;';
					
					array_push($datosRemesa, array("remesa_creada"=>"","aseguradora_id"=>"","link_poliza"=>"","link_factura"=>"","id" => '', "final"=>0,"prima_neta_final"=>"","pago_final"=>"","com_esp_final"=>"","com_des_final"=>"","scom_esp_final"=>"","scom_des_final"=>"","com_paga_final"=>"","total_sob_descontada"=>number_format($total_sob_descontada, 2),"total_sob_esperada"=>number_format($total_sob_esperada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_esperada"=>number_format($comision_esperada_comparar_total,4),"comision_pagada_total"=>number_format($comision_pagada_total, 2),"comision_pagada"=>number_format($comision_pagada, 2),"sobcomision_descontada"=>"","sobcomision_esperada"=>"","porcentaje_sobre_comision"=>"","comision_descontado"=>"","comision_esperada"=>"","porcentaje_comision"=>"","prima_neta"=>"","fecha_operacion"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => $nombre_ramo_anterior, 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => '', 'monto' => '' ,'estado' => '', 'estilos' => $estilo ));
					
					$total_com_esperada=0;
					$total_com_descontada=0;
					$total_sob_esperada=0;
					$total_sob_descontada=0;
					$comision_pagada_total=0;
					$ramo_anterior=$value->id_ramo;
					$comision_esperada_comparar_total=0;
				}
				
				if($var!=0)
				{
					$total_com_esperada+=$comision_esperada;
					$total_com_descontada+=$comision_descontado;
					$total_sob_esperada+=$sobcomision_esperada;
					$total_sob_descontada+=$sobcomision_descontada;
					
					$comision_pagada=$value->comision_pagada;
					
					$comision_pagada_total+=$comision_pagada;
					
					$comision_esperada_comparar=$value->monto_comision-$value->comision_descontada+$value->monto_scomision-$value->scomision_descontada;
					
					if($comision_esperada_comparar!=$comision_pagada)
					{
						$estilo='font-weight: normal; color:#ff0000;';
					}
					else
						$estilo='font-weight: normal;';

					array_push($datosRemesa, array("remesa_creada"=>"","aseguradora_id"=>"","id" => $value->id, "link_poliza"=>$url_poliza,"link_factura"=>$url_factura,"final"=>0,"prima_neta_final"=>"","pago_final"=>"","com_esp_final"=>"","com_des_final"=>"","scom_esp_final"=>"","scom_des_final"=>"","com_paga_final"=>"","total_sob_descontada"=>number_format($total_sob_descontada, 2),"total_sob_esperada"=>number_format($total_sob_esperada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_esperada"=>number_format($comision_esperada_comparar_total,4),"comision_pagada_total"=>number_format($comision_pagada_total, 2),"comision_pagada"=>number_format($comision_pagada, 2),"sobcomision_descontada"=>number_format($sobcomision_descontada, 2),"sobcomision_esperada"=>number_format($sobcomision_esperada, 2),"porcentaje_sobre_comision"=>$value->sobre_comision,"comision_descontado"=>number_format($comision_descontado, 2),"comision_esperada"=>number_format($comision_esperada,4),"porcentaje_comision"=>$value->comision,"prima_neta"=>number_format($value->pago_sobre_prima,2),"fecha_operacion"=>date('Y-m-d'),"uuid_poliza"=>bin2hex($value->polizas->uuid_polizas),"uuid_factura"=>bin2hex($value->uuid_comision),"numero_factura" => $value->no_comision, "numero_poliza" => $value->polizas->numero , 'ramo_id'=>$value->id_ramo,'nombre_ramo' => $value->datosRamos->nombre, 'inicio_vigencia' => date($value->fecha_pago), 'fin_vigencia' => date($value->fecha_pago), 'nombre_cliente' => $value->cliente->nombre, 'fecha_factura' => date($value->facturasComisiones->fecha_desde), 'monto' => number_format($valor_real, 2) ,'estado' => $value->facturasComisiones->estado, 'estilos' => $estilo ));
				}
				
				$com_paga_final+=$comision_pagada;
				
				$var=$var+1;
				
				$ramo=$value->id_ramo;
			}
		
			if($ramo!="")
				$nombre_ramo_anterior=$this->Ramos->find($ramo)->nombre;
			else
				$nombre_ramo_anterior='';
			
			if($comision_esperada_comparar_total!=$comision_pagada_total)
				$estilo='font-weight: bold; background-color:#efefef; color:#ff0000;';
			else
				$estilo='font-weight: bold; background-color:#efefef;';
			
			array_push($datosRemesa, array("remesa_creada"=>"","aseguradora_id"=>"","link_poliza"=>"","link_factura"=>"","id" => '', "final"=>0,"prima_neta_final"=>"","pago_final"=>"","com_esp_final"=>"","com_des_final"=>"","scom_esp_final"=>"","scom_des_final"=>"","com_paga_final"=>"", "total_sob_descontada"=>number_format($total_sob_descontada, 2),"total_sob_esperada"=>number_format($total_sob_esperada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_descontada"=>number_format($total_com_descontada, 2),"total_com_esperada"=>number_format($comision_esperada_comparar_total,4),"comision_pagada_total"=>number_format($comision_pagada_total, 2),"comision_pagada"=>number_format($comision_pagada_total, 2),"sobcomision_descontada"=>"","sobcomision_esperada"=>"","porcentaje_sobre_comision"=>"","comision_descontado"=>"","comision_esperada"=>"","porcentaje_comision"=>"","prima_neta"=>"","fecha_operacion"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => $nombre_ramo_anterior, 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => '', 'monto' => '' ,'estado' => '', 'estilos' => $estilo));
			
			if($com_paga_final!=$com_esp_final)
				$estilo='font-weight: bold; background-color:#cccccc; color:#ff0000;';
			else
				$estilo='font-weight: bold; background-color:#cccccc;';
			
			$campoupdate["monto"] =$sumamonto;
			$campoupdate["updated_at"] =date('Y-m-d H:i:s');
			$actalizar_remesa=$this->RemesasEntrantes->where(['id' => $id_remesa->id])->update($campoupdate);
			
			$remesa=$this->RemesasEntrantes->find($id_remesa->id);
			
			$uuid_remesa=bin2hex($remesa->uuid_remesa_entrante);
			
			array_push($datosRemesa, array("remesa_creada"=>$uuid_remesa,"aseguradora_id"=>$id_aseguradora,"link_poliza"=>"","link_factura"=>"","id" => '', "final"=>1,"prima_neta_final"=>number_format($prima_neta_final, 2),"pago_final"=>number_format($pago_final, 2),"com_esp_final"=>number_format($com_esp_final, 4),"com_des_final"=>number_format($com_des_final, 2),"scom_esp_final"=>number_format($scom_esp_final, 2),"scom_des_final"=>number_format($scom_des_final, 2),"com_paga_final"=>number_format($com_paga_final, 2),"total_sob_descontada"=>"","total_sob_esperada"=>"","total_com_descontada"=>"","total_com_descontada"=>"","total_com_esperada"=>"","comision_pagada_total"=>"","comision_pagada"=>"","sobcomision_descontada"=>"","sobcomision_esperada"=>"","porcentaje_sobre_comision"=>"","comision_descontado"=>"","comision_esperada"=>"","porcentaje_comision"=>"","prima_neta"=>"","fecha_operacion"=>'',"uuid_poliza"=>'',"uuid_factura"=>'',"numero_factura" => '', "numero_poliza" => '' , 'ramo_id'=>'','nombre_ramo' => $nombre_ramo_anterior, 'inicio_vigencia' => '', 'fin_vigencia' => '', 'nombre_cliente' => '', 'fecha_factura' => '', 'monto' => '' ,'estado' => '', 'estilos' => $estilo ));

			$formulario = "formularioProcesada";			
		}
       
		$clause = array('empresa_id' => $this->id_empresa);
		$GetRamos = $this->ramoRepository->listar_cuentas($clause);
		$nombreRamos = array();
		foreach ($GetRamos as $value) {
			if(in_array($value['id'], $id_ramos)){
				array_push($nombreRamos, array('nombre' => $value['nombre']));
			}
		}
		
		$nombre = $codigo_remesa;
		$aseguradora = Aseguradoras::where(['id' => $id_aseguradora])->first();
		
		$no_recibo='';
		$monto_recibo='';
		$nombre_recibo='';
		if($id_remesa->estado=='por_liquidar')
		{
			if($id_remesa->id_recibo!='')
			{
				$no_recibo=$id_remesa->id_recibo;
				
				$datosrecibo=Movimiento_monetario_orm::find($no_recibo);
				$montorecibo=Items_recibos_orm::where('id_recibo',$no_recibo)->sum('credito');
				
				$monto_recibo=$montorecibo;
				$nombre_recibo=$datosrecibo->codigo.' '.$datosrecibo->narracion;
			}
		}
		
		$data = ['datos'=>$id_remesa,'monto_recibo'=>$monto_recibo,'nombre_recibo'=>$nombre_recibo,'nombreRamos'=>$nombreRamos,'aseguradora' => $aseguradora, 'fecha_inicial' => $fecha_inicial, 'fecha_final' => $fecha_final, 'datosRemesa' => $datosRemesa];
		$dompdf = new Dompdf();
		$html = $this->load->view('pdf/' . $formulario, $data,true);
		$dompdf->loadHtml($html);
		$dompdf->set_paper ('A4','landscape'); 
		//$dompdf->setPaper('A4', 'landscape');
		$dompdf->render();
		$dompdf->stream($nombre, array("Attachment" => false));
		exit(0);

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
                
		$remesasentrantes = $this->RemesasEntrantesRepository->exportar($clause);
		if(empty($remesasentrantes)){
			return false;
		}
		$i=0;
		foreach ($remesasentrantes AS $row)
		{
			if($row->estado=='en_proceso')
				$estado='En proceso';
			else if($row->estado=='por_liquidar')
				$estado='Por liquidar';
			if($row->estado=='liquidada')
			{
				$estado='Liquidada';
			}
				
			$csvdata[$i]['no_remesa'] = $row->no_remesa;
			$csvdata[$i]["pagos_remesados"] = $row->pagos_remesados;
			$csvdata[$i]["nom_aseguradora"] = utf8_decode(Util::verificar_valor($row->nom_aseguradora));
			$csvdata[$i]["monto"] = $row->monto;
			$csvdata[$i]["created_at"] = $row->created_at;
			$csvdata[$i]["usuario_id"] = $row->nom_usuario." ".$row->ape_usuario;
			$csvdata[$i]["estado"] = $estado;
			$i++;
		}
		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'N. Remesa',
			'Pagos Remesados',
			'Aseguradora',
			'Monto',
			'Fecha',
			'Usuario',
			'Estado'
		]);
		$csv->insertAll($csvdata);
		$csv->output("remesas_entrantes-". date('ymd') .".csv");
		exit();
    }
	
	function ajax_get_eliminar_comisiones()
	{
		$remesa=$this->input->post('remesa');
		$comisiones=$this->input->post('comisiones');
		
		$datosremesa=$this->RemesasEntrantes->where('no_remesa',$remesa)->first();
		$id_remesa=$datosremesa->id;
		
		$actualizardatos['id_remesa']=NULL;
		$actualizar=$this->ComisionesSeguros->whereIn('id',$comisiones)->update($actualizardatos);
		
		$eliminar=$this->COmisionesSegurosRemesas->whereIn('id_comision',$campos['comisiones'])->where('id_remesa',$id_remesa)->delete();
		
		$response='si';
		
		echo json_encode($response);
        exit;
	}
	
	function ajax_get_datos_mov_dinero()
	{
		$no_recibo=$this->input->post('recibo');
		
		$datosrecibo=Movimiento_monetario_orm::find($no_recibo);
		$montorecibo=Items_recibos_orm::where('id_recibo',$no_recibo)->sum('credito');
		
		$monto_recibo=number_format($montorecibo,2);
		
		echo json_encode($monto_recibo);
        exit;
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
            'public/assets/css/modules/stylesheets/remesasentrantes.css',
			'public/assets/css/modules/stylesheets/cobros.css'
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