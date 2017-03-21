<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 02:30 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ComisionesSeguros\Models\ComisionesSeguros as ComisionesSeguros;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\ComisionesSeguros\Repository\ComisionesSegurosRepository as ComisionesSegurosRepository;
use League\Csv\Writer as Writer;
use Dompdf\Dompdf;
use Flexio\Modulo\ComisionesSeguros\Models\SegComisionesParticipacion as SegComisionesParticipacion;

class Comisiones_seguros extends CRM_Controller
{

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;
	protected $aseguradoras;
	protected $ComisionesSegurosRepository;
	protected $ComisionesSeguros;
	protected $SegComisionesParticipacion;

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
		$this->ComisionesSegurosRepository=new ComisionesSegurosRepository();
		$this->ComisionesSeguros=new ComisionesSeguros();
		$this->SegComisionesParticipacion=new SegComisionesParticipacion();
    }
	
	public function ocultotabla($id_cliente = NULL)
    {

        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/comisiones_seguros/tabla.js',
        ));

        if (!empty($id_cliente)) {

            // Agregra variables PHP como variables JS
            $this->assets->agregar_var_js(array(
                "id_cliente" => $id_cliente
            ));

        }

        $this->load->view('tabla');
    }
	
	function ocultoformulario($data = array()) {
        $clause = array('empresa_id' => $this->id_empresa);        
        
        $this->load->view('formulario',$data);
	}

	function ver($uuid=null){
		if( !$this->auth->has_permission('acceso', 'comisiones_seguros/ver/(:any)')) { 
			// No, tiene permiso, redireccionarlo.
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para ver detalle de la comisión', 'titulo' => 'Comisiones');
			$this->session->set_flashdata('mensaje', $mensaje);
			redirect(base_url('comisiones_seguros/listar'));
		}

		$data = array();

		$comision = $this->ComisionesSeguros->where(['uuid_comision' => hex2bin(strtolower($uuid))])->first();

		$this->_js();
		$this->_css();
		
		$this->assets->agregar_js(array(
			'public/assets/js/modules/comisiones_seguros/ver.js',
        ));
		
		$this->assets->agregar_var_js(array(
			"uuid_comision" =>bin2hex($comision->uuid_comision)
        ));

		$breadcrumb = array(
			"titulo" => '<i class="fa fa-archive"></i> Comisiones: '.$comision->no_comision,
			"ruta" => array(
				0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
				1 => array("nombre" => '<b>Comisiones</b>', "activo" => true, "url" => 'comisiones_seguros/listar'),
				2 => array("nombre" => '<b>'.$comision->no_comision.'</b>', "activo" => true)
				),
			"filtro" => false,
			"menu" => array()
			);

		$breadcrumb["menu"] = array(
			"url" => '',
			"clase" => '',
			"nombre" => "Acción"
			);
		
		if($comision->estado=='por_liquidar')
			$estado='Por liquidar';
		else if($comision->estado=='liquidada')
			$estado='Liquidada';
		else if($comision->estado=='pagada')
			$estado='Pagada';
		else if($comision->estado=='pagada_parcial')
			$estado='Pagada parcial';
		else
			$estado='Con diferencia';
		
		if($comision->id_remesa!="")
		{
			$remesa=$comision->datosRemesa->no_remesa;
			$fecha_liqui=$comision->datosRemesa->fecha_liquidada;
		}
		else
		{
			$remesa='';
			$fecha_liqui='';
		}
		
		
		
		$participacioncomision=$this->SegComisionesParticipacion->where('comision_id',$comision->id)->get();
		$data["campos"] = array(
			"campos" => array(
				"uuid_comision" => $uuid,
				"no_poliza" => $comision->polizas->numero,
				"no_recibo" => $comision->datosCobro->codigo,
				"cliente" => $comision->cliente->nombre,
				"ramo" => $comision->datosRamos->nombre,
				"impuesto_pago" => number_format($comision->impuesto_pago,2),
				"pago_sobre_prima" => number_format($comision->pago_sobre_prima,2),
				"sobre_comision" => $comision->sobre_comision,
				"monto_scomision" => number_format($comision->monto_scomision,2),
				"no_remesa" =>$remesa ,
				"fecha" => $comision->fecha,
				"monto_recibo" => number_format($comision->monto_recibo,2),
				"no_factura" => $comision->facturasComisiones->codigo,
				"aseguradora" => $comision->datosAseguradora->nombre,
				"poliza" => $comision->polizas->numero,
				"comision" => $comision->comision,
				"monto_comision" => number_format($comision->monto_comision,2),
				"comision_pendiente" => number_format($comision->comision_pendiente,2),
				"lugar_pago" => $comision->lugar_pago,
				"estado" => $estado,
				"comision_recibir"=>number_format(($comision->monto_comision + $comision->monto_scomision),2),
				"comision_descontada"=>number_format($comision->comision_descontada,2),
				"fecha_liquidacion" => $fecha_liqui,
				"comision_pagada" => number_format($comision->comision_pagada,2),
				"participacioncomision"=>$participacioncomision
			),

		);
		$menuOpciones["#exportarComisionBtn"] = "Imprimir";
		$breadcrumb["menu"]["opciones"] = $menuOpciones;

		$this->template->agregar_titulo_header('Ver comision');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar($breadcrumb);
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
        if (!$this->auth->has_permission('acceso', 'comisiones_seguros/listar') == true) {
            $acceso = 0;
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para ingresar a comisiones', 'titulo' => 'Comisiones');
            $this->session->set_flashdata('mensaje', $mensaje);

            redirect(base_url(''));
        }

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Comisiones',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Comisiones</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );
		$this->assets->agregar_js(array(
			'public/assets/js/modules/comisiones_seguros/listar.js',
        ));

        $breadcrumb["menu"] = array(
            "url" => '#',
            "clase" => 'modalOpcionesCrear',
            "nombre" => "Acción"
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

        $this->template->agregar_titulo_header('Listado de comisiones');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }
	
	 public function ajax_listar()
    {
        //Just Allow ajax request

        if(!$this->input->is_ajax_request()){
            return false;
        }
		
		$clause = array(
    		"empresa_id" =>  $this->id_empresa
    	);
        
		$no_comision= $this->input->post('no_comision', true);
		$nombre_aseguradora= $this->input->post('nombre_aseguradora', true);
		$inicio_fecha= $this->input->post('inicio_fecha', true);
		$fin_fecha= $this->input->post('fin_fecha', true);
		$no_cobro= $this->input->post('no_cobro', true);
		$estado= $this->input->post('estado', true);
		
		if(!empty($no_comision)){
    		$clause["no_comision"] = array('LIKE', "%$no_comision%");
    	}
		if(!empty($nombre_aseguradora)){
    		$clause["id_aseguradora"] = $nombre_aseguradora;
    	}
		if(!empty($inicio_fecha)){
			$fecha1=date('Y-m-d', strtotime($inicio_fecha));
    		$clause["fecha1"] = $fecha1;
    	}
		if(!empty($fin_fecha)){
			$fecha2=date('Y-m-d', strtotime($fin_fecha));
    		$clause["fecha2"] = $fecha2;
    	}
		if(!empty($no_cobro)){
    		$clause["no_cobro"] = $no_cobro;
    	}
		if(!empty($estado)){
    		$clause["seg_remesas_entrantes.estado"] = $estado;
    	}
		
		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		
    	$count = $this->ComisionesSegurosRepository->listar($clause, NULL, NULL, NULL, NULL)->count();
    		
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    		 
    	$rows = $this->ComisionesSegurosRepository->listar($clause, $sidx, $sord, $limit, $start);
    	
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;

        if(!empty($rows)){
            foreach ($rows as  $row){
                $hidden_options = "";
				
				if($row->estado=='liquidada')
				{
					$clase_estado='background-color: #F8AD46';
					$estado='Liquidada';
				}
				else if($row->estado=='por_liquidar')
				{
					$clase_estado='background-color: #5bc0de';
					$estado='Por liquidar';
				}
				else if($row->estado=='con_diferencia')
				{
					$clase_estado='background-color: #fc0d1b';
					$estado='Con diferencia';
				}
				else if($row->estado=='pagada')
				{
					$clase_estado='background-color: #5cb85c';
					$estado='Pagada';
				}
				
				else if($row->estado=='pagada_parcial')
				{
					$clase_estado='background-color: #6ede6e';
					$estado='Pagada parcial';
				}
				
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				$hidden_options = '<a href="'. base_url('comisiones_seguros/ver/'. bin2hex($row->uuid_comision)) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
				
				if($row['comision_pendiente']>=0)
					$estilomonto='totales-success';
				else
					$estilomonto='totales-danger';
			
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'no_comision'=> '<a href="'.base_url('comisiones_seguros/ver/'.bin2hex($row->uuid_comision)).'">'.$row['no_comision']."</a>",
                    'no_recibo' =>  '<a href="'.base_url('cobros_seguros/ver/'.bin2hex($row->uuid_cobro_seguro)).'?reg=com">'.$row['codigo_cobro']."</a>",
                    'aseguradora_id' => $row['nom_aseguradora'],
                    'monto_comision' => '<label class="totales-success">'.number_format($row['monto_comision'], 2, '.', ',').'</label>',
					'comision_pendiente' => '<label class="'.$estilomonto.'">'.number_format($row['comision_pendiente'], 2, '.', ',').'</label>',
					'fecha' => $row['fecha'],
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
                
		$comisiones = $this->ComisionesSegurosRepository->exportar($clause);
		if(empty($comisiones)){
			return false;
		}
		$i=0;
		foreach ($comisiones AS $row)
		{
			if($row->estado=='con_diferencia')
				$estado='Con diferencia';
			else if($row->estado=='por_liquidar')
				$estado='Por liquidar';
			else if($row->estado=='liquidada')
				$estado='Liquidada';
			else if($row->estado=='pagada')
				$estado='Pagada';
			else if($row->estado=='pagada_parcial')
				$estado='Pagada parcial';
			
			$csvdata[$i]['no_comision'] = $row->no_comision;
			$csvdata[$i]["no_recibo"] = $row->codigo_cobro;
			$csvdata[$i]["nom_aseguradora"] = utf8_decode(Util::verificar_valor($row->nom_aseguradora));
			$csvdata[$i]["monto_recibo"] = $row->monto_recibo;
			$csvdata[$i]["comision_pendiente"] = $row->comision_pendiente;
			$csvdata[$i]["fecha"] = $row->fecha;
			$csvdata[$i]["estado"] = $estado;
			$i++;
		}
		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			utf8_decode('N. Comisión'),
			'N. Recibo',
			'Aseguradora',
			utf8_decode('Monto comisión'),
			'Monto pendiente',
			'Fecha',
			'Estado'
		]);
		$csv->insertAll($csvdata);
		$csv->output("comision-". date('ymd') .".csv");
		exit();
    }
	
	function imprimirComisiones($uuid = null)
	{
		$comision = $this->ComisionesSeguros->where(['uuid_comision' => hex2bin(strtolower($uuid))])->first();
		
		$logo=$comision->datosEmpresa->logo;
		
		if($comision->estado=='por_liquidar')
			$estado='Por liquidar';
		else if($comision->estado=='liquidada')
			$estado='Liquidada';
		else if($comision->estado=='pagada')
			$estado='Pagada';
		else if($comision->estado=='pagada_parcial')
			$estado='Pagada parcial';
		else
			$estado='Con diferencia';
		
		if($comision->id_remesa!="")
		{
			$remesa=$comision->datosRemesa->no_remesa;
		}
		else
			$remesa='';
		
		$campos = [
			"uuid_comision" => $uuid,
			"no_comision" => $comision->no_comision,
			"no_poliza" => $comision->polizas->numero,
			"cliente" => $comision->cliente->nombre,
			"ramo" => $comision->datosRamos->nombre,
			"impuesto_pago" => $comision->impuesto_pago,
			"pago_sobre_prima" => number_format($comision->pago_sobre_prima,2),
			"sobre_comision" => $comision->sobre_comision,
			"monto_scomision" => number_format($comision->monto_scomision,2),
			"no_remesa" => $remesa,
			"fecha" => $comision->fecha,
			"monto_recibo" => number_format($comision->monto_recibo,2),
			"no_factura" => $comision->facturasComisiones->codigo,
			"aseguradora" => $comision->datosAseguradora->nombre,
			"poliza" => $comision->polizas->numero,
			"comision" => $comision->comision,
			"monto_comision" => number_format($comision->monto_comision,2),
			"comision_pendiente" => number_format($comision->comision_pendiente,2),
			"lugar_pago" => $comision->lugar_pago,
			"estado" => $estado,
			"logo" =>$logo
		];
		//$html = $this->load->view('pdf/comisiones', $campos);
		$dompdf = new Dompdf();
		$html = $this->load->view('pdf/comisiones', $campos, true);
		$dompdf->loadHtml($html);
		$dompdf->set_paper ('A4','landscape'); 
		//$dompdf->setPaper('A4', 'landscape');
		$dompdf->render();
		$dompdf->stream('Comision-'.$comision->no_comision, array("Attachment" => false));
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
            'public/assets/css/modules/stylesheets/comisionesseguros.css'
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