<?php
namespace Flexio\Modulo\Contabilidad\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Contabilidad\Models\Cuentas;


class CuentasCobrar extends Model
{

    protected $table = 'contab_cuenta_por_cobrar';
    protected $fillable = ['cuenta_id','empresa_id'];
    public $timestamps = false;

    public function cuentas_info()
    {
      return $this->hasOne(Cuentas::Class, 'id', 'cuenta_id');
    }

  }