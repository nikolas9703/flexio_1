<?php
namespace Flexio\Modulo\Catalogos\Services;
use Illuminate\Http\Request;
use Flexio\Library\Jqgrid\JqgridAbstract;
use Flexio\Modulo\Catalogos\Models\Catalogo;
use Flexio\Library\Util\FormRequest;


class CatalogoJqgrid extends JqgridAbstract{

  protected $filters;
  protected $anticipo;
  protected $request;

  function __construct(){
    $this->filters = new CatalogoQueryFilters;
    $this->scoped = new Catalogo;
    $this->request = Request::capture();

  }

  function listar($clause = []){

    list($page, $limit, $sidx, $sord) = $this->inicializar();

    $clause = array_merge($clause, $this->camposBuscar());

    $count = $this->registros($clause)->count();

    list($total_pages, $page, $start) = $this->paginacion($count, $limit, $page);

    $results = $this->registros($clause,$sidx, $sord, $limit, $start)->get();

    $response = $this->armarJqgrid($results,$page, $total_pages, $count);

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

    $accesos = array(
      0 => "No",
      1 => "Si"
    );

    $estados = array(
      0 => "Inactivo",
      1 => "Activo"
    );

    $response = new \stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->records   = $count;

   if($registos->count() > 0){
      foreach($registos as $i => $row){
        $hidden_options = "";
        $color = $row->activo == 1 ? 'label-primary' : 'label-danger';
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options = '<a href="#" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success editarTipoSubcontrato">Editar</a>';
        $response->rows[$i]["id"] = $row->id;
        $response->rows[$i]["cell"] = array(
            $row->valor,
            $accesos[(int)$row->con_acceso],
            '<span class="label '. $color .'">'. $estados[(int)$row->activo] .'</span>',
            $link_option,
            $hidden_options,
            (int)$row->con_acceso,
            (int)$row->activo,
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
