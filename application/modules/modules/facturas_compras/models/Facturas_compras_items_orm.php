<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Facturas_compras_items_orm extends Model
{

    protected $table = 'faccom_facturas_items';

    protected $fillable = ['*'];

    protected $guarded = ['id'];

    protected $codeIgniter;


    public function __construct(array $attributes = array()){
        $this->codeIgniter = & get_instance();

        parent::__construct($attributes);

        //Cargando Modelos
        $this->codeIgniter->load->model("inventarios/Items_orm");
        $this->codeIgniter->load->model("contabilidad/Cuentas_orm");
        $this->codeIgniter->load->model("contabilidad/Impuestos_orm");
    }

    public function item()
    {
        return $this->belongsTo('Items_orm', 'item_id', 'id');
    }

    public function cuentaDeGasto()
    {
        return $this->belongsTo('Cuentas_orm', 'cuenta_id', 'id');
    }

    public function facturas_compras_items(){
        return $this->belongsTo('Facturas_compras_items_orm','factura_id');
    }

    public function impuesto(){
        return $this->belongsTo('Impuestos_orm','impuesto_id')->select(['uuid_impuesto','impuesto','id','cuenta_id','nombre']);
    }

    public function inventario_item(){
      return $this->belongsTo('Items_orm','item_id')->select(['id','codigo','nombre', 'descripcion']);
    }
    
}
