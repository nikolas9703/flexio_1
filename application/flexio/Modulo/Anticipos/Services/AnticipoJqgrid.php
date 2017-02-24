<?php
namespace Flexio\Modulo\Anticipos\Services;
use Illuminate\Http\Request;
use Flexio\Library\Jqgrid\JqgridAbstract;
use Flexio\Modulo\Anticipos\Providers\QueryFilters;
use Flexio\Modulo\Anticipos\Models\Anticipo;
use Flexio\Library\Util\FormRequest;


class AnticipoJqgrid extends JqgridAbstract{

  protected $filters;
  protected $anticipo;
  protected $request;

  function __construct(){
    $this->filters = new AnticipoFilters;
    $this->scoped = new Anticipo;
    $this->request = Request::capture();
  }

  function listar($clause = []){

    list($page, $limit, $sidx, $sord) = $this->inicializar();

    $clause = array_filter(array_merge($clause, $this->camposBuscar()));
    
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
        $link_option    = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options = '<a href="'. base_url('anticipos/ver/'. $row->uuid_anticipo) .'" data-id="'. $row->uuid_anticipo .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
        $hidden_options .= '<a href="javascript:" data-id="'. $row->uuid_anticipo .'" class="btn btn-block btn-outline btn-success subirArchivoBtn">Subir Documentos</a>';
        $response->rows[$i]["id"] = $row->id;

        $response->rows[$i]["cell"] = array(
            $row->id,
            '<a class="link" href="'. base_url('anticipos/ver/'. $row->uuid_anticipo) .'" >'.$row->codigo.'</a>',
            $row->present()->nombre_anticipable,
            $row->fecha_anticipo,
            $row->present()->monto,
            $row->present()->documento,
            //$row->present()->metodo_anticipo,
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
