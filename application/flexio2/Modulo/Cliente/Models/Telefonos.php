<?php
namespace Flexio\Modulo\Cliente\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Telefonos extends Model
{

    protected $table = 'cli_clientes_telefonos';
    protected $fillable = ['telefono','tipo'];
    protected $guarded = ['id','cliente_id'];
    public $timestamps      = true;



}