<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Ordenes_orm extends Model
{

    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'ord_ordenes';


    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Indica el formato de la fecha en el modelo
     * en caso de que aplique
     *
     * @var string
     */
    protected $dateFormat = 'U';


    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['fecha', 'referencia', 'numero', 'uuid_centro','uuid_lugar', 'uuid_pedido', 'uuid_proveedor', 'credito', 'dias', 'id_estado', 'id_empresa'];


    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * Instancia de CodeIgniter
     */
    protected $Ci;


    public function __construct() {
        $this->Ci = & get_instance();

        $this->Ci->load->model("centros/Centros_orm");
        $this->Ci->load->model("proveedores/Proveedores_orm");
        $this->Ci->load->model("ordenes/Ordenes_items_orm");
        $this->Ci->load->model("usuarios/Usuario_orm");
    }
   /* public function operacion()
    {
        return $this->morphTo();
    }*/
    public function entradas()
    {
        return $this->morphMany('Entradas_ord', 'operacion');
    }

    public function facturas()
    {
        return $this->morphMany("Facturas_compras_orm", "operacion");
    }

    public static  function facturas_realizadas($ordenid = NULL)
    {
    	 $incompletas =  array();

     	$facturas_realizadas = Capsule::table('faccom_facturas AS f')
          ->rightjoin('faccom_facturas_items AS fi', 'fi.factura_id', '=', 'f.id')
          ->where('f.operacion_type', 'Ordenes_orm')->where('f.operacion_id', $ordenid)
          ->distinct()
          ->get(array('fi.id', 'fi.item_id'));

          if(!empty($facturas_realizadas )){
          	foreach ($facturas_realizadas AS $i => $row){
          		 $incompletas[] = $row->item_id;
          	}
          }

          return $incompletas;
    }

    public function getUuidOrdenAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getNumeroAttribute($value)
    {
        return is_numeric($value) ? 'OC'.sprintf('%08d', $value) : $value;
    }

    public function getNumeroDocumentoAttribute(){

        return $this->numero;

    }

    public function getFacturableAttribute()
    {
        //2 => Por facturar
        //3 => Facturada parcial
        return $this->id_estado == "2" || $this->id_estado == "3";
    }

    public function getProveedorNombreAttribute() {

        if(is_null($this->proveedor)){
            return "";
        }

        return $this->proveedor->nombre;

    }

    /**
     * Obtiene fecha de creacion formateada

     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    public function getFechaCreacionAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }

    /**
     * Obtiene el registro de centro asociado con el pedido.
     */
    public function centro()
    {
        return $this->belongsTo('Centros_orm', 'uuid_centro', 'uuid_centro');
    }

    public function bodega()
    {
        return $this->belongsTo('Bodegas_orm', 'uuid_lugar', 'uuid_bodega');
    }

    public function pedido()
    {
        return $this->belongsTo('Pedidos_orm', 'uuid_pedido', 'uuid_pedido');
    }

    public function proveedor()
    {
        return $this->belongsTo('Proveedores_orm', 'uuid_proveedor', 'uuid_proveedor');
    }

    //Usuario quien crea la Orden de Compra
    public function comprador()
    {
        return $this->belongsTo('Usuario_orm', 'creado_por', 'id');
    }

    /**
     * Obtiene el registro de centro asociado con el pedido.
     */
    public function estado()
    {
        $this->Ci->load->model("Ordenes_estados_orm");
        return $this->belongsTo('Ordenes_estados_orm', 'id_estado', 'id_cat');
    }

    /**
     * Obtiene la lista de items asociadas al pedido
     */
    public function items()
    {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Items', 'lines_items', 'tipoable_id', 'item_id')
                ->withPivot('id', 'uuid_line_item', 'categoria_id', 'empresa_id', 'cantidad', 'unidad_id', 'precio_unidad', 'impuesto_id', 'descuento', 'cuenta_id', 'precio_total', 'impuesto_total', 'descuento_total', 'observacion', 'cantidad2')
                ->where("tipoable_type", 'Flexio\\Modulo\\OrdenesCompra\\Models\\OrdenesCompra');
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("ord_ordenes.id_empresa", $empresa_id);
    }

    public function scopeDeCategoria($query, $categorias) {

          return $query->whereHas("items", function($items) use ($categorias){
              $items->whereIn("categoria_id", $categorias);
          });
        }

    public function scopeDePedido($query, $uuid_pedido)
    {
        return $query->where("uuid_pedido", hex2bin($uuid_pedido));
    }

    public function scopeDeFacturaDecompra($query, $factura_compra_id)
    {
        return $query->whereHas("facturas", function($factura) use ($factura_compra_id){
            $factura->where("id", $factura_compra_id);
        });
    }

    public function scopeListasParaFacturar($query)
    {
        //2.- Orden por facturar
        //3.- Orden facturada parcial
        return $query->where("id_estado", "2")->orWhere("id_estado", "3");
    }
    public function pedidos(){
      return $this->hasMany('Pedidos_orm', 'uuid_pedido');
    }

    public function findBy($clause = array())
    {

         $ordenes = Ordenes_orm::where("uuid_orden", $clause);



        return $ordenes->get();
    }


    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
       // $ordenes_id = !empty($clause["id"]) ? $clause["id"] : array();

    $query = self::with(array('proveedor', 'centro', 'estado' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}));

     foreach($clause AS $field => $value)
			{

				//Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}

				//verificar si valor es array
				if(is_array($value)){

					if(preg_match("/(fecha)/i", $field)){
						$query->where($field, $value[0], $value[1]);
					}else{
						$query->whereIn("id", $value);
					}



				}else{
					$query->where($field, '=', $value);
				}
			}
    return $query->get();
    }

    /**
     * Actualiza estado de la orden de compra actual
     */
    public function actualizarEstado()
    {
        //items de la orden de compra -> solo id
        $ordenes_items = [];
        foreach ($this->items as $item) {
            $ordenes_items[$item->id] = isset($ordenes_items[$item->id]) ? $ordenes_items[$item->id] + $item->pivot->cantidad : $item->pivot->cantidad;
        }

        //obtengo las facturas asociadas a la orden de compra
        //facturas que no tengan el estado de aprobadas
        //facturas que no tengan el estado de anulada
        $facturas = Facturas_compras_orm::where("operacion_type", "Ordenes_orm")
            ->where("operacion_id", $this->id);

        //items de las facturas asociadas a la orden de compra
        $facturas_items = [];
        foreach ($facturas->get() as $factura) {
            if ($factura->valida) {
                foreach ($factura->facturas_compras_items as $factura_item) {
                    $facturas_items[$factura_item->item_id] = isset($facturas_items[$factura_item->item_id]) ? $facturas_items[$factura_item->item_id] + $factura_item->cantidad : $factura_item->cantidad;
                }
            }
        }
        // Se inicializa el valor a Por facturar si no tiene facturas asociadas
        //2.- Por facturar
        //3.- Orden facturada parcial
        //4.- Orden facturada completo
        $this->id_estado = count($facturas_items) > 0 ? 4 : 2;

        if (count($facturas_items) < count($ordenes_items)) {
            $this->id_estado = 3;
        } else {
            foreach ($ordenes_items as $key => $value) {
                if ((isset($ordenes_items[$key]) && isset($facturas_items[$key])) && doubleval($facturas_items[$key]) < doubleval($ordenes_items[$key])) {
                    $this->id_estado = 3;
                    break;
                }
            }
        }

        $this->save();
    }

     public function present() {
      return new \Flexio\Modulo\OrdenesCompra\Presenter\OrdenCompraPresenter($this);
    }

}
