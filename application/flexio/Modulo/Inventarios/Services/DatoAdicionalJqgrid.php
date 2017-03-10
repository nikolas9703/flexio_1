<?php

namespace Flexio\Modulo\Inventarios\Services;

use Flexio\Library\Jqgrid\JqgridAbstract;
use Flexio\Modulo\Inventarios\Models\DatoAdicional;

class DatoAdicionalJqgrid extends JqgridAbstract
{

    protected $scoped;

    public function __construct(DatoAdicional $scoped)
    {
        $this->scoped = $scoped;
        parent::__construct();
    }

    public function armarJqgrid($registos, $page, $total_pages, $count)
    {
        $response = new \stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;

        if ($registos->count() > 0) {
            foreach ($registos as $i => $row) {

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row->id.'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                $hidden_options = '';
                $hidden_options .= '<a href="#" data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success editar-btn">Editar campo</a>';
                $hidden_options .= '<a href="#" data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success cambiar-estado-btn">Cambiar estado</a>';

                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->nombre,
                    $row->present()->requerido,
                    $row->present()->en_busqueda_avanzada,
                    $row->present()->estado,
                    $link_option,
                    $hidden_options,
                );
            }
        }

        return $response;
    }


}
