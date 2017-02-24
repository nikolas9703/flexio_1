<?php
namespace Flexio\Modulo\CotizacionesAlquiler\Services;
use Illuminate\Http\Request;
use Flexio\Library\Jqgrid\JqgridAbstract;
use Flexio\Modulo\CotizacionesAlquiler\Models\CotizacionesAlquiler;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Library\Util\FormRequest;


class CotizacionAlquilerJqgrid extends JqgridAbstract{

  protected $filters;
  protected $request;
  protected $me;
  function __construct($me){
    $this->filters = new CotizacionAlquilerQueryFilters;
    $this->scoped = new CotizacionesAlquiler;
    $this->request = Request::capture();
    $this->me = $me;
  }

  function listar($clause = []){

    list($page, $limit, $sidx, $sord) = $this->inicializar();

    $clause = array_merge($clause, $this->camposBuscar());


    $count = $this->registros($clause)->count();

    list($total_pages, $page, $start) = $this->paginacion($count, $limit, $page);

    $anticipos = $this->registros($clause,$sidx, $sord, $limit, $start)->get();

    $response = $this->armarJqgrid($anticipos,$page, $total_pages, $count);

    return $response;
  }

  function registros($clause = array(),$sidx=null, $sord=null, $limit=null, $start=null){
    $builder = $this->scoped->newQuery();
    $registros = $this->filters->apply($builder, $clause);

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
        $coti = new CotizacionesAlquiler;
        $count = count($coti->contratos_alquiler_exist($row->id));
        
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options = '<a href="' . base_url('cotizaciones_alquiler/editar/' . $row->uuid_cotizacion) . '" data-id="' . $row->uuid_cotizacion . '" class="btn btn-block btn-outline btn-success">Ver Cotizacion</a>';
        if ($this->me->has_permission('acceso', 'contratos_alquiler/crear') &&  $row->estado == 'ganado' && $row->tipo == 'alquiler' && $count<1) {
          $hidden_options .= '<a href="' . base_url('contratos_alquiler/crear/cotizacion/' . $row->uuid_cotizacion) . '" data-uuid="' . $row->uuid_cotizacion . '"   class="btn btn-block btn-outline btn-success">Convertir a contrato de alquiler</a>';
        }

        $response->rows[$i]["id"] = $row->id;
		
		$c = CentrosContables::where("id",$row->centro_contable_id)->get()->toArray();
		$centro_contable = $c[0]["nombre"];
		
        $response->rows[$i]["cell"] = array(
			"uuid" => $row->uuid_cotizacion,
			"codigo" => '<a class="link" href="' . base_url('cotizaciones_alquiler/editar/' . $row->uuid_cotizacion) . '">' . $row->codigo . '</a>',
			"cliente" => '<a class="link">' . $row->cliente_nombre . '</a>',
			"fecha_desde" => $row->fecha_desde,
			"fecha_hasta" => $row->fecha_hasta,
			"estado" => $row->present()->estado_label,
			"vendedor" => '<a class="link">' . $row->vendedor_nombre . '</a>',
			"centro_contable_id" => $centro_contable,
			"options" => $link_option,
			"link" => $hidden_options
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
