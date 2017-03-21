<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use Flexio\Modulo\Usuarios\FormRequest;
use Flexio\Modulo\Cotizaciones\Repository\CotizacionCatalogoRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\ClientesPotenciales\Repository\ClientesPotencialesRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerCatalogosRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;

class Ajax_catalogo extends CRM_Controller{
    protected $cotizacionCatalogoRepository;
    protected $centro_contables;
    protected $flexio_session;
    protected $empresa_id;
    protected $usuarioRol;
    protected $clientes;
    protected $items;

    function __construct() {
        parent::__construct();

        $this->cotizacionCatalogoRepository = new CotizacionCatalogoRepository;
        $this->centro_contables = new CentrosContablesRepository;
        $this->flexio_session = new FlexioSession;
        $this->empresa_id = $this->flexio_session->empresaId();
        $this->usuarioRol = new UsuariosRepository;
        $this->clientes = new ClienteRepository;
        $this->items = new ItemsRepository;
    }

    function catalogos_ventas(){
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $catalogo = [];

        $catalogo['terminos_pagos'] = $this->cotizacionCatalogoRepository->getTerminoPago();
        $catalogo['estados']         = $this->cotizacionCatalogoRepository->getEtapas();
        $catalogo['vendedores'] = $this->usuarioRol->rolVendedor(['empresa_id'=>$this->empresa_id]);
        $centro_contables = $this->centro_contables->get(['empresa_id'=>$this->empresa_id,'transaccionales'=>true]);
        $catalogo['centros_contables'] = $this->centro_contables->getCollectionCentrosContables($centro_contables);
        $catalogo['articulos'] = $this->articulos_catalogos();
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($catalogo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }
    function cat_clientes(){

        $clause = ['empresa_id'=>$this->empresa_id];
        if(isset($_POST['cliente_id'])){
            $clause['id'] = $_POST['cliente_id'];
        }
        $limit = $this->input->post("limit", true);
        $nombre = $this->input->post('q', true);
        if(!empty($nombre)){
            $clause['nombre'] = $nombre;
        }

        $clientes = $this->clientes->getClientesEstadoActivo($clause,null,null,$limit,0)->get(array('id','nombre','codigo'));
        $clientes = $clientes->map(function($cliente){
            return [
                'id'=> $cliente->id,
                'nombre' => $cliente->codigo . ' - '.$cliente->nombre,
                'saldo_pendiente' => $cliente->saldo_pendiente,
                'credito_favor' => $cliente->credito_favor
            ];
        });
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($clientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }
    function catalogo_clientes(){
        $clause = ['empresa_id'=>$this->empresa_id];

        //$clientes = $this->clientes->getAll($clause,['id','nombre','codigo']);
        $clientes = $this->clientes->getClientesEstadoActivo($clause)->get(array('id','nombre','codigo'));
        $clientes->load('centro_facturable');
        $clientes = $clientes->map(function($cliente){
            return [
                'id'=> $cliente->id,
                'nombre' => $cliente->codigo . ' - '.$cliente->nombre,
                'saldo_pendiente' => $cliente->saldo_pendiente,
                'credito_favor' => $cliente->credito_favor,
                'centro_facturable'  => $cliente->centro_facturable
            ];
        });
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($clientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }
    function catalogo_clientes_activo(){
        $clause = ['empresa_id'=>$this->empresa_id];
        $clientes = $this->clientes->getClientesEstadoActivo($clause)->get(array('id','nombre','codigo'));
        $clientes->load('centro_facturable');
        $clientes = $clientes->map(function($cliente){
            return [
                'id'=> $cliente->id,
                'nombre' => $cliente->codigo . ' - '.$cliente->nombre,
                'saldo_pendiente' => $cliente->saldo_pendiente,
                'credito_favor' => $cliente->credito,
                'centro_facturable'  => $cliente->centro_facturable
            ];
        });
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($clientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }
    function catalogo_clientes_potenciales(){
        $clientes_potenciales = new ClientesPotencialesRepository;
        $clause = ['empresa_id'=>$this->empresa_id];
        if(isset($_POST['cliente_id'])){
            $clause['id'] = $_POST['cliente_id'];
        }
        if(isset($_POST['q'])){
            $clause['q'] = $_POST['q'];
        }
        $clientes = $clientes_potenciales->getAll($clause,['id_cliente_potencial','nombre']);
        $catalogo = $clientes->map(function($cliente){
            return ['id'=>$cliente->id_cliente_potencial,'nombre'=>$cliente->nombre];
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($catalogo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }

    function articulos_catalogos(){
        $catalogo = [];
        $clause = ['empresa_id'=>$this->empresa_id];
        $categorias = new CategoriasRepository;
        $catalogo_alquiler = new ContratosAlquilerCatalogosRepository;
        $impuestos = new ImpuestosRepository;
        $cuentas = new CuentasRepository;
        //poner condicion aqui para categotias si lleva tipo
        $categoria_alquiler = $categorias->getCollectionCategorias($categorias->getCategoriasAlquiler($clause));
        //poner condicion para tipo de cuenta
        $cuentas_articulos = $cuentas->get(array_merge($clause,['transaccionales'=> true,'tipo_cuenta_id'=> 4]));


        $catalogo['categorias'] = $categoria_alquiler;
        $catalogo['periodos_tarifario'] = $catalogo_alquiler->get(['tipo'=>'tarifa']);
        $catalogo['impuestos'] = $impuestos->get($clause);
        $catalogo['cuentas'] = $cuentas_articulos;
        return  $catalogo;
        /*$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($catalogo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;*/
    }

    public function ajax_get_items_categoria()
    {
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $response = [];
        $clause = $this->input->post();
        $items = $this->items->getChunck(['categoria_id'=>$clause['categoria_id'],'empresa_id'=>$this->empresa_id, 'estado' => 1]);
        $response['items'] = $this->items->getCollectionVentas($items);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }

    public function ajax_get_items()
    {
        //usado en el refactory de facturas
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $collection = new \Flexio\Modulo\Inventarios\Collections\ItemsVentas;

        $response = [];
        $clause = $this->input->post();
        $items = $this->items->getChunck(['categoria_id'=>$clause['categoria_id'],'empresa_id'=>$this->empresa_id, 'estado' => 1]);
        $response['items'] = $collection::getCollectionVentas($items);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }

    public function item_typehead(){
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $busqueda = $this->input->get('search');
        $condicion = ['nombre'=>$busqueda,'empresa_id'=>$this->empresa_id];
        $items = $this->items->getItemConCategorias($condicion);
        $response= $this->items->getCollectionVentas($items);
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();

    }
    /*
      usado en anticipos
     */
    public function catalogo_ordenes_por_facturar(){
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $clause = [];
        $ordenesCompras = new Flexio\Modulo\OrdenesCompra\Repository\RepositorioOrdenCompra;
        $clause['empresa_id'] = $this->empresa_id;

        $id = $this->input->post('id');
        if(!empty($id)){
            $ordenCompra = $ordenesCompras->conId($id)->fetch();
        }else{
            $clause['id_estado'] = 2;
            $ordenCompra = $ordenesCompras->getOrdenes($this->empresa_id)->porFacturar()->conProveedorActivo()->fetch();
            $ordenCompra->load('anticipos_no_anulados');
            $ordenCompra = $ordenCompra->filter(function($ord){
                $total_anticipo = $ord->anticipos_no_anulados->sum('monto');
                return $ord->monto > $total_anticipo;
            })->values();
        }


        //$ordenCompra->load("proveedor");
        $response = $ordenCompra->map(function($orden){
            return [
                'id'=> $orden->id,
                'nombre' => $orden->numero .' - '.$orden->proveedor_nombre,
                'proveedor'  => $orden->proveedor,
                'monto' => $orden->monto - $orden->anticipos_no_anulados->sum('monto'),
                'anticipos' => $orden->anticipos_no_anulados
            ];
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }
    /*usado en anticipos*/
    public function catalogo_subcontratos_compras(){
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $clause = [];
        $subContratoCompras = new Flexio\Modulo\SubContratos\Repository\RepositorioSubContrato;
        $clause['empresa_id'] = $this->empresa_id;

        $id = $this->input->post('id');
        if(!empty($id)){
            $subContratoCompras = $subContratoCompras->getContratos($this->empresa_id)->conId($id)->fetch();
        }else{
            $subContratoCompras = $subContratoCompras->getContratos($clause)->contratoVigente()->conProveedorActivo()->fetch();
            $subContratoCompras->load('anticipos_no_anulados');
            $subContratoCompras = $subContratoCompras->filter(function($ord){
                $total_anticipo = $ord->anticipos_no_anulados->sum('monto');
                return $ord->subcontrato_montos()->sum('monto') > $total_anticipo;
            })->values();
        }


        $response = $subContratoCompras->map(function($sub){
            return [
                'id'=> $sub->id,
                'nombre' => $sub->codigo .' - '.$sub->proveedor_nombre,
                'proveedor'  => $sub->proveedor,
                'monto' => $sub->subcontrato_montos()->sum('monto') - $sub->anticipos_no_anulados->sum('monto'),
                'anticipos' => $sub->anticipos_no_anulados
            ];
        });
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }
    /*
    * @info usado en anticipos
    */
    function catalogo_ordenes_ventas_por_facturar(){
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $clause = [];
        $ordenesVenta = new Flexio\Modulo\OrdenesVentas\Repository\RepositoryOrdenVenta;
        $clause['empresa_id'] = $this->empresa_id;

        $id = $this->input->post('id');
        if(!empty($id)){
            $clause['id'] =$id;
            $ordenCompra = $ordenesVenta->getOrdenes($this->empresa_id)->fetch();
        }else{
            $clause['estado'] = 'por_facturar';
            $ordenCompra = $ordenesVenta->getOrdenes($this->empresa_id)->porFacturar()->fetch();
            $ordenCompra->load('anticipos_no_anulados');
            $ordenCompra = $ordenCompra->filter(function($ord){
                $total_anticipo = $ord->anticipos_no_anulados->sum('monto');
                return $ord->total > $total_anticipo;
            })->values();
        }


        $response = $ordenCompra->map(function($orden){
            return [
                'id'=> $orden->id,
                'nombre' => $orden->codigo .' - '.$orden->cliente_nombre,
                'proveedor'  => $orden->cliente,
                'monto' => $orden->total - $orden->anticipos_no_anulados->sum('monto'),
                'anticipos' => $orden->anticipos_no_anulados
            ];
        });
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }
    /*
     * usado para anticipos
     */
    function catalogo_contratos_ventas(){

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $clause = [];
        $contratoVentas = new Flexio\Modulo\Contratos\Repository\RepositorioContrato;
        $clause['empresa_id'] = $this->empresa_id;

        $id = $this->input->post('id');
        if(!empty($id)){

            $contratoVentas = $contratoVentas->getContratos($clause)->conId($id)->fetch();
        }else{
            $contratoVentas = $contratoVentas->getContratos($clause)->conClienteActivo()->fetch();
            $contratoVentas->load('anticipos_no_anulados');
            $contratoVentas = $contratoVentas->filter(function($ord){
                $total_anticipo = $ord->anticipos_no_anulados->sum('monto');
                return $ord->monto_contrato > $total_anticipo;
            })->values();
        }


        $response = $contratoVentas->map(function($sub){
            return collect([
                'id'=> $sub->id,
                'nombre' => $sub->codigo .' - '.$sub->cliente_nombre,
                'proveedor'  => $sub->cliente,
                'monto' => $sub->monto_contrato - $sub->anticipos_no_anulados->sum('monto'),
                'anticipos' => $sub->anticipos_no_anulados
            ]);
        });
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }
    /**/
    function facturas_por_cobrar_cobrado_parcial(){
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $this->empresa_id;
        $clause = [];
        $facturasObj = new Flexio\Modulo\FacturasVentas\Repository\FacturaVentaRepositorio;
        $id = $this->input->post('id');
        if(empty($id)){
            $faturas = $facturasObj->getFacturas($this->empresa_id)->porCobrar()->cobradoParcial()->fetch();
        }

        $faturas->load('clientes','ordenes_ventas');

        $response =  $faturas->map(function($fac){
            return collect([
                'id'=> $fac->id,
                'nombre'=> $fac->codigo ." ".$fac->nombre_cliente,
                'cliente'=> $fac->cliente,
                'ordenes_ventas' => $fac->ordenes_ventas
            ]);
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }


    /**
     * @return bool
     */
    function facturas_nota_debito()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $limit = 10;
        $this->empresa_id;
        $facturasRepository = new Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository;
        $clause = array('empresa_id' => $this->empresa_id);
        if (isset($_POST['q'])) {
            $clause['q'] = $_POST['q'];
        }
        $facturas =  $facturasRepository->cobradoCompletoSinNotaDebitoSinEstadosPorProveedor($clause, $limit)
            ->map(function($factura){
                $proveedor=$factura->proveedor;
                return [
                    'id' => $factura->id,
                    'factura_id' => $factura->id,
                    'proveedor_id'=> $proveedor->id,
                    'proveedor'=> $proveedor,
                    'proveedores'=>collect([$proveedor]),
                    'uuid_nota_debito' => $factura->uuid_nota_debito,
                    'monto_factura' => $factura->total,
                    'fecha_factura' => $factura->fecha_desde,
                    'centro_contable_id' => $factura->centro_contable_id,
                    'nombre' => (!empty($proveedor) ? $proveedor->nombre." -" : " -"). " {$factura->codigo}",
                    'prov_id' => $proveedor->id,
                ];
            });

        $response = $facturas;

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }
}
