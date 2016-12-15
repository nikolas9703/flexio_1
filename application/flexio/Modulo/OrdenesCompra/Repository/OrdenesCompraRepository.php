<?php
namespace Flexio\Modulo\OrdenesCompra\Repository;

//utilities
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

//repositorios
use Flexio\Modulo\Inventarios\Repository\UnidadesRepository as unidadesRep;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository as impuestosRep;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository as cuentasRep;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Entradas\Repository\EntradasRepository as entradasRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Inventarios\Models\LinesItems;
use Flexio\Modulo\Pedidos\Repository\PedidoRepository;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository;
//modelos
use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra as OrdenesCompra;
use Flexio\Modulo\OrdenesCompra\Models\OrdenesHistorial;
use Flexio\Modulo\Usuarios\Models\Usuarios;

use Flexio\Library\Util\FlexioSession;
class OrdenesCompraRepository{

    //variables del entorno
    private $prefijo = "OC";

    //repositorios
    private $unidadesRep;
    private $impuestosRep;
    private $cuentasRep;
    private $bodegasRep;
    private $entradasRep;
    private $itemsRep;
    protected $PedidoRepository;
    protected $PoliticasRepository;
     protected $session;

    public function __construct() {
        $this->unidadesRep  = new unidadesRep();
        $this->impuestosRep = new impuestosRep();
        $this->cuentasRep   = new cuentasRep();
        $this->bodegasRep   = new bodegasRep();
        $this->entradasRep  = new entradasRep();
        $this->itemsRep     = new itemsRep();
        $this->PedidoRepository = new PedidoRepository();
        $this->PoliticasRepository = new PoliticasRepository();
         $this->session = new FlexioSession;
    }

    public function find($orden_compra_id) {
        return OrdenesCompra::find($orden_compra_id);
    }

    public function findByUuid($uuid) {
        return OrdenesCompra::findByUuid($uuid);
    }

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $ordenes_compra = OrdenesCompra::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($ordenes_compra, $clause);

        if($sidx!=NULL && $sord!=NULL){$ordenes_compra->orderBy($sidx, $sord);}
        if($limit!=NULL){$ordenes_compra->skip($start)->take($limit);}

        return $ordenes_compra->get();
    }

    public function count($clause = array())
    {
        $ordenes_compra = OrdenesCompra::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($ordenes_compra, $clause);

        return $ordenes_compra->count();
    }

    private function _filtros($ordenes_compra, $clause)
    {
        if(isset($clause["uuid_pedido"]) and !empty($clause["uuid_pedido"])){$ordenes_compra->dePedido($clause["uuid_pedido"]);}
        if(isset($clause["id_estado"]) and !empty($clause["id_estado"])){$ordenes_compra->where('id_estado',$clause["id_estado"]);}
        if(isset($clause["id"]) and !empty($clause["id"])){$ordenes_compra->where('id',$clause["id"]);}
        //bool -> true and false
        if(isset($clause["facturables"]) and $clause["facturables"]){$ordenes_compra->facturables();}
    }

    public function getCollectionOrdenesCompra($ordenes_compra){

        return $ordenes_compra->map(function($orden_compra){

            $articulo = new \Flexio\Library\Articulos\OrdenCompraArticulo;
            $proveedor_name = count($orden_compra->proveedor) ? $orden_compra->proveedor->nombre : "Proveedor inactivo";
            $proveedor_id = count($orden_compra->proveedor) ? $orden_compra->proveedor->id : "";

            return [
                'id' => $orden_compra->id,
                'nombre' => $proveedor_name .' - '.$orden_compra->numero .' ('.$orden_compra->estado->etiqueta.')',
                'proveedor_id' => $proveedor_id,
                'terminos_pago' => $orden_compra->termino_pago,
                'centro_contable_id' => $orden_compra->centro_contable->id,
                'recibir_en_id' => $orden_compra->bodega->id,
                "saldo_proveedor" => 0,
                "credito_proveedor" => 0,
                "articulos" => $articulo->get($orden_compra->items, $orden_compra)
            ];
        });

    }

    public function getColletionCampos($orden) {


        $articulo = new \Flexio\Library\Articulos\OrdenCompraArticulo;

        return collect([
            "fecha" => $orden->fecha_creacion,
            "proveedor_id" => $orden->uuid_proveedor,
            "centro_contable_id" => $orden->uuid_centro,
            "recibir_en_id" => $orden->uuid_lugar,
            "referencia" => isset($orden->referencia) ? $orden->referencia : "",
            "terminos_pago" => $orden->termino_pago,
            "codigo" => $orden->numero,
            "estado" => $orden->id_estado,
            "observaciones" => $orden->observaciones,
            "id" => $orden->id,
            "creado_por" => $orden->creado_por,
            "articulos" => $articulo->get($orden->items, $orden),
            "comentario" => $orden->comentario,
            "saldo" => count($orden->proveedor) ? $orden->proveedor->saldo_pendiente : 0,
            "credito" => count($orden->proveedor) ? $orden->proveedor->credito : 0,
            "valido_hasta" => $orden->valido_hasta,
            "proveedor_info" => $orden->proveedor,

        ]);

    }

    public function getCollectionCamposItems($items) {
        $aux = [];
        foreach ($items as $item)
        {
            $unidad     = $this->unidadesRep->find($item->pivot->unidad_id);
            $impuesto   = $this->impuestosRep->find($item->pivot->impuesto_id);
            $cuenta     = $this->cuentasRep->find($item->pivot->cuenta_id);
            $aux[] = array(
                "categoria"         => $item->pivot->categoria_id,
                 "item"             => $item->uuid_item,
                "descripcion"       => $item->descripcion,
                "cantidad"          => $item->pivot->cantidad,
                "unidad"            => $unidad->uuid_unidad,
                "precio_unidad"     => $item->pivot->precio_unidad,
                "impuesto"          => $impuesto->uuid_impuesto,
                "descuento"         => $item->pivot->descuento,
                "cuenta"            => $cuenta->uuid_cuenta,
                "precio_total"      => "",//se calcula
                "id_pedido_item"    => $item->pivot->id
            );
        }
        return $aux;
    }

    public function create($post) {

        $orden = new OrdenesCompra;
        $orden->fecha_creacion = Carbon::createFromFormat('d/m/Y', str_replace('-', '/', $post['campo']['fecha_creacion']));
        $orden->creado_por = $post['campo']["usuario_id"];
        $orden->id_empresa = $post['campo']["empresa_id"];
        $orden->numero = $post['campo']["codigo"];

        $pedido = $this->PedidoRepository->find($post['empezable_id']);
        $orden->uuid_pedido = count($pedido) ? hex2bin($pedido->uuid_pedido) : '';

        $this->_save($orden, $post);
        $this->_syncItems($orden, $post["items"]);

        return $orden;

    }

    private function _save($orden, $post){

        $campo  = $post["campo"];
        //dd($post);
        $orden->id_estado       = $campo["estado"];
        $orden->referencia      = $campo["referencia"];
        $orden->observaciones   = $campo["observaciones"];
        $orden->termino_pago    = $campo["termino_pago"];
        $orden->monto           = $campo["monto"];

        $orden->uuid_centro     = hex2bin(strtolower($campo["centro"]));
        $orden->uuid_lugar      = hex2bin(strtolower($campo["lugar"]));
        $orden->uuid_proveedor  = hex2bin(strtolower($campo["proveedor"]));
        //$orden->uuid_pedido     = hex2bin(strtolower($campo["pedido"]));

        $orden->save();

        //$this->addHistorial($orden,'creado', array());

    }

    private function _createEntrada($orden, $post)
    {
        $campo = $post["campo"];
        if($orden->id_estado == "2")//Orden de Compra -> Por Facturar || Este estado no puede ser modificado despues de ser asignado
        {
            if($this->bodegasRep->find($orden->bodega->id)->raiz->entrada_id == 1)// 1 -> entrada manual : 2 -> entrada automatica
            {
                //estado_id:1 => Entrada por recibir...
                $this->entradasRep->create(array("tipo_id" => $orden->id, "estado_id" => "1", "tipo" => "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra", "empresa_id" => $campo["empresa_id"]));
            }
        }
    }

    private function _syncItems($orden, $items)
    {
        $orden->lines_items()->whereNotIn('id',array_pluck($items,'id_pedido_item'))->delete();
        foreach($items as $oi){

                $line_item_id = (isset($oi['id_pedido_item']) and !empty($oi['id_pedido_item'])) ? $oi['id_pedido_item'] : '';
                $line_item = $orden->lines_items()->firstOrNew(['id'=>$line_item_id]);

                $line_item->categoria_id = $oi["categoria"];
                $line_item->item_id = $oi["item_id"];
                $line_item->cuenta_id = $oi["cuenta"];
                $line_item->cantidad = $oi["cantidad"];
                $line_item->cantidad2 = 0;
                $line_item->unidad_id = $oi["unidad"];
                $line_item->precio_unidad = $oi["precio_unidad"];
                $line_item->descuento = $oi["descuento"];
                $line_item->impuesto_id = $oi["impuesto"];
                $line_item->impuesto_total = $oi["impuesto_total"];
                $line_item->descuento_total = $oi["descuento_total"];
                $line_item->precio_total = $oi["precio_total"];
                $line_item->comentario = (isset($oi['comentario']))?$oi['comentario']:'';
                //opcionales
                $line_item->atributo_id = (isset($oi['atributo_id']) and !empty($oi['atributo_id'])) ? $oi['atributo_id'] : 0;
                $line_item->atributo_text = (isset($oi['atributo_text']) and !empty($oi['atributo_text'])) ? $oi['atributo_text'] : '';

                $line_item->save();
        }

    }

    public function save($post) {

        $orden = OrdenesCompra::find($post['campo']['id']);
        $this->_save($orden, $post);
        $this->_syncItems($orden, $post["items"]);
        $this->_createEntrada($orden, $post);
         return $orden;
    }

    function agregarComentario($ordenId, $comentarios){
      	$ordenes = OrdenesCompra::find($ordenId);
    	$comentario = new Comentario($comentarios);

    	$ordenes->comentario()->save($comentario);

    	return $ordenes;
    }
     /*function createDescripcion($tipo = array(), $objeto= array()){

         if($tipo == 'comentario'){

          }
         if($tipo == 'creado'){
             $descripcion = "Se ha creado la orden";
          }

      	return  $descripcion;
    }*/

     function addHistorial($orden = array(),  $objeto ){

       $descripcion = strip_tags($objeto['comentario']);

        $create = [
          'codigo' => $orden->numero,
          'usuario_id' => $orden->creado_por,
          'empresa_id' => $orden->id_empresa,
          'orden_id'=> $orden->id,
          'tipo'   => 'comentario',
          'descripcion' => $descripcion
      ];
         OrdenesHistorial::create($create);
   }
   function gePoliticasTransaccciones($role){

       $clause = array(
           "modulo_id" =>2,
           "empresa_id"=>$this->session->empresaId(),
           "role_id"=> $role,
           //"usuario_id"=>$this->session->usuarioId(), //Comentado  por orden de Roberto
           "estado_id" =>1
       );
       $politicas = $this->PoliticasRepository->getAllPoliticas($clause);

       return $politicas;
   }
}
