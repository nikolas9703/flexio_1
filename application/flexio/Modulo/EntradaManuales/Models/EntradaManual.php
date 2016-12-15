<?php

namespace Flexio\Modulo\EntradaManuales\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\GenerarCodigo;


class EntradaManual extends Model
{
    protected $table = 'contab_entrada_manual';
    protected $fillable = ['uuid_entrada','codigo','nombre','empresa_id'];
    protected $guarded = ['id'];


    public function __construct(array $attributes = array())
    {
          $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_entrada' => Capsule::raw("ORDER_UUID(uuid())")
          )), true);
          parent::__construct($attributes);
    }

    //mutators
    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
    }

    public function getUuidEntradaAttribute($value){
  		return strtoupper(bin2hex($value));
  	}

    function setCodigoAttribute($value){
        return $this->attributes['codigo'] = GenerarCodigo::setCodigo('EM'.Carbon::now()->format('y'), $value);
    }


    //relationships
    function transacciones(){
        return $this->hasMany(AsientoContable::class,'transaccionable_id')->where('transaccionable_type','Flexio\Modulo\EntradaManuales\Models\EntradaManual');
    }

  	function comentarios(){
        return $this->hasMany('Comentario_orm','entrada_manual_id');
    }

    //scope

    public function getNumeroDocumentoEnlaceAttribute() {
        $attrs = [
            "href"  => base_url("entrada_manual/ver/".$this->uuid_entrada),
            "class" => "link"
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
        return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->codigo)->getSalida();
    }

    public static function findByUuid($uuid){
        return self::where('uuid_entrada',hex2bin($uuid))->first();
    }

    ///funciones

    function addLineaTransaccion($atributos){
        return new AsientoContable($atributos);
    }

    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
  	$query = self::where($clause);
  	if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
  	if($limit!=NULL) $query->skip($start)->take($limit);
    return $query->get();
    }

  	public function transaccion()
    {
      // transaccionable es el campo polimorfico de contab_transaccions en AsientoContable
      return $this->morphMany(AsientoContable::class, 'transaccionable');
    }
}
