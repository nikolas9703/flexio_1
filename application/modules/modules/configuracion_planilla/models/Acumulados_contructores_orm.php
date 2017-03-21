 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Acumulados_contructores_orm extends Model
{
	protected $table = 'pln_config_acumulados_constructores';
	protected $fillable = ['id','operador_id','operador_valor','tipo_calculo_uno','valor_calculo_uno','tipo_calculo_dos','valor_calculo_dos','acumulado_id'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	 
 }
