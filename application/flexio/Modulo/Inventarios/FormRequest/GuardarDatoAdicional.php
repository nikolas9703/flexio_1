<?php

namespace Flexio\Modulo\Inventarios\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

//models
use Flexio\Modulo\Inventarios\Models\DatoAdicional;

class GuardarDatoAdicional
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
        $dato_adicional = !empty($params) ? $params : FormRequest::data_formulario($this->request->input());
        if (isset($dato_adicional['id']) && !empty($dato_adicional['id'])) {
            return $this->update($dato_adicional);
        }

        $dato_adicional['empresa_id'] = $this->session->empresaId();
        $dato_adicional['created_by'] = $this->session->usuarioId();
        return $this->create($dato_adicional);
    }

    public function update($campos)
    {
        return Capsule::transaction(function () use ($campos) {
            $dato_adicional = DatoAdicional::find($campos['id']);
            $dato_adicional->update($campos);

            return $dato_adicional;
        });
    }

    public function create($campos)
    {
        return Capsule::transaction(function () use ($campos) {
            $dato_adicional = DatoAdicional::create($campos);
            return $dato_adicional;
        });
    }

}
