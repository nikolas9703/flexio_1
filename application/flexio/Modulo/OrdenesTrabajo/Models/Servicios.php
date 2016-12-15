<?php
namespace Flexio\Modulo\OrdenesTrabajo\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Cotizaciones\Models\LineItem as LineItem;
use Flexio\Modulo\Inventarios\Models\Items;
use Flexio\Modulo\OrdenesTrabajo\Models\OrdenTrabajo;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Servicios extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['categoria_id', 'item_id', 'serie_id', 'equipo_id'];
    protected $revisionFormattedFieldNames = [
      'categoria_id'  => 'Categoría de item',
      'item_id'       => 'Item',
      'serie_id'      => 'Serie',
      'equipo_id'     => 'Equipo Asignado'
    ];

    protected $table    = 'odt_servicios';
    protected $fillable = ['orden_id', 'categoria_id', 'item_id', 'serie_id', 'equipo_id'];
    protected $guarded	= ['id'];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }

    public function orden_trabajo() {
    	return $this->hasOne('Flexio\Modulo\OrdenesTrabajo\Models\OrdenTrabajo', 'id', 'orden_id');
    }

	   public function items() {
        return $this->morphMany(LineItem::class, 'tipoable');
    }

    public function categoria_item() {
    	return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Categoria', 'categoria_item_id');
    }

    public function item() {
    	return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Items', '
    	');
    }

    public function categoria_servicio() {
    	return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Categoria', 'categoria_servicio_id');
    }

    public function item_servicio() {
    	return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Items', 'item_servicio_id');
    }
}
