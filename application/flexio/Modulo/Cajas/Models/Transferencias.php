<?php
namespace Flexio\Modulo\Cajas\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Cajas\Models\TransferenciasPagos as TransferenciasPagos;
use Flexio\Modulo\Contabilidad\Models\Cuentas as Cuentas;
use Flexio\Modulo\Cajas\Models\Cajas as Cajas;

class Transferencias extends Model
{
    protected $table    = 'ca_transferencias';
    protected $fillable = ['empresa_id', 'caja_id', 'cuenta_id',  'numero', 'monto', 'fecha', 'creado_por','transferencia_desde','tipo_transferencia_hasta'];
    protected $guarded	= ['id'];
    protected $appends      = ['enlace'];
    public function pagos(){
    	return $this->hasMany(TransferenciasPagos::class, 'transferencia_id');
    }

    public function caja(){
        return $this->belongsTo(Cajas::class, 'caja_id', 'id');
    }

    public function cuenta(){
    	return $this->hasOne(Cuentas::class, 'id', 'cuenta_id');
    }
    public function empresa()
    {
    	return  $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa', 'empresa_id');
    }
    public function getNumeroDocumentoEnlaceAttribute()
    {
    	$attrs = [
    	"href"  => $this->enlace,
    	"class" => "link"
    			];

    	$html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
    	return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero)->getSalida();
    }
 
    public function getEnlaceAttribute()
    {

    	return base_url("cajas/ver/".$this->caja->uuid_caja);
    }


}
