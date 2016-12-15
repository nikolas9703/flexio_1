<?php
namespace Flexio\Modulo\Politicas\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class PoliticasCategoria extends Model
{
  protected $table = 'ptr_transacciones_categoria';
 	protected $fillable = ['transaccion_id','categoria_id'];
 }
