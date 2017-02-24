<?php
namespace Flexio\Modulo\Contabilidad\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Contabilidad\Models\Cuentas;


class CuentasCentro extends Model
{

    protected $table = 'contab_cuentas_centros';
    protected $fillable = ['cuenta_id','centro_id','empresa_id'];
    public $timestamps = false;

    public function cuentas_info()
    {
      return $this->hasOne(Cuentas::Class, 'id', 'cuenta_id');
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
         return $query->where("empresa_id", $empresa_id);
    }
    public function scopeDeCentro($query, $centro_id)
    {
         return $query->where("centro_id", $centro_id);
    }
  }
