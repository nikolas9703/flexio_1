<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\SegCatalogo\Models\SegCatalogo;
use Flexio\Modulo\Ramos\Models\Ramos;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Solicitudes\Models\SolicitudesParticipacion as ParticipacionModel;
use Flexio\Modulo\Planes\Models\Planes;

use Flexio\Modulo\Polizas\Models\PolizasPrima;
use Flexio\Modulo\Polizas\Models\PolizasVigencia;
use Flexio\Modulo\Polizas\Models\PolizasCobertura;
use Flexio\Modulo\Polizas\Models\PolizasDeduccion;
use Flexio\Modulo\Polizas\Models\PolizasParticipacion;
use Flexio\Modulo\Polizas\Models\PolizasCliente;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro;


class Polizas extends Model
{
    protected $table        = 'pol_polizas';    
    protected $fillable     = ['uuid_polizas', 'numero', 'creado_por', 'updated_at', 'created_at', 'empresa_id', 'cliente','ramo_id','ramo', 'tipo_ramo', 'id_tipo_int_asegurado','usuario', 'estado', 'inicio_vigencia', 'fin_vigencia', 'frecuencia_facturacion', 'ultima_factura', 'categoria', 'solicitud', 'aseguradora_id', 'plan_id', 'comision', 'poliza_declarativa','renovacion_id', 'porcentaje_sobre_comision', 'impuesto', 'desc_comision', 'centro_contable','fecha_renovacion'];
    protected $guarded      = ['id'];
    
    //scopes
    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_polizas' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    /**
     * Conteo de las polizas existentes
     *
     * @return [array] [description]
     */
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $query = self::with(array('clientefk', 'categoriafk', 'usuariofk','aseguradorafk'));
		if(!empty($clause['empresa_id'])){
			$query->where(array('empresa_id' => $clause['empresa_id']));
			unset($clause['empresa_id']);
		}
		
		$okSQL = 0;
		if(!empty($clause['declarativa'])){
			$declarativa = $clause['declarativa'];    
			$sqlDecl = self::select("pol_polizas.id")	
							->join('seg_solicitudes','seg_solicitudes.numero','=','pol_polizas.solicitud')
							->join('seg_solicitudes_vigencia','seg_solicitudes_vigencia.id_solicitudes','=','seg_solicitudes.id')
							->where(array('seg_solicitudes_vigencia.poliza_declarativa' => $declarativa))
							->get(array('id'))->toArray();
			if(!empty($sqlDecl)){
				$polizas_id = (!empty($sqlDecl) ? array_map(function($sqlDecl){ return $sqlDecl["id"]; }, $sqlDecl) : "");
				
				$query->whereIn("id", $sqlDecl);
				if($query->count()!=0){
					$okSQL = 1;
				}else{
					$okSQL = 0;
				}
			}
			unset($clause['declarativa']);
		}else{
			$okSQL = 1;
		}
		if(!empty($clause['cliente']) AND $okSQL==1){
			$cliente_data = $clause['cliente'];    
			$cliente = Cliente::where("nombre", $cliente_data[0], $cliente_data[1])->get(array('id'))->toArray();
			if(count($cliente)!=0){
				$cliente_id = (!empty($cliente) ? array_map(function($cliente){ return $cliente["id"]; }, $cliente) : "");
				
				$query->whereIn("cliente", $cliente_id);
				if($query->count()!=0){
					$okSQL = 1;					
				}else{
					$okSQL = 0;
				}
			}else{
				$query->where("id",0);
				$okSQL = 0;
			}
			unset($clause['cliente']);
		}else{
			$okSQL = 1;
		}
		
		$query->join('seg_ramos_usuarios', 'seg_ramos_usuarios.id_ramo', '=', 'pol_polizas.ramo_id');
        $query->where("seg_ramos_usuarios.id_usuario", $clause['usuario_id']);  
		$query->groupBy('pol_polizas.id');
		unset($clause['usuario_id']);
		
		if(isset($clause['ramo']) AND count($clause['ramo'])>0 AND $okSQL==1){
			$query->whereIn("ramo", $clause['ramo']);
			unset($clause['ramo']);
		}

		if(isset($clause['numero']) and !empty($clause['numero'])){
			$numero_poliza = $clause["numero"];
			$query->where("pol_polizas.numero", "LIKE", "%".$numero_poliza."%");
			unset($clause["numero"]);
		}
		
		if(isset($clause['inicio_vigencia']) and !empty($clause['inicio_vigencia'])){
			$ini_vig = $clause["inicio_vigencia"];
			$query->whereDate("inicio_vigencia", $ini_vig[0], $ini_vig[1]);
			unset($clause["inicio_vigencia"]);
		}
		if(isset($clause['fin_vigencia']) and !empty($clause['fin_vigencia'])){
			$fin_vig = $clause["fin_vigencia"];
			$query->whereDate("fin_vigencia", $fin_vig[0], $fin_vig[1]);
			unset($clause["fin_vigencia"]);
		}
		
		if(count($clause)>0 AND $okSQL==1){
			$query->where($clause);
		}
		if($sord!=NULL && ($sidx!=NULL && $sidx!="nombre")){
			$query->orderBy($sidx, $sord);
		}else{
			//$query->orderBy("fin_vigencia", "asc")->orderBy("inicio_vigencia", "asc");
			$query->orderByRaw('FIELD(pol_polizas.estado, "Por Facturar", "Expirada", "Facturada", "No Renovada", "Renovada")');
			$query->orderBy("cliente", "ASC");
			$query->orderBy("ramo", "ASC");
			$query->orderBy("inicio_vigencia", "ASC");

		}
		if($limit!=NULL) $query->skip($start)->take($limit);


        return $query->get();
	}
	
	function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    public static function listar_polizas_agt($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

		if($clause['agente_id']!="")
		{
			$sol = ParticipacionModel::where("agente", $clause['agente_id'])->select("id_solicitud")->get()->toArray();
		}
		
		$polizas = self::join("seg_solicitudes", "seg_solicitudes.numero" , "=", "pol_polizas.solicitud")
			->join("seg_aseguradoras", "seg_aseguradoras.id" , "=", "pol_polizas.aseguradora_id")		
			->join("cli_clientes", "cli_clientes.id", "=", "pol_polizas.cliente");
		
		if($clause['agente_id']!=""){
			$polizas->whereIn("seg_solicitudes.id", $sol);
		}
		
		if($clause['aseguradora_id']!="")
		{
			$polizas->where('pol_polizas.aseguradora_id',$clause['aseguradora_id']);
		}
		$polizas->join('seg_ramos_usuarios', 'seg_ramos_usuarios.id_ramo', '=', 'pol_polizas.ramo_id');
        $polizas->where("seg_ramos_usuarios.id_usuario", $clause['usuario_id']);  
		$polizas->groupBy('pol_polizas.id');
		unset($clause['usuario_id']);
		
		$polizas->where(function($query) use($clause,$sidx,$sord,$limit,$start){            
	            if((isset($clause['num_poliza'])) && (!empty($clause['num_poliza']))) $query->where('pol_polizas.numero','LIKE' , "%".$clause['num_poliza']."%");
	            if((isset($clause['cliente'])) && (!empty($clause['cliente']))) $query->where('cli_clientes.nombre',"LIKE",  "%".$clause['cliente']."%");
	            if((isset($clause['aseguradora'])) && (!empty($clause['aseguradora']))) $query->where('seg_aseguradoras.nombre',"LIKE", "%".$clause['aseguradora']."%");
	            if((isset($clause['ramo'])) && (!empty($clause['ramo']))) $query->where('pol_polizas.ramo', 'LIKE' , "%".$clause['ramo']."%");
	            if($limit!=NULL) $query->skip($start)->take($limit);            
            });
		if($sidx!=NULL && $sord!=NULL){ $polizas->orderBy($sidx, $sord); }

        return $polizas->select("pol_polizas.id", "pol_polizas.uuid_polizas","pol_polizas.numero", "cli_clientes.nombre as cliente", "seg_aseguradoras.nombre as aseguradora", "pol_polizas.ramo", "pol_polizas.inicio_vigencia", "pol_polizas.fin_vigencia", "seg_solicitudes.fecha_creacion", "pol_polizas.estado")->get();
	}

	public static function exportarPolizasAgt($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

		$pol = $clause['polizas'];

		$polizas = self::join("seg_solicitudes", "seg_solicitudes.numero" , "=", "pol_polizas.solicitud")
			->join("seg_aseguradoras", "seg_aseguradoras.id" , "=", "pol_polizas.aseguradora_id")		
			->join("cli_clientes", "cli_clientes.id", "=", "pol_polizas.cliente")
			->whereIn("pol_polizas.id", $pol)
			->where(function($query) use($clause,$sidx,$sord,$limit,$start){            
	            if((isset($clause['num_poliza'])) && (!empty($clause['num_poliza']))) $query->where('pol_polizas.numero','LIKE' , "%".$clause['num_poliza']."%");
	            if((isset($clause['cliente'])) && (!empty($clause['cliente']))) $query->where('cli_clientes.nombre',"LIKE",  "%".$clause['cliente']."%");
	            if((isset($clause['aseguradora'])) && (!empty($clause['aseguradora']))) $query->where('seg_aseguradoras.nombre',"LIKE", "%".$clause['aseguradora']."%");
	            if((isset($clause['ramo'])) && (!empty($clause['ramo']))) $query->where('pol_polizas.ramo', 'LIKE' , "%".$clause['ramo']."%");
	            if($limit!=NULL) $query->skip($start)->take($limit);            
            });
		if($sidx!=NULL && $sord!=NULL){ $polizas->orderBy($sidx, $sord); }

        return $polizas->select("pol_polizas.id", "pol_polizas.uuid_polizas","pol_polizas.numero", "cli_clientes.nombre as cliente", "seg_aseguradoras.nombre as aseguradora", "pol_polizas.ramo", "pol_polizas.inicio_vigencia", "pol_polizas.fin_vigencia", "seg_solicitudes.fecha_creacion", "pol_polizas.estado")->get();
	}


    public static function findByUuid($uuid){
        return self::where('uuid_aseguradora',hex2bin($uuid))->first();
    }
	public function clientefk() {
    	return $this->hasOne(Cliente::class, 'id', 'cliente');
    }
	public function usuariofk(){
        return $this->hasOne(Usuarios::class, 'id', 'usuario');
    }
	public function categoriafk(){
        return $this->hasOne(SegCatalogo::class, 'id', 'categoria');
    }
	public function aseguradorafk(){
        return $this->hasOne(Aseguradoras::class, 'id', 'aseguradora_id');
    }
    public function planesfk(){
    	return $this->hasOne(Planes::class, 'id', 'plan_id');
    }
	
	public function datosRamos(){
    	return $this->hasOne(Ramos::class, 'id', 'ramo_id');
    }
	
    public function coberturasfk(){
    	return $this->hasMany(PolizasCobertura::class, 'id_poliza', 'id');
    }
    public function deduccionesfk(){
    	return $this->hasMany(PolizasDeduccion::class, 'id_poliza', 'id');
    }
    public function vigenciafk(){
    	return $this->hasOne(PolizasVigencia::class, 'id_poliza', 'id');
    }
    public function primafk(){
    	return $this->hasOne(PolizasPrima::class, 'id_poliza', 'id');
    }
    public function participacionfk(){
    	return $this->hasMany(PolizasParticipacion::class, 'id_poliza', 'id');
    }
    public function clientepolizafk(){
    	return $this->hasOne(PolizasCliente::class, 'id_poliza', 'id');
    }

    public function datosEmpresa(){
        return $this->hasOne(Empresa::class, 'id', 'empresa_id');
    }

    public function centros(){
        return $this->belongsTo(CentrosContables::class, 'centro_contable', 'id');
    }
	
	public function polizasPrimas(){
    	return $this->belongsTo(PolizasPrima::class, 'id_poliza');
    }

    public function facturasegurofk(){
    	return $this->hasMany(FacturaSeguro::class, 'id_poliza', 'id');
    }

    

}