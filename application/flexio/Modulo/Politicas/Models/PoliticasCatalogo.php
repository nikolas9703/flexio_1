<?php
namespace Flexio\Modulo\Politicas\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class PoliticasCatalogo extends Model
{
  protected $table = 'ptr_transacciones_catalogo';
	protected $guarded = ['id'];
}
