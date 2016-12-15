<?php
namespace Flexio\Modulo\Planes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class Planes extends Model
{
    protected $table        = 'seg_planes';    
    protected $fillable     = ['uuid_planes', 'nombre', 'id_aseguradora', 'id_ramo', 'update_at', 'created_at','desc_comision','id_impuesto'];
    protected $guarded      = ['id'];
    
    //scopes
    public function scopeDePlanes($query, $aseguradora_id) {
        return $query->where("id_aseguradora", $aseguradora_id);
    }   
	
	/*public function creadopor() {
        return $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }*/
}