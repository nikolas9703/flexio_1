<?php

namespace Flexio\Library\Jqgrid;

use Illuminate\Http\Request;
use Flexio\Library\Util\FormRequest;


abstract class JqgridAbstract
{
    protected $request;
    protected $scoped;

    function __construct()
    {
        $this->request = Request::capture();
    }

    public function inicializar()
    {
        $page =  $this->request->input('page',null);
        $limit = $this->request->input('rows',null);
        $sidx =  $this->request->input('sidx',null);
        $sord =  $this->request->input('sord',null);

        return [ $page, $limit, $sidx, $sord];
    }

    public function paginacion($count = 0, $limit = 10, $page = 1)
    {
        //Calcule total pages if $coutn is higher than zero.
		$total_pages = ($count > 0 ? ceil($count/$limit) : 0);

		// if for some reasons the requested page is greater than the total
		if ($page > $total_pages) $page = $total_pages;

		//calculate the starting position of the rows
		$start = $limit * $page - $limit; // do not put $limit*($page - 1)

		// if for some reasons start position is negative set it to 0
		if($start < 0) $start = 0;

		return array($total_pages, $page, $start );
    }

    public function listar($clause = [])
    {
        list($page, $limit, $sidx, $sord) = $this->inicializar();

        $clause = array_filter(array_merge($clause, $this->camposBuscar()));

        $count = $this->registros($clause)->count();

        list($total_pages, $page, $start) = $this->paginacion($count, $limit, $page);

        $aux = $this->registros($clause,$sidx, $sord, $limit, $start)->get();

        $response = $this->armarJqgrid($aux, $page, $total_pages, $count);

        return $response;
    }

    public function registros($clause = array(),$sidx=null, $sord=null, $limit=null, $start=null)
    {
        $builder = $this->scoped->newQuery();

        $registros = $this->scoped->deFiltro($clause);

        if(!is_null($sidx) && !is_null($sord)) $registros->orderBy($sidx, $sord);
	    if(!is_null($limit) && !is_null($start)) $registros->skip($start)->take($limit);

        return $registros;
    }

    public function camposBuscar()
    {
        $campos = FormRequest::data_formulario($this->request->input('campo'));
        if(is_null($campos)){
            return [];
        }
        return $campos;
    }

}
