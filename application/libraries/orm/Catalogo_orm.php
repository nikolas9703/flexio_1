 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Catalogo_orm extends Model
{
	protected $table = 'mod_catalogos';
	protected $fillable = ['id','identificador','valor','etiqueta','orden','activo'];
	protected $guarded = ['id_cat'];
	public $timestamps = false;
	
	 protected $primaryKey = 'id_cat';
 }
