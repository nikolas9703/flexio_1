<?php
namespace Flexio\Modulo\ReporteFinanciero\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
//NOTA se creo este archivo para no cargar cuentas recursivas
class ReporteCuentas extends Model
{

    protected $table        = 'contab_cuentas';

}
