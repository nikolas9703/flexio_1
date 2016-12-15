<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 02:30 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
use Illuminate\Database\Capsule\Manager as Capsule;
class Planes extends CRM_Controller
{

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;


    function __construct() {
        parent::__construct();

        $this->load->helper(array('file', 'string', 'util'));
        $this->load->model('aseguradoras/Aseguradoras_orm');
        $this->load->model('Contabilidad/tipo_cuentas_orm');
        $this->load->model('Contabilidad/Cuentas_orm');
        $this->load->model('aseguradoras/Catalogo_tipo_poliza_orm');
        $this->load->model('aseguradoras/Catalogo_tipo_intereses_orm');
        $this->load->model('aseguradoras/Ramos_orm');
        $this->load->model('contactos/Contacto_orm');
        $this->load->model('aseguradoras/Planes_orm');
        $this->load->model('aseguradoras/Coberturas_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/organizacion_orm');

        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("huuid_usuario");
        $this->id_empresa = $this->empresaObj->id;
    }

    function crearsubpanel() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/planes/planes.js',
        ));

        $this->template->vista_parcial(array(
            'planes',
            //'crear',
        ));
    }

    public function ocultotabla($id_cliente = NULL) {

        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/planes/tabla.js',
        ));

        if (!empty($id_cliente)) {

            // Agregra variables PHP como variables JS
            $this->assets->agregar_var_js(array(
                "id_cliente" => $id_cliente
            ));

        }

        $this->load->view('tabla');
    }

    function ajax_listar_planes() {
        // Just Allow ajax request
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $uuid = $this->input->post('id_cliente');

        $aseguradora  = new Aseguradoras_orm();
        $aseguradora  = $aseguradora->where("uuid_aseguradora", "=", hex2bin(strtolower($uuid)))->first();
        

        $clause = array(
            "id_aseguradora"=> $aseguradora->id,
        );

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Planes_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        
        $rows = Planes_orm::listar($clause,$sidx, $sord, $limit, $start);
        
        //Constructing a JSON
        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;
        $i=0;

        if(!empty($rows)){
            foreach ($rows AS $i => $row){
                $hidden_options = '<a href="'.  base_url('configuracion_seguros/configuracion/planes-ver/'.$row['uuid_planes']) .'" class="btn btn-block btn-outline btn-success">Ver Plan</a>';

                $hidden_options .= '<a href="'. base_url('configuracion_seguros/configuracion/planes-editar/'.$row['uuid_planes']) .'" class="btn btn-block btn-outline btn-success">Editar</a>';

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row['id'].'" data-nombre="'.$row['plan'].'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    $row['id'],
                    "<a href='" .  base_url('configuracion_seguros/configuracion/planes-ver/'.$row['uuid_planes']) . "'>" . $row['plan']  . "</a>",
                    isset($row['producto']) ? $row['producto'] : "",
                    isset($row['ramo']) ? $row['ramo'] : "",
                   // isset($row['clase']) ? $row['clase'] : "",
                    !empty($row['comision']) ? $row['comision']."%" : "",
                    !empty($row['sobre_comision']) ? $row['sobre_comision']."%" : "",
                    ($row['desc_comision'] == 'SI') ? $row['desc_comision'] : 'NO',
                    $link_option,
                    $hidden_options
                );
            }
        }

        echo json_encode($response);
        exit;


    }
}