<?php
namespace Flexio\Modulo\Oportunidades\Models;

use Illuminate\Database\Eloquent\Model;
//use Carbon\Carbon;
//use Illuminate\Database\Capsule\Manager as Capsule;


class OportunidadesCatalogos extends Model
{
    
    protected $table    = 'opo_oportunidades_catalogos';
    protected $fillable = ['nombre','valor','tipo'];
    protected $guarded  = ['id'];


    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    
    public function getNombreSpanAttribute()
    {
        $colors = [
            '1' => '#F0AD4E',//prospecto
            '2' => '#5BC0DE',//en negociacion
            '3' => '#5CB85C',//ganada
            '4' => '#D9534F',//perdida
            '5' => '#000000'//anulada
        ];
        $attrs = [
            'class' => 'label',
            'style' => "color: white;background-color: ".$colors[$this->id].";font-size: 10px;font-weight: 600;padding: 5px 8px;text-shadow: none;margin: 3px;"
        ];
        
        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlSpan')->setAttrs($attrs)->setHtml($this->nombre)->getSalida();
    }

}
