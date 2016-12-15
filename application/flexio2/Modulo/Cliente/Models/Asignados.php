<?php 
namespace Flexio\Modulo\Cliente\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Asignados extends Model
{ 

    protected $table = 'cli_clientes_asignados';
    protected $fillable = ['usuario_id','linea_negocio'];
    protected $guarded = ['id','cliente_id'];
    public $timestamps      = true;
    

   
}
