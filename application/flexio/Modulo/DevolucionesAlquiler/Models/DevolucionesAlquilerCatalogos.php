<?php
namespace Flexio\Modulo\DevolucionesAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
//use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class DevolucionesAlquilerCatalogos extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = false;
    protected $keepRevisionOf = ['nombre','valor','tipo'];
    protected $table    = 'devalq_devoluciones_alquiler_catalogos';
    protected $fillable = ['nombre','valor','tipo'];
    protected $guarded  = ['id'];


    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    public static function boot() {
        parent::boot();
    }

    public function getNombreSpanAttribute()
    {
        $colors = [
            '1' => '#F0AD4E',//por aprobar
            '2' => '#5CB85C',//devuelto
            '3' => '#222222',//anulado
        ];
        $attrs = [
            'class' => 'label',
            'style' => "color: white;background-color: ".$colors[$this->id].";font-size: 10px;font-weight: 600;padding: 5px 8px;text-shadow: none;margin: 3px;"
        ];
        
        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlSpan')->setAttrs($attrs)->setHtml($this->nombre)->getSalida();
    }

}
