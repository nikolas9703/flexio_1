<?php
namespace Flexio\Modulo\EntradaManuales\Services;
use Illuminate\Http\Request;
use Flexio\Library\Jqgrid\JqgridAbstract;
use Flexio\Modulo\EntradaManuales\Models\EntradaManual;
use Flexio\Library\Util\FormRequest;


class EntradaManualJqgrid extends JqgridAbstract{

  protected $filters;
  protected $scoped;
  protected $request;

  function __construct(){
    $this->filters = new EntradaManualQueryFilters;
    $this->scoped = new EntradaManual;
    $this->request = Request::capture();
  }

  function listar($clause = []){

    list($page, $limit, $sidx, $sord) = $this->inicializar();

    $clause = array_merge($clause, $this->camposBuscar());
    //dd($clause);

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
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options = '<a href="'. base_url('entrada_manual/ver/'. $row->uuid_entrada) .'" data-id="'. $row->uuid_entrada .'" class="btn btn-block btn-outline btn-success">Ver Entrada Manual</a>';


        $response->rows[$i]["id"] = $row->id;
        $response->rows[$i]["cell"] = array(
         $row->id,
         '<a href="'. base_url('entrada_manual/ver/'. $row->uuid_entrada) .'">'. $row->codigo . '</a>',
         $row->present()->fecha_hora,
         $row->usuario_nombre,
         $row->present()->debito,
         $row->present()->credito,
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
