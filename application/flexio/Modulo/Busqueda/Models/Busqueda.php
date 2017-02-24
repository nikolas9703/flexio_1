<?php
namespace Flexio\Modulo\Busqueda\Models;
use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Busqueda\Models\Variables;
class Busqueda extends Model
{
    protected $table        = 'flexio_busqueda';
    public $timestamps      = true;
    protected $fillable = ['busqueda','campos','modulo','usuario_id','empresa_id'];
    protected $guarded = ['id'];
    //scopes
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }
}
