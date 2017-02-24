<?php
namespace Flexio\Modulo\Planes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Ramos\Models\Ramos;
use Flexio\Modulo\Planes\Models\PlanesComisiones;

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

    public function ramos() {
        return $this->hasOne(Ramos::class, 'id', 'id_ramo');
    }
    public function comisiones(){
        return $this->hasOne( PlanesComisiones::class, 'id_planes', 'id');
    } 
}