<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Ordenes de Ventas
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/15/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Repository\ConfiguracionContabilidad\CuentaPorCobrarRepository as CuentaPorCobrar;
use Flexio\Repository\ConfiguracionContabilidad\CajaMenudaRepository as CajaMenuda;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaPorPagarRepository as CuentaPorPagar;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaBancoRepository as CuentaBanco;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaPorAbonarRepository as CuentaPorAbonar;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaInventarioRepository as CuentaInventario;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaPlanillaRepository as CuentaPlanilla;
use Flexio\Modulo\Empresa\Repository\EmpresaRepository;


class Configuracion_contabilidad extends CRM_Controller
{
    private $empresa_id;
    private $usuario_uuid;
    private $empresaObj;
    protected $cuenta_por_cobrar;
    protected $caja_menuda;
    protected $cuenta_por_pagar;
    protected $cuenta_banco;
    protected $cuenta_abono;
    protected $cuenta_inventario;
    protected $empresa_repository;
    protected $cuenta_planilla;

    function __construct()
    {

        parent::__construct();
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('contabilidad/Cuentas_orm');

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm(), 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->usuario_uuid = $this->session->userdata("huuid_usuario");
        $this->empresa_id = $this->empresaObj->id;
        $this->cuenta_por_cobrar = new CuentaPorCobrar();
        $this->caja_menuda = new CajaMenuda();
        $this->cuenta_por_pagar = new CuentaPorPagar;
        $this->cuenta_banco = new CuentaBanco;
        $this->cuenta_abono = new CuentaPorAbonar;
        $this->cuenta_inventario = new CuentaInventario;
        $this->empresa_repository = new EmpresaRepository();
        $this->cuenta_planilla = new CuentaPlanilla();
    }

    public function index()
    {
        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            $acceso = 0;
            $mensaje = array(
                'estado' => 500,
                'mensaje' => '<span class="fa-stack fa-lg"><i class="fa fa fa-user fa-stack-1x text-black"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span> <b>Usted no cuenta con permiso para esta solicitud</b>',
                'clase' => 'alert-danger'
            );
        }

        $data = array();
        $this->_js();
        $this->_css();
        // Breadcrum Array
        $condicion = array(
            'empresa_id' => $this->empresa_id,
            'tipo_cuenta_id' => 2
        );
        // $cuentas_pasivo =
        $ids_pasivo = Cuentas_orm::where($condicion)->lists('padre_id');

        $cuentas = Cuentas_orm::whereNotIn('id', $ids_pasivo->toArray())->where(function ($query) use ($condicion) {
            $query->where($condicion);
        })->get(array(
            'id',
            'nombre',
            'codigo'
        ));

        $data ['pasivos'] = $cuentas->toArray();
        $data ['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Contabilidad: Configuraci&oacute;n',
            "filtro" => false,
            "menu" => array()
        );
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = $this->empresa_repository->findByUuid($uuid_empresa);
        $this->assets->agregar_var_js([
            "retiene_impuesto" => $empresa->retiene_impuesto
        ]);
        $this->template->agregar_titulo_header('Contabilidad Configuracion');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    function ocultotablaimpuesto()
    {
        $this->load->view('tabla_impuesto');
    }


    function ocultotablacuentacobrar()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/arbol_cobros.js'
        ));
        $this->load->view('cuenta_por_cobrar');
    }

    function cajamenuda()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/caja_menuda.js'
        ));
        $this->load->view('caja_menuda');
    }

    function porpagaracreedor()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/cuentas_por_pagar_acreedor.js'
        ));
        $this->load->view('por_pagar_acreedor');
    }

    function bancos()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/cuenta_bancos.js'
        ));
        $this->load->view('cuenta_bancos');
    }

    function planilla()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/cuenta_planilla.js'
        ));
        $this->load->view('planilla');
    }

    function ajax_cuenta_planilla()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $condicion = array(
            'empresa_id' => $this->empresa_id,
            'tipo_cuenta_id' => 1
        );
        ///cambiar esta funcion 0_o
        $cuentas = Cuentas_orm::misCuentas($condicion);
        // $cuentas->load('config_cuenta_por_cobrar');
        // dd($cuentas);
        $response = new stdClass();
        $response->plugins = [
            //	"contextmenu",
            "wholerow"
        ];
        $response->core->check_callback [0] = true;
        $i = 0;
        if (!empty($cuentas)) {
            foreach ($cuentas as $row) {
                $response->core->data [$i] = array(
                    'id' => (string)$row ['id'],
                    'parent' => $row ["padre_id"] == 0 ? "#" : (string)$row ["padre_id"],
                    'text' => $row ["codigo"] . " " . $row ["nombre"],
                    'icon' => $row ["is_padre"] === true ? 'fa fa-folder fa-lg' : "fa fa-calculator fa-lg",
                    'codigo' => $row ["codigo"],
                    'es_padre' => $row ["is_padre"],
                    'state' => array(
                        'disabled' => $row ["is_padre"] === true ? true : false,
                        'opened' => $row ["codigo"] == "1." ? true : false
                    )
                );

                $i++;
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }

    function ajax_cuenta_activo()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $condicion = array(
            'empresa_id' => $this->empresa_id,
            'tipo_cuenta_id' => 1
        );
        ///cambiar esta funcion 0_o
        $cuentas = Cuentas_orm::misCuentas($condicion);
        // $cuentas->load('config_cuenta_por_cobrar');
        // dd($cuentas);
        $response = new stdClass();
        $response->plugins = [
            //	"contextmenu",
            "wholerow"
        ];
        $response->core->check_callback [0] = true;
        $i = 0;
        if (!empty($cuentas)) {
            foreach ($cuentas as $row) {
                $response->core->data [$i] = array(
                    'id' => (string)$row ['id'],
                    'parent' => $row ["padre_id"] == 0 ? "#" : (string)$row ["padre_id"],
                    'text' => $row ["codigo"] . " " . $row ["nombre"],
                    'icon' => $row ["is_padre"] === true ? 'fa fa-folder fa-lg' : "fa fa-calculator fa-lg",
                    'codigo' => $row ["codigo"],
                    'es_padre' => $row ["is_padre"],
                    'state' => array(
                        'disabled' => $row ["is_padre"] === true ? true : false,
                        'opened' => $row ["codigo"] == "1." ? true : false
                    )
                );

                $i++;
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }

    public function ajax_catalogo_cuentas()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $condicion = array_merge($this->input->post(), ["empresa_id" => $this->empresa_id]);

        ///cambiar esta funcion 0_o
        $cuentas = Cuentas_orm::misCuentas($condicion);

        $response = new stdClass();
        $response->plugins = [
            "wholerow"
        ];
        $response->core->check_callback [0] = true;

        if (!empty($cuentas)) {
            foreach ($cuentas as $i => $row) {
                $response->core->data [$i] = array(
                    'id' => (string)$row ['id'],
                    'parent' => $row ["padre_id"] == 0 ? "#" : (string)$row ["padre_id"],
                    'text' => $row ["codigo"] . " " . $row ["nombre"],
                    'icon' => $row ["is_padre"] === true ? 'fa fa-folder fa-lg' : "fa fa-calculator fa-lg",
                    'codigo' => $row ["codigo"],
                    'es_padre' => $row ["is_padre"],
                    'state' => array(
                        'disabled' => $row ["is_padre"] === true ? true : false,
                        'opened' => false
                        //'opened' => strlen($row ["codigo"]) == 2 ? true : false
                    )
                );
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }

    function ajax_get_cuenta_planilla()
    {
        $cuenta = [];
        $empresa = [
            'empresa_id' => $this->empresa_id
        ];

        if ($this->cuenta_planilla->tieneCuenta($empresa)) {
            $cuenta = $this->cuenta_planilla->getAll($empresa);
            //dd($cuenta->toArray());
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cuenta))->_display();
        exit;
    }

    function ajax_get_cuenta_por_cobrar()
    {
        $cuenta = [];
        $empresa = [
            'empresa_id' => $this->empresa_id
        ];
        if ($this->cuenta_por_cobrar->tieneCuenta($empresa)) {
            $cuenta = $this->cuenta_por_cobrar->getAll($empresa);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cuenta))->_display();
        exit();
    }

    function ajax_get_cuenta_por_pagar()
    {
        $cuenta = [];
        $tipo = $this->input->post('tipo');
        $empresa = [
            'empresa_id' => $this->empresa_id,
            'tipo' => empty($tipo) ? 'proveedor' : $tipo
        ];
        if ($this->cuenta_por_pagar->tieneCuenta($empresa)) {
            $cuenta = $this->cuenta_por_pagar->getAll($empresa);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cuenta))->_display();
        exit;
    }

    function ajax_cuenta_banco()
    {
        $cuenta = [];
        $empresa = ['empresa_id' => $this->empresa_id];

        if ($this->cuenta_banco->tieneCuenta($empresa)) {
            $cuenta = $this->cuenta_banco->getAll($empresa);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cuenta))->_display();
        exit;
    }

    function ajax_guardar_planilla()
    {
        $id = $this->input->post('id', TRUE);
        $crear = [
            'empresa_id' => $this->empresa_id,
            'cuenta_id' => $id
        ];
        $cuenta_planilla = $this->cuenta_planilla;
        $dato = Capsule::transaction(function () use ($cuenta_planilla, $crear) {
            try {
                $cuenta_planilla->create($crear);
                return $mensaje = [
                    'tipo' => 'success',
                    'mensaje' => 'la cuenta fue guardada con &eacute;xito'
                ];
            } catch (Illuminate\Database\QueryException $e) {
                return $mensaje = [
                    'tipo' => 'error',
                    'mensaje' => 'su solicitud no fue procesada'
                ];
            }
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($dato))->_display();
        exit();
    }

    function ajax_guardar_por_cobrar()
    {
        $id = $this->input->post('id', TRUE);
        $crear = [
            'empresa_id' => $this->empresa_id,
            'cuenta_id' => $id
        ];
        $cuenta_por_cobrar = $this->cuenta_por_cobrar;
        $dato = Capsule::transaction(function () use ($cuenta_por_cobrar, $crear) {
            try {
                $cuenta_por_cobrar->create($crear);
                return $mensaje = [
                    'tipo' => 'success',
                    'mensaje' => 'la cuenta fue guardada con &eacute;xito'
                ];
            } catch (Illuminate\Database\QueryException $e) {
                return $mensaje = [
                    'tipo' => 'error',
                    'mensaje' => 'su solicitud no fue procesada'
                ];
            }
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($dato))->_display();
        exit();
    }

    function ajax_eliminar_cuenta_planilla()
    {
        $id = (int)$this->input->post('cuenta_id', TRUE);
        // dd($id);
        $condicion = [
            'empresa_id' => $this->empresa_id,
            'cuenta_id' => $id
        ];
        $mensaje = [];


        $hasTransactions = $this->cuenta_planilla->tienes_transacciones($condicion);

        if (!$hasTransactions) {
            $this->cuenta_planilla->delete($condicion);
        }

        $mensaje = [
            'puede_eliminar' => !$hasTransactions,
            'tipo' => 'success',
            'mensaje' => $hasTransactions ? 'la cuenta tiene transacciones y no puede ser eliminada' : 'cuenta por pagar eliminada'
        ];

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($mensaje))->_display();
        exit();
    }

    function ajax_eliminar_cuenta_cobrar()
    {
        $id = $this->input->post('cuenta_id', TRUE);
        $condicion = [
            'empresa_id' => $this->empresa_id,
            'cuenta_id' => $id
        ];
        $mensaje = [];
        if ($this->cuenta_por_cobrar->tienes_transacciones($condicion)) {
            $mensaje = [
                'puede_eliminar' => false,
                'tipo' => 'success',
                'mensaje' => 'la cuenta tiene transacciones y no puede ser eliminada'
            ];
        } else {
            $this->cuenta_por_cobrar->delete($condicion);
            $mensaje = [
                'puede_eliminar' => true,
                'tipo' => 'success',
                'mensaje' => 'cuenta por pagar eliminada'
            ];
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($mensaje))->_display();
        exit();
    }

    function ajax_seleccionar_caja_menuda()
    {
        $cuenta = [];
        $empresa = [
            'empresa_id' => $this->empresa_id
        ];
        if ($this->caja_menuda->tieneCuenta($empresa)) {
            $cuenta = $this->caja_menuda->getAll($empresa);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cuenta))->_display();
        exit;
    }

    function ajax_guardar_cuenta_caja_menuda()
    {
        $id = $this->input->post('id', TRUE);

        $crear = [
            'empresa_id' => $this->empresa_id,
            'cuenta_id' => $id
        ];
        $caja_menuda = $this->caja_menuda;
        $dato = Capsule::transaction(function () use ($caja_menuda, $crear) {
            try {
                $caja_menuda->create($crear);
                return $mensaje = [
                    'tipo' => 'success',
                    'mensaje' => 'la cuenta fue guardada con &eacute;xito'
                ];
            } catch (Illuminate\Database\QueryException $e) {
                return $mensaje = [
                    'tipo' => 'error',
                    'mensaje' => 'su solicitud no fue procesada'
                ];
            }
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($dato))->_display();
        exit;
    }

    function ajax_eliminar_cuenta_caja_menuda()
    {
        $id = $this->input->post('cuenta_id', TRUE);
        $condicion = [
            'empresa_id' => $this->empresa_id,
            'cuenta_id' => $id
        ];
        $mensaje = [];
        if ($this->caja_menuda->tienes_transacciones($condicion)) {
            $mensaje = [
                'puede_eliminar' => false,
                'tipo' => 'success',
                'mensaje' => 'la cuenta tiene transacciones y no puede ser eliminada'
            ];
        } else {
            $this->caja_menuda->delete($condicion);
            $mensaje = [
                'puede_eliminar' => true,
                'tipo' => 'success',
                'mensaje' => 'cuenta de caja menuda eliminada'
            ];
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($mensaje))->_display();
        exit;
    }

    function cuenta_por_pagar()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/cuentas_por_pagar.js'
        ));
        $this->load->view('cuenta_por_pagar');
    }

    function ajax_cuenta_pasivo()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $condicion = array(
            'empresa_id' => $this->empresa_id,
            'tipo_cuenta_id' => 2
        );
        $cuentas = Cuentas_orm::misCuentas($condicion);
        // $cuentas->load('config_cuenta_por_cobrar');
        // dd($cuentas);
        $response = new stdClass();
        $response->plugins = [
            //"contextmenu",
            "wholerow"
        ];
        $response->core->check_callback [0] = true;

        $i = 0;
        if (!empty($cuentas)) {
            foreach ($cuentas as $row) {
                $response->core->data [$i] = array(
                    'id' => (string)$row['id'],
                    'parent' => $row ["padre_id"] == 0 ? "#" : (string)$row["padre_id"],
                    'text' => $row["codigo"] . " " . $row ["nombre"],
                    'icon' => $row["is_padre"] === true ? 'fa fa-folder fa-lg' : "fa fa-calculator fa-lg",
                    'codigo' => $row["codigo"],
                    'es_padre' => $row["is_padre"],
                    'state' => array(
                        'disabled' => $row["is_padre"] === true ? true : false,
                        'opened' => $row["codigo"] == "2." ? true : false
                    )
                );

                $i++;
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }

    public function ajax_guardar_cuenta_proveedor_por_pagar()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $id = $this->input->post('id', TRUE);
        $tipo = $this->input->post('tipo', TRUE);
        $crear = [
            'empresa_id' => $this->empresa_id,
            'cuenta_id' => $id,
            'tipo' => $tipo
        ];
        $cuenta_por_pagar = $this->cuenta_por_pagar;
        $dato = Capsule::transaction(function () use ($cuenta_por_pagar, $crear) {
            try {
                $cuenta_por_pagar->create($crear);
                return $mensaje = [
                    'tipo' => 'success',
                    'mensaje' => 'la cuenta fue guardada con &eacute;xito'
                ];
            } catch (Illuminate\Database\QueryException $e) {
                return $mensaje = [
                    'tipo' => 'error',
                    'mensaje' => 'su solicitud no fue procesada'
                ];
            }
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($dato))->_display();
        exit;
    }

    function ajax_eliminar_cuenta_por_pagar()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $id = $this->input->post('cuenta_id', TRUE);
        $tipo = $this->input->post('tipo', TRUE);
        $mensaje = [];
        $condicion = ['empresa_id' => $this->empresa_id, 'cuenta_id' => $id, 'tipo' => $tipo];

        if ($this->cuenta_por_pagar->tienes_transacciones($condicion)) {
            $mensaje = [
                'puede_eliminar' => false,
                'tipo' => 'warning',
                'mensaje' => 'la cuenta tiene transacciones y no puede ser eliminada'
            ];
        } else {
            $cuenta_por_pagar = $this->cuenta_por_pagar;
            $mensaje = Capsule::transaction(function () use ($cuenta_por_pagar, $condicion) {
                try {
                    $cuenta_por_pagar->delete($condicion);
                    return [
                        'puede_eliminar' => true,
                        'tipo' => 'success',
                        'mensaje' => 'cuenta por pagar eliminada'
                    ];
                } catch (Illuminate\Database\QueryException $e) {
                    return [
                        'puede_eliminar' => false,
                        'tipo' => 'error',
                        'mensaje' => 'su solicitud no fue procesada'
                    ];
                }
            });
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($mensaje))->_display();
        exit();
    }

    function ajax_guardar_cuenta_banco()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $ids = $this->input->post('id', TRUE);
        $crear = [
            'empresa_id' => $this->empresa_id,
        ];
        $cuenta_banco = $this->cuenta_banco;
        $dato = Capsule::transaction(function () use ($cuenta_banco, $crear, $ids) {
            try {
                foreach ($ids as $id) {
                    $crear['cuenta_id'] = $id;
                    $cuenta_banco->create($crear);
                }
                return $mensaje = [
                    'tipo' => 'success',
                    'mensaje' => 'la cuenta fue guardada con &eacute;xito'
                ];
            } catch (Illuminate\Database\QueryException $e) {
                return $mensaje = [
                    'tipo' => 'error',
                    'mensaje' => 'su solicitud no fue procesada'
                ];
            }
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($dato))->_display();
        exit;
    }

    function ajax_eliminar_cuenta_banco()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $id = $this->input->post('cuenta_id', TRUE);
        $mensaje = [];
        $condicion = ['empresa_id' => $this->empresa_id, 'cuenta_id' => $id];

        if ($this->cuenta_banco->tienes_transacciones($condicion)) {
            $mensaje = [
                'puede_eliminar' => false,
                'tipo' => 'warning',
                'mensaje' => 'la cuenta tiene transacciones y no puede ser eliminada'
            ];
        } else {
            $cuenta_banco = $this->cuenta_banco;
            $mensaje = Capsule::transaction(function () use ($cuenta_banco, $condicion) {
                try {
                    $cuenta_banco->delete($condicion);
                    return [
                        'puede_eliminar' => true,
                        'tipo' => 'success',
                        'mensaje' => 'cuenta por pagar eliminada'
                    ];
                } catch (Illuminate\Database\QueryException $e) {
                    return [
                        'puede_eliminar' => false,
                        'tipo' => 'error',
                        'mensaje' => 'su solicitud no fue procesada'
                    ];
                }
            });
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($mensaje))->_display();
        exit();
    }

    //carga la vista de abonos
    function abonos()
    {
        $this->load->view('abonos');
    }

    function abonarcliente()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/cuentas_por_abonar_cliente.js'
        ));
        $this->load->view('abonos_clientes');
    }

    function abonarproveedor()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/cuentas_por_abonar_proveedor.js'
        ));
        $this->load->view('abonos_proveedores');
    }

    function ajax_get_cuenta_abono()
    {
        $cuenta = [];
        $tipo = $this->input->post('tipo');
        $empresa = [
            'empresa_id' => $this->empresa_id,
            'tipo' => $tipo
        ];
        if ($this->cuenta_abono->tieneCuenta($empresa)) {
            $cuenta = $this->cuenta_abono->getAll($empresa);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cuenta))->_display();
        exit;
    }

    function ajax_guardar_cuenta_abono()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $id = $this->input->post('id', TRUE);
        $tipo = $this->input->post('tipo', TRUE);
        $crear = [
            'empresa_id' => $this->empresa_id,
            'cuenta_id' => $id,
            'tipo' => $tipo
        ];
        $cuenta_abono = $this->cuenta_abono;
        $dato = Capsule::transaction(function () use ($cuenta_abono, $crear) {
            try {
                $cuenta_abono->create($crear);
                return $mensaje = [
                    'tipo' => 'success',
                    'mensaje' => 'la cuenta fue guardada con &eacute;xito'
                ];
            } catch (Illuminate\Database\QueryException $e) {
                return $mensaje = [
                    'tipo' => 'error',
                    'mensaje' => 'su solicitud no fue procesada'
                ];
            }
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($dato))->_display();
        exit;
    }

    function ajax_eliminar_cuenta_abono()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $id = $this->input->post('cuenta_id', TRUE);
        $tipo = $this->input->post('tipo', TRUE);
        $mensaje = [];
        $condicion = ['empresa_id' => $this->empresa_id, 'cuenta_id' => $id, 'tipo' => $tipo];

        if ($this->cuenta_abono->tienes_transacciones($condicion)) {
            $mensaje = [
                'puede_eliminar' => false,
                'tipo' => 'warning',
                'mensaje' => 'la cuenta tiene transacciones y no puede ser eliminada'
            ];
        } else {
            $cuenta_abono = $this->cuenta_abono;
            $mensaje = Capsule::transaction(function () use ($cuenta_abono, $condicion) {
                try {
                    $cuenta_abono->delete($condicion);
                    return [
                        'puede_eliminar' => true,
                        'tipo' => 'success',
                        'mensaje' => 'cuenta por pagar eliminada'
                    ];
                } catch (Illuminate\Database\QueryException $e) {
                    return [
                        'puede_eliminar' => false,
                        'tipo' => 'error',
                        'mensaje' => 'su solicitud no fue procesada'
                    ];
                }
            });
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($mensaje))->_display();
        exit();
    }

    //configuracion inventarios
    function inventarios()
    {
        $this->load->view('inventarios');
    }

    function inventario_facturado()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/cuentas_inventario_facturado.js'
        ));
        $this->load->view('inventario_facturado');
    }

    function inventario_recibido_activo()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/inventario_recibido_activo.js'
        ));
        $this->load->view('inventario_recibido_activo');
    }

    function inventario_recibido_pasivo()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contabilidad/inventario_recibido_pasivo.js'
        ));
        $this->load->view('inventario_recibido_pasivo');
    }

    function ajax_get_cuenta_inventario()
    {
        $cuenta = [];
        $tipo = $this->input->post('tipo');
        $empresa = [
            'empresa_id' => $this->empresa_id,
            'tipo' => $tipo
        ];
        if ($this->cuenta_inventario->tieneCuenta($empresa)) {
            $cuenta = $this->cuenta_inventario->getAll($empresa);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cuenta))->_display();
        exit;
    }

    function ajax_guardar_cuenta_inventario()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $id = $this->input->post('id', TRUE);
        $tipo = $this->input->post('tipo', TRUE);
        $crear = [
            'empresa_id' => $this->empresa_id,
            'cuenta_id' => $id,
            'tipo' => $tipo
        ];
        $cuenta_inventario = $this->cuenta_inventario;
        $dato = Capsule::transaction(function () use ($cuenta_inventario, $crear) {
            try {
                $cuenta_inventario->create($crear);
                return $mensaje = [
                    'tipo' => 'success',
                    'mensaje' => 'la cuenta fue guardada con &eacute;xito'
                ];
            } catch (Illuminate\Database\QueryException $e) {
                return $mensaje = [
                    'tipo' => 'error',
                    'mensaje' => 'su solicitud no fue procesada'
                ];
            }
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($dato))->_display();
        exit;
    }

    function ajax_eliminar_cuenta_inventario()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $id = $this->input->post('cuenta_id', TRUE);
        $tipo = $this->input->post('tipo', TRUE);
        $mensaje = [];
        $condicion = ['empresa_id' => $this->empresa_id, 'cuenta_id' => $id, 'tipo' => $tipo];

        if ($this->cuenta_inventario->tienes_transacciones($condicion)) {
            $mensaje = [
                'puede_eliminar' => false,
                'tipo' => 'warning',
                'mensaje' => 'la cuenta tiene transacciones y no puede ser eliminada'
            ];
        } else {
            $cuenta_inventario = $this->cuenta_inventario;
            $mensaje = Capsule::transaction(function () use ($cuenta_inventario, $condicion) {
                try {
                    $cuenta_inventario->delete($condicion);
                    return [
                        'puede_eliminar' => true,
                        'tipo' => 'success',
                        'mensaje' => 'cuenta por pagar eliminada'
                    ];
                } catch (Illuminate\Database\QueryException $e) {
                    return [
                        'puede_eliminar' => false,
                        'tipo' => 'error',
                        'mensaje' => 'su solicitud no fue procesada'
                    ];
                }
            });
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($mensaje))->_display();
        exit();
    }

    private function _js()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery.progresstimer.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jstree.min.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/modules/configuracion_contabilidad/routes.js',
            'public/assets/js/modules/configuracion_contabilidad/impuesto_controller.js',
            'public/assets/js/default/formulario.js'
        ));
    }

    private function _css()
    {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
            'public/assets/css/plugins/jquery/jstree/default/style.min.css',
            'public/assets/css/modules/stylesheets/cuentas_por_pagar.css',
            'public/assets/css/modules/stylesheets/configuracion_contabilidad.css'
        ));
    }


}
