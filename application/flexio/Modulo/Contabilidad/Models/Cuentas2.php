<?php
namespace Flexio\Modulo\Contabilidad\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

//Kimi:
//Esta clase solo se usa en pagos,
//En pagos hay un caso donde se duplica el uso de cuentas, pero tiene una relacion pilimorfica, tuve que crear esto separado

class Cuentas2 extends Model
{

    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','nombre','detalle','estado','balance','padre_id', 'tipo_cuenta_id', 'empresa_id', 'impuesto_id', 'uuid_cuenta'];

    use SignoCuenta;
    protected $table = 'contab_cuentas';
    protected $fillable = ['codigo','nombre','detalle','estado','balance','padre_id', 'tipo_cuenta_id', 'empresa_id', 'impuesto_id', 'uuid_cuenta'];
    protected $guarded = ['id'];
    public $timestamps = true;


}
