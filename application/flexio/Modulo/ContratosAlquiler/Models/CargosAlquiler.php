<?php
namespace Flexio\Modulo\ContratosAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\Inventarios\Models\Items as Itemsprueba; //dath
use Flexio\Modulo\Contabilidad\Models\Impuestos;

class CargosAlquiler extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['uuid_cargo', 'numero', 'empresa_id','cargoable_id','cargoable_type','contrato_id','item_id','serie','cantidad','tarifa','total_cargo','ciclo','ciclo_id','devuelto','fecha_cargo', 'fecha_devolucion','created_at','updated_at'];

    protected $table    = 'car_cargos_alquiler';
    protected $fillable = ['uuid_cargo', 'numero', 'empresa_id','cargoable_id','cargoable_type','contrato_id','item_id','serie','cantidad','tarifa','total_cargo','ciclo','ciclo_id','devuelto','cantidad_devuelta','fecha_cargo', 'fecha_devolucion', 'estado','created_at','updated_at'];
    protected $guarded  = ['id'];

    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_cargo' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }
    public static function boot() {
        parent::boot();
    }

    public function cargoable(){
    	return $this->morpTo();
    }

    public function getSePuedeAnularAttribute()
    {
        $fecha_cargo = $this->fecha_cargo;
        $item_id = $this->item_id;
        $ordenes_alquiler = $this->contrato->ordenes_alquiler()->where(function($orden_alquiler) use ($fecha_cargo, $item_id){
            $orden_alquiler->whereHas('items',function($line_item) use ($fecha_cargo, $item_id){
                $line_item->where('lines_items.tarifa_fecha_desde', '<=', $fecha_cargo);
                $line_item->where('lines_items.tarifa_fecha_hasta', '>=', $fecha_cargo);
                $line_item->where('lines_items.item_id', $item_id);
            });
        })->first();


        if(count($ordenes_alquiler) && $ordenes_alquiler->where('estado','facturado_parcial')->orWhere('estado','facturado_completo')->count() || $this->estado == 'anulado')
        {
            return false;
        }

        if(count($ordenes_alquiler))
        {
            $line_item = $ordenes_alquiler->items->first();
            $impuesto = Impuestos::find($line_item->impuesto_id);
            $impuesto_porcentaje = count($impuesto) ? $impuesto->impuesto : 0;
            $line_item->tarifa_cantidad_periodo -= 1;
            $precio_total = $line_item->tarifa_cantidad_periodo * $line_item->precio_unidad;
            $line_item->descuento_total = $precio_total * ($line_item->descuento/100);
            $line_item->impuesto_total = ($precio_total - $line_item->descuento_total)  * ($impuesto_porcentaje/100);
            $line_item->precio_total = $precio_total;//subtotal -> no incluye descuento ni impuesto
            $line_item->save();
            //la orden de alquiler actualiza los datos automaticamente al guardar los cambios debido
            //a que el componente de tabla dinamica dell formulario crear/editar recalcula en funcion
            //de los valores de lines_items
        }
        return true;
    }

    public function contrato()
    {
        return $this->belongsTo('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler', 'contrato_id');
    }

    public function entregas_alquiler(){
    	return $this->belongsTo(EntregasAlquiler::class, 'cargoable_id');
    }

    public function item() {
        return $this->belongsTo(Itemsprueba::class, 'item_id');//dath
    }

    public function scopePorFacturar($query) {
        return $query->where('estado', 'por_facturar');
    }

    public function scopeClauseFiltro($query, $clause) {
      $item = !empty($clause["item"]) ? $clause["item"] : array();
  		$contrato_codigo = !empty($clause["contrato"]) ? $clause["contrato"] : array();
      $contrato_id = !empty($clause["contrato_id"]) ? $clause["contrato_id"] : array();

  		//filtro item
  		if(!empty($item)){
  			$items = Itemsprueba::where("nombre", "LIKE", "%$item%")->get(array("id"))->pluck('id')->toArray();
  			$query->whereIn("item_id", $items);
			$clause["item_id"] = $items[0];//dath
  		}

      //Clause Contrato
  		if(!empty($contrato_codigo) || !empty($contrato_id)){

        $query_contrato = ContratosAlquiler::deEmpresa($clause);
        if(!empty($contrato_codigo)){
          $query_contrato->deCodigo($contrato_codigo);
        }
        if(!empty($contrato_id)){
          $query_contrato->where("id", $contrato_id);
          unset($clause["contrato_id"]);
        }
        $contratos = $query_contrato->get(array("id"))->pluck('id')->toArray();

  			$query->whereIn("contrato_id", $contratos);
  		}

  		//Si existen variables de limite
  		if($clause!=NULL && !empty($clause) && is_array($clause))
  		{
  			foreach($clause AS $field => $value)
  			{
  				if($field == "nombre_centro" || $field == "fecha_entrega" || $field == "impuesto" || $field == "item" || $field == "contrato" || $field == "contrato_id"){
  					continue;
  				}

  				//Verificar si el campo tiene el simbolo @ y removerselo.
  				if(preg_match('/@/i', $field)){
  					$field = str_replace("@", "", $field);
  				}

  				//verificar si valor es array
  				if(is_array($value)){
            if(empty($value[1])){
              continue;
            }
  					$query->where($field, $value[0], $value[1]);
  				}else{
            if(empty($value)){
              continue;
            }
  					$query->where($field, $value);
  				}
  			}
  		}
      //dd($query);
      return $query;
    }
}
