<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Presupuesto_catalogo_orm extends Model
{
	protected $table = 'pres_catalogo';
	protected $fillable = ['nombre'];
	protected $guarded = ['id'];

}  
