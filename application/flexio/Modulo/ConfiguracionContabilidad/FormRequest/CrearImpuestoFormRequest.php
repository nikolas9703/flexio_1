<?php

namespace Flexio\Modulo\ConfiguracionContabilidad\FormRequest;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Http\Request;
use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\ConfiguracionContabilidad\Models\Impuesto;

class CrearImpuestoFormRequest{
  protected $request;

  function __construct() {
    $this->request = Request::capture();
  }

  function guardar($campos) {

    $datos = FormRequest::data_formulario($this->request->all());
    $datos = array_merge($datos, $campos);

    return Capsule::transaction(function()use($datos){

      if(isset($datos['id'])) return $this->actualizarImpuesto($datos);

      return $this->crearImpuesto($datos);
    });
  }

  function crearImpuesto($datos) {
    $impuesto = Impuesto::registrar();
    $impuesto->fill($datos)->save();
    return $impuesto;
  }

  function actualizarImpuesto($datos) {
    $impuesto = Impuesto::find($datos['id']);
    $impuesto->update($datos);
    return $impuesto;
  }
}
