<?php
/**
 * Intereses Asegurados
 *
 * Modulo para administrar la creacion, edicion de Intereses Asegurados
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/29/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Flexio\Library\Util\GenerarCodigo as GenerarCodigo;
use Dompdf\Dompdf;
use Carbon\Carbon;
//Repositorios
use Flexio\Modulo\InteresesAsegurados\Repository\InteresesAseguradosRepository as interesesAseguradosRep;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados as AseguradosModel;
use Flexio\Modulo\InteresesAsegurados\Models\VehiculoAsegurados as VehiculoModel;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat as InteresesAsegurados_catModel;
use Flexio\Modulo\SegCatalogo\Models\SegCatalogo as SegCatalogosModel;
use Flexio\Modulo\Acreedores\Repository\AcreedoresRepository as AcreedoresRep;

//

class Intereses_asegurados2 extends CRM_Controller
{
    private $empresa_id;
    private $id_usuario;
    private $empresaObj;
    private $AseguradosModel;
    private $VehiculoModel;
    private $AereoModel;
    private $MaritimoModel;
    private $PersonasModel;
    private $ProyectoModel;
    private $ArticulomoModel;
    private $UbicacionmoModel;


    //flexio
    private $interesesAseguradosRep;
    private $catalogosAseguradosRep;
    private $assetsInteresesAsegurados;
    protected $upload_folder = './public/uploads/';

    public function __construct() {
        parent::__construct();
        $this->load->model('modulos/Catalogos_orm');
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('beneficiarios/Beneficiarios_orm');
        $this->load->model("intereses_asegurados2/Intereses_asegurados_cat_orm");
        //HMVC Load Modules
        $this->load->module(array('documentos'));
        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $empresaObj         = new Buscar(new Empresa_orm,'uuid_empresa');
        //variables para el entorno del modulo
        $this->empresaObj   = $empresaObj->findByUuid($uuid_empresa);
        $this->usuario_id   = $this->session->userdata("huuid_usuario");
        $this->empresa_id   = $this->empresaObj->id;
        //flexio
        $this->interesesAseguradosRep    = new interesesAseguradosRep();
        $this->assetsInteresesAsegurados = new assetsInteresesAsegurados();
        $this->AseguradosModel = new AseguradosModel();
        $this->VehiculoModel = new VehiculoModel();
        $this->AereoModel = new AereoModel();
        $this->MaritimoModel = new MaritimoModel();
        $this->UbicacionmoModel = new UbicacionmoModel();
        $this->ArticulomoModel = new ArticulomoModel();
        $this->PersonasModel = new PersonasModel();
        $this->ProyectoModel = new ProyectoModel();
        $this->CargaModel = new CargaModel();
        $this->catalogosAseguradosRep = new CatalogosRep();
        $this->acreedoresRep = new AcreedoresRep();
    }

    public function index() {
        redirect("intereses_asegurados/listar");
    }

    private function _mensaje() {
        $aux = [];
        if($this->session->userdata('idInteresAsegurado')){
          $this->session->unset_userdata('idInteresAsegurado');

          $aux["clase"]       = "alert-success";
          $aux["contenido"]   = "Se ha creado el Interés Asegurado satisfactoriamente.";
      }
      else if($this->session->userdata('updatedInteresAsegurado'))
      {
          $this->session->unset_userdata('updatedInteresAsegurado');

          $aux["clase"]       = "alert-success";
          $aux["contenido"]   = "Se ha actualizado el Interés Asegurado satisfactoriamente.";
      }
      return $aux;
  }

  private function _breadcrumbListar() {
    $breadcrumb = array(
        "titulo" => '<i class="fa fa-archive"></i> Intereses Asegurados',
        "ruta"   => array(
            0    => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
            1    => array("nombre" => '<b>Intereses Asegurados</b>', "activo" => true)
            ),
        "filtro"    => false,
        "menu"      => array()
        );

    if ($this->auth->has_permission('acceso', 'intereses_asegurados/crear')){
        $breadcrumb["menu"] = array(
          "url"	=> 'javascript:',
          "clase" => 'crearBoton',
          "nombre" => "Crear"
          );
    }

    if ($this->auth->has_permission('listar__exportarInteresesAsegurados', 'intereses_asegurados/listar')){
        $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
    }

    return $breadcrumb;
}

public function listar() {
 $data = array();

 $this->assets->agregar_css($this->assetsInteresesAsegurados->agregar_css_principal());
 $this->assets->agregar_css($this->assetsInteresesAsegurados->agregar_css_listar());

 $this->assets->agregar_js($this->assetsInteresesAsegurados->agregar_js_principal());
 $this->assets->agregar_js($this->assetsInteresesAsegurados->agregar_js_listar());

        //defino mi mensaje
 if(!is_null($this->session->flashdata('mensaje'))){
    $mensaje = json_encode($this->session->flashdata('mensaje'));
}else{
    $mensaje = '';
}
$this->assets->agregar_var_js(array(
    "toast_mensaje" => $mensaje
    ));

$data["usuarios"] = Usuario_orm::get(array('id','nombre','apellido'));

$data["tipos_intereses_asegurados"]= Intereses_asegurados_cat_orm::tipos()->get(array("id_cat", "valor", "etiqueta"));
$data["estado"]= $this->catalogosAseguradosRep->getEstados();

    	//Breadcrum Array
$breadcrumb = $this->_breadcrumbListar();

unset($data["mensaje"]);

$this->template->agregar_titulo_header('Listado de Intereses Asegurados');
$this->template->agregar_breadcrumb($breadcrumb);
$this->template->agregar_contenido($data);
$this->template->visualizar($breadcrumb);

}

public function ajax_obtener_item() {
    	//Just Allow ajax request
 if($this->input->is_ajax_request())
 {
    $this->load->model("inventarios/Items_orm");
    $uuid = $this->input->post("uuid", true);

    $registro   = Items_orm
    ::where("uuid_item", "=", hex2bin(strtolower($uuid)))
    ->get();

    $item       = array();
    $i          = 0;
    foreach ($registro as $row)
    {
        $item[$i] = array(
            "descripcion"   => $row->descripcion,
            "unidades"      => $row->unidades
            );
        $i += 1;
    }

    $response               = array();
    $response["success"]    = false;
    $response["item"]       = $item;

    if(!empty($response["item"]))
    {
        $response["success"]    = true;
    }

    echo json_encode($response);
    exit();
}

}

public function ajax_obtener_pedido_item() {
    	//Just Allow ajax request
 if($this->input->is_ajax_request())
 {
    $this->load->model("pedidos/Pedidos_items_orm");

    $id_pedido_item = $this->input->post("id_pedido_item", true);
    $registro       = Pedidos_items_orm::find($id_pedido_item)->toArray();


    $response               = array();
    $response["success"]    = false;
    $response["registro"]   = $registro;

    if(!empty($response["registro"]))
    {
        $response["success"]    = true;
    }

    echo json_encode($response);
    exit();
}

}

public function ajax_listar() {
 if(!$this->input->is_ajax_request()){
    return false;
}
$numero = $this->input->post('numero', true);
$tipo = $this->input->post('tipo', true);
$identificacion = $this->input->post('identificacion', true);
$estado = $this->input->post('estado', true);
if(!empty($numero)){
  $clause["numero"] = $numero;
}
if(!empty($tipo)){
  $clause["tipo"] = $tipo;
}
if(!empty($identificacion)){
  $clause["identificacion"] = $identificacion;
}
if(!empty($estado)){
  $clause["estado"] = $estado;
}
$clause['empresa_id'] = $this->empresa_id;
list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
$count = $this->interesesAseguradosRep->count($clause);
list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
$intereses_asegurados = $this->interesesAseguradosRep->listar($clause, $sidx, $sord, $limit, $start);
$response           = new stdClass();
$response->page     = $page;
$response->total    = $total_pages;
$response->records  = $count;
if($count){
    $i=0;
    foreach($intereses_asegurados as $i => $row){
        $hidden_options = "";
        $link_option    = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options .= '<a href="'. base_url('intereses_asegurados/ver/'. bin2hex($row->uuid_intereses)) .'" class="btn btn-block btn-outline btn-success">Ver interés asegurado</a>';
        $hidden_options .= '<a href="#" data-id="'.$row->interesestable_id .'" data-type="'. $row->interesestable_type .'" class="btn btn-block btn-outline btn-success subir_archivo_intereses">Subir archivo</a>';
        $response->rows[$i]["id"]   = $row->id;
        $response->rows[$i]["cell"] = $this->_getResponseCell($row, $link_option, $hidden_options);

        $i++;
    }
}

$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
->set_output(json_encode($response))->_display();
exit;
}

private function _getResponseCell($row, $link_option, $hidden_options) {
    $estado = Util::verificar_valor($row->estado_catalogo->etiqueta);
    $estado_color = trim($estado) == "Activo" ? 'background-color:#5CB85C' : 'background-color: red';
    return [
    '<a style="color:blue;" class="link" href="'. base_url('intereses_asegurados/ver/'. bin2hex($row->uuid_intereses)) .'" >'. $row->numero .'</a>',
    $row->tipo->etiqueta,
    $row->identificacion,
    '<span style="color:white; '. $estado_color .'" class="btn btn-xs btn-block">'. $estado .'</span>',
    $link_option,
    $hidden_options
    ];
}
function ajax_anular() {
    $response = array();
    $response["success"]    = false;
    $response["mensaje"]    = "Error de sistema. Comuniquelo con el administrador de sistema";
    $response["clase"]      = "alert-danger";

    $uuid   = $this->input->post("uuid", true);
    if(!empty($uuid))
    {
        $registro   = Pedidos_orm
        ::where("uuid_pedido", "=", hex2bin(strtolower($uuid)))
        ->first();

            //DEFINO EL ESTADO COMO ANULADO = 6
        $registro->id_estado = "6";
        if($registro->save())
        {
            $response["success"]    = true;
            $response["mensaje"]    = "Su solicitud fue procesada satifastoriamente.";
            $response["clase"]      = "alert-success";
        }

    }

    echo json_encode($response);
    exit();
}

function ajax_eliminar_pedido_item() {
    	//Just Allow ajax request
 if(!$this->input->is_ajax_request()){
  return false;
}

$this->load->model("pedidos/Pedidos_items_orm");

$id_registro    = $this->input->post("id_registro", true);
$registro       = Pedidos_items_orm::find($id_registro);

$response   = array(
    "respuesta" => $registro->delete(),
    "mensaje"   => "Se ha eliminado el registro satisfactoriamente"
    );


$json       = '{"results":['.json_encode($response).']}';
echo $json;
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
$clause = array(
    "empresa_id"  => $this->empresa_id
    );
$clause['intereses'] = $id;

$intereses = $this->interesesAseguradosRep->listar($clause, NULL, NULL, NULL, NULL);

if(empty($intereses)){
   return false;
}
$i=0;
foreach ($intereses AS $row)
{
   $csvdata[$i]['numero'] = $row->numero;
   $csvdata[$i]["tipo"] = utf8_decode(Util::verificar_valor($row->tipo->etiqueta));
   $csvdata[$i]["identificacion"] = utf8_decode(Util::verificar_valor($row->identificacion));
   $csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row->estado_catalogo->etiqueta));
   $i++;
}
		//we create the CSV into memory
$csv = Writer::createFromFileObject(new SplTempFileObject());
$csv->insertOne([
   'Numero',
   'Tipo',
   'Identificacion',
   'Estado'
   ]);
$csv->insertAll($csvdata);
$csv->output("intereses-". date('ymd') .".csv");
exit();
}

function ajax_reabrir() {
    $response = array();
    $response["success"]    = false;
    $response["mensaje"]    = "Error de sistema. Comuniquelo con el administrador de sistema";
    $response["clase"]      = "alert-danger";

    $uuid   = $this->input->post("uuid", true);
    if(!empty($uuid))
    {
        $registro   = Pedidos_orm
        ::where("uuid_pedido", "=", hex2bin(strtolower($uuid)))
        ->first();

            //DEFINO EL ESTADO COMO ABIERTO = 1
        $registro->id_estado = "1";
        if($registro->save())
        {
            $response["success"]    = true;
            $response["mensaje"]    = "Su solicitud fue procesada satifastoriamente.";
            $response["clase"]      = "alert-success";
        }

    }

    echo json_encode($response);
    exit();
}

private function _getProveedor($interes_asegurado) {
    return [
    "id"        => $interes_asegurado->id,
    "nombre"    => $interes_asegurado->nombre,
            "credito"   => $interes_asegurado->credito, //Por desarrollar -> depende de abonos
            "saldo"     => (string)($interes_asegurado->total_saldo_pendiente()) ? : "0.00"
            ];
        }

        function ajax_get_interes_asegurado() {

            $interes_asegurado_id   = $this->input->post("interes_asegurado_id");
            $interes_asegurado      = InteresesAsegurados_orm::find($interes_asegurado_id);
            $registro       = array();

            if(count($interes_asegurado))
            {
                $registro = $this->_getProveedor($interes_asegurado);
            }

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($registro))->_display();

            exit;
        }

        function ajax_check_vehiculo() {

            $chasis   = $this->input->post("chasis");
            $chasis_obj = $this->interesesAseguradosRep->identificacion($chasis);
            if(empty($chasis_obj)){
                echo('USER_AVAILABLE');
            }else{
                echo('USER_EXISTS');
            }
        }
        /*function ajax_check_persona() {

            $identificacion   = $this->input->post("identificacion");
           // $identificacion_obj = $this->interesesAseguradosRep->identificacion_persona($identificacion);
            if(empty($identificacion_obj)){
                echo('USER_AVAILABLE');
            }else{
                echo('USER_EXISTS');
            }
        }*/
        function ajax_check_aereo() {

            $chasis   = $this->input->post("serie");
            $chasis_obj = $this->interesesAseguradosRep->identificacion_aereo($chasis);
            if(empty($chasis_obj)){
                echo('USER_AVAILABLE');
            }else{
                echo('USER_EXISTS');
            }
        }
        function ajax_check_maritimo() {

            $serie   = $this->input->post("serie");
            $serie_obj = $this->interesesAseguradosRep->identificacion_maritimo($serie);
            if(empty($serie_obj)){
                echo('USER_AVAILABLE');
            }else{
                echo('USER_EXISTS');
            }
        }
        function ajax_check_ubicacion() {

            $ubicacion   = $this->input->post("ubicacion");
            $ubicacion_obj = $this->interesesAseguradosRep->identificacion_ubicacion($ubicacion);
            if(empty($ubicacion_obj)){
                echo('USER_AVAILABLE');
            }else{
                echo('USER_EXISTS');
            }
        }
        function ajax_check_proyecto() {

            $nombre_proyecto   = $this->input->post("nombre_proyecto", true);
            $no_orden   = $this->input->post("no_orden_proyecto", true);
            $orden_obj = $this->interesesAseguradosRep->identificacion_proyecto($nombre_proyecto, $no_orden);
            if(empty($orden_obj)){
                echo('USER_AVAILABLE');
            }else{
                echo('USER_EXISTS');
            }
        }
        function ajax_check_carga() {

            $no_liquidacion   = $this->input->post("no_liquidacion");
            $liquidacion_obj = $this->interesesAseguradosRep->identificacion_carga($no_liquidacion);
            if(empty($liquidacion_obj)){
                echo('USER_AVAILABLE');
            }else{
                echo('USER_EXISTS');
            }
        }

        function ajax_exportar() {
            $id_registros = $this->input->post("id_registros", true);

            if(!$id_registros){
                return false;
            }

            $id_registros = explode("-", $id_registros);

        //EN CASO DE QUE SEAN UUID LOS CAMBIO AL
        //FORMATO QUE ESTA EN LA BASE DE DATOS
            foreach ($id_registros as &$row)
            {
                $row = hex2bin(strtolower($row));
            }

            $registros  = InteresesAsegurados_orm
            ::whereIn("uuid_interes_asegurado", $id_registros)
            ->get();

            $items = array();
            $i = 0;
            foreach($registros as $registro)
            {
            //Categorias
                $categorias = array();
                $aux        = $registro->categorias;
                foreach ($aux as $categoria)
                {
                    $categorias[] = $categoria->nombre;
                }

                $items[$i]["Nombre"]        = $registro->nombre;
                $items[$i]["Telefono"]      = $registro->telefono;
                $items[$i]["E-mail"]        = $registro->email;
                $items[$i]["Categoria(s)"]  = (empty($categorias)) ? "No tiene" : implode(", ", $categorias);
                $items[$i]["Tipo"]          = $registro->tipo->nombre;
                $items[$i]["O/C abiertas"]  = "".$registro->ordenesAbiertas()."";
                $items[$i]["Total a pagar"] = "###";

                $i += 1;
            }

            if(empty($items)){
                return false;
            }

            $objecto        = new stdClass();
            $objecto->count = count($items);
            $objecto->items = $items;

            echo json_encode($objecto);
            exit();
        }

    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla() {
        $this->assets->agregar_js($this->assetsInteresesAsegurados->agregar_js_ocultotabla());

        $this->load->view('tabla');
    }

    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function personaformularioparcial($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/intereses_asegurados/crear.js',
            'public/assets/js/modules/intereses_asegurados/vue.crear.js'
            ));
        if(empty($data))
        {
            $data["campos"] = array();
        }
        //persona
        $data['info']['provincias'] = Catalogos_orm::where('identificador','like','Provincias')->orderBy("orden")->get(array('valor','etiqueta'));
        $data['info']['letras'] = Catalogos_orm::where('identificador','like','Letra')->get(array('id_cat','etiqueta'));
        $data['tipo_identificacion'] = Catalogos_orm::where('identificador','like','tipo_identificacion')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['estado_civil'] = Catalogos_orm::where('identificador','like','Estado Civil')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['sexo'] = Catalogos_orm::where('identificador','like','Sexo')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['estado'] = Catalogos_orm::where('identificador','like','estado')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $this->load->view('formulariopersona', $data);
    }

    public function vehiculoformularioparcial($data = array()) {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/intereses_asegurados/crear_vehiculo.js'
            ));
        if(empty($data))
        {
            $data["campos"] = array();
        }
        //persona
        $data['uso'] = Catalogos_orm::where('identificador','like','uso_vehiculo')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['condicion'] = Catalogos_orm::where('identificador','like','condicion_vehiculo')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $clause['empresa_id'] = $this->empresa_id;
        $clause['tipo'] = 1;
        $data['acreedores'] = $this->acreedoresRep->get($clause);
        $data['estado'] = Catalogos_orm::where('identificador','like','estado')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $this->load->view('formulariovehiculo', $data);
    }

    public function casco_aereoformularioparcial($data = array()) {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/intereses_asegurados/crear_aereo.js'
            ));
        if(empty($data))
        {
            $data["campos"] = array();
        }
        //persona
        $data['uso'] = Catalogos_orm::where('identificador','like','uso_vehiculo')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['condicion'] = Catalogos_orm::where('identificador','like','condicion_vehiculo')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $clause['empresa_id'] = $this->empresa_id;
        $clause['tipo'] = 1;
        $data['acreedores'] = $this->acreedoresRep->get($clause);
        $data['estado'] = Catalogos_orm::where('identificador','like','estado')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $this->load->view('formulariocascoaereo', $data);
    }
    public function casco_maritimoformularioparcial($data = array()) {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/intereses_asegurados/crear_maritimo.js'
            ));
        if(empty($data))
        {
            $data["campos"] = array();
        }
        //persona
        $clause['empresa_id'] = $this->empresa_id;
        $data['tipos'] = Catalogos_orm::where('identificador','like','tipo_maritimo')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['acreedores'] = $this->acreedoresRep->get($clause);
        $data['estado'] = Catalogos_orm::where('identificador','like','estado')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $this->load->view('formulariocascomaritimo', $data);
    }

    public function proyecto_actividadformularioparcial($data = array()) {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/intereses_asegurados/crear_proyecto.js'
            ));
        if(empty($data))
        {
            $data["campos"] = array();
        }
        //persona
        $data['uso'] = Catalogos_orm::where('identificador','like','uso_vehiculo')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['condicion'] = Catalogos_orm::where('identificador','like','condicion_vehiculo')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $clause['empresa_id'] = $this->empresa_id;
        $clause['tipo'] = 1;
        $data['acreedores'] = $this->acreedoresRep->get($clause);
        $data['tipo_propuesta'] = Catalogos_orm::where('identificador','like','tipo_propuesta')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['tipo_fianza'] = Catalogos_orm::where('identificador','like','tipo_propuesta_proyecto')->orderBy("orden")->get(array('valor','etiqueta'));
        $data['validez_fianza'] = Catalogos_orm::where('identificador','like','validez_fianza')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['estado'] = Catalogos_orm::where('identificador','like','estado')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $this->load->view('formularioproyecto', $data);
    }
    public function cargaformularioparcial($data = array()) {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/intereses_asegurados/crear_carga.js'
            ));
        if(empty($data))
        {
            $data["campos"] = array();
        }
        //persona
        $clause['empresa_id'] = $this->empresa_id;
        $clause['tipo'] = 1;
        $data['acreedores'] = $this->acreedoresRep->get($clause);
        $data['tipo_empaque'] = Catalogos_orm::where('identificador','like','tipo_empaque')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['condicion_envio'] = Catalogos_orm::where('identificador','like','condicion_envio')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['medio_transporte'] = Catalogos_orm::where('identificador','like','medio_transporte')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['tipo_obligacion'] = Catalogos_orm::where('identificador','like','tipo_obligacion')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['estado'] = Catalogos_orm::where('identificador','like','estado')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $this->load->view('formulariocarga', $data);
    }
    public function articuloformularioparcial($data = array()) {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/intereses_asegurados/crear_articulo.js'
            ));
        if(empty($data)){
            $data["campos"] = array();
        }
        //persona
        $clause['empresa_id'] = $this->empresa_id;
        $data['condicion'] = Catalogos_orm::where('identificador','like','condicion_vehiculo')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $data['estado'] = Catalogos_orm::where('identificador','like','estado')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $this->load->view('formularioarticulo', $data);
    }
    public function ubicacionformularioparcial($data = array()) {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/intereses_asegurados/crear_ubicacion.js'
            ));
        if(empty($data))
        {
            $data["campos"] = array();
        }
        //persona
        $clause['empresa_id'] = $this->empresa_id;
        $data['acreedores'] = $this->acreedoresRep->get($clause);
        $data['estado'] = Catalogos_orm::where('identificador','like','estado')->orderBy("orden")->get(array('id_cat','etiqueta'));
        $this->load->view('formularioubicacion', $data);
    }

    function crear($formulario=NULL) {

        $data       = array();
        $mensaje    = array();

        if($formulario != NULL){
            $this->assets->agregar_var_js(array(
                "formulario_seleccionado" => $formulario,
                "vista" => "crear"
                ));
        }

        if(!empty($_POST))
        {
            //Se recibe el parámetro y se usa para buscar el controlador del interés asegurado
            $var=ucfirst($formulario)."_orm";
            if($var::create($this->input->post("campo"))){
                redirect(base_url('intereses_asegurados/listar'));
            }else{
                //Establecer el mensaje a mostrar
                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de crear el pedido.";
            }
        }

    	//Introducir mensaje de error al arreglo
    	//para mostrarlo en caso de haber error
        $data["message"] = $mensaje;


        $data["tipos_intereses_asegurados"]= Intereses_asegurados_cat_orm::tipos()->get(array("id_cat", "valor", "etiqueta"));

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
            'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css'
            ));

        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/modules/intereses_asegurados/formulario.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.numeric.extensions.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js'

            ));
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-building"></i> Intereses Asegurados: Crear',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
                1 => array("nombre" => "Intereses Asegurados", "url" => "intereses_asegurados/listar",  "activo" => false),
                2 => array("nombre" => '<b>Crear</b>', "activo" => true)
                ),
            "filtro"    => false,
            "menu"      => array()
            );


        $this->template->agregar_titulo_header('Intereses Asegurados');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    function guardar() {

        if($_POST){
            unset($_POST["campo"]["guardar"]);
            $campo = Util::set_fieldset("campo");
    //formato de identificacion

            if(!empty($campo['letra']) || $campo['letra'] == 0){
                $cedula = $campo['provincia']."-".$campo['letra']."-".$campo['tomo']."-".$campo['asiento'];
                $campo['ruc'] = $cedula;

                unset($campo['pasaporte']);
            }else{
                $campo['ruc'] = $campo['pasaporte'];
                $cedula = $campo['pasaporte'];
            }

            if($campo['identificacion'] == '45'){
             $cedula = $campo['tomo_ruc']."-".$campo['folio']."-".$campo['asiento_ruc']."-".$campo['digito'];
             $campo['ruc'] = $cedula;
         }if(!empty($campo['pasaporte']) || $campo['letra'] == 'PAS'){
            $cedula = $campo['pasaporte'];
            $campo['ruc'] = $cedula;
        }if($campo['identificacion'] == 'RUC'){
            $cedula = $campo['tomo_ruc']."-".$campo['folio']."-".$campo['asiento_ruc']."-".$campo['digito'];
            $campo['ruc'] = $cedula;
        }
        if(!isset($campo['uuid'])){
            $campo['empresa_id'] = $this->empresa_id;
            $campo['fecha_creacion'] = date('Y-m-d H:i:s');
        }
        Capsule::beginTransaction();
        try {
            if(empty($campo['uuid'])){
                $clause['empresa_id'] = $this->empresa_id;
                $total = $this->interesesAseguradosRep->listar_persona($clause);
                $codigo = Util::generar_codigo('PER' , count($total) + 1);
                $campo["numero"] = $codigo;
                $campo['identificacion'] = $campo['ruc'];

                $intereses_asegurados = $this->PersonasModel->create($campo);
    //guardar tabla principal
                $fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
                $fieldset['empresa_id'] = $this->empresa_id;
                $fieldset['interesestable_type'] = 5;
                $fieldset['interesestable_id'] = $intereses_asegurados->id;
                $fieldset['numero'] = $codigo;
                $fieldset['identificacion'] = $intereses_asegurados->identificacion;
                $fieldset['estado'] = $intereses_asegurados->estado;
                $intereses_asegurados->interesesAsegurados()->create($fieldset);
    //Subir documentos
                if(!empty($_FILES['file'])){
                    $vehiculo_id = $intereses_asegurados->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->PersonasModel->find($vehiculo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }else{
                $personaObj  = $this->PersonasModel->find($campo['uuid']);
                unset($campo['uuid']);
                unset($campo['ruc']);
                unset($campo['provincia']);
                unset($campo['letra']);
                unset($campo['tomo']);
                unset($campo['asiento']);
                $campo['identificacion'] = $cedula;
                $personaObj->update($campo);
    //Tabla principal
                $intereses_asegurados = $this->AseguradosModel->findByInteresesTable($personaObj->id, $personaObj->tipo_id);
                $fieldset['identificacion'] = $personaObj->identificacion;
                $fieldset['estado'] = $personaObj->estado;
                $intereses_asegurados->update($fieldset);
    //Subir documentos
                if(!empty($_FILES['file'])){
                    $vehiculo_id = $personaObj->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->PersonasModel->find($vehiculo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }
            Capsule::commit();
        }catch(ValidationException $e){
            log_message('error', $e);
            Capsule::rollback();
        }

        if(!is_null($intereses_asegurados)){
            $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');
        }else{
            $mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
        }


    }else{
        $mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }

    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('intereses_asegurados/listar'));
}
function guardar_vehiculo() {
    if($_POST){
        unset($_POST["campo"]["guardar"]);
        $campo = Util::set_fieldset("campo");
        if(!isset($campo['uuid'])){
            $campo['empresa_id'] = $this->empresa_id;
        }
        Capsule::beginTransaction();
        try {
            if(empty($campo['uuid'])){
                $campo["uuid_vehiculo"] = Capsule::raw("ORDER_UUID(uuid())");
                $clause['empresa_id'] = $this->empresa_id;
                $total = $this->interesesAseguradosRep->listar_vehiculo($clause);

                $codigo = Util::generar_codigo('VEH' , count($total) + 1);
                $campo["numero"] = $codigo;
                $vehiculo = $this->VehiculoModel->create($campo);
    //guardar tabla principal
                $fieldset['uuid_intereses'] = $vehiculo->uuid_vehiculo;
                $fieldset['empresa_id'] = $vehiculo->empresa_id;
                $fieldset['interesestable_type'] = 8;
                $fieldset['interesestable_id'] = $vehiculo->id;
                $fieldset['numero'] = $codigo;
                $fieldset['identificacion'] = $vehiculo->chasis;
                $fieldset['estado'] = $vehiculo->estado;
                $vehiculo->interesesAsegurados()->create($fieldset);
    //Subir documentos
                if(!empty($_FILES['file'])){

                    $vehiculo_id = $vehiculo->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->VehiculoModel->find($vehiculo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }else{
                $vehiculoObj  = $this->VehiculoModel->find($campo['uuid']);
                unset($campo['uuid']);
                unset($campo['ruc']);
                unset($campo['provincia']);
                unset($campo['letra']);
                unset($campo['tomo']);
                unset($campo['asiento']);
                $campo['identificacion'] = $vehiculoObj->chasis;
                $vehiculoObj->update($campo);
    //Tabla principal
                $intereses_asegurados = $this->AseguradosModel->findByInteresesTable($vehiculoObj->id, $vehiculoObj->tipo_id);
                $fieldset['identificacion'] = $vehiculoObj->chasis;
                $fieldset['estado'] = $vehiculoObj->estado;
                $intereses_asegurados->update($fieldset);
    //Subir documentos
                if(!empty($_FILES['file'])){
                    $vehiculo_id = $vehiculoObj->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->VehiculoModel->find($vehiculo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }
            Capsule::commit();
        }catch(ValidationException $e){
            log_message('error', $e);
            Capsule::rollback();
        }

        if(!is_null($vehiculo)){
            $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

        }else{
            $mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
        }


    }else{
        $mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }

    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('intereses_asegurados/listar'));

}
function guardar_aereo() {

    if($_POST){
        unset($_POST["campo"]["guardar"]);
        $campo = Util::set_fieldset("campo");
        if(!isset($campo['uuid'])){
            $campo['empresa_id'] = $this->empresa_id;
        }
        Capsule::beginTransaction();
        try {
            if(empty($campo['uuid'])){
                $clause['empresa_id'] = $this->empresa_id;
                $total = $this->interesesAseguradosRep->listar_aereo($clause);
                $codigo = Util::generar_codigo('CAE' , count($total) + 1);
                $campo["numero"] = $codigo;
                $casco_aereo = $this->AereoModel->create($campo);
    //guardar tabla principal
                $fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
                $fieldset['empresa_id'] = $casco_aereo->empresa_id;
                $fieldset['interesestable_type'] = 3;
                $fieldset['interesestable_id'] = $casco_aereo->id;
                $fieldset['numero'] = $codigo;
                $fieldset['identificacion'] = $casco_aereo->serie;
                $fieldset['estado'] = $campo['estado'];
                $casco_aereo->interesesAsegurados()->create($fieldset);
    //Subir documentos
                if(!empty($_FILES['file'])){

                    $aereo_id = $casco_aereo->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->AereoModel->find($aereo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }else{
                $aereoObj  = $this->AereoModel->find($campo['uuid']);
                unset($campo['uuid']);
                $aereoObj->update($campo);
    //Tabla principal
                $intereses_asegurados = $this->AseguradosModel->findByInteresesTable($aereoObj->id, $aereoObj->tipo_id);
                $fieldset['identificacion'] = $aereoObj->serie;
                $fieldset['estado'] = $aereoObj->estado;
                $intereses_asegurados->update($fieldset);

    //Subir documentos
                if(!empty($_FILES['file'])){
                    $vehiculo_id = $aereoObj->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->AereoModel->find($vehiculo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }
            Capsule::commit();
        }catch(ValidationException $e){
            log_message('error', $e);
            Capsule::rollback();
        }

        if(!is_null($casco_aereo) || !is_null($aereoObj)){
            $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

        }else{
            $mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
        }


    }else{
        $mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }

    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('intereses_asegurados/listar'));

}

function guardar_maritimo() {

    if($_POST){
        unset($_POST["campo"]["guardar"]);
        $campo = Util::set_fieldset("campo");
        if(!isset($campo['uuid'])){
            $campo['empresa_id'] = $this->empresa_id;
        }
        Capsule::beginTransaction();
        try {
            if(empty($campo['uuid'])){
                $clause['empresa_id'] = $this->empresa_id;
                $total = $this->interesesAseguradosRep->listar_maritimo($clause);
                $codigo = Util::generar_codigo('CMA' , count($total) + 1);
                $campo["numero"] = $codigo;
                $casco_maritimo = $this->MaritimoModel->create($campo);
    //guardar tabla principal
                $fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
                $fieldset['empresa_id'] = $casco_maritimo->empresa_id;
                $fieldset['interesestable_type'] = 4;
                $fieldset['interesestable_id'] = $casco_maritimo->id;
                $fieldset['numero'] = $codigo;
                $fieldset['identificacion'] = $casco_maritimo->serie;
                $fieldset['estado'] = $campo['estado'];
                $casco_maritimo->interesesAsegurados()->create($fieldset);
    //Subir documentos
                if(!empty($_FILES['file'])){

                    $aereo_id = $casco_maritimo->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->MaritimoModel->find($aereo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }else{
                $maritimoObj  = $this->MaritimoModel->find($campo['uuid']);
                unset($campo['uuid']);
                $maritimoObj->update($campo);
    //Tabla principal
                $intereses_asegurados = $this->AseguradosModel->findByInteresesTable($maritimoObj->id, $maritimoObj->tipo_id);
                $fieldset['identificacion'] = $maritimoObj->serie;
                $fieldset['estado'] = $maritimoObj->estado;
                $intereses_asegurados->update($fieldset);
    //Subir documentos
                if(!empty($_FILES['file'])){
                    $vehiculo_id = $maritimoObj->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->MaritimoModel->find($vehiculo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }
            Capsule::commit();
        }catch(ValidationException $e){
            log_message('error', $e);
            Capsule::rollback();
        }

        if(!is_null($casco_maritimo) || !is_null($maritimoObj)){
            $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

        }else{
            $mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
        }


    }else{
        $mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }

    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('intereses_asegurados/listar'));

}

function guardar_proyecto() {
    if($_POST){
        unset($_POST["campo"]["guardar"]);
        $campo = Util::set_fieldset("campo");
        if(!isset($campo['uuid'])){
            $campo['empresa_id'] = $this->empresa_id;
        }
        Capsule::beginTransaction();
        try {
            $campo['acreedor'] = !empty($campo['acreedor_opcional']) ? $campo['acreedor_opcional'] : $campo['acreedor'];
            $campo['tipo_propuesta'] = !empty($campo['tipo_propuesta_opcional']) ? $campo['tipo_propuesta_opcional'] : $campo['tipo_propuesta'];
            $campo['validez_fianza'] = !empty($campo['validez_fianza_opcional']) ? $campo['validez_fianza_opcional'] : $campo['validez_fianza'];
            if(empty($campo['uuid'])){
                $clause['empresa_id'] = $this->empresa_id;
                $total = $this->interesesAseguradosRep->listar_proyecto($clause);
                $codigo = Util::generar_codigo('PRO' , count($total) + 1);
                $campo["numero"] = $codigo;
                $proyecto_actividad = $this->ProyectoModel->create($campo);
    //guardar tabla principal
                $fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
                $fieldset['empresa_id'] = $proyecto_actividad->empresa_id;
                $fieldset['interesestable_type'] = 6;
                $fieldset['interesestable_id'] = $proyecto_actividad->id;
                $fieldset['numero'] = $codigo;
                $fieldset['identificacion'] = $proyecto_actividad->no_orden;
                $fieldset['estado'] = $campo['estado'];
                $proyecto_actividad->interesesAsegurados()->create($fieldset);
    //Subir documentos
                if(!empty($_FILES['file'])){
                    $proyecto_id = $proyecto_actividad->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->ProyectoModel->find($proyecto_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }else{
                $proyectoObj  = $this->ProyectoModel->find($campo['uuid']);
                unset($campo['uuid']);
                $proyectoObj->update($campo);
    //Tabla principal
                $intereses_asegurados = $this->AseguradosModel->findByInteresesTable($proyectoObj->id, $proyectoObj->tipo_id);
                $fieldset['identificacion'] = $proyectoObj->nombre_proyecto;
                $fieldset['estado'] = $proyectoObj->estado;
                $intereses_asegurados->update($fieldset);
    //Subir documentos
                if(!empty($_FILES['file'])){
                    $vehiculo_id = $proyectoObj->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->ProyectoModel->find($vehiculo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }
            Capsule::commit();
        }catch(ValidationException $e){
            log_message('error', $e);
            Capsule::rollback();
        }

        if(!is_null($proyecto_actividad) || !is_null($proyectoObj)){
            $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

        }else{
            $mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
        }


    }else{
        $mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }

    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('intereses_asegurados/listar'));

}
function guardar_carga() {

    if($_POST){
        unset($_POST["campo"]["guardar"]);
        $campo = Util::set_fieldset("campo");
        if(!isset($campo['uuid'])){
            $campo['empresa_id'] = $this->empresa_id;
        }
        Capsule::beginTransaction();
        try {
            $campo['acreedor'] = !empty($campo['acreedor_opcional']) ? $campo['acreedor_opcional'] : $campo['acreedor'];
            $campo['tipo_obligacion'] = !empty($campo['tipo_obligacion_opcional']) ? $campo['tipo_obligacion_opcional'] : $campo['tipo_obligacion'];
            if(empty($campo['uuid'])){
                $clause['empresa_id'] = $this->empresa_id;
                $total = $this->interesesAseguradosRep->listar_carga($clause);
                $codigo = Util::generar_codigo('CGA' , count($total) + 1);
                $campo["numero"] = $codigo;
                $campo["fecha_despacho"] = !empty($campo['fecha_despacho']) ? $campo['fecha_despacho'] : NULL;
                $campo["fecha_arribo"] = !empty($campo['fecha_arribo']) ? $campo['fecha_arribo'] : NULL;
                $carga = $this->CargaModel->create($campo);
    //guardar tabla principal
                $fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
                $fieldset['empresa_id'] = $carga->empresa_id;
                $fieldset['interesestable_type'] = 2;
                $fieldset['interesestable_id'] = $carga->id;
                $fieldset['numero'] = $codigo;
                $fieldset['identificacion'] = $carga->no_liquidacion;
                $fieldset['estado'] = $campo['estado'];
                $carga->interesesAsegurados()->create($fieldset);
    //Subir documentos
                if(!empty($_FILES['file'])){
                    $carga_id = $carga->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->CargaModel->find($carga_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }else{
	//dd($_POST);
                $cargaObj  = $this->CargaModel->find($campo['uuid']);
                unset($campo['uuid']);
                if(!empty($campo['fecha_despacho']) || !empty($campo['fecha_arribo'])){
                 unset($campo['fecha_despacho']);
             }
             $campo["fecha_arribo"] = !empty($campo['fecha_arribo']) ? $campo['fecha_arribo'] : NULL;
             $cargaObj->update($campo);
    //Tabla principal
             $intereses_asegurados = $this->AseguradosModel->findByInteresesTable($cargaObj->id, $cargaObj->tipo_id);
             $fieldset['identificacion'] = $cargaObj->no_liquidacion;
             $fieldset['estado'] = $cargaObj->estado;
             $intereses_asegurados->update($fieldset);
    //Subir documentos
             if(!empty($_FILES['file'])){
                $vehiculo_id = $cargaObj->id;
                unset($_POST["campo"]);
                $modeloInstancia = $this->CargaModel->find($vehiculo_id);
                $this->documentos->subir($modeloInstancia);
            }
        }
        Capsule::commit();
    }catch(ValidationException $e){
        log_message('error', $e);
        Capsule::rollback();
    }

    if(!is_null($carga) || !is_null($cargaObj)){
        $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

    }else{
        $mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }


}else{
    $mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
}

$this->session->set_flashdata('mensaje', $mensaje);
redirect(base_url('intereses_asegurados/listar'));

}

function guardar_articulo() {

    if($_POST){
        unset($_POST["campo"]["guardar"]);
        $campo = Util::set_fieldset("campo");
        if(!isset($campo['uuid'])){
            $campo['empresa_id'] = $this->empresa_id;
        }
        Capsule::beginTransaction();
        try {
            if(empty($campo['uuid'])){
                $clause['empresa_id'] = $this->empresa_id;
                $total = $this->interesesAseguradosRep->listar_articulo($clause);
                $codigo = Util::generar_codigo('ART' , count($total) + 1);
                $campo["numero"] = $codigo;
                $articulo = $this->ArticulomoModel->create($campo);
                //guardar tabla principal
                $fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
                $fieldset['empresa_id'] = $articulo->empresa_id;
                $fieldset['interesestable_type'] = 1;
                $fieldset['interesestable_id'] = $articulo->id;
                $fieldset['numero'] = $codigo;
                $fieldset['identificacion'] = $articulo->nombre;
                $fieldset['estado'] = $campo['estado'];
                $articulo->interesesAsegurados()->create($fieldset);
                //Subir documentos
                if(!empty($_FILES['file'])){

                    $articulo_id = $articulo->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->ArticulomoModel->find($articulo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }else{
                $articuloObj  = $this->ArticulomoModel->find($campo['uuid']);
                unset($campo['uuid']);
                $articuloObj->update($campo);
                //Tabla principal
                $intereses_asegurados = $this->AseguradosModel->findByInteresesTable($articuloObj->id, $articuloObj->tipo_id);
                $fieldset['identificacion'] = $articuloObj->nombre;
                $fieldset['estado'] = $articuloObj->estado;
                $intereses_asegurados->update($fieldset);
                //Subir documentos
                if(!empty($_FILES['file'])){
                    $vehiculo_id = $articuloObj->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->ArticulomoModel->find($vehiculo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }
            Capsule::commit();
        }catch(ValidationException $e){
            log_message('error', $e);
            Capsule::rollback();
        }

        if(!is_null($articulo) || !is_null($articuloObj)){
            $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

        }else{
            $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
        }


    }else{
        $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }

    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('intereses_asegurados/listar'));

}

function guardar_ubicacion() {

    if($_POST){
        unset($_POST["campo"]["guardar"]);
        $campo = Util::set_fieldset("campo");
        if(!isset($campo['uuid'])){
            $campo['empresa_id'] = $this->empresa_id;
        }
        Capsule::beginTransaction();
        try {
            if(empty($campo['uuid'])){
                $ubicacion_obj = $this->interesesAseguradosRep->identificacion_ubicacion($campo['direccion']);
                if(empty($ubicacion_obj)){
                    $clause['empresa_id'] = $this->empresa_id;
                    $total = $this->interesesAseguradosRep->listar_ubicacion($clause);
                    $codigo = Util::generar_codigo('UBI' , count($total) + 1);
                    $campo["numero"] = $codigo;
                    $ubicacion = $this->UbicacionmoModel->create($campo);
                    //guardar tabla principal
                    $fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
                    $fieldset['empresa_id'] = $ubicacion->empresa_id;
                    $fieldset['interesestable_type'] = 7;
                    $fieldset['interesestable_id'] = $ubicacion->id;
                    $fieldset['numero'] = $codigo;
                    $fieldset['identificacion'] = $ubicacion->direccion;
                    $fieldset['estado'] = $campo['estado'];
                    $ubicacion->interesesAsegurados()->create($fieldset);
                    //Subir documentos
                    if(!empty($_FILES['file'])){

                        $ubicacion_id = $ubicacion->id;
                        unset($_POST["campo"]);
                        $modeloInstancia = $this->UbicacionmoModel->find($ubicacion_id);
                        $this->documentos->subir($modeloInstancia);
                    }
                }else{
                    $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
                    $this->session->set_flashdata('mensaje', $mensaje);
                    redirect(base_url('intereses_asegurados/listar'));
                }

            }else{
                $ubicacionObj  = $this->UbicacionmoModel->find($campo['uuid']);
                unset($campo['uuid']);
                $ubicacionObj->update($campo);
                //Tabla principal
                $intereses_asegurados = $this->AseguradosModel->findByInteresesTable($ubicacionObj->id, $ubicacionObj->tipo_id);
                $fieldset['identificacion'] = $ubicacionObj->direccion;
                $fieldset['estado'] = $ubicacionObj->estado;
                $intereses_asegurados->update($fieldset);
                //Subir documentos
                if(!empty($_FILES['file'])){
                    $vehiculo_id = $ubicacionObj->id;
                    unset($_POST["campo"]);
                    $modeloInstancia = $this->UbicacionmoModel->find($vehiculo_id);
                    $this->documentos->subir($modeloInstancia);
                }
            }
            Capsule::commit();
        }catch(ValidationException $e){
            log_message('error', $e);
            Capsule::rollback();
        }

        if(!is_null($ubicacion) || !is_null($ubicacionObj)){
            $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

        }else{
            $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
        }


    }else{
        $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }

    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('intereses_asegurados/listar'));

}


function editar($uuid=NULL, $formulario=NULL) {
    if(!$uuid){
        echo "Error.";
        exit();
    }
    $data       = array();
    $mensaje    = array();

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
        'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css'
        ));

    $this->assets->agregar_js(array(
        'public/assets/js/default/jquery-ui.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        'public/assets/js/plugins/jquery/jquery.sticky.js',
        'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
        'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
        'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
        'public/assets/js/plugins/jquery/chosen.jquery.min.js',
        'public/assets/js/default/lodash.min.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/bootstrap/daterangepicker.js',
        'public/assets/js/default/toast.controller.js',
        'public/assets/js/modules/intereses_asegurados/formulario.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.numeric.extensions.js',
        'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
        'public/assets/js/default/vue.js',

        ));

        //Cargando el registro
    $intereses_asegurados  = $this->AseguradosModel->where("uuid_intereses", "=", hex2bin(strtolower($uuid)))->first();
    if(!is_null($intereses_asegurados->persona) && $intereses_asegurados->interesestable_type == 5) {
        $intereses_data = $intereses_asegurados->persona;
        $identificaciones = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $intereses_asegurados->identificacion) ? "111" : "112";
    }
    if(!is_null($intereses_asegurados->articulo) && $intereses_asegurados->interesestable_type == 1) {
        $intereses_data = $intereses_asegurados->articulo;
    }
    if(!is_null($intereses_asegurados->ubicacion) && $intereses_asegurados->interesestable_type == 7) {
        $intereses_data = $intereses_asegurados->ubicacion;
    }
    if(!is_null($intereses_asegurados->carga) && $intereses_asegurados->interesestable_type == 2) {
        $intereses_data = $intereses_asegurados->carga;
    }
    if(!is_null($intereses_asegurados->vehiculo) && $intereses_asegurados->interesestable_type == 8) {
        $intereses_data = $intereses_asegurados->vehiculo;
    }
    if(!is_null($intereses_asegurados->casco_aereo) && $intereses_asegurados->interesestable_type == 3) {
        $intereses_data = $intereses_asegurados->casco_aereo;
    }
    if(!is_null($intereses_asegurados->casco_maritimo) && $intereses_asegurados->interesestable_type == 4) {
        $intereses_data = $intereses_asegurados->casco_maritimo;
    }
    if(!is_null($intereses_asegurados->proyecto_actividad) && $intereses_asegurados->interesestable_type == 6) {
        $intereses_data = $intereses_asegurados->proyecto_actividad;
    }

    $this->assets->agregar_var_js(array(
        "formulario_seleccionado" => $intereses_asegurados->tipo->valor,
        "identificaciones" => !empty($identificaciones) ? $identificaciones : '',
        "data" => $intereses_data,
        "vista" => "ver",
        "intereses_asegurados_id_" . $intereses_asegurados->tipo->valor => $intereses_data->id,
        "permiso_editar" => $this->auth->has_permission('ver__editarInteresesAsegurados', 'intereses_asegurados/ver/(:any)') ? 'true' : 'false',
        ));
    	//Introducir mensaje de error al arreglo
    	//para mostrarlo en caso de haber error
    $data["message"] = $mensaje;

    $breadcrumb = array(
        "titulo" => '<i class="fa fa-building"></i> Intereses Asegurados: '.$intereses_asegurados->numero,
        "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
            1 => array("nombre" => "Intereses Asegurados", "url" => "intereses_asegurados/listar",  "activo" => false),
            2 => array("nombre" => $intereses_asegurados->numero, "activo" => true)
            ),
        "filtro"    => false,
        "menu"      => array()
        );
    $data["data"] = $intereses_data;
    $data["tipos_intereses_asegurados"]= Intereses_asegurados_cat_orm::tipos()->get(array("id_cat", "valor", "etiqueta"));

    $this->template->agregar_titulo_header('Intereses Asegurados');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);
}

function documentos_campos() {

 return array(
     array(
      "type"		=> "text",
      "name" 		=> "nombre_",
      "id" 		=> "nombre_documento",
      "model" 	=> "campos.nombre_documento",
      "class"		=> "form-control",
      "ng-model" 	=> "campos.nombre_documento",
      "label"		=> "Nombre del documento"
      ));
}

function ajax_guardar_documentos() {
 if(empty($_POST)){
  return false;
}

$intereses_id = $this->input->post('intereses_id', true);
$intereses_type = $this->input->post('intereses_type', true);

if($intereses_type == 1){
    $modeloInstancia = $this->ArticulomoModel->find($intereses_id);
}
if($intereses_type == 2){
    $modeloInstancia = $this->CargaModel->find($intereses_id);
}
if($intereses_type == 3){
    $modeloInstancia = $this->AereoModel->find($intereses_id);
}
if($intereses_type == 4){
    $modeloInstancia = $this->MaritimoModel->find($intereses_id);
}
if($intereses_type == 5){
    $modeloInstancia = $this->PersonasModel->find($intereses_id);
}
if($intereses_type == 6){
    $modeloInstancia = $this->ProyectoModel->find($intereses_id);
}
if($intereses_type == 7){
    $modeloInstancia = $this->UbicacionmoModel->find($intereses_id);
}
if($intereses_type == 8){
    $modeloInstancia = $this->VehiculoModel->find($intereses_id);
}
$this->documentos->subir($modeloInstancia);
}
public function formularioModal($data=NULL) {

  $this->assets->agregar_js(array(
      		//'public/assets/js/modules/documentos/formulario.controller.js'
   ));

  $this->load->view('formularioModalDocumento', $data);
}

}
