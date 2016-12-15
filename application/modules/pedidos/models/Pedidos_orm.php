<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;

class Pedidos_orm extends Model
{

    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'ped_pedidos';


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
    protected $fillable = ['fecha', 'numero', 'referencia', 'uuid_centro', 'id_estado', 'id_empresa'];


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

        //Cargo modelos
        $this->Ci->load->model("ordenes/Ordenes_orm");
        $this->Ci->load->model("pedidos/Pedidos_estados_orm");
        $this->Ci->load->model("facturas_compras/Facturas_compras_orm");
    }


    /**
     * Obtiene uuid_pedido
     *
     * Se convierte la data binaria en una representacion
     * hexadecimal
     *
     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    public function getUuidPedidoAttribute($value)
    {
        return strtoupper(bin2hex($value));
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

    public function getNumeroAttribute($value)
    {
        return sprintf('%s', $value);
    }

    /**
     * Obtiene el registro de centro asociado con el pedido.
     */
    public function centro()
    {
        $this->Ci->load->model("centros/Centros_orm");
        return $this->belongsTo('Centros_orm', 'uuid_centro', 'uuid_centro');
    }

    /**
     * Obtiene el registro de centro asociado con el pedido.
     */
    public function estado()
    {
        return $this->belongsTo('Pedidos_estados_orm', 'id_estado', 'id_cat');
    }

    /**
     * Obtiene la lista de items asociadas al pedido
     */
    public function items()
    {
        $this->Ci->load->model("pedidos/Pedidos_items_orm");
        return $this->hasMany('Pedidos_items_orm', 'id_pedido', 'id');
    }

    /**
     * Lo hago de esta manera porque lo identificadores no coinciden por el tema
     * de la transformacion de variables de binary a hexadecimal
     *
     * @return model
     */
    public function ordenes()
    {
        return Ordenes_orm::dePedido($this->uuid_pedido)->get();
    }
     public function facturas()
    {
         return Facturas_compras_orm::dePedido($this->uuid_pedido)->get();
    }

    public function comp_actualizarEstado()
    {
        $total      = 0;
        $anuladas   = 0;
        foreach($this->ordenes() as $ordenes)
        {
            $total++;
            if($ordenes->id_estado == "5")$anuladas++;
        }

//        echo "<pre>";
//        print_r($total."==".$anuladas);
//        echo "<pre>";
//        die();

        //Verifico si todas las ordenes estan anuladas
        //Si es verdadero el pedido pasa a estado "Por aprobar"
        if($total == $anuladas)
        {
            //1.- Por aprobar
            $estado = "1";
        }
        else
        {
            //Si la cantidad Ordenada o Trasladada es menor a la que exige el pedido
            //este siempre tendra el estado parcial. Cuando todas las cantidades satisfagan
            //lo requerido el pedido pasa a estado procesado
            //3.- Parcial || 4.- Procesado
            $estado = "4";
            foreach ($this->items as $pedido_item)
            {
                $cantidadOrdenadaTrasladada = $pedido_item->item->cantidadOrdenadaTrasladada($this->uuid_pedido);

                if($cantidadOrdenadaTrasladada < $pedido_item->cantidadPorFactorConversion())
                {
                    $estado = "3";
                    break;
                }
            }
        }


        $this->id_estado = $estado;
        $this->save();
    }

    public static function findByUuid($uuid){
        return self::where('uuid_pedido',hex2bin($uuid))->first();
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("id_empresa", $empresa_id);
    }

    public function scopeDeCentro($query)
    {
        return $query;
    }

    public function ordenes_compras()
    {
        return $this->hasMany("Ordenes_orm", "uuid_pedido", "uuid_pedido");
    }

    public function scopeDeOrdenDeCompra($query, $orden_compra_id)
    {
        return $query->whereHas("ordenes_compras", function($q) use ($orden_compra_id){
            $q->where("id", $orden_compra_id);
        });
    }

    public function scopeDeFacturaDeCompra($query, $factura_compra_id)
    {
        return $query->whereHas("ordenes_compras", function($orden_compra) use ($factura_compra_id){
            $orden_compra->whereHas("facturas", function($facturas) use($factura_compra_id){
                $facturas->where("id", $factura_compra_id);
            });
        });
    }

    public function getComprableAttribute()
    {
        $items_por_aprobar = $this->items()
                            ->join('inv_items', 'inv_items.id', '=', 'ped_pedidos_inv_items.id_item')
                            ->where('inv_items.estado', 9)->count();

        //en cotizacion o pedido parcial
        return ($this->id_estado == 2 || $this->id_estado == 3) && $items_por_aprobar == 0;
    }

    public function scopeEnCotizacion($query)
    {
        return $query->where("id_estado", "2");//2 => En cotizacion
    }

    public function scopeEnCotizacionOParcial($query)
    {
        return  $query->where("id_estado", "2")//2 => En cotizacion
                ->orWhere("id_estado", "3"); //3 => Parcial
    }

    public function comentario(){
    	return $this->morphMany(Comentario::class,'comentable');
    }
}
