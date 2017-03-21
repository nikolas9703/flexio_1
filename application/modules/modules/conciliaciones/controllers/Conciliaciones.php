<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Conciliaciones
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */

//utilities
use Carbon\Carbon                       as Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;

//repositories
use Flexio\Modulo\Conciliaciones\Repository\ConciliacionesRepository as conciliacionesRep;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository as cuentasRep;
use Flexio\Modulo\EntradaManuales\Repository\TransaccionesRepository as transaccionesRep;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaBancoRepository as CuentaBanco;
//use Flexio\Modulo\SubContratos\Repository\SubContratoRepository        as SubContratoRepository;

class Conciliaciones extends CRM_Controller
{
    /**
     * Atributos
     */
    private $empresa_id;
    private $empresaObj;
    protected $conciliacionesRep;
    protected $cuentasRep;
    protected $transaccionesRep;
    protected $cuenta_banco;
    //protected $subcontratosRepositorio;
    /**
     * Método constructor
     */
    public function __construct()
    {
        parent::__construct();
        //cargar los modelos
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Usuario_orm');

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $empresaObj         = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj   = $empresaObj->findByUuid($uuid_empresa);
        $this->empresa_id   = $this->empresaObj->id;

        //repositories
        $this->conciliacionesRep    = new conciliacionesRep();
        $this->cuentasRep           = new cuentasRep();
        $this->transaccionesRep     = new transaccionesRep();
        $this->cuenta_banco = new CuentaBanco;
        //$this->subcontratosRepositorio = new SubContratoRepository;
    }

    public function _getRuta()
    {
        return [
            0   => [
                "nombre" => "Contabilidad",
                "activo" => false
                ],
            1 => [
                "nombre" => '<b>Conciliaciones</b>',
                "activo" => true
            ]
        ];
    }


    /**
     * Método de la vista de los subcontratos
     */
    public function listar()
    {
        $data = array();
        $mensaje ='';
        if(!$this->auth->has_permission('acceso'))
        {
            redirect ( '/' );
        }
        if(!empty($this->session->flashdata('mensaje')))
        {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        }
        $this->_Css();
        $this->_js();

        $breadcrumb = array( "titulo" => '<i class="fa fa-calculator"></i> Conciliaci&oacute;n bancaria',
            "ruta" => $this->_getRuta(),
            "menu" => [
                "nombre"    => "Crear",
                "url"       => "conciliaciones/crear",
                "opciones"  => array("#exportarListaConciliaciones" => "Exportar")
            ]
        );
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));

        $clause = array('empresa_id' => $this->empresa_id);
        $clause["transaccionales"]  = true;
        $clause["padre_id"]         = '6';//Cuentas de banco 1.1.2
        $data['cuentas'] = $this->cuentasRep->get($clause);

        $this->template->agregar_titulo_header('Conciliaci&oacute;n bancaria');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ocultotabla()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/conciliaciones/tabla.js'
        ));
        $this->load->view('tabla');
    }

    /**
     * Método listar los registros de los subcontratos en ocultotabla()
     */
    public function ajax_listar()
    {
        if(!$this->input->is_ajax_request())
        {
            return false;
        }

        $clause                 = $this->input->post();
        $clause["empresa_id"]   = $this->empresa_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->conciliacionesRep->count($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $conciliaciones = $this->conciliacionesRep->get($clause ,$sidx, $sord, $limit, $start);

        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;

        if($count)
        {
            foreach ($conciliaciones as $i => $row)
            {
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_conciliacion .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="'. base_url('conciliaciones/ver/'. $row->uuid_conciliacion) .'" data-id="'. $row->uuid_conciliacion .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';

                $response->rows[$i]["id"] = $row->uuid_conciliacion;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_conciliacion,
                    '<a href="'. base_url('conciliaciones/ver/'. $row->uuid_conciliacion) .'" class="link">'.$row->codigo.'</a>',
                    $row->cuenta->nombre_completo,
                    $row->balance_banco_label,
                    $row->balance_flexio_label,
                    $row->diferencia_label,
                    $row->rango_fecha,
                    $link_option,
                    $hidden_options
                );
            }
        }

        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }

    public function ajax_get_transacciones()
    {
        if(!$this->input->is_ajax_request())
        {
            return false;
        }
        
        $clause                 = $this->input->post();
        $clause["empresa_id"]   = $this->empresa_id;

        $clause_ultima_conciliacion =
        [
          "empresa_id"   => $this->empresa_id,
          "cuenta_id"   =>   $clause["cuenta_id"]
        ];

        
        if($clause["vista"] == "crear"){$clause["no_conciliados"] = true;}
        
        $transacciones          = $this->transaccionesRep->getCollectionTransacciones($this->transaccionesRep->get($clause));

        $ultima_conciliacion    = $this->conciliacionesRep->get($clause_ultima_conciliacion, 'fecha_fin', 'desc', 1, 0);
        $balance_flexio         = count($ultima_conciliacion) ? $ultima_conciliacion[0]->balance_flexio : 0;

        $registro = [
            "balance_flexio"    => $balance_flexio,
            "transacciones"     => $transacciones
        ];
        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($registro, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }



    /**
     * Método para crear un nuevo subcontrato
     */
    public function crear()
    {
        $acceso     = 1;
        $mensaje    = $data = [];
        if(!$this->auth->has_permission('acceso','conciliaciones/crear'))
        {
            $acceso     = 0;
            $mensaje    = array(
                'estado'  => 500,
                'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>',
                'clase'   => 'alert-danger'
            );
        }
        $this->_Css();
        $this->_js();
        $this->assets->agregar_css([
          'public/assets/css/plugins/iCheck/custom.css',
          'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
          'public/assets/css/plugins/bootstrap/select2.min.css',
        ]);
        $this->assets->agregar_js(array(
            //'public/assets/js/plugins/iCheck/icheck.min.js',
            'public/assets/js/default/vue/filters/numeros.js',
            'public/assets/js/default/vue/directives/inputs.js',
            'public/assets/js/default/vue/directives/icheck.js',
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/modules/conciliaciones/formulario_crear.js',
        ));
        $this->assets->agregar_var_js(array(
            "vista"     => 'crear',
            "acceso"    => $acceso
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Conciliaci&oacute;n bancaria: Crear',
            "ruta" => array(
                0 => [
                    "nombre" => "Contabilidad",
                    "activo" => false
                ],
                1 => [
                    "nombre" => '<b>Conciliaci&oacute;n bancaria</b>',
                    "activo" => true,
                    "url" => 'conciliaciones/listar'
                ],
                2 => [
                    "nombre" => '<b>Crear</b>',
                    "activo" => true
                ]
            ),
            /*"menu" => [
                "nombre" => "Acci&oacute;n",
                "url" => "#",
                "opciones" => array('/subcontratos/agregar_adenda/'.$subcontrato->uuid_subcontrato => 'Crear Adenda',
                    '#exportar_adenda'=>'Exportar Adenda')
            ]*/
        );
        $this->template->agregar_titulo_header('Conciliaci&oacute;n bancaria: Crear');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    /**
     * Método del formulario de subcontrato
     */
    public function ocultoformulario($info = [])
    {
        $data = $clause = [];

        $clause["empresa_id"]       = $this->empresa_id;
        $clause["transaccionales"]  = true;
        //$clause["padre_id"]         = '6';//Cuentas de banco 1.1.2



        $empresa = ['empresa_id' => $this->empresa_id];

        $data["cuenta_bancos"] ="";
        if($this->cuenta_banco->tieneCuenta($empresa)) {
          $data["cuenta_bancos"] = $this->cuenta_banco->getAll($empresa);
          $data["cuenta_bancos"]->load("cuenta");
          }


        $ultima_conciliacion = $this->conciliacionesRep->get($clause, 'fecha_fin', 'desc', 1, 0);


        $balance_flexio = count($ultima_conciliacion) ? $ultima_conciliacion[0]->balance_flexio : 0;
        $this->assets->agregar_var_js(array(
            "cuentas_bancos"    => $data["cuenta_bancos"],//$this->cuentasRep->get($clause),
            "balance_flexio"    => $balance_flexio
        ));

        $data['info'] = $info;

        $this->load->view('formulario', $data);
    }

    public function ocultoformulario_balance($info = [])
    {
        $data = $clause = [];

        $data['info'] = $info;

        $this->load->view('formulario_balance', $data);
    }

    public function ocultoformulario_tabla($info = [])
    {
        $data = $clause = [];

        $data['info'] = $info;

        $this->load->view('formulario_tabla', $data);
    }



    /**
     * Método para mostrar el subcontrato
     */
    public function ver($uuid = null)
    {

        $acceso = 1;
        $mensaje = array();
        $data = array();
        $conciliacion = $this->conciliacionesRep->findByUuid($uuid);
        //dd($subcontrato);

        if(!$this->auth->has_permission('acceso','conciliaciones/ver/(:any)') && !is_null($conciliacion))
        {
            // No, tiene permiso
            $acceso = 0;
            $mensaje = array(
                'estado'  => 500,
                'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>',
                'clase'   => 'alert-danger'
            );
        }
        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/iCheck/icheck.min.js',
            'public/assets/js/default/vue/filters/numeros.js',
            'public/assets/js/default/vue/directives/inputs.js',
            //'public/assets/js/default/vue/directives/icheck.js',
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/modules/conciliaciones/formulario_crear.js',
        ));

        $this->assets->agregar_var_js(array(
            "vista"        => 'ver',
            "acceso"       => $acceso,
            "conciliacion"  => $uuid
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Conciliación bancaria: Detalle ' .$conciliacion->codigo,
            "ruta" => array(
                0 => [
                    "nombre" => "<b>contabilidad</b>",
                    "activo" => false
                ],
                1 => [
                    "nombre" => '<b>Conciliaci&oacute;n bancaria</b>',
                    "activo" => true,
                    "url" => 'conciliaciones/listar'
                ],
                2 => [
                    "nombre" => 'ver',
                    "activo" => true
                ]
            ),
            /*"menu" => [
                "nombre" => "Acci&oacute;n",
                "url" => "#",
                "opciones" => array('/subcontratos/agregar_adenda/'.$subcontrato->uuid_subcontrato => 'Crear Adenda',
                    '#exportar_adenda'=>'Exportar Adenda')
            ]*/
        );

        $this->template->agregar_titulo_header('Conciliacion Bancaria');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }



    function ajax_conciliacion(){
        if (!$this->input->is_ajax_request()) {
         return false;
        }
        $uuid =$this->input->post('uuid');
        $conciliacion = $this->conciliacionesRep->findByUuid($uuid);
        if(is_null($conciliacion)){
            return [];
        }
        $conciliacion->load('balance_transacciones');
        $transaciones = $conciliacion->balance_transacciones->transform(function($map){
            return [
                'conciliacion_id' => $map->conciliacion_id,
                'balance_verificado' => ['monto' => $map->balance_verificado,'checked'=>true,'order'=> $map->order],
                'color' => $map->color,
                'monto' => $map->monto,
                'numero' => $map->codigo,
                'transaccion' => $map->nombre,
                'fecha' => $map->created_at,
                'id'=> $map->id
            ];
        });
        //$conciliacion->balance_transacciones = $transaciones;
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($conciliacion))->_display();
        exit;
    }





    /**
     * Método para generar código del subcontrato
     */
    private function _generar_codigo()
    {
        $clause_empresa = ['empresa_id' => $this->empresa_id];
        $total = $this->conciliacionesRep->count($clause_empresa);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('CNB'.$year,$total + 1);
        return $codigo;
    }



    /**
     * Método para guardar subcontratos
     */
    public function guardar()
    {
        if($_POST)
        {
            $array_conciliacion                         = Util::set_fieldset("campo");
            $array_conciliacion["empresa_id"]           = $this->empresa_id;
            $array_conciliacion["codigo"]               = $this->_generar_codigo();
            $array_conciliacion["created_by"]           = 1;


            $transacciones = [];
            $j = 0;
            foreach ($this->input->post("transacciones") as $transaccion)
            {
                $transacciones[$j]                  = Util::set_fieldset("transacciones", $j);
                $transacciones[$j]['empresa_id']    = $this->empresa_id;
                $j++;
            }
            $create = array(
                'conciliacion'  => $array_conciliacion,
                'transacciones' => $transacciones
            );
            $conciliacion = Capsule::transaction(function() use ($create){
                try{
                    return $this->conciliacionesRep->create($create);
                }catch(Illuminate\Database\QueryException $e){
                    log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
                }
            });
            if(!is_null($conciliacion)){
                $mensaje = array(
                    'estado' => 200,
                    'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$conciliacion->codigo);
            }else{
                $mensaje = array(
                    'estado'=>500,
                    'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('conciliaciones/listar'));
        }
    }

    /**
     * Método para guardar adendas
     */


    public function ajax_subcontrato_info()
    {
        $uuid = $tipo = $this->input->post('uuid');
        $subcontrato = $this->subcontratosRepositorio->findByUuid($uuid);
        $subcontrato->load('proveedor');
        $subcontrato->toArray();

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($subcontrato))->_display();
        exit;
    }

    /**
     * Método para cargar los Js
     * @return array
     */
    private function _Css()
    {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'public/assets/css/modules/stylesheets/subcontratos.css',
            'public/assets/css/plugins/iCheck/custom.css',
        ));
    }

    /**
     * Método para cargar los Js
     * @return array
     */
    private function _js()
    {
        $this->assets->agregar_js(array(

            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            //'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            //'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            //'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            //'public/assets/js/default/lodash.min.js',
            //'public/assets/js/default/accounting.min.js',
            //'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            //'public/assets/js/plugins/bootstrap/daterangepicker.js',
            //'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/modules/conciliaciones/plugins.js',
        ));
    }
}
