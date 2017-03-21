<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

class Entrega_inventario_orm extends Model
{
	protected $table = 'col_entrega_inventario';
	protected $fillable = ['empresa_id', 'colaborador_id', 'departamento_id', 'bodega_uuid', 'categoria_id', 'item_id', 'duracion_id', 'tipo_reemplazo_id', 'codigo', 'cantidad', 'fecha_entrega', 'proxima_entrega', 'entregado_por', 'archivo_ruta', 'archivo_nombre', 'creado_por'];
	protected $guarded = ['id'];
	
	public function departamento(){
		return $this->hasOne('Departamentos_orm', 'id', 'departamento_id');
	}
	
	public function colaborador()
	{
		return $this->hasOne('Colaboradores_orm', 'id', 'colaborador_id');
	}
	
	public function entregado_por()
	{
		return $this->hasOne('Usuario_orm', 'id', 'entregado_por');
	}
	
	public function duracion()
	{
		return $this->hasOne('Estado_orm', 'id_cat', 'duracion_id');
	}
	
	public function categoria(){
		return $this->hasOne('Items_categorias_orm', 'id', 'categoria_id');
	}
	
	public function item()
	{
		return $this->hasOne('Items_orm', 'id', 'item_id');
	}
	
	public function reemplazo()
	{
		return $this->hasOne('Estado_orm', 'id_cat', 'tipo_reemplazo_id');
	}
	
	/**
	 * Listado de Cargos
	 *
	 * @return object
	 */
	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
		$nombre_item = !empty($clause["nombre_item"]) ? $clause["nombre_item"] : array();
		
		$query = self::with(array('duracion', 'colaborador', 'reemplazo',
		'entregado_por' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/creado_por/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		},
		'categoria' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/centro_contable/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		},
		'departamento' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/departamento/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}, 
		'item' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}));
		
		//Filtrar Nombre de Item
		if(!empty($nombre_item)){
			$items = Items_orm::where("nombre", $nombre_item[0], $nombre_item[1])->get(array('id'))->toArray();
			if(!empty($items)){
				$item_id = (!empty($items) ? array_map(function($items){ return $items["id"]; }, $items) : "");
				$query->whereIn("item_id", $item_id);
			}
		}
	
		//Si existen parametros de busqueda
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				if($field == "nombre_item"){
					continue;
				}
				
				//Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}

				if(is_array($value)){
					$query->where($field, $value[0], $value[1]);
				}else{
					$query->where($field, '=', $value);
				}
			}
		}

		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/(item|departamento|categoria)/i", $sidx)){
				$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);

		return $query->get();
	}
}