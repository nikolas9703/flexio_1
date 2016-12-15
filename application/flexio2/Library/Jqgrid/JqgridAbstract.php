<?php
namespace Flexio\Library\Jqgrid;
use Illuminate\Http\Request;


abstract class JqgridAbstract{

  protected $request;

  function __construct()
  {
        $this->request = Request::capture();
  }


  public function inicializar() {
    //$this->request = Request::capture();
    $page =  $this->request->input('page',null);
    $limit = $this->request->input('rows',null);
    $sidx =  $this->request->input('sidx',null);
    $sord =  $this->request->input('sord',null);

    return [ $page, $limit, $sidx, $sord];
  }

  public function paginacion($count = 0, $limit = 10, $page = 1) {
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
}
