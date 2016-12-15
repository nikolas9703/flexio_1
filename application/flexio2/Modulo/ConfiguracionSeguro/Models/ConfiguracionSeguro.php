<?php
namespace Flexio\Modulo\ConfiguracionSeguro\Models;


use Flexio\Modulo\Usuarios\Models\Usuarios;
use Illuminate\Database\Eloquent\Model as Model;

class ConfiguracionSeguro extends Model
{
    protected $table = 'seg_ramos';
    protected $fillable = ['nombre','descripcion','empresa_id','padre_id','estado','uuid_ramos', 'updated_at', 'created_at','codigo_ramo','id_tipo_int_asegurado','id_tipo_poliza'];
    protected $guarded = ['id'];

    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    } 

    public function creadopor() {
        return $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }

    public function getAllRecords(){

    	return  ConfiguracionSeguro::all();
    }
}
