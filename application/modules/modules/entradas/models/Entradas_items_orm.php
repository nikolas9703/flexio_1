<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Entradas_items_orm extends Model
{
    
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'ent_entradas_items';
    
    
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
    protected $dateFormat = 'Y-m-d H:i:s';
    
    
    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['*'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    
    public function operacion()
    {
        return $this->morphTo();
    }
    
    
    public function entrada()
    {
        return $this->belongsTo('Entradas_orm', 'entrada_id', 'id');
    }
    
    public function orden_item()
    {
        return $this->belongsTo('Ordenes_items_orm', 'operacion_id', 'id');
    }
    
    public function traslado_item()
    {
        return $this->belongsTo('Traslados_items_orm', 'operacion_id', 'id');
    }
    
    public static function findByOperacion($operacion_id, $operacion_type){
        return  self::where('operacion_id', $operacion_id)
                ->where("operacion_type", $operacion_type)
                ->first();
    }
    
    public function scopeWithExistencia($query, $item, $uuid_bodega)
    {
        return $query->where(function($q) use ($item, $uuid_bodega){
            $q->where("operacion_type",'Ordenes_items_orm')
            ->whereHas("orden_item", function($q2) use ($item, $uuid_bodega){
                $q2->where("id_item", $item->id)
                ->whereHas("orden", function($q3) use ($uuid_bodega){
                    $q3->where("uuid_lugar", hex2bin(strtolower($uuid_bodega)));
                });
            })
            ->orWhere("operacion_type",'Traslados_items_orm')
            ->whereHas("traslado_item", function($q2) use ($item, $uuid_bodega){
                $q2->where("id_item", $item->id)
                ->whereHas("traslado", function($q3) use ($uuid_bodega){
                    $q3->where("uuid_lugar", hex2bin(strtolower($uuid_bodega)));
                });
            });
        });
    }
    
    public function scopeWithExistenciaAllItems($query, $uuid_bodega)
    {
        return $query->where(function($q) use ($uuid_bodega){
            $q->where("operacion_type",'Ordenes_items_orm')
            ->whereHas("orden_item", function($q2) use ($uuid_bodega){
                $q2->whereHas("orden", function($q3) use ($uuid_bodega){
                    $q3->where("uuid_lugar", hex2bin(strtolower($uuid_bodega)));
                });
            })
            ->orWhere("operacion_type",'Traslados_items_orm')
            ->whereHas("traslado_item", function($q2) use ($uuid_bodega){
                $q2->whereHas("traslado", function($q3) use ($uuid_bodega){
                    $q3->where("uuid_lugar", hex2bin(strtolower($uuid_bodega)));
                });
            });
        })->where("ent_entradas_items.cantidad_recibida", ">", Capsule::raw("ent_entradas_items.cantidad_saliente"));
    }
    
    public function scopeWithExistenciaAllBodegas($query, $item)
    {
        return $query->where(function($q) use ($item){
            $q->where("operacion_type",'Ordenes_items_orm')
            ->whereHas("orden_item", function($q2) use ($item){
                $q2->where("id_item", $item->id)
                ->whereHas("orden", function($q3){
                    //$q3->where("uuid_lugar", hex2bin(strtolower($uuid_bodega)));
                });
            })
            ->orWhere("operacion_type",'Traslados_items_orm')
            ->whereHas("traslado_item", function($q2) use ($item){
                $q2->where("id_item", $item->id)
                ->whereHas("traslado", function($q3){
                    //$q3->where("uuid_lugar", hex2bin(strtolower($uuid_bodega)));
                });
            });
        })->where("ent_entradas_items.cantidad_recibida", ">", Capsule::raw("ent_entradas_items.cantidad_saliente"));
    }
    
    public function cantidadDisponible()
    {
        return $this->cantidad_recibida - $this->cantidad_saliente;
    }
}