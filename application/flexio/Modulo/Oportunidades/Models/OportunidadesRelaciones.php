<?php
namespace Flexio\Modulo\Oportunidades\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;

class OportunidadesRelaciones extends Model
{
    
    protected $table    = 'opo_oportunidades_relaciones';
    protected $fillable = ['oportunidad_id','relacionable_id','relacionable_type'];
    protected $guarded  = ['id'];
    public $timestamp = false;


    public function relacionable(){
        
        $this->morphTo();
        
    }
    

}
