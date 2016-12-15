<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Planilla_deducciones_orm extends Model
{
	protected $table = 'pln_planilla_deducciones';
	protected $fillable = ['deduccion_id', 'planilla_id','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	
	
	
	
	
	public static function   lista_deducciones_planilla($planilla_id = NULL ){
	
		$resultado_deducciones = array();
	
		$deducciones = Capsule::table('pln_planilla_deducciones as ded')
		->leftJoin('pln_config_deducciones as config_ded', 'config_ded.id', '=', 'ded.deduccion_id')
		->where('ded.planilla_id', $planilla_id)
		->distinct()
		->get();
		if(!empty($deducciones)){
			foreach ($deducciones as  $row){
					
				$resultado_deducciones[] = array(
						"id"	=> $row->id,
						"key"	=> $row->key,
						"nombre"	=> $row->nombre,
						"rata_colaborador_tipo" => $row->rata_colaborador_tipo,
						"rata_colaborador" => $row->rata_colaborador,
						"rata_patrono_tipo" => $row->rata_patrono_tipo,
						"rata_patrono" => $row->rata_patrono,
						"limite1" =>$row->limite1,
						"limite2" =>$row->limite2
				);
			}
	
		}
	
		return $resultado_deducciones;
	}
	public static function   lista_descuentos_colaborador($colaborador_id = NULL ){
	
		$descuento_lista = array();
	
		$descuentos = Capsule::table('desc_descuentos as desc')
		->leftJoin('desc_descuentos_cat as cat', 'cat.id_cat', '=', 'desc.estado_id')
		->leftJoin('pro_proveedores as acr', 'acr.id', '=', 'desc.acreedor_id')
		->where('desc.colaborador_id', $colaborador_id)
		->where('cat.valor', 'aprobado')
		->distinct()
		->get();
		if(!empty($descuentos)){
			foreach ($descuentos as  $descuento){
				$saldo_restante = $descuento->monto_adeudado - $descuento->monto_ciclo ;
				$descuento_lista[] = array(
						"id"	=> $descuento->id,
						"tipo_descuento_id"	=> $descuento->tipo_descuento_id,
						"codigo"	=> $descuento->codigo,
						"acreedor"	=> $descuento->nombre,
						"monto_ciclo"	=> $descuento->monto_ciclo ,
						"saldo_restante"	=> $saldo_restante
				);
			}
		}
		return $descuento_lista;
	}
}