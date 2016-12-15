<?php
namespace Flexio\Modulo\Ajustes\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class AjustesRazones extends Model{

    protected $table        = 'aju_ajustes_razones';
    protected $fillable     = ['uuid_razon', 'nombre', 'descripcion', 'estado_id', 'empresa_id', 'created_by'];
    protected $guarded      = ['id'];
    public $timestamps      = true;

    //buscadores
    public static function findByUuid($uuid_razon){
        return self::where("uuid_razon", hex2bin($uuid_razon))->first();
    }

    //GETS
    public function getUuidRazonAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidAttribute()
    {
        return $this->uuid_razon;
    }

    //SCOPES
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeActivas($query, $razon_id = '')
    {
        return $query->where(function($q) use($razon_id){
            $q->where("estado_id", 6)//estado activo
                    ->orWhere("id", $razon_id);
        });
    }

    //RELATIONS
    public function estado()
    {
        return $this->belongsTo('Flexio\Modulo\Ajustes\Models\AjustesCat', "estado_id", "id_cat");
    }

    function present(){
        return new \Flexio\Modulo\Inventarios\Presenter\ConfiguracionInventarioPresenter($this);
    }
}
