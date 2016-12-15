<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 02:30 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
use Illuminate\Database\Capsule\Manager as Capsule;
class Reportes extends CRM_Controller
{

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;


    function __construct()
    {
        parent::__construct();

        $this->load->helper(array('file', 'string', 'util'));
        $this->load->model('aseguradoras/Aseguradoras_orm');
        $this->load->model('Contabilidad/tipo_cuentas_orm');
        $this->load->model('Contabilidad/Cuentas_orm');
        $this->load->model('aseguradoras/Ramos_orm');
        $this->load->model('contactos/Contacto_orm');
        $this->load->model('aseguradoras/Reportes_orm');
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

    function crearsubpanel()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reportes/reportes.js',
        ));

        $this->template->vista_parcial(array(
            'reportes',
            //'crear',
        ));
    }

    public function ocultotabla($id_cliente = NULL)
    {

        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reportes/tabla.js',
        ));

        if (!empty($id_cliente)) {

            // Agregra variables PHP como variables JS
            $this->assets->agregar_var_js(array(
                "id_cliente" => $id_cliente
            ));

        }

        $this->load->view('tabla');
    }

    function ajax_listar_reportes(){
        // Just Allow ajax request
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $uuid = $this->input->post('id_cliente');

        $aseguradora  = new Aseguradoras_orm();
        $aseguradora  = $aseguradora
            ->where("uuid_aseguradora", "=", hex2bin(strtolower($uuid)))
            ->first();

        $clause = array(
            "id_aseguradora"=> $aseguradora->id,
        );

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Reportes_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $rows = Reportes_orm::listar($clause,$sidx, $sord, $limit, $start);

        $where= array('empresa_id' => $this->id_empresa);
        $nombre="";
        $ramos = Ramos_orm::listar($where, $nombre ,$sidx, $sord, $limit, $start);

        $i = 0;
        foreach($rows as $plan){

            foreach($ramos as $ramo){

                if($plan->id_ramo == $ramo['id']){
                    if($ramo['level'] == 3) {
                        $data[$i]['plan'] = $plan['nombre'];
                        $data[$i]['id'] = $plan['id'];
                        $data[$i]['uuid_reportes'] = $plan['uuid_reportes'];
                        $data[$i]['clase'] = $ramo['nombre'];
                        $area = Ramos_orm::where('id', '=', $ramo['padre_id'])->get()->toArray();
                        $data[$i]['area'] = $area[0]['nombre'];
                        $ram = Ramos_orm::where('id', '=', $area[0]['padre_id'])->get()->toArray();
                        $data[$i]['ramo'] = $ram[0]['nombre'];

                    }else if($ramo['level'] == 2){
                        $data[$i]['plan'] = $plan['nombre'];
                        $data[$i]['id'] = $plan['id'];
                        $data[$i]['uuid_reportes'] = $plan['uuid_reportes'];
                        $data[$i]['area'] = $ramo['nombre'];
                        $ram = Ramos_orm::where('id', '=', $ramo['padre_id'])->get()->toArray();
                        $data[$i]['ramo'] = $ram[0]['nombre'];

                    }else {
                        $data[$i]['plan'] = $plan['nombre'];
                        $data[$i]['id'] = $plan['id'];
                        $data[$i]['uuid_reportes'] = $plan['uuid_reportes'];
                        $data[$i]['ramo'] = $ramo['nombre'];
                    }
                }
            }
            $i++;
        }
        /*print_r($rows->toArray());
        print_r($ramo);
        print_r($data);
        die();*/
        //Constructing a JSON
        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;
        $i=0;

        if(!empty($data)){
            foreach ($data AS $i => $row){
                $hidden_options = '<a href="#" class="btn btn-block btn-outline btn-success">Ver Plan</a>';

                $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success">Editar</a>';

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row['id'].'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    $row['id'],
                    "<a href='" . base_url('reportes/ver/'.strtoupper(bin2hex($row['uuid_reportes']))) . "'>" . $row['plan']  . "</a>",
                    isset($row['area']) ? $row['area'] : "",
                    isset($row['ramo']) ? $row['ramo'] : "",
                    isset($row['clase']) ? $row['clase'] : "",
                    $link_option,
                    $hidden_options
                );
            }
        }

        echo json_encode($response);
        exit;


    }
}