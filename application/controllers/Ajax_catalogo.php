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
        $items = $this->items->getChunck(['categoria_id'=>$clause['categoria_id'],'empresa_id'=>$this->empresa_id]);
        $response['items'] = $this->items->getCollectionVentas($items);

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

  public function catalogo_ordenes_por_facturar(){
    if(!$this->input->is_ajax_request()){
        return false;
    }
     $clause = [];
    $ordenesCompras = new Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository;
    $clause['empresa_id'] = $this->empresa_id;

    $id = $this->input->post('id');
    if(!empty($id)){
      $clause['id'] =$id;
    }else{
      $clause['id_estado'] = 2;
    }

    $ordenCompra = $ordenesCompras->get($clause);
    //$ordenCompra->load("proveedor");
    $response = $ordenCompra->map(function($orden){
      return [
        'id'=> $orden->id,
        'nombre' => $orden->numero .' - '.$orden->proveedor_nombre,
        'proveedor'  => $orden->proveedor,
        'monto' => $orden->monto
      ];
    });

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
    exit();
  }

  public function catalogo_subcontratos_compras(){
    if(!$this->input->is_ajax_request()){
        return false;
    }
    $clause = [];
    $subContratoCompras = new Flexio\Modulo\SubContratos\Repository\SubContratoRepository;
    $clause['empresa_id'] = $this->empresa_id;

    $id = $this->input->post('id');
    if(!empty($id)){
      $clause['id'] = $id;
    }

    $subContratoCompras = $subContratoCompras->getSubContratos($clause);
    $response = $subContratoCompras->map(function($sub){
      return [
        'id'=> $sub->id,
        'nombre' => $sub->codigo .' - '.$sub->proveedor_nombre,
        'proveedor'  => $sub->proveedor,
        'monto' => $sub->monto_subcontrato
      ];
    });
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
    exit();
  }

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
    }


    $response = $ordenCompra->map(function($orden){
      return [
        'id'=> $orden->id,
        'nombre' => $orden->codigo .' - '.$orden->cliente_nombre,
        'proveedor'  => $orden->cliente,
        'monto' => $orden->total
      ];
    });
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
    exit();
  }

  function catalogo_contratos_ventas(){

      if(!$this->input->is_ajax_request()){
          return false;
      }
      $clause = [];
      $contratoVentas = new Flexio\Modulo\Contratos\Repository\ContratoRepository;
      $clause['empresa_id'] = $this->empresa_id;

      $id = $this->input->post('id');
      if(!empty($id)){
        $clause['id'] = $id;
      }

      $contratoVentas = $contratoVentas->getContratos($clause);
      $response = $contratoVentas->map(function($sub){
        return collect([
          'id'=> $sub->id,
          'nombre' => $sub->codigo .' - '.$sub->cliente_nombre,
          'proveedor'  => $sub->cliente,
          'monto' => $sub->monto_contrato
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


}
