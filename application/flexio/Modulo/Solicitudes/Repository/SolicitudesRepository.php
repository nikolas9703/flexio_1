<?php
namespace Flexio\Modulo\Solicitudes\Repository;

use Flexio\Modulo\Solicitudes\Models\Solicitudes;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Solicitudes\Models\SolicitudesVigencia;
use Flexio\Modulo\Solicitudes\Models\SolicitudesPrima;
use Flexio\Modulo\Solicitudes\Models\SolicitudesParticipacion;
use Flexio\Modulo\Solicitudes\Models\SolicitudesAcreedores;
use Flexio\Modulo\Solicitudes\Models\SolicitudesAcreedores_detalles;

class SolicitudesRepository {
    
   public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
	//filtros
        $solicitudes = Solicitudes::deEmpresa($clause["empresa_id"]); 
       
		//Si existen variables de orden
        if($sidx != 'estado'){
			$solicitudes->orderBy('estado', 'ASC');
        }
		if($sidx!=NULL && $sord!=NULL){
			
			$solicitudes->orderBy($sidx, $sord);               
		}       
		//Si existen variables de limite	
		return $solicitudes->get();
	}
    public function listar_solicitudes($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	

	$query = Solicitudes::with(array('cliente', 'aseguradora', 'tipo', 'usuario' => function($query) use($clause, $sidx, $sord){
			if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}));

        if(!empty($clause['cliente'])){        
        $cliente_data = $clause['cliente'];    
        $cliente = Cliente::where("nombre", $cliente_data[0], $cliente_data[1])->get(array('id'))->toArray();       
        if(!empty($cliente)){
                $cliente_id = (!empty($cliente) ? array_map(function($cliente){ return $cliente["id"]; }, $cliente) : "");
                 
                $query->whereIn("seg_solicitudes.cliente_id", $cliente_id);              
        }
        }
		
		$query->leftjoin('seg_ramos_usuarios', 'seg_ramos_usuarios.id_ramo', '=', 'seg_solicitudes.ramo_id');
        $query->where("seg_ramos_usuarios.id_usuario", $clause['usuario_id']);  
		$query->groupBy('seg_solicitudes.id');
		unset($clause['usuario_id']);
		
        if(!empty($clause['ramo'])){           
        $query->whereIn("ramo", $clause['ramo']);    
        }
		
		if(!empty($clause['interes_asegurado'])){        
			$query->whereIn("seg_solicitudes.id", $clause['interes_asegurado']);    
        }
		if(!empty($clause['inicio_creacion']) AND !empty($clause['fin_creacion'])){
			//$query->where("fecha_creacion",array('>=',$clause["inicio_creacion"]));
			//$query->where("fecha_creacion",array('<=',$clause["fin_creacion"]));
			$query->whereBetween('seg_solicitudes.fecha_creacion', array($clause["inicio_creacion"], $clause["fin_creacion"]));
		}else if(!empty($clause['inicio_creacion'])){
			$query->where('seg_solicitudes.fecha_creacion',">=",$clause["inicio_creacion"]);
		}else if(!empty($clause['fin_creacion'])){
			$query->where('seg_solicitudes.fecha_creacion',"<=",$clause["fin_creacion"]);
		}
		
		 if(!empty($clause['cliente_id'])){  
			$query->leftjoin('cli_clientes', 'seg_solicitudes.cliente_id', '=', 'cli_clientes.id');
            $query->where("cli_clientes.nombre", 'LIKE','%'.$clause['cliente_id'].'%');  
        }
		
		 if(!empty($clause['aseguradora_id'])){  
			$query->leftjoin('seg_aseguradoras', 'seg_solicitudes.aseguradora_id', '=', 'seg_aseguradoras.id');
            $query->where("seg_aseguradoras.nombre", 'LIKE','%'.$clause['aseguradora_id'].'%');  
        }
		
		if(!empty($clause['ramo_id'])){  
			$query->leftjoin('seg_ramos', 'seg_solicitudes.ramo_id', '=', 'seg_ramos.id');
            $query->where("seg_ramos.nombre", 'LIKE','%'.$clause['ramo_id'].'%');  
        }
		
		if(!empty($clause['id_tipo_poliza'])){  
			if($clause['id_tipo_poliza']!=0)
				$query->where("id_tipo_poliza",$clause['id_tipo_poliza'] );  
        }
		
		if(!empty($clause['agentes_id'])){  
			$query->leftjoin('seg_solicitudes_participacion', 'seg_solicitudes.id', '=', 'seg_solicitudes_participacion.id_solicitud');
            $query->where("seg_solicitudes_participacion.agente",$clause['agentes_id']);  
			$query->groupBy('seg_solicitudes_participacion.id_solicitud');
        }
		
        unset($clause['cliente']);
        unset($clause['ramo']);
        unset($clause['inicio_creacion']);
        unset($clause['fin_creacion']);
        unset($clause['fecha_creacion']);
		unset($clause['interes_asegurado']);
		unset($clause['cliente_id']);
		unset($clause['aseguradora_id']);
		unset($clause['ramo_id']);
		unset($clause['id_tipo_poliza']);
		unset($clause['agentes_id']);
        
        if($clause!=NULL && !empty($clause) && is_array($clause))
        {
			foreach($clause AS $field => $value)
			{  
				if($field=='aseguradora_id1')
					$field='aseguradora_id';
				if($field=='id' && count($value)){
					$query->whereIn('seg_solicitudes.'.$field,$value);
				}
				//verificar si valor es array
				elseif(is_array($value) ){
					
						$query->where('seg_solicitudes.'.$field, $value[0], $value[1]);
						
				}else{
						$query->where('seg_solicitudes.'.$field, '=', $value);
				}
			}
        }
		
		if(preg_match("/(fecha_creacion1)/i", $sidx)){
			$sidx='fecha_creacion';
					$query->orderByRaw('FIELD(seg_solicitudes.estado,"Pendiente","En Trámite","Aprobada","Rechazada","Anulada")');
					$query->orderBy("seg_solicitudes.fecha_creacion", 'desc');
					$query->orderBy("seg_solicitudes.numero", 'desc');
				}
		//Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
				if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
					
					if($sidx=='nombre_cliente')
					{
						$query->leftjoin('cli_clientes', 'seg_solicitudes.cliente_id', '=', 'cli_clientes.id');
						$query->orderBy('cli_clientes.nombre', $sord);
					}
					else if($sidx=='aseguradora_id')
					{
						$query->leftjoin('seg_aseguradoras', 'seg_solicitudes.aseguradora_id', '=', 'seg_aseguradoras.id');
						$query->orderBy('seg_aseguradoras.nombre', $sord);
					}
					else if($sidx=='ramo_id')
					{
						$query->leftjoin('seg_ramos', 'seg_solicitudes.ramo_id', '=', 'seg_ramos.id');
						$query->orderBy('seg_ramos.nombre', $sord);
					}
					else
					{
						$query->orderBy('seg_solicitudes.'.$sidx, $sord); 
					}
                }
        }

        //Si existen variables de limite
        if($limit!=NULL) $query->skip($start)->take($limit);
        //return $query->get(array('id', Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre"), 'cedula', 'created_at', Capsule::raw("HEX(uuid_colaborador) AS uuid")));
        
        return $query->select('seg_solicitudes.*')->get();
    }

    public function verSolicitudes($uuid){
        $solicitudes = Solicitudes::where('uuid_solicitudes',$uuid);

        return $solicitudes->first();
    }

    public function verAseguradas($id){
        $aseguradas = Aseguradoras::where('id',$id);

        return $aseguradas->get(array('id','nombre'));
    }
    
    public function verVigencia($id_solicitudes){
        $vigencia = SolicitudesVigencia::where('id_solicitudes',$id_solicitudes);

        return $vigencia->first();
    }

    public function verPrima($id_solicitudes){
        $prima = SolicitudesPrima::where('id_solicitudes',$id_solicitudes);

        return $prima->first();
    }

    public function verParticipacion($id_solicitudes){
        $participacion = SolicitudesParticipacion::where('id_solicitud',$id_solicitudes);
        
        return $participacion->get();

    }

    public function verAcreedores($id_solicitudes){
        $acreedores = SolicitudesAcreedores::where('id_solicitud',$id_solicitudes);
        
        return $acreedores->get();

    }

    public function verAcreedoresDetalle($id){
        $acreedores = SolicitudesAcreedores_detalles::where('idinteres_detalle',$id);
        
        return $acreedores->get();

    }
}