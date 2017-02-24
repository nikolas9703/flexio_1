<?php 
namespace Flexio\Modulo\Ramos\Repository;


use Flexio\Modulo\Ramos\Models\Ramos as Ramos;

/**
* 
*/
class RamoRepository 
{
	private  static $ramos_ = array();
	
	static function listar_cuentas($clause = array()) {
		self::$ramos_ = array();
		$empresa_id = $clause['empresa_id'];
		$clause['padre_id'] = 0;
		$clause['estado'] = 1;

		$result_search = Ramos::where(function($query) use ($clause){
			$query->where($clause)->orderBy('nombre','ASC');
		})->orderBy('nombre','ASC');

		$padres = $result_search->get();
		$padres->map(function($ramos) use($empresa_id){
            $i = 0; //level 0 padres
            self::recursiva($ramos, $empresa_id,$i);
        });
		return self::$ramos_;
	}
	
	
	static function  recursiva(Ramos $cuenta,$empresa_id, $level) {
		$cuenta->where('empresa_id', $empresa_id)->orderBy('nombre','ASC');
		$cuenta->interesAsegurado;
		$cuenta->tipoPoliza;
		$level++;
		$AUX = $cuenta->toArray();
		$AUX["level"] = $level;
	    array_push(self::$ramos_, $AUX);
		
		if($cuenta->ramos_item->where('empresa_id', $empresa_id)->count() > 0){

			$cuenta->ramos_item->where('empresa_id', $empresa_id)->map(function($item) use($empresa_id,$level){

				self::recursiva($item,$empresa_id,$level);

			});

		}
	}

}