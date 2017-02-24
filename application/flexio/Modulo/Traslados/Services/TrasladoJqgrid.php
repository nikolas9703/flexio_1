<?php

namespace Flexio\Modulo\Traslados\Services;

use Flexio\Library\Jqgrid\JqgridAbstract;
use Flexio\Modulo\Traslados\Models\Traslados;

class TrasladoJqgrid extends JqgridAbstract
{
    protected $scoped;

    public function __construct()
    {
        parent::__construct();
        $this->scoped = new Traslados;
    }

    public function armarJqgrid($registos, $page, $total_pages, $count)
    {
        $response = new \stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;

        if($registos->count() > 0)
        {
            foreach($registos as $i => $row)
            {
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="'. base_url('traslados/ver/'. $row->uuid_traslado) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
                $response->rows[$i]["id"] = $row->id;

                $response->rows[$i]["cell"] = [
                    '<a class="link" href="'. base_url('traslados/ver/'. $row->uuid_traslado) .'" >'.$row->codigo.'</a>',
                    $row->fecha_creacion,
                    $row->present()->fecha_entrega,
                    count($row->deBodega) ? $row->deBodega->nombre : '',
                    count($row->bodega) ? $row->bodega->nombre : '',
                    $row->present()->estado_label,
                    $link_option,
                    $hidden_options
                ];
            }
        }

        return $response;
    }



}
