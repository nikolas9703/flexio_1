<?php
namespace Flexio\Modulo\Talleres\Models;

use Illuminate\Database\Eloquent\Model;
use Flexio\Modulo\Colaboradores\Models\Colaboradores;

class EquipoColaboradores extends Model
{

    protected $table = 'tal_talleres_colaboradores';
    protected $fillable = ['colaborador_id', 'equipo_trabajo_id', 'created_at'];
    protected $guarded = ['id'];

    public static function equipoColaborador($id_equipo = NULL, $id_colaborador = NULL){
       
        self::insert([
            ['equipo_trabajo_id' => $id_equipo, 'colaborador_id' => $id_colaborador]
        ]);
    }

    public function idsEquipoColaborador($id_equipo = NULL){

        return self::select('colaborador_id')
            ->where('equipo_trabajo_id', '=', $id_equipo)->get();
    }

    public static function buscar($equipo_id, $col_id){

        $condition = ['colaborador_id' => $col_id, 'equipo_trabajo_id' => $equipo_id];
        return self::where($condition);
	}
	
    public static function borrar($equipo_id=NULL, $id = NULL){
       return self::where('equipo_trabajo_id', $equipo_id)->where('colaborador_id', $id)->delete();
        
    }

    public function colaborador() {
    	return $this->belongsTo(Colaboradores::class, 'colaborador_id');
    }
}