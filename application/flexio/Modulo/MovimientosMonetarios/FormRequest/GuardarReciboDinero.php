<?php

namespace Flexio\Modulo\MovimientosMonetarios\FormRequest;

//models
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\MovimientosMonetarios\Models\MovimientoRecibo;
use Flexio\Modulo\MovimientosMonetarios\Models\ItemRecibo;

//transforms
use Flexio\Modulo\MovimientosMonetarios\transform\MovimientoReciboTransform;

//transactios
use Flexio\Modulo\MovimientosMonetarios\Transacciones\MovimientosMonetariosRecibo;
use Flexio\FormRequest\Guardar;

//utils
use Flexio\Library\Util\FormRequest;

class GuardarReciboDinero extends Guardar
{
    protected $prefijo = 'RC';

    public function __construct()
    {
        parent::__construct(new MovimientoReciboTransform, new MovimientoRecibo);
    }

    public function guardar($params = [])
    {
        $recibo_dinero = !empty($params) ? $params : FormRequest::data_formulario($this->request->input());
        if(isset($recibo_dinero['id']) && !empty($recibo_dinero['id'])){
            return $this->update($recibo_dinero);
        }
        return $this->create($recibo_dinero);
    }

    public function create($campo)
    {
        return  Capsule::transaction(function() use ($campo) {
            $this->create_validate($campo);
            $modelName = $this->modelName;
            $campo['empresa_id'] = $this->session->empresaId();
            $campo['usuario_id'] = $this->session->usuarioId();
            $campo['codigo'] = $this->setCodigo();
            $params = $this->transform->campo($campo);
            $recibo_dinero = $modelName::create($params);

            //rows saves
            $filas = $this->getFilas($params['transacciones']);
            $recibo_dinero->items()->saveMany($filas);

            //Transacciones
            $transaction = new MovimientosMonetariosRecibo;
            $transaction->haceTransaccion($recibo_dinero);

            return $recibo_dinero;
        });
    }

    public function actualizar($campo)
    {
        return  Capsule::transaction(function() use ($campo) {
            $modelName = $this->modelName;
            $params = $this->transform->campo($campo);
            $recibo_dinero = $modelName::find($campo["campo"]["id"]);
            $recibo_dinero->update($params);

            //rows saves
            $filas = $this->getFilas($params['transacciones']);
            $recibo_dinero->items()->saveMany($filas);

            //Transacciones
            $transaction = new MovimientosMonetariosRecibo;
            $transaction->haceTransaccion($recibo_dinero);

            return $recibo_dinero;
        });
    }

    private function getFilas($filas)
    {
        $filasTransform = new \Flexio\Modulo\MovimientosMonetarios\Transform\MovimientoItemTransform(get_class(new ItemRecibo));
        return $filasTransform->crearInstancia($filas);
    }

    private function create_validate($campo)
    {
        if(!isset($campo['empezable_id']) || empty($campo['empezable_id']))throw new \Exception('Indique desde donde empezar el recibo de dinero');
    }

}
