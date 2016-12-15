 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Deducciones_contructores_orm extends Model
{
	protected $table = 'pln_config_deducciones_constructores';
	protected $fillable = ['id','cuando','operador','monto','aplicar','deduccion_id'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	 
 }
