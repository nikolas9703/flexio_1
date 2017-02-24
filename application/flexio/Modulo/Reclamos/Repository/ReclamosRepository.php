<?php
namespace Flexio\Modulo\Reclamos\Repository;

use Flexio\Modulo\Reclamos\Models\Reclamos;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Reclamos\Models\ReclamosAccidentes;
use Flexio\Modulo\Reclamos\Models\ReclamosCoberturas;
use Flexio\Modulo\Reclamos\Models\ReclamosDeduccion;


class ReclamosRepository {
    
   public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
	//filtros
        $reclamos = Reclamos::deEmpresa($clause["empresa_id"]); 

        if (isset($clause["id_ramo"]) && $clause["id_ramo"] != "" ) {
            $reclamos->where("id_ramo", $clause["id_ramo"]);
        }
       
		//Si existen variables de orden
        if($sidx != 'estado'){
			$reclamos->orderBy('estado', 'ASC');
        }
		if($sidx!=NULL && $sord!=NULL){
			
			$reclamos->orderBy($sidx, $sord);               
		}       
		//Si existen variables de limite	
		return $reclamos->get();
	}
    public function listar_reclamos($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	

		$reclamos = Reclamos::join("cli_clientes", "cli_clientes.id", "=", "rec_reclamos.id_cliente")
                        ->join("pol_polizas", "pol_polizas.id", "=", "rec_reclamos.id_poliza")
                        ->join("usuarios","usuarios.id","=","rec_reclamos.id_usuario")
                        ->where(function($query) use($clause) {
                            $query->where('rec_reclamos.empresa_id','=',$clause['empresa_id']);
                            //$query->where('fac_facturas.estado','!=',"por_aprobar");
                           
                            if (isset($clause['numero_reclamo'])) $query->where("rec_reclamos.numero", "LIKE", "%".$clause['numero_reclamo']."%");
                            if (isset($clause['numero_caso'])) $query->where("rec_reclamos.numero_caso", "LIKE", "%".$clause['numero_caso']."%");
                            if (isset($clause['numero_poliza'])) $query->where("pol_polizas.numero", "LIKE", "%".$clause['numero_poliza']."%");
                            if (isset($clause['id_cliente'])) $query->where("cli_clientes.id","=", $clause['id_cliente']);
                            if (isset($clause['id_usuario'])) $query->where("rec_reclamos.id_usuario","=", $clause['id_usuario']);
                            if (isset($clause['aseguradora_id'])) $query->where("pol_polizas.aseguradora_id","=", $clause['aseguradora_id']);
                            if (isset($clause['ramo']) && count($clause['ramo'])>0) $query->whereIn("rec_reclamos.id_ramo", $clause['ramo']);
                            if (isset($clause['estado'])) $query->where("rec_reclamos.estado","=", $clause['estado']);
                            if (isset($clause['usuario'])) $query->where("usuarios.nombre","=", $clause['usuario']);
                                                     
                            if(isset($clause['fecha_desde']))$query->where('rec_reclamos.fecha','>=',$clause['fecha_desde']);
                            if(isset($clause['fecha_hasta']))$query->where('rec_reclamos.fecha','<=',$clause['fecha_hasta']);
                            if (isset($clause['id'])) { $query->whereIn("rec_reclamos.id", $clause['id']); }                            
                        });

        $reclamos->select("rec_reclamos.id","rec_reclamos.uuid_reclamos", "rec_reclamos.numero as recnumero", "rec_reclamos.fecha", "rec_reclamos.fecha_siniestro", "rec_reclamos.fecha_notificacion", "cli_clientes.nombre as clinombre", "rec_reclamos.estado as estado", "usuarios.nombre as usunombre", "usuarios.apellido as usuapellido","rec_reclamos.id_ramo", "pol_polizas.numero as polnumero", "pol_polizas.ramo", "pol_polizas.uuid_polizas", "cli_clientes.uuid_cliente", "rec_reclamos.numero_caso", "rec_reclamos.updated_at");
        $reclamos->orderByRaw('FIELD(rec_reclamos.estado,"Pendiente doc.","En analisis","Legal","En pago","Cerrado", "Anulado")');
        $reclamos->orderBy('rec_reclamos.numero', 'DESC');

        if ($sidx != NULL && $sord != NULL)
            $reclamos->orderBy($sidx, $sord);
        if ($limit != NULL)
            $reclamos->skip($start)->take($limit);
        //print_r($facturas->toSql());
        return $reclamos->get();
    }

    public function verReclamos($uuid){
        $reclamos = Reclamos::where('uuid_reclamos',$uuid);
        return $reclamos->first();
    }

    public function verAccidentes($id_reclamo){
        $accidentes = ReclamosAccidentes::where('id_reclamo',$id_reclamo);
        return $accidentes->get();
    }
    
    public function verCoberturas($id_reclamo){
        $coberturas = ReclamosCoberturas::where('id_reclamo',$id_reclamo);
        return $coberturas->get();
    }

    public function verDeducciones($id_reclamo){
        $deducciones = ReclamosDeduccion::where('id_reclamo',$id_reclamo);
        return $deducciones->get();
    }

}