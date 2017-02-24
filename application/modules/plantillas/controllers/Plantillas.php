<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Colaboradores
 *
 * Modulo para administrar la creacion, edicion de colaboradores.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  05/22/2015
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Plantillas\Repository\PlantillaRepository as PlantillaRepository;
use Flexio\Modulo\Plantillas\Repository\PlantillaCatalogoRepository as PlantillaCatalogoRepository;
use Flexio\Modulo\Plantillas\Repository\PlantillaSolicitadaRepository as PlantillaSolicitadaRepository;
use Flexio\Modulo\Acreedores\Repository\AcreedoresRepository as AcreedoresRepository;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use League\Csv\Writer as Writer;
use Flexio\Modulo\Plantillas\Models\TiposPlantilla as TiposPlantilla;

class Plantillas extends CRM_Controller {

    /**
     * @var int
     */
    protected $usuario_id;

    /**
     * @var int
     */
    protected $empresa_id;

    /**
     * @var int
     */
    protected $modulo_id;

    /**
     * @var string
     */
    protected $nombre_modulo;
    protected $PlantillaRepository;
    protected $PlantillaCatalogoRepository;
    protected $AcreedoresRepository;
    protected $PlantillaSolicitadaRepository;

    /**
     * @var string
     */
    protected $upload_folder = './public/uploads/';

    function __construct() {
        parent::__construct();

        $this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('colaboradores/estado_orm');
        $this->load->model('colaboradores/colaboradores_contratos_orm');
        $this->load->model('liquidaciones/Liquidaciones_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model('centros/centros_orm');
        $this->load->model('colaboradores/dependientes_orm');
        $this->load->model('colaboradores/beneficiarios_orm');
        $this->load->model('planilla/Pagadas_deducciones_orm');
        $this->load->model('descuentos/Descuentos_orm');
        $this->load->model('planilla/Planilla_orm');
        $this->load->model('planilla/Planilla_colaborador_orm');
        $this->load->model('planilla/Pagadas_colaborador_orm');
        $this->load->model('modulos/Catalogos_orm');
        $this->load->library('orm/catalogo_orm');
        $this->load->model('configuracion_rrhh/cargos_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        $this->load->model('evaluaciones/evaluaciones_orm');

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);

        $this->usuario_id = $usuario->id;

        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;

        //Obtener el id de modulo
        $controllername = $this->router->fetch_class();
        $modulo = Modulos_orm::where("controlador", $controllername)->get()->toArray();
        $this->modulo_id = $modulo[0]["id"];

        $this->nombre_modulo = $this->router->fetch_class();

        $this->PlantillaRepository = new PlantillaRepository();

        $this->PlantillaCatalogoRepository = new PlantillaCatalogoRepository();

        $this->AcreedoresRepository = new AcreedoresRepository();

        $this->PlantillaSolicitadaRepository = new PlantillaSolicitadaRepository();

        $this->TiposPlantilla = new TiposPlantilla();
    }

    public function listar() {
        $data = array(
            "lista_cargos" => Cargos_orm::lista($this->empresa_id),
        );

        //Seleccionar Centros Contables
        $cat_centros = Capsule::select(Capsule::raw("SELECT * FROM cen_centros WHERE empresa_id = :empresa_id1 AND estado='Activo' AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = :empresa_id2 AND estado='Activo') ORDER BY nombre ASC"), array(
                    'empresa_id1' => $this->empresa_id,
                    'empresa_id2' => $this->empresa_id
        ));
        $cat_centros = (!empty($cat_centros) ? array_map(function($cat_centros) {
                            return array("id" => $cat_centros->id, "nombre" => $cat_centros->nombre);
                        }, $cat_centros) : "");
        $data["lista_centros"] = $cat_centros;

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
            'public/assets/css/plugins/bootstrap/jquery.bootstrap-touchspin.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
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
            'public/assets/js/default/toast.controller.js'
        ));

        //------------------------------------------
        // Para mensaje de creacion satisfactoria
        //------------------------------------------
        $mensaje = !empty($this->session->flashdata('mensaje')) ? json_encode(array('estado' => 200, 'mensaje' => $this->session->flashdata('mensaje'))) : '';
        if (!empty($mensaje)) {
            $this->assets->agregar_var_js(array(
                "toast_mensaje" => $mensaje
            ));
        }

        //Lisado de plantillas agrupado por tipo
        $grupo_plantillas = $this->PlantillaRepository->getAllGroupByTipo(array("estado" => 1));
        $data["plantillas"] = $grupo_plantillas;
        //dd($grupo_plantillas);
        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "grupo_plantillas" => json_encode($grupo_plantillas),
            "colaborador_id" => ""
                // "plantillas" => json_encode($data)
        ));

        //print_r($grupo_plantillas);
        //Breadcrum Array

        $breadcrumb = array(
          "titulo" => '<i class="fa fa-users"></i> Plantillas',
          "filtro" => true,
          "ruta" => array(
              0 => array(
                  "nombre" => "Recursos humanos",
                  "activo" => false,
               ),
              1=> array(
                    "nombre" => '<b>Plantillas</b>',
                    "activo" => true,
              )
          ),
      );

        $breadcrumb["menu"] = array(
            "url" => 'javascript:',
            "clase" => 'ocionesCrearBtn',
            "nombre" => "Crear"
        );
        $menuOpciones = array();
        $menuOpciones["#exportarPlanillasBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Plantillas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar($grid = NULL) {
        Capsule::enableQueryLog();
        $clause = array(
            "empresa_id" => $this->empresa_id
        );

        $colaborador_id = $this->input->post('colaborador_id', true);

        $plantilla_tipo = $this->input->post('plantilla_tipo', true);
        $fecha_desde = $this->input->post('fecha_desde', true);
        $fecha_hasta = $this->input->post('fecha_hasta', true);
        $estado = $this->input->post('estado', true);

        if (!empty($plantilla_tipo)) {
            $arrayTipos = preg_split('[-]', $plantilla_tipo);
            $nombre = $arrayTipos[1];
            $nombres = array("nombre" => $nombre);
            $plantilla = $this->PlantillaRepository->listar($nombres, NULL, NULL, NULL, NULL)->toArray();
            $clause['plantilla_id'] = $plantilla[0]['id'];
        }
        if (!empty($estado)) {
            $clause['estado_id'] = $estado;
        }
        if (!empty($fecha_desde)) {
            $clause['fecha_desde'] = Carbon::createFromFormat('d/m/Y', $fecha_desde)->format('Y-m-d');
        }
        if (!empty($fecha_hasta)) {
            $clause ['fecha_hasta'] = Carbon::createFromFormat('d/m/Y', $fecha_hasta)->format('Y-m-d');
            // dd($clause);
        }
        if (!empty($colaborador_id)) {
            $clause['colaborador_id'] = $colaborador_id;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->PlantillaSolicitadaRepository->listar($clause, NULL, NULL, NULL, NULL)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $rows = $this->PlantillaSolicitadaRepository->listar($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() AS $i => $row) {
                $uuid_plantilla = $row['uuid_plantilla'];
                $tipoId = $row['nombre_plantilla']['tipo_id'];
                $plantilla_tipo = TiposPlantilla::where('id', $tipoId)->get()->toArray();
                $tipo_nombre = $plantilla_tipo[0]['nombre'];
                $nombre_completo = $tipo_nombre . " - " . $row['nombre_plantilla']['nombre'];
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                //$hidden_options .= '<a href="' . base_url('plantillas/ver/' . bin2hex($row['uuid_plantilla'])) . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success verDetalle">Ver Detalle</a>';
                $hidden_options .= '<a href="' . base_url('plantillas/ver/' . $uuid_plantilla) . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
                $estado = Util::verificar_valor($row['estado']['etiqueta']);
                $estado_color = $row['estado']['etiqueta'] == "Activo" ? 'background-color:#5CB85C' : 'background-color:#f8ac59';
                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    '<a href="' . base_url('plantillas/ver/' .$uuid_plantilla). '" style="color:blue;">' . Util::verificar_valor($row['codigo']) . '</a>',
                    $nombre_completo,
                    $row['created_at'] != "" ? Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d/m/Y') : "",
                    $link_option,
                    $hidden_options,
                );

                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Cargar Vista Parcial de Tabla de Evaluaciones
     *
     * @return void
     */
    public function ocultotabla($modulo_id = NULL) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/plantillas/listar.js',
            'public/assets/js/modules/plantillas/tabla.js'
        ));

        if (preg_match("/planilla/i", $this->router->fetch_class())) {

            if (is_array($modulo_id)) {
                $index = "";
                $moduloval = "";
                foreach ($modulo_id AS $index => $value) {
                    $index = preg_replace("/(es|s)$/i", "_id", $index);
                    $moduloval = $value;
                }
                if (!empty($index)) {
                    $this->assets->agregar_var_js(array(
                        $index => json_encode($moduloval)
                    ));
                }
            } else {
                $this->assets->agregar_var_js(array(
                    "modulo_id" => $modulo_id
                ));
            }
        }

        $this->load->view('tabla');
    }

    /**
     * Cargar Plantilla
     *
     * @return void
     */
    public function plantilla($data = NULL) {
        $this->assets->agregar_js(array(
                //'public/assets/js/modules/plantillas/crear.vue.js'
        ));

        $this->load->view('plantilla', $data);
    }

    public function crear($uuid_plantilla = NULL, $plantilla_id = NULL) {
        $data = array();
        $mensaje = array();
        $titulo_formulario = '<i class="fa fa-users"></i> Plantillas: Crear';





        $plantillaInfs = NULL;
        $plantilla_codigo = NULL;
        $plantillaInf_colaborador=NULL;
        $platillaInf_firmado =NULL;
        $plantillaInf_id=NULL;
        // dd($plantilla_id);
        if (!empty($uuid_plantilla) && empty($plantilla_id)) {
            $plantillas['uuid_plantilla'] = $uuid_plantilla;
            $plantillaInf = $this->PlantillaSolicitadaRepository->ver($plantillas)->toArray();
            $plantilla_coment = $this->PlantillaSolicitadaRepository->findByUuid($uuid_plantilla);
            //dd($plantilla_coment);
            $plantilla_coment->load('comentario_timeline');
            //dd($plantilla_coment['comentario_timeline']['comentario']);
            $plantillaInfss = $plantillaInf[0];
            $tipoId = $plantillaInfss['nombre_plantilla']['tipo_id'];
            $plantilla_tipo = TiposPlantilla::where('id', $tipoId)->get()->toArray();
            $tipo_nombre = $plantilla_tipo[0]['nombre'];
            $nombre_completo = $tipo_nombre . " - " . $plantillaInfss['nombre_plantilla']['nombre'];
            $plantilla_codigo = $plantillaInfss['plantilla'];
            //   dd($nombre_completo);
            $plantillaInfs = array(array(
                    'id' => $plantillaInfss['id'],
                    'plantilla_id' => $plantillaInfss['plantilla_id'],
                    'plantilla_nombre' => $nombre_completo,
                    'colaborador_id' => $plantillaInfss['colaborador_id'],
                    'firmado_por' => $plantillaInfss['firmado_por'],
                    'prefijo_id' => $plantillaInfss['prefijo_id'],
                    'estado_id' => $plantillaInfss['estado_id'],
                    'acreedor' => $plantillaInfss['acreedor'],
                    //'plantilla'=>$plantillaInf[0]['plantilla'],
                    'codigo' => $plantillaInfss['codigo']));

            $plantillaInf_colaborador = $plantillaInfss['colaborador_id'];
            $platillaInf_firmado = $plantillaInfss['firmado_por'];
            $plantillaInf_id = $plantillaInfss['plantilla_id'];
            $this->assets->agregar_var_js(array(
                    "plantilla_id_selected" => $plantillaInfss['plantilla_id'],
                    "id_plant_detalle" => $plantillaInfss['id'],
                    'vista' => 'ver',
                    "coment" =>(isset($plantilla_coment->comentario_timeline)) ? $plantilla_coment->comentario_timeline : "",
                ));

            $titulo_formulario = '<i class="fa fa-users"></i> Plantillas: ' . $plantillaInfss['codigo'];
            $formulario = '<b>Detalle</b>';
        }else{
            $formulario = '<b>Crear</b>';
        }


        $breadcrumb = array(
          "titulo" => $titulo_formulario,
          "filtro" => true,
          "ruta" => array(
              0 => array(
                  "nombre" => "Recursos humanos",
                  "activo" => false,
               ),
              1=> array(
                    "nombre" => 'Plantillas',
                    "activo" => false,
                    "url"=>'plantillas/listar'
              ),
              2=> array(
                    "nombre" => $formulario,
                    "activo" => true,
              )
          ),
      );
        $colaboradores = Colaboradores_orm::lista($this->empresa_id);
        $colaboradores = (!empty($colaboradores) ? array_map(function($colaboradores) {
                            return array("id" => $colaboradores["id"], "nombre" => $colaboradores["nombre_completo"]);
                        }, $colaboradores) : "");

        $acreedores = $this->AcreedoresRepository->get(array("empresa_id" => $this->empresa_id))->toArray();
        $acreedores = (!empty($acreedores) ? array_map(function($acreedores) {
                            return array("id" => $acreedores["id"], "nombre" => $acreedores["nombre"]);
                        }, $acreedores) : "");

        $prefijos = $this->PlantillaCatalogoRepository->getPrefijos()->toArray();
        $prefijos = (!empty($prefijos) ? array_map(function($prefijos) {
                            return array("id" => $prefijos["id"], "nombre" => $prefijos["etiqueta"]);
                        }, $prefijos) : "");

        $firmado_por_colaborador = Colaboradores_orm::lista($this->empresa_id);
        $firmado_por_colaborador = (!empty($firmado_por_colaborador) ? array_map(function($firmado_por_colaborador) {
                            return array("id" => $firmado_por_colaborador["id"], "nombre" => $firmado_por_colaborador["nombre_completo"]);
                        }, $firmado_por_colaborador) : "");

        $estados = $this->PlantillaCatalogoRepository->getEstados()->toArray();
        $estados = (!empty($estados) ? array_map(function($estados) {
                            return array("id" => $estados["id"], "nombre" => $estados["etiqueta"]);
                        }, $estados) : "");

        //Lisado de plantillas agrupado por tipo
        $grupo_plantillas = $this->PlantillaRepository->getAllGroupByTipo(array("estado" => 1));
        $data["grupo_plantillas"] = $grupo_plantillas;
        $data["plantilla_inf"] = $plantillaInfs;
        $data["plantilla_plantilla"] = $plantilla_codigo;

        //Variables JS
        $this->assets->agregar_var_js(array(
            "grupo_plantillas" => json_encode($grupo_plantillas),
            "colaboradores" => json_encode($colaboradores),
            "acreedores" => json_encode($acreedores),
            "prefijos" => json_encode($prefijos),
            "firmado_por" => json_encode($firmado_por_colaborador),
            "estados" => json_encode($estados),
            "plantilla_ver" => json_encode($plantillaInfs),
            "colaborador_id_selected" => json_encode($plantillaInf_colaborador),
            "firmado_por_id_selected" => json_encode($platillaInf_firmado)
        ));


        //Verificar si existe variable $formulario
        if ($plantilla_id) {
            if (!empty($plantilla_id)) {
                $this->assets->agregar_var_js(array(
                    "plantilla_id_selected" => $plantilla_id
                ));
            }
        }

        $colaborador_id = $this->input->post('colaborador_id', true);

    	if(!empty($colaborador_id)){
    		$data["colaborador_id_selected"] = $colaborador_id;
    		$this->assets->agregar_var_js(array(
    			"colaborador_id_selected" => $colaborador_id
    		));
    	}

        $menuOpciones = array(
            "#exportarBtn" => "Exportar"
        );
        $breadcrumb["menu"] = array(
            "nombre" => "Acci&oacute;n",
            "url" => "#",
            "clase" => 'menus',
            "opciones" => $menuOpciones
        );

        $this->assets->agregar_css(array(
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/modules/stylesheets/plantillas.css',
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/default/vue.js',
            'public/assets/js/default/vue-validator.min.js',
            'public/assets/js/default/vue-resource.min.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            //'public/assets/js/plugins/jspdf/jspdf.min.js',
            'public/assets/js/plugins/jspdf/jspdf.min.js',
            //'public/assets/js/plugins/jspdf/libs/html2pdf.js',
            'public/assets/js/plugins/jspdf/libs/html2canvas/dist/html2canvas.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/plantillas/update.chosen.js',
            'public/assets/js/modules/plantillas/ckeditor.config.js',
            'public/assets/js/modules/plantillas/crear.vue.js',
        ));

        $this->template->agregar_titulo_header('Plantillas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function ajax_seleccionar_plantilla() {

        $plantilla_id = $this->input->post('plantilla_id', true);

        if (empty($plantilla_id)) {
            return false;
        }
        $plantilla_info = $this->PlantillaRepository->find($plantilla_id)->where("id", $plantilla_id)->first(array("archivo_nombre", "archivo_ruta"))->toArray();

        $contenido = "";
        $archivo_nombre = !empty($plantilla_info["archivo_nombre"]) ? $plantilla_info["archivo_nombre"] : "";
        $archivo_ruta = !empty($plantilla_info["archivo_ruta"]) ? $plantilla_info["archivo_ruta"] : "";
        $archivo_ruta_completa = $archivo_ruta . "/" . $archivo_nombre;

        if (file_exists($archivo_ruta_completa)) {
            $contenido = file_get_contents($archivo_ruta_completa, true);
        }

        $response = new stdClass();
        $response->contenido = $contenido;
        echo json_encode($response);
        exit;
    }

    function ajax_seleccionar_datos() {
        $colaborador_id = $this->input->post('colaborador_id', true);
        $firmado_por_id = $this->input->post('firmado_por_id', true);

        if (empty($colaborador_id)) {
            return false;
        }
        //DATOS DEL COLABORADOR
        $colaborador = Colaboradores_orm::with(array('cargo', 'empresa', 'ciclo', 'colaborador_sexo', 'colaborador_estado_civil', 'centro_contable', 'departamento', 'beneficiario_principal', 'beneficiario_contingente', 'beneficiario_pariente', 'dependientes', 'colaboradores_contratos'))->where("id", $colaborador_id)->get()->toArray();
        $firmado_por_colaborador = Colaboradores_orm::with(array('cargo', 'empresa', 'ciclo'))->where("id", $firmado_por_id)->get()->toArray();
        if (empty($colaborador)) {
            return false;
        }


        if (!empty($colaborador[0]["beneficiario_principal"])) {
            $clause_principal = array("colaborador_id" => $colaborador[0]["id"], "tipo" => "principal");
            $beneficiario_principal = Beneficiarios_orm::beneficiario_principal($clause_principal)->toArray();


            $i = 0;

            foreach ($beneficiario_principal AS $key => $value) {

                $principal_index[$i] = $i + 1;

                $beneficiarios_principales[$i] = $value["nombre"];

                $beneficiarios_principales_parentesco[$i] = $value["beneficiario_agente_catalogo"]["etiqueta"];

                $beneficiarios_principales_cedula[$i] = $value["cedula"];

                $beneficiarios_principales_porcentaje[$i] = $value["porcentaje"];

                $i++;
            }
        }


        if (!empty($colaborador[0]["beneficiario_contingente"])) {
            $clause_contingente = array("colaborador_id" => $colaborador[0]["id"], "tipo" => "contingente");
            $beneficiario_contingente = Beneficiarios_orm::beneficiario_contingente($clause_contingente)->toArray();


            $i = 0;

            foreach ($beneficiario_contingente AS $key => $value) {

                $contingente_index[$i] = $i + 1;

                $beneficiarios_contingentes[$i] = $value["nombre"];

                $beneficiarios_contingentes_parentesco[$i] = $value["beneficiario_agente_catalogo"]["etiqueta"];

                $beneficiarios_contingentes_cedula[$i] = $value["cedula"];

                $beneficiarios_contingentes_porcentaje[$i] = $value["porcentaje"];

                $i++;
            }
        }

        $nombre = !empty($colaborador[0]["nombre"]) ? $colaborador[0]["nombre"] : "";
        $firmado_por_nombre = !empty($firmado_por_colaborador[0]["nombre"]) ? $firmado_por_colaborador[0]["nombre"] . " " . $firmado_por_colaborador[0]["apellido"] : "";
        $apellido = !empty($colaborador[0]["apellido"]) ? $colaborador[0]["apellido"] : "";
        $colaborador_nacimiento = !empty($colaborador[0]["fecha_nacimiento"]) ? $colaborador[0]["fecha_nacimiento"] : "";
        $colaborador_ciclo = !empty($colaborador[0]["ciclo"]) ? $colaborador[0]["ciclo"]["etiqueta"] : "";
        $colaborador_sexo = !empty($colaborador[0]["colaborador_sexo"]) ? $colaborador[0]["colaborador_sexo"]["etiqueta"] : "";
        $colaborador_edad = !empty($colaborador[0]["edad"]) ? $colaborador[0]["edad"] : "";
        $colaborador_botas = !empty($colaborador[0]["no_botas"]) ? $colaborador[0]["no_botas"] : "";
        $colaborador_estado_civil = !empty($colaborador[0]["colaborador_estado_civil"]) ? $colaborador[0]["colaborador_estado_civil"]["etiqueta"] : "";
        $colaborador_nacionalidad = !empty($colaborador[0]["lugar_nacimiento"]) ? $colaborador[0]["lugar_nacimiento"] : "";
        $colaborador_direccion = !empty($colaborador[0]["direccion"]) ? $colaborador[0]["direccion"] : "";
        $colaborador_cuenta = !empty($colaborador[0]["numero_cuenta"]) ? $colaborador[0]["numero_cuenta"] : "";
        $cedula = !empty($colaborador[0]["cedula"]) ? $colaborador[0]["cedula"] : "";
        $firmado_por_cedula = !empty($firmado_por_colaborador[0]["cedula"]) ? $firmado_por_colaborador[0]["cedula"] : "";
        $firmado_por_cargo = !empty($firmado_por_colaborador[0]["cargo"]) ? $firmado_por_colaborador[0]["cargo"]["nombre"] : "";
        $cargo = !empty($colaborador[0]["cargo"]) ? $colaborador[0]["cargo"]["nombre"] : "";
        $empresa = !empty($colaborador[0]["empresa"]) ? $colaborador[0]["empresa"]["nombre"] : "";
        $empresa_direccion = !empty($colaborador[0]["empresa"]) ? $colaborador[0]["empresa"]["descripcion"] : "";
        $empresa_tomo = !empty($colaborador[0]["empresa"]) ? $colaborador[0]["empresa"]["tomo"] : "";
        $empresa_folio = !empty($colaborador[0]["empresa"]) ? $colaborador[0]["empresa"]["folio"] : "";
        $empresa_asiento = !empty($colaborador[0]["empresa"]) ? $colaborador[0]["empresa"]["asiento"] : "";
        $colaborador_seguro_social = !empty($colaborador[0]["seguro_social"]) ? $colaborador[0]["seguro_social"] : "";
        $tipo_salario = !empty($colaborador[0]["tipo_salario"]) ? $colaborador[0]["tipo_salario"] : "";
        $salario = preg_match('/hora/i', $tipo_salario) ? (!empty($colaborador[0]["rata_hora"]) ? $colaborador[0]["rata_hora"] : "") : (!empty($colaborador[0]["salario_mensual"]) ? $colaborador[0]["salario_mensual"] : "");
        $salario_mensual = !empty($colaborador[0]["salario_mensual"]) ? $colaborador[0]["salario_mensual"] : "";
        $fecha_inicio_labores = !empty($colaborador[0]["fecha_inicio_labores"]) ? $colaborador[0]["fecha_inicio_labores"] : "";
        $centro_contable = !empty($colaborador[0]["centro_contable"]) ? $colaborador[0]["centro_contable"]["nombre"] : "";
        $area_negocio = !empty($colaborador[0]["departamento"]) ? $colaborador[0]["departamento"]["nombre"] : "";
        $horas_semanales = !empty($colaborador[0]) ? $colaborador[0]["horas_semanales"] : "";

        $beneficiario_principal_no = !empty($principal_index) ? $principal_index : "";
        $beneficiario_principal = !empty($beneficiarios_principales) ? $beneficiarios_principales : "";
        $beneficiario_principal_parentesco = !empty($beneficiarios_principales_parentesco) ? $beneficiarios_principales_parentesco : "";
        $beneficiario_principal_cedula = !empty($beneficiarios_principales_cedula) ? $beneficiarios_principales_cedula : "";
        $beneficiario_principal_porcentaje = !empty($beneficiarios_principales_porcentaje) ? $beneficiarios_principales_porcentaje : "";

        $beneficiario_contingente_no = !empty($contingente_index) ? $contingente_index : "";
        $beneficiario_contingente = !empty($beneficiarios_contingentes) ? $beneficiarios_contingentes : "";
        $beneficiario_contingente_parentesco = !empty($beneficiarios_contingentes_parentesco) ? $beneficiarios_contingentes_parentesco : "";
        $beneficiario_contingente_cedula = !empty($beneficiarios_contingentes_cedula) ? $beneficiarios_contingentes_cedula : "";
        $beneficiario_contingente_porcentaje = !empty($beneficiarios_contingentes_porcentaje) ? $beneficiarios_contingentes_porcentaje : "";
        $tutor_nombre_menores = !empty($colaborador[0]["tutor_nombre"]) ? $colaborador[0]["tutor_nombre"] : "";
        $gastos_mortuoria_nombre = !empty($colaborador[0]["designado_nombre"]) ? $colaborador[0]["designado_nombre"] : "";
        $gastos_mortuoria_cedula = !empty($colaborador[0]["designado_cedula"]) ? $colaborador[0]["designado_cedula"] : "";

        if (!empty($colaborador[0]["colaboradores_contratos"])) {


            $clause_liquidaciones = array("colaborador_id" => $colaborador[0]["id"]);


            $liquidaciones = Colaboradores_contratos_orm::colaboradores_contratos($clause_liquidaciones)->toArray();

            $i = 0;

            foreach ($liquidaciones AS $key => $value) {
                Date::setLocale('es');
                $liquidaciones_fecha_desde = new Date(strtotime($value["fecha_ingreso"]));
                $liqui_fecha[$i] = str_replace("De", "de", ucwords($liquidaciones_fecha_desde->format('j \d\e F \d\e Y')));
                $liquidaciones_fecha_hasta = new Date(strtotime($value["fecha_salida"]));
                $liqui_fecha_salida[$i] = str_replace("De", "de", ucwords($liquidaciones_fecha_hasta->format('j \d\e F \d\e Y')));


                $i++;
            }

            $liquidaciones_fecha_inicio = !empty($liqui_fecha) ? $liqui_fecha : "";
            $liquidaciones_fecha_salida = !empty($liqui_fecha_salida) ? $liqui_fecha_salida : "";
            $liquidaciones_fecha_salida_ultima = !empty(end($liqui_fecha_salida)) ? end($liqui_fecha_salida) : "";
        }

        //SALARIOS

        $salario_hora = !empty($colaborador[0]["rata_hora"]) ? $colaborador[0]["rata_hora"] : "";

        $clause_salario_info["colaborador_id"] = $colaborador[0]["id"];
        $salario_info = Pagadas_colaborador_orm::seguro_social($clause_salario_info)->toArray();
        if(!empty($salario_info)){
        //CALCULANDO S.S.
        $i = 0;
        $seguro_social = 0;
        foreach ($salario_info AS $key => $value) {

            $deducciones = $value["deducciones"];

            foreach ($deducciones AS $key => $value) {

                $salarios_index = $i + 1;
                $seguro_social+= $value["descuento"];
            }
            $i++;
        }
        if($seguro_social>0){
          $seguro_social_total = $seguro_social / $salarios_index;
        }
        else {
            $seguro_social_total = 0;
        }
        $seguro_social_final = number_format((float) $seguro_social_total, 2, '.', '');

        //CALCULANDO SEGURO EDUCATIVO
        $seguro_educativo_info = Pagadas_colaborador_orm::seguro_educativo($clause_salario_info)->toArray();


        $i = 0;
        $seguro_educativo = 0;
        foreach ($seguro_educativo_info AS $key => $value) {

            $deducciones_educativo = $value["deducciones"];

            foreach ($deducciones_educativo AS $key => $value) {

                $seguro_educativo_index = $i + 1;
                $seguro_educativo+= $value["descuento"];
            }
            $i++;
        }

        $seguro_educativo_total = ($seguro_educativo>0)?$seguro_educativo / $seguro_educativo_index:0;

        $seguro_educativo_final = number_format((float) $seguro_educativo_total, 2, '.', '');

        //CALCULANDO ISR
        $isr_info = Pagadas_colaborador_orm::impuesto_renta($clause_salario_info)->toArray();


        $i = 0;
        $isr = 0;
        foreach ($isr_info AS $key => $value) {

            $deducciones_isr = $value["deducciones"];

            foreach ($deducciones_isr AS $key => $value) {

                $isr_index = $i + 1;
                $isr+= $value["descuento"];
            }
            $i++;
        }


        $impuesto_sobre_renta = ($isr>0)?$isr / $isr_index:0;



        $impuesto_sobre_renta_final = number_format((float) $impuesto_sobre_renta, 2, '.', '');

        //CALCULANDO COUT SINDICAL
        $cuota_sindical_info = Pagadas_colaborador_orm::cuota_sindical($clause_salario_info)->toArray();


        $i = 0;
        $isr = 0;
        foreach ($cuota_sindical_info AS $key => $value) {

            $cuota_sindical = $value["deducciones"];

            foreach ($cuota_sindical AS $key => $value) {

                $isr_index = $i + 1;
                $isr+= $value["descuento"];
            }
            $i++;
        }

         $cuota_sindical_total = ($isr>0)?$isr / $isr_index:0;

        $cuota_sindical_final = number_format((float) $cuota_sindical_total, 2, '.', '');

        //DESCUENTO DIRECTO
        $clause_descuentos = array("colaborador_id" => $colaborador[0]["id"], "estado_id" => 6);
        $descuentos_info = Descuentos_orm::descuentos_colaborador($clause_descuentos)->toArray();

        $i = 0;
        $sum_descuentos = 0;
        foreach ($descuentos_info AS $key => $value) {

            if ($value["ciclo_id"] = 61) {

                $monto_ciclo = $value["monto_ciclo"] * 2;
            } else if ($value["ciclo_id"] = 62) {

                $monto_ciclo = $value["monto_ciclo"];
            } else if ($value["ciclo_id"] = 63) {

                $monto_ciclo = $value["monto_ciclo"] * 4;
            } else if ($value["ciclo_id"] = 64) {

                $monto_ciclo = $value["monto_ciclo"] * 2;
            }

            $sum_descuentos+= $monto_ciclo;

            $i++;
        }

        $descuentos_totales = number_format((float) $sum_descuentos, 2, '.', '');

        //SALARIO NETO


        $salario_neto_total = $salario_mensual - $seguro_social_final - $seguro_educativo_final - $impuesto_sobre_renta_final - $cuota_sindical_final - $descuentos_totales;
        }
        //CALCULANDO SEGURO SOCIAL
        /* $i=0;
          $sum_seguro_social=0;
          foreach($salario_info AS $key => $value){
          $salarios_index = $i + 1;

          $sum_salario_bruto+= $value["salario_bruto"];
          $i++;
          } */

        //DATOS DEL USUARIO
        $usuario = Usuario_orm::with(array("roles"))->where("id", $this->usuario_id)->get()->toArray();
        //$usuario_roles = $usuario->roles;
        Date::setLocale('es');
        $fecha_inicio_labores = new Date(strtotime($fecha_inicio_labores));
        $fecha_nacimiento_colaborador = new Date(strtotime($colaborador_nacimiento));
        //  $liquidaciones_fecha_ini = new Date(strtotime($liquidaciones_fecha_inicio));
        // $fecha_liquidacion = new Date(strtotime($liquidaciones_fecha_salida));

        echo json_encode(array(
            "sistema" => array(
                "logo" => '<img alt="image" src="' . base_url("public/themes/erp/images/logo_flexio_background_transparent_recortado_miniV1.png") . '" class="img-responsive">',
                "fecha_creacion" => str_replace("De", "de", ucwords(Date::now()->format('j \d\e F \d\e Y')))
            ),
            "colaborador" => array(
                "nombre" => $nombre,
                "apellido" => $apellido,
                "colaborador_sexo" => $colaborador_sexo,
                "colaborador_edad" => $colaborador_edad,
                "colaborador_estado_civil" => $colaborador_estado_civil,
                "colaborador_nacionalidad" => $colaborador_nacionalidad,
                "colaborador_direccion" => $colaborador_direccion,
                "colaborador_ciclo" => $colaborador_ciclo,
                "cargo" => $cargo,
                "cedula" => $cedula,
                "seguro_social_colaborador" => $colaborador_seguro_social,
                "salario" => $salario_mensual,
                "salario_hora" => $salario_hora,
                "tipo_salario" => $tipo_salario == "Hora" ? "por " . $tipo_salario : $tipo_salario,
                "horas_semanales" => $horas_semanales,
                "empresa" => $empresa,
                "empresa_direccion" => $empresa_direccion,
                "empresa_tomo" => $empresa_tomo,
                "empresa_folio" => $empresa_folio,
                "empresa_asiento" => $empresa_asiento,
                "fecha_inicio_labores" => !empty($colaborador[0]["fecha_inicio_labores"]) ? str_replace("De", "de", ucwords($fecha_inicio_labores->format('j \d\e F \d\e Y'))) : "",
                "fecha_nacimiento" => !empty($fecha_nacimiento_colaborador) ? str_replace("De", "de", ucwords($fecha_nacimiento_colaborador->format('j \d\e F \d\e Y'))) : "",
                "centro_contable" => $centro_contable,
                "area_negocio" => $area_negocio,
                "beneficiario_principal_no" => $beneficiario_principal_no,
                "beneficiario_principal" => $beneficiario_principal,
                "beneficiario_principal_parentesco" => $beneficiario_principal_parentesco,
                "beneficiario_principal_cedula" => $beneficiario_principal_cedula,
                "beneficiario_principal_porcentaje" => $beneficiario_principal_porcentaje,
                "beneficiario_contingente_no" => $beneficiario_contingente_no,
                "beneficiario_contingente" => $beneficiario_contingente,
                "beneficiario_contingente_parentesco" => $beneficiario_contingente_parentesco,
                "beneficiario_contingente_cedula" => $beneficiario_contingente_cedula,
                "beneficiario_contingente_porcentaje" => $beneficiario_contingente_porcentaje,
                "tutor_nombre_menores" => $tutor_nombre_menores,
                "gastos_mortuoria_nombre" => $gastos_mortuoria_nombre,
                "gastos_mortuoria_cedula" => $gastos_mortuoria_cedula,
                "liquidaciones_fecha" => !empty($liquidaciones_fecha_inicio) ? $liquidaciones_fecha_inicio : "",
                "liquidaciones_fecha_salida" => !empty($liquidaciones_fecha_salida) ? $liquidaciones_fecha_salida : "",
                "liquidaciones_fecha_salida_ultima" => !empty($liquidaciones_fecha_salida_ultima) ? $liquidaciones_fecha_salida_ultima : "",
                "seguro_social" => !empty($seguro_social_final) ? $seguro_social_final : "",
                "seguro_educativo" => !empty($seguro_educativo_final) ? $seguro_educativo_final : "",
                "impuesto_renta" => !empty($impuesto_sobre_renta_final) ? $impuesto_sobre_renta_final : "",
                "cuota_sindical" => !empty($cuota_sindical_final) ? $cuota_sindical_final : "",
                "descuento_directo" => !empty($descuentos_totales) ? $descuentos_totales : "",
                "salario_neto" => !empty($salario_neto_total) ? $salario_neto_total : "",
                "colaborador_botas" => !empty($colaborador_botas) ? $colaborador_botas : "",
                "numero_cuenta" => !empty($colaborador_cuenta) ? $colaborador_cuenta : ""
            ),
            "usuario" => array(
                "nombre" => $usuario[0]["nombre"],
                "apellido" => $usuario[0]["apellido"],
                "nombre_completo" => $usuario[0]["nombre_completo"],
                "telefono" => $usuario[0]["telefono"],
                "cargo" => ucwords($usuario[0]["roles"][0]["nombre"]),
            // "cedula"                => $usuario[0]["cedula"]
            ),
            "firmado_por" => array(
                "nombre_completo" => $firmado_por_nombre,
                "firmado_por_cedula" => $firmado_por_cedula,
                "firmado_por_cargo" => $firmado_por_cargo
            ),
        ));
        exit;
    }

    function ajax_guardar_plantilla() {


        /**
         * Inicializar Transaccion
         */
        Capsule::beginTransaction();

        try {

            $plantilla_solicitada_id = $this->input->post('id', true);
           // dd($plantilla_solicitada_id);
            $plantilla_id = $this->input->post('plantilla_id', true);
            $colaborador_id = $this->input->post('colaborador_id', true);
            $destinatario_id = $this->input->post('destinatario_id', true);
            $prefijo_id = $this->input->post('prefijo_id', true);
            $estado_id = $this->input->post('estado_id', true);
            $plantilla = $_POST['plantilla'];

            $firmado_por = $this->input->post('firmado_por', true);

            //Verificar si existe $plantilla_solicitada_id
            $plantilla_solicitada = $this->PlantillaSolicitadaRepository->find($plantilla_solicitada_id);

            if (!empty($plantilla_solicitada)) {
               // dd($plantilla_solicitada);
                $fieldset = array(
                    "empresa_id" => $this->empresa_id,
                    "id" => $plantilla_solicitada_id,
                    "colaborador_id" => $colaborador_id,
                    "plantilla_id" => $plantilla_id,
                    "prefijo_id" => $prefijo_id,
                    "estado_id" => $estado_id,
                    "acreedor" => $destinatario_id,
                    "plantilla" => $plantilla,
                    "creado_por" => $this->usuario_id,
                    "firmado_por" => $firmado_por
                );

                //--------------------
                // Actualizar
                //--------------------
                //dd($fieldset);
                $plantilla_solicitada = $this->PlantillaSolicitadaRepository->update($fieldset);
            } else {
                $fieldset = array(
                    "uuid_plantilla" => Capsule::raw("ORDER_UUID(uuid())"),
                    "empresa_id" => $this->empresa_id,
                    "colaborador_id" => $colaborador_id,
                    "plantilla_id" => $plantilla_id,
                    "firmado_por" => $firmado_por,
                    "prefijo_id" => $prefijo_id,
                    "estado_id" => $estado_id,
                    "acreedor" => $destinatario_id,
                    "plantilla" => $plantilla,
                    "creado_por" => $this->usuario_id,
                    "codigo" => Capsule::raw("CODIGO_PLANTILLAS('PT', " . $this->empresa_id . ")")
                );

                //--------------------
                // Guardar
                //--------------------
                $plantilla_solicitada = $this->PlantillaSolicitadaRepository->create($fieldset);
            }
        } catch (ValidationException $e) {

            // Rollback
            Capsule::rollback();

            log_message("error", "MODULO: " . __METHOD__ . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . ".\r\n");

            echo json_encode(array(
                "guardado" => false,
                "mensaje" => "Hubo un error tratando de " . (!empty($plantilla_solicitada_id) ? "actualizar" : "guardar") . " la plantilla."
            ));
            exit;
        }

        // If we reach here, then
        // data is valid and working.
        // Commit the queries!
        Capsule::commit();

        $this->session->set_flashdata('mensaje', "Se ha " . (!empty($plantilla_solicitada_id) ? "actualizado" : "guardado") . " la plantilla satisfactoriamente.");

        echo json_encode(array(
            "guardado" => true,
            "mensaje" => "Se ha " . (!empty($plantilla_solicitada_id) ? "actualizado" : "guardado") . " la plantilla satisfactoriamente."
        ));
        exit;
    }

    public function exportar() {
        if (empty($_POST)) {
            die();
        }

        $ids = $this->input->post('ids', true);
        //dd($ids);
        $id_plantillas = explode(",", $ids);
        //dd($id_clientes);
        if (empty($id_plantillas)) {
            return false;
        }

        $clause = array(
            "id" => $id_plantillas
        );
        //dd($clause);
        $rows = $this->PlantillaSolicitadaRepository->exportar($clause)->toArray();
        // dd($rows);
        if (empty($rows)) {
            return false;
        }
        $i = 0;

        foreach ($rows as $row) {
            //dd($row);
            $datos_excel[$i]['codigo'] = Util::verificar_valor($row['codigo']);
            $datos_excel[$i]['nombre'] = $row['nombre_plantilla']['nombre'];
            $datos_excel[$i]['fecha_creacion'] = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d/m/Y');
            $datos_excel[$i]['estado'] = $row['estado']['etiqueta'];
            $i++;
        }

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'No. de plantilla',
            'Nombre de plantilla',
            'Fecha de creacion',
            'Estado'
        ]);
        $csv->insertAll($datos_excel);
        $csv->output("Pantillas-" . date('ymd') . ".csv");
        die;
    }

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            //'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/plantillas/vue.comentario.js',
            'public/assets/js/modules/plantillas/formulario_comentario.js',
        ));

        $this->load->view('formulario_comentarios');
        $this->load->view('comentarios');

    }

    function ajax_guardar_comentario() {

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
       // dd($model_id, $comentario);
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->usuario_id];
        $plantilla = $this->PlantillaSolicitadaRepository->agregarComentario($model_id, $comentario);
        $plantilla->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($plantilla->comentario_timeline->toArray()))->_display();
        exit;
    }
}
