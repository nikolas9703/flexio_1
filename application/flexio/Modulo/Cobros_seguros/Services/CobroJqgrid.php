<?php
namespace Flexio\Modulo\Cobros_seguros\Services;
use Illuminate\Http\Request;
use Flexio\Library\Jqgrid\JqgridAbstract;
use Flexio\Modulo\Cobros_seguros\Providers\QueryFilters;
use Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros;
use Flexio\Library\Util\FormRequest;


class CobroJqgrid extends JqgridAbstract{

	protected $filters;
	protected $anticipo;
	protected $request;

	function __construct(){
		$this->filters = new CobroQueryFilters;
		$this->scoped = new Cobros_seguros;
		$this->request = Request::capture();
	}

	function listar($clause = []){

		list($page, $limit, $sidx, $sord) = $this->inicializar();

		$clause = array_merge($clause, $this->camposBuscar());

		$contador = $this->registros($clause)->where("formulario","seguros");
		if(isset($clause['empezable_id']))
		{
			$contador->whereIn("empezable_id",$clause['empezable_id']);
		}
		
		$count=$contador->count();

		list($total_pages, $page, $start) = $this->paginacion($count, $limit, $page);

		$anticipostodos = $this->registros($clause,$sidx, $sord, $limit, $start);
		if(isset($clause['empezable_id']))
		{
			$anticipostodos->whereIn("empezable_id",$clause['empezable_id']);
		}
		$anticipos=$anticipostodos->where("formulario","seguros")->get();

		$response = $this->armarJqgrid($anticipos,$page, $total_pages, $count);

		return $response;
	}

	function registros($clause = array(),$sidx=null, $sord=null, $limit=null, $start=null){
		
		$builder = $this->scoped->newQuery();
		$registros = $this->filters->apply($builder, $clause);
		
		if($sidx=='codigo')
			$sidx='cob_cobros.codigo';
		if($sidx=='cliente')
		{
			$registros->join("cli_clientes as cliente2", "cliente2.id", "=", "cob_cobros.cliente_id");
			$sidx='cliente2.nombre';
		}
		
		if($sidx=='metodo_pago')
		{
			$registros->join("cob_cobro_metodo_pago as pago2", "pago2.cobro_id", "=", "cob_cobros.id");
			$sidx='pago2.tipo_pago';
		}
		//var_dump($registros);
		$registros->orderByRaw('FIELD(cob_cobros.estado, "vencido", "agendado", "por aplicar", "aplicado", "anulado")');
		if(!is_null($sidx) && !is_null($sord)) $registros->orderBy($sidx, $sord);
		if(!is_null($limit) && !is_null($start)) $registros->skip($start)->take($limit);

		return $registros;
	}


	function armarJqgrid($registos, $page, $total_pages, $count){

		$response = new \stdClass();
		$response->page     = $page;
		$response->total    = $total_pages;
		$response->records   = $count;


		if($registos->count() > 0){
			foreach($registos as $i => $row){
				$hidden_options = "";
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_cobro .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				$hidden_options = '<a href="'. base_url('cobros_seguros/ver/'. $row->uuid_cobro) .'" data-id="'. $row->uuid_cobro .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';

				$response->rows[$i]["id"] = $row->uuid_cobro;

				$response->rows[$i]["cell"] = array(
					$row->uuid_cobro,
					'<a class="link" href="'. base_url('cobros_seguros/ver/'. $row->uuid_cobro) .'" >'.$row->codigo.'</a>',
					'<a class="link">'.$row->cliente_nombre.'</a>',
					$row->fecha_pago,
					$row->present()->monto_pagado,
					$row->present()->metodo_pago,
					$row->present()->estado_label,
					$link_option,
					$hidden_options
				);
			}
		}

		return $response;
	}

	function camposBuscar(){
		$campos = FormRequest::data_formulario($this->request->input('campo'));
		if(is_null($campos)){
			return [];
		}
		return $campos;
	}

}
