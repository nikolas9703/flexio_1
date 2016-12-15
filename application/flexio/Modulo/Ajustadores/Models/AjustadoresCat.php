<?php 
namespace Flexio\Modulo\Ajustadores\Models;
use Illuminate\Database\Eloquent\Model as Model;

class AjustadoresCat extends Model
{
protected $table = 'cli_clientes_catalogo';
    protected $guarded = ['id','uuid_ajustadores'];  
}
