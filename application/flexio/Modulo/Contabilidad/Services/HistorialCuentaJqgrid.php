<?php
namespace Flexio\Modulo\Contabilidad\Services;
use Illuminate\Http\Request;
use Flexio\Library\Jqgrid\JqgridAbstract;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Flexio\Library\Util\FormRequest;

class HistorialCuentaJqgrid extends JqgridAbstract{
    protected $filters;
    protected $scoped;
    protected $request;

    function __construct(){
        $this->filters = new HistorialCuentaQueryFilters;
        $this->scoped = new AsientoContable;
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

    function  armarJqgrid($registos, $page, $total_pages, $count){

      $response = new \stdClass();
      $response->page     = $page;
      $response->total    = $total_pages;
      $response->records   = $count;


     if($registos->count() > 0){
        
        foreach($registos as $i => $row){
          $hidden_options = "";
          $link_option    = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

          $hidden_options = '<a href="'. $row->present()->enlace .'" data-id="'. $row->uuid_cobro .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';

          $response->rows[$i]["id"] = $row->id;

          $response->rows[$i]["cell"] = array(
              'id'                => $row->id,
              'no_transaccion'    =>$row->present()->codigo,
              'fecha'             => $row->created_at,
              'centro_contable' => $row->nombre_centro_contable,
              'transaccion'       => $row->nombre,
              'debito'            => $row->present()->debito,
              'credito'           => $row->present()->credito,
              'opciones'          => $link_option,
              'link'              => $hidden_options,
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
