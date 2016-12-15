<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 02:30 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
use Illuminate\Database\Capsule\Manager as Capsule;
class Remesas extends CRM_Controller
{

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;


    function __construct()
    {
        parent::__construct();

        $this->load->helper(array('file', 'string', 'util'));
        $this->load->model('remesas/Remesas_orm');


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
            'public/assets/js/modules/remesas/remesas.js',
        ));

        $this->template->vista_parcial(array(
            'remesas',
            //'crear',
        ));
    }

    public function ocultotabla($id_cliente = NULL)
    {

        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/remesas/tabla.js',
        ));

        if (!empty($id_cliente)) {

            // Agregra variables PHP como variables JS
            $this->assets->agregar_var_js(array(
                "id_cliente" => $id_cliente
            ));

        }

        $this->load->view('tabla');
    }


    public function ajax_listar_remesas()
    {
        //Just Allow ajax request

        if(!$this->input->is_ajax_request()){
            return false;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $tipo = $this->input->post('tipo');
        $nombre = (string)$this->input->post('nombre');
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        //fix count
        $count = Remesas_orm::where('empresa_id',$empresa->id)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $clause= array('empresa_id' => $empresa->id);
        if(!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;
        //if(!empty($nombre)) $clause['nombre'] = array('like',"%$nombre%");

        $cuentas = Remesas_orm::listar($clause, $nombre ,$sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->record  = $count;
        $i=0;

        if(!empty($cuentas)){
            foreach ($cuentas as  $row){
                $tituloBoton = ($row['estado']!=1)?'Habilitar':'Deshabilitar';
                $estado = ($row['estado']==1)?0:1;
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="' . $row->nombre . '" data-contacto="' . $row->uuid_contacto . '"><i class="fa fa-cog"></i> <span class="hidden-sm hidden-xs">Opciones</span></button>';
                $hidden_options = "";
                $level = substr_count($row['nombre'],".");
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'remesa'=> $row['remesa'],
                    'fecha' => $row['fecha'],
                    'archivo' => $row['archivo'],
                    'poliza' => $row['poliza'],
                    'usuario' => $row['usuario'],
//                    'opciones' =>$link_option,
                    'link' => $link_option,
                    "level" => isset($row["level"]) ? $row["level"] : "0", //level
                    'parent' => $row["padre_id"]==0? "NULL": (string)$row["padre_id"], //parent
                    //'isLeaf' =>(Ramos_orm::is_parent($row['id']) == true)? false: true, //isLeaf
                    'expanded' =>  false, //expended
                    'loaded' => true, //loaded
                ) );
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }
}