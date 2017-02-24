<?php 

namespace Flexio\Modulo\ComisionesSeguros\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Empresa\Models\Empresa;
use Carbon\Carbon as Carbon;

class ComisionesSegurosRemesas extends Model
{
    protected $table = 'seg_comisiones_remesas';
    protected $fillable = ['id','id_remesa','id_comision'];
    protected $guarded = ['id'];
    public $timestamps = false;


    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array()), true);
        parent::__construct($attributes);
    }
	
	public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("seg_comisiones.id_empresa", $empresa_id);
    }  

    public static function findByUuid($uuid){
        return self::where('uuid_remesa_entrante',hex2bin($uuid))->first();
    }
}
