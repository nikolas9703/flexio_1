<?php

namespace Flexio\Modulo\SubContratos\Services;

use Illuminate\Http\Request;
use Flexio\Library\Jqgrid\JqgridAbstract;
use Flexio\Modulo\SubContratos\Models\SubContrato;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

class SubContratoJqgrid extends JqgridAbstract
{
    protected $filters;
    protected $anticipo;
    protected $request;
    protected $auth;
    protected $FlexioSession;

    public function __construct($auth)
    {
        $this->filters = new SubContratoQueryFilters();
        $this->scoped = new SubContrato();
        $this->request = Request::capture();
        $this->auth = $auth;
        $this->FlexioSession = new FlexioSession();
    }

    public function listar($clause = [])
    {
        list($page, $limit, $sidx, $sord) = $this->inicializar();

        $clause = array_merge($clause, $this->camposBuscar());

        $count = $this->registros($clause)->count();

        list($total_pages, $page, $start) = $this->paginacion($count, $limit, $page);

        $anticipos = $this->registros($clause, $sidx, $sord, $limit, $start)->get();

        $response = $this->armarJqgrid($anticipos, $page, $total_pages, $count);

        return $response;
    }

    public function registros($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $builder = $this->scoped->newQuery();
        $registros = $this->filters->apply($builder, $clause);
        $centros = $this->FlexioSession->usuarioCentrosContables();
        if (!in_array('todos', $centros)) {
            $registros->whereIn('centro_id', $centros);
        }
        if (!is_null($sidx) && !is_null($sord)) {
            $registros->orderBy($sidx, $sord);
        }
        if (!is_null($limit) && !is_null($start)) {
            $registros->skip($start)->take($limit);
        }

        return $registros;
    }

    public function armarJqgrid($registos, $page, $total_pages, $count)
    {
        $response = new \stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;

        if ($registos->count() > 0) {
            foreach ($registos as $i => $row) {
                $hidden_options = '';
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row->uuid_subcontrato.'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="'.base_url('subcontratos/ver/'.$row->uuid_subcontrato).'" data-id="'.$row->uuid_subcontrato.'" class="btn btn-block btn-outline btn-success">Ver Contrato</a>';

                if ($row->facturable and $this->auth->has_permission('acceso', 'facturas_compras/crear')) {
                    $hidden_options .= '<a href="'.base_url('facturas_compras/crear/subcontrato'.$row->id).'" class="btn btn-block btn-outline btn-success">Agregar Factura</a>';
                }

                if ($row->estado == 'vigente') {
                    $hidden_options .= '<a href="'.base_url('subcontratos/agregar_adenda/'.$row->uuid_subcontrato).'" data-id="'.$row->uuid_subcontrato.'" class="btn btn-block btn-outline btn-success">Crear adenda</a>';
                    $hidden_options .= '<button  data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success imprimir-estado-cuenta">Imprimir estado de subcontrato</button>';

                }
                if ($row->estado == 'vigente' && $row->subcontrato_montos()->sum('monto') > $row->anticipos_no_anulados->sum('monto')) {
                    $hidden_options .= '<a href="'.base_url('anticipos/crear/?subcontrato='.$row->uuid_subcontrato).'" data-id="'.$row->uuid_subcontrato.'" class="btn btn-block btn-outline btn-success">Crear anticipo</a>';
                }
                if ($row->estado == 'vigente') {
                    $hidden_options .= '<a href="#" data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success pagarRetenidoBtn">Pagar retenido</a>';
                }
                $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="'.$row['id'].'" data-uuid="'.$row->uuid_subcontrato.'" >Subir documentos</a>';
                $hidden_options .= '<a  href="'.base_url('subcontratos/historial/'.$row->uuid_subcontrato).'"   data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success">Ver bit&aacute;cora</a>';
                $response->rows[$i]['id'] = $row->uuid_subcontrato;
                $response->rows[$i]['cell'] = array(
            $row->uuid_subcontrato,
            '<a style="color:blue;" class="link" href="'.base_url('subcontratos/ver/'.$row->uuid_subcontrato).'">'.$row->codigo.'</a>',
            '<a class="link">'.$row->proveedor->nombre.' '.$row->proveedor->apellido.'</a>',
            $row->tipo_subcontrato,
            $row->present()->monto_original,
            $row->present()->monto_adenda,
            $row->present()->monto_subcontrato,
            $row->present()->facturado,
            $row->present()->por_facturar,
            $row->centro_contable->nombre,
            $row->present()->estado,
            $link_option,
            $hidden_options,
        );
            }
        }

        return $response;
    }

    public function camposBuscar()
    {
        $campos = FormRequest::data_formulario($this->request->input('campo'));
        if (is_null($campos)) {
            return [];
        }

        return $campos;
    }
}
