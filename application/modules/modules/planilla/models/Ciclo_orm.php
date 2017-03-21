 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Ciclo_orm extends Model
{
	protected $table = 'mod_catalogos';
	protected $fillable = ['identificador','etiqueta'];
	protected $guarded = ['id_cat'];
	public $timestamps = false;
	
	 protected $primaryKey = 'id_cat';
 }
