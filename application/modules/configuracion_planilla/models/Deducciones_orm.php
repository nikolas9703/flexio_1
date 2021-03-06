 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Deducciones_orm extends Model
{

 	protected $table = 'pln_config_deducciones';
	protected $fillable = ['id','nombre','cuenta_pasivo_id','rata_colaborador','rata_colaborador_tipo','rata_patrono','rata_patrono_tipo','descripcion','estado_id','empresa_id','creado_por','uuid_deduccion','limite1','limite2'];
	protected $guarded = ['id'];
	public $timestamps = false;

	public function cuenta_pasivo(){
		return $this->hasOne('Cuentas_orm', 'id', 'cuenta_pasivo_id');
	}
	public function contructores(){
		return $this->hasMany('Deducciones_contructores_orm', 'deduccion_id');
	}

	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{

 		$query = self::with(array('cuenta_pasivo' ));

		if($clause!=NULL && !empty($clause) && is_array($clause))
		{

			foreach($clause AS $field => $value)
			{
				//Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}

				//verificar si valor es array
				if(is_array($value)){
					$query->where($field, $value[0], $value[1]);
				}else{
					$query->where($field, '=', $value);
				}
			}
		}

		if($limit!=NULL) $query->skip($start)->take($limit);
		return $query->get();
	}
 }
