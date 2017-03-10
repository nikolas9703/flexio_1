<?php

trait HistorialCuenta {

    function historial_transacciones($uuid_cuenta = null) {
        $this->load->model('pagos/Pagos_orm');
        $this->load->model('facturas_compras/Facturas_compras_orm');
        //verificar estos modelos viejos

        $repositorioCuenta = new Flexio\Modulo\Contabilidad\Repository\CuentaRepositorio;
        $colleccionCuenta = new Flexio\Modulo\Contabilidad\Services\CuentaColleccion;
        $centrosContableObj = new Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
        $centros_contables = $centrosContableObj->get(['empresa_id'=>$this->empresa_id,'transaccionales'=>true]);
        $cuentas = $repositorioCuenta->getCuentas($this->empresa_id)->uuid($uuid_cuenta)
                                     ->fetch();

        $cuentas->load('cuentas_item');
        
        $array_ids = $colleccionCuenta->soloTransaccionales($cuentas);
        //dd($array_ids);
        $cuenta_ids = array_pluck($array_ids, "id");

        $data=array();
        $this->_js();
        $this->_Css();

        $this->assets->agregar_var_js(array(
            "uuid_cuenta"     => isset($uuid_cuenta) ? $uuid_cuenta : "0",
            'campo' => collect(['cuenta_ids'=>$cuenta_ids])
        ));

        //$cuenta = Cuentas_orm::findByUuid($uuid_cuenta);

        //Breadcrum Array

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Plan contable: Historial de cuenta / '.$cuentas[0]->codigo." ".$cuentas[0]->nombre,
            "filtro" => false,
            "menu" => array(
                "url"	 => 'javascript:',
            )
        );

        $breadcrumb["menu"]["opciones"]["#exportarTablaHistorial"] = "Exportar";

        $data['centros_contable'] = $centros_contables;
        $this->template->agregar_titulo_header('Plan Contable');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    //Vista parcial de tabla de historial
    public function ocultotablahistorial() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/contabilidad/tabla_historial.js'
        ));

        $this->load->view('historial');
    }

    public function ajax_historial() {
        //Just Allow ajax request
        $this->load->model('pagos/Pagos_orm');
        $this->load->model('facturas_compras/Facturas_compras_orm');
        $this->load->model('movimiento_monetario/Movimiento_retiros_orm');

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $jqgrid = new Flexio\Modulo\Contabilidad\Services\HistorialCuentaJqgrid;

        $clause= array(
            'empresa'    => $this->empresa_id,
        );
        $response = $jqgrid->listar($clause);
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function exportar_historial_transacciones(){
        if(empty($_POST)){
            return false;
        }
        $ids = $this->input->post('ids', true);
        $cuenta_ids = $this->input->post('cuenta_ids', true);
        $centro_contable = $this->input->post('exportar_centro_contable');
        $fecha_min = $this->input->post('exportar_fecha_min');
        $fecha_max = $this->input->post('exportar_fecha_max');
        $codigo = $this->input->post('exportar_transaccion');
        $id = explode(",", $ids);
        $cuentas = explode(",", $cuenta_ids);
        $centro_contable = explode(",", $centro_contable);
        $clause = ['empresa' => $this->empresa_id];
        $clause['cuenta_ids'] = count($cuentas) > 0? array_filter($cuentas):"";
        $clause['id'] = count($id) > 0? array_filter($id):"";
        $clause['centro_contable'] = count($centro_contable) > 0? array_filter($centro_contable):"";
        $clause['fecha_min'] = $fecha_min;
        $clause['fecha_max'] = $fecha_max;
        $clause['codigo'] = $codigo;

        $HistorialCsv = new Flexio\Modulo\Contabilidad\Exportar\Csv\HistorialTransaccionCsv;
        $csv = $HistorialCsv->crearCsv($clause);

        if(!is_null($csv)){
            $csv->output("historial-transacciones-". date('ymd') .".csv");
            die;
        }
        return false;
    }

}
