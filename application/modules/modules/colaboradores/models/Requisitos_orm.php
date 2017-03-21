<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

class Requisitos_orm extends Model
{
	protected $table = 'col_colaboradores_requisitos';
	protected $fillable = ['empresa_id', 'colaborador_id', 'requisito_id', 'entregado', 'fecha_expiracion', 'archivo_ruta', 'archivo_nombre', 'creado_por'];
	protected $guarded = ['id'];
	
	public static function lista_requisitos($colaborador_id=NULL)
	{
		//Seleccionar listado de requisitos
		$requisitos_lista = Catalogo_requisitos_orm::where("estado_id", 1)->get()->toArray();
		
		//Armar array de requisitos entregados
		//por el colaborador
		if(!empty($requisitos_lista))
		{
			$i=0;
			foreach ($requisitos_lista AS $key => $requisito)
			{
				$requisito_entregado = Requisitos_orm::where("requisito_id", $requisito["id"])->where("colaborador_id", $colaborador_id)->get()->toArray();
		
				if(!empty($requisito_entregado)){
					$requisitos_lista[$i]["checked"] = !empty($requisito_entregado[0]["entregado"]) && $requisito_entregado[0]["entregado"] == 1 ? true : false;
					$requisitos_lista[$i]["fecha_expiracion"] = !empty($requisito_entregado[0]["fecha_expiracion"]) && $requisito_entregado[0]["fecha_expiracion"] != "" ? Carbon::createFromFormat('Y-m-d', $requisito_entregado[0]["fecha_expiracion"])->format('d/m/Y') : '';
					$requisitos_lista[$i]["archivo_nombre"] = !empty($requisito_entregado[0]["archivo_nombre"]) && $requisito_entregado[0]["archivo_nombre"] != "" && $requisito_entregado[0]["archivo_nombre"] != NULL ? $requisito_entregado[0]["archivo_nombre"] : '';
					$requisitos_lista[$i]["archivo_ruta"] = !empty($requisito_entregado[0]["archivo_ruta"]) ? $requisito_entregado[0]["archivo_ruta"] : '';
				}else{
					$requisitos_lista[$i]["checked"] = false;
					$requisitos_lista[$i]["fecha_expiracion"] = '';
					$requisitos_lista[$i]["archivo_nombre"] = '';
				}
				$i++;
			}
		}
		
		return $requisitos_lista;
	}
}