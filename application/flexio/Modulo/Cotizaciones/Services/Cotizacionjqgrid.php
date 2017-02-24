<?php
namespace Flexio\Modulo\Cotizaciones\Services;
use Illuminate\Http\Request;
use Flexio\Library\Jqgrid\JqgridAbstract;
use Flexio\Modulo\Cotizaciones\Models\Cotizacion;
use Flexio\Library\Util\FormRequest;


class Cotizacionjqgrid extends JqgridAbstract{

  protected $filters;
  protected $anticipo;
  protected $request;

  function __construct(){
    $this->filters = new CotizacionFilters;
    $this->scoped = new Cotizacion;
    $this->request = Request::capture();
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
        $modulo = ($row->tipo == 'venta') ? 'cotizaciones/ver' : 'cotizaciones_alquiler/editar';
        $orden = $row->ordenes_validas()->count();
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_cotizacion . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options = '<a href="' . base_url($modulo . '/' . $row->uuid_cotizacion) . '" data-id="' . $row->uuid_cotizacion . '" class="btn btn-block btn-outline btn-success">Ver Cotizacion</a>';

        if ($orden == 0 && ($row->estado == 'aprobado') && $row->cliente_tipo == "cliente" && $row->tipo == "venta") $hidden_options .= '<a href="' . base_url('ordenes_ventas/crear/cotizacion' . $row->id) . '" class="btn btn-block btn-outline btn-success convertirOrdenVenta">Convertir a Órden de Venta</a>';

        if ($this->auth->has_permission('acceso', 'oportunidades/crear/') && ($row->estado == 'aprobado')) {
            $hidden_options .= '<a href="#" data-uuid="' . $row->uuid_cotizacion . '" data-id="' . $row->id . '" data-cliente_id="' . $row->cliente_id . '" class="btn btn-block btn-outline btn-success agregar-oportunidad">Agregar a oportunidad</a>';
        }
        $hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_cotizacion . '" class="exportarTablaCliente btn btn-block btn-outline btn-success subirArchivoBtn">Subir Documento</a>';
        if ($this->auth->has_permission('acceso', 'contratos_alquiler/crear/') &&  $row->estado == 'ganado' && $row->tipo == 'alquiler') {
                  $hidden_options .= '<a href="' . base_url('contratos_alquiler/crear/cotizacion' . $row->uuid_cotizacion) . '" data-uuid="' . $row->uuid_cotizacion . '"   class="btn btn-block btn-outline btn-success">Convertir a contrato de alquiler</a>';
        }


        $response->rows[$i]["id"] = $row->uuid_cotizacion;
        $response->rows[$i]["cell"] = array(
            $row->uuid_cotizacion,
            '<a class="link" href="' . base_url($modulo . '/' . $row->uuid_cotizacion) . '">' . $row->codigo . '</a>',
            '<a class="link">' . $row->cliente_nombre . '</a>',
            $row->fecha_desde,
            $row->fecha_hasta,
            $row->formatEstado()->estado_label,
            '<a class="link">' . $row->vendedor_nombre . '</a>',
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
