<?php
namespace Flexio\Modulo\Oportunidades\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\Comentario\Models\Comentario;

class Oportunidades extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf =['codigo','empresa_id','created_at','updated_at','cliente_id','nombre','monto','fecha_cierre','asignado_a_id','etapa_id'];

    protected $table = 'opo_oportunidades';
    protected $fillable = ['codigo','empresa_id','created_at','updated_at','cliente_id','nombre','monto','fecha_cierre','asignado_a_id','etapa_id'];
    protected $guarded = ['id','uuid_oportunidad'];
    protected $appends = ['icono','enlace'];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_oportunidad' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }
    public function comentario_timeline() {

        return $this->morphMany(Comentario::class,'comentable');

    }

    public function getIconoAttribute()
    {
        return 'fa fa-line-chart';
    }

    public function getEnlaceAttribute()
    {
        return base_url("oportunidades/editar/".$this->uuid_oportunidad);
    }

    public function landing_comments()
    {
        return $this->morphMany(Comentario::class,'comentable');
    }

    public function getUuidOportunidadAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->codigo;
    }

    public function getNumeroDocumentoEnlaceAttribute()
    {
        $attrs = [
            'href'  => $this->enlace,
            'class' => 'link'
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlA')->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }

    public function getMontoCurrencyAttribute()
    {
        return "$".number_format($this->monto, 2, '.', ',');
    }


    public function getMontoLabelAttribute()
    {
        $colors = [
            '1' => '#F0AD4E',//prospecto
            '2' => '#5BC0DE',//en negociacion
            '3' => '#5CB85C',//ganada
            '4' => '#D9534F',//perdida
            '5' => '#000000'//anulada
        ];

        $attrs = [
            'style'  => 'border: '.$colors[$this->etapa_id].' solid 2px;color: '.$colors[$this->etapa_id].';width: 100%;background: transparent;padding: 2px 7px;text-align: center;font-weight: bold;'
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlLabel')->setAttrs($attrs)->setHtml($this->monto_currency)->getSalida();
    }

    public function getFechaCierreAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date);
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date);
    }

    public function setFechaCierreAttribute($date)
    {
        return $this->attributes['fecha_cierre'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

    public function cliente(){

        if($this->cliente_tipo == 'cliente_potencial'){

             return $this->belongsTo('Flexio\Modulo\ClientesPotenciales\Models\ClientesPotenciales','cliente_id');

        }

        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente','cliente_id');

    }

    public function relaciones(){

        return $this->hasMany('Flexio\Modulo\Oportunidades\Models\OportunidadesRelaciones', 'oportunidad_id');

    }

    public function usuario()
    {
        return $this->belongsTo('Flexio\Modulo\Usuarios\Models\Usuarios','asignado_a_id');
    }

    public function estado()
    {
        return $this->belongsTo('Flexio\Modulo\Oportunidades\Models\OportunidadesCatalogos','etapa_id');
    }

    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa','empresa_id');
    }

    public function scopeDeCodigo($query, $codigo = '')
    {
        return $query->where('codigo', 'like', '%'.$codigo.'%');
    }

    public function scopeDesde($query, $fecha_desde)
    {
        return $query->whereDate('created_at','>=',Carbon::createFromFormat('d/m/Y', $fecha_desde)->format('Y-m-d'));
    }

    public function scopeHasta($query, $fecha_hasta)
    {
        return $query->whereDate('created_at','<=',Carbon::createFromFormat('d/m/Y', $fecha_hasta)->format('Y-m-d'));
    }

    public function scopeDesdeMonto($query, $monto_desde)
    {
        return $query->where('monto','>=',$monto_desde);
    }

    public function scopeHastaMonto($query, $monto_hasta)
    {
        return $query->where('monto','<=',$monto_hasta);
    }


}
