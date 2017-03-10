<?php
namespace Flexio\Modulo\Conciliaciones\Models;

use Illuminate\Database\Eloquent\Model      as Model;
use Illuminate\Database\Capsule\Manager     as Capsule;
use Carbon\Carbon                           as Carbon;

class Conciliaciones extends Model
{
    protected $table = 'conc_conciliaciones';
    protected $fillable = [
        'codigo',
        'balance_banco',
        'balance_flexio',
        'diferencia',
        'fecha_inicio',
        'fecha_fin',
        'cuenta_id',
        'empresa_id',
        'created_by'
    ];
    protected $guarded = ['id','uuid_conciliacion'];


    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_conciliacion' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    //Gets
    public function getUuidConciliacionAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getFechaInicioAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d h:i:s', $date)->format('d/m/Y');
    }

    public function getFechaFinAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d h:i:s', $date)->format('d/m/Y');
    }

    public function getBalanceBancoLabelAttribute()
    {
        $attrs = [
            "style" => "width:100%;padding:2px 7px;text-align:center;font-weight:bold;border:#27AAE1 solid 2px;color: #27AAE1;"
        ];
        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType("htmlLabel")->setAttrs($attrs)->setHtml($this->balance_banco)->getSalida();
    }

    public function getBalanceFlexioLabelAttribute()
    {
        $attrs = [
            "style" => "width:100%;padding:2px 7px;text-align:center;font-weight:bold;border:#46BD5B solid 2px;color: #46BD5B;"
        ];
        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType("htmlLabel")->setAttrs($attrs)->setHtml($this->balance_flexio)->getSalida();
    }

    public function setDiferenciaAttribute($value){
        $value = str_replace("$", "", $value);
        $this->attributes['diferencia'] =  str_replace(",", "", $value);
    }

    public function setBalanceFlexioAttribute($value){
        $value = str_replace("$", "", $value);
        $this->attributes['balance_flexio'] =  str_replace(",", "", $value);   
    }

    public function setBalanceBancoAttribute($value){
        $value = str_replace("$", "", $value);
        $this->attributes['balance_banco'] =  str_replace(",", "", $value);   

    }

    public function getDiferenciaLabelAttribute()
    {
        $attrs = [
            "style" => "width:100%;padding:2px 7px;text-align:center;font-weight:bold;border:#D9534F solid 2px;color: #D9534F;"
        ];
        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType("htmlLabel")->setAttrs($attrs)->setHtml($this->diferencia)->getSalida();
    }

    public function getCodigoEnlaceAttribute()
    {
        $attrs = [
            "href"  => base_url("conciliaciones/ver/".$this->uuid_conciliacion),
            "class" => "link"
        ];
        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->codigo)->getSalida();
    }

    public function getRangoFechaAttribute()
    {
        return $this->fecha_inicio .' - '.$this->fecha_fin;
    }

    //Scopes
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeCuenta($query, $cuenta_id)
    {
        return $query->where("cuenta_id", $cuenta_id);
    }

    public function scopeDeFechaInicio($query, $fecha_inicio)
    {
        $aux = Carbon::createFromFormat('d/m/Y', $fecha_inicio)->format('Y-m-d');
        return $query->whereDate("fecha_inicio", '>=', $aux);
    }

    public function scopeDeFechaFin($query, $fecha_fin)
    {
        $aux = Carbon::createFromFormat('d/m/Y', $fecha_fin)->format('Y-m-d');
        return $query->where("fecha_fin", '<=', $aux);
    }


    //otros
    public function cuenta()
    {
        return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Cuentas', 'cuenta_id');
    }

    public function balance_transacciones(){
        return $this->hasMany('Flexio\Modulo\EntradaManuales\Models\AsientoContable','conciliacion_id');
    }

}
