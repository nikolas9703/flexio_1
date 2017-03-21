<?php

namespace Flexio\Modulo\MovimientosMonetarios\FormRequest;

//models
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\MovimientosMonetarios\Models\MovimientosRetiros as MovimientoRetiro;
use Flexio\Modulo\MovimientosMonetarios\Models\ItemsRetiros as ItemRetiro;

//transforms
use Flexio\Modulo\MovimientosMonetarios\transform\MovimientoRetiroTransform;
use Flexio\FormRequest\Guardar;

//utils
use Flexio\Library\Util\FormRequest;

class GuardarRetiroDinero extends Guardar
{

    protected $prefijo = 'WD';

    public function __construct()
    {
        parent::__construct(new MovimientoRetiroTransform, new MovimientoRetiro);
    }

    public function guardar($params = [])
    {
        $retiro_dinero = !empty($params) ? $params : FormRequest::data_formulario($this->request->input());
        if(isset($retiro_dinero['id']) && !empty($retiro_dinero['id'])){
            return $this->update($retiro_dinero);
        }
        return $this->create($retiro_dinero);
    }

    public function create($campo)
    {
        return  Capsule::transaction(function() use ($campo) {
            $modelName = $this->modelName;
            $this->create_validate($campo);
            $campo['empresa_id'] = $this->session->empresaId();
            $campo['usuario_id'] = $this->session->usuarioId();
            $campo['codigo'] = $this->setCodigo();
            $params = $this->transform->campo($campo);
            $retiro_dinero = $modelName::create($params);

            //rows saves
            $filas = $this->getFilas($params['transacciones']);
            $retiro_dinero->items()->saveMany($filas);

            //Transacciones
            $params_pago = $this->transform->pago($retiro_dinero);
            $GuardarPago = new \Flexio\Modulo\Pagos\FormRequest\GuardarPagos();
            $GuardarPago->save($params_pago);

            return $retiro_dinero;
        });
    }

    public function actualizar($campo)
    {
        return  Capsule::transaction(function() use ($campo) {
            $modelName = $this->modelName;
            $params = $this->transform->campo($campo);
            $retiro_dinero = $modelName::find($campo["campo"]["id"]);
            $retiro_dinero->update($params);

            //rows saves
            $filas = $this->getFilas($params['transacciones']);
            $retiro_dinero->items()->saveMany($filas);

            return $retiro_dinero;
        });
    }

    private function getFilas($filas)
    {
        $filasTransform = new \Flexio\Modulo\MovimientosMonetarios\Transform\MovimientoItemTransform(get_class(new ItemRetiro));
        return $filasTransform->crearInstancia($filas);
    }

    private function create_validate($campo)
    {
        if(!isset($campo['empezable_id']) || empty($campo['empezable_id']))throw new \Exception('Indique desde donde empezar el retiro de dinero');
    }

}
