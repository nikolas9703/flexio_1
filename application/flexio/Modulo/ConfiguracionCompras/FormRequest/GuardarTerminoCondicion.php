<?php

namespace Flexio\Modulo\ConfiguracionCompras\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

//models
use Flexio\Modulo\ConfiguracionCompras\Models\TerminoCondicion;

class GuardarTerminoCondicion
{
    protected $request;
    protected $session;

    public function __construct()
    {
        $this->request = Request::capture();
        $this->session = new FlexioSession();
    }

    public function guardar($params = [])
    {
        $termino_condicion = !empty($params) ? $params : FormRequest::data_formulario($this->request->input());
        if (isset($termino_condicion['id']) && !empty($termino_condicion['id'])) {
            return $this->update($termino_condicion);
        }

        $termino_condicion['empresa_id'] = $this->session->empresaId();
        $termino_condicion['created_by'] = $this->session->usuarioId();
        return $this->create($termino_condicion);
    }

    public function update($campos)
    {
        return Capsule::transaction(function () use ($campos) {
            $termino_condicion = TerminoCondicion::find($campos['id']);
            $termino_condicion->update($campos);
            //relations
            $this->categories_sync($termino_condicion, $campos);

            return $termino_condicion;
        });
    }

    public function create($campos)
    {
        return Capsule::transaction(function () use ($campos) {
            $termino_condicion = TerminoCondicion::create($campos);
            //relations
            $this->categories_sync($termino_condicion, $campos);
            return $termino_condicion;
        });
    }

    private function categories_sync(TerminoCondicion $termino_condicion, $campos)
    {
        if(isset($campos['categorias']) && !empty($campos['categorias'])){
            $termino_condicion->categorias()->sync($campos['categorias']);
        }else if(isset($campos['categorias'])){
            $termino_condicion->categorias()->sync([]);
        }
    }

}
