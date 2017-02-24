<?php
namespace Flexio\Modulo\FacturasVentas\Catalogo;

use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaRepositorio;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\OrdenesVentas\Api\OrdenesVentaDetalle;



trait FacturaVentaEmpezable{

    function ajax_catalogo_ordenes_ventas(){
        $empresa_id = FlexioSession::now()->empresaId();
        $repositorioOrdenVenta = new \Flexio\Modulo\OrdenesVentas\Repository\RepositoryOrdenVenta;
        $id = $this->input->post('id');
        $api = new \Flexio\Modulo\OrdenesVentas\Api\OrdenesVentaDetalle();
        if(empty($id)){
           $ordenes = $repositorioOrdenVenta->getOrdenes($empresa_id)->porFacturar()->fetch();   
        }else{
           $ordenes = $repositorioOrdenVenta->getOrdenes($empresa_id)->conId($id)->fetch();   
        }
        $response = $api->transformCollection($ordenes);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output($response)->_display();
        exit();
    }

    function ajax_catalogo_contrato_ventas(){
        $empresa_id = FlexioSession::now()->empresaId();

        $repositorioContratoVenta = new \Flexio\Modulo\Contratos\Repository\RepositorioContrato;
        $id = $this->input->post('id');
        $api = new \Flexio\Modulo\Contratos\Api\ContratoDetalle();

        if(empty($id)){
           $contrato = $repositorioContratoVenta->getContratos($empresa_id)->fetch();   
        }else{
           $contrato = $repositorioContratoVenta->getContratos($empresa_id)->conId($id)->fetch();   
        }
        $response = $api->transformCollection($contrato);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output($response)->_display();
        exit();
    }

    function ajax_catalogo_ordenes_alquiler(){
        $empresa_id = FlexioSession::now()->empresaId();

        $repositorioOrdenAlquiler = new \Flexio\Modulo\OrdenesAlquiler\Repository\RepositorioOrdenesAlquiler;
        $id = $this->input->post('id');
        $api = new \Flexio\Modulo\OrdenesAlquiler\Api\OrdenesAlquilerDetalle();

        if(empty($id)){
           $orden = $repositorioOrdenAlquiler->getOrdenes($empresa_id)->porFacturar()->fetch();   
        }else{
           $orden = $repositorioOrdenAlquiler->getOrdenes($empresa_id)->conId($id)->fetch();   
        }

        $response = $api->transformCollection($orden);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output($response)->_display();
        exit();
    }
}
