<?php
namespace Flexio\Modulo\EntradaManuales\HttpRequest;

use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\Contabilidad\Models\Cuentas;
use Flexio\Modulo\EntradaManuales\TransformData\TransformTransaccion;
use Flexio\Library\Util\GenerarCodigo;
use Flexio\Modulo\EntradaManuales\Models\EntradaManual;
use Illuminate\Http\Request;
use Carbon\Carbon as Carbon;

class EntradaManualRequest
{

    protected $request;

    function __construct()
    {
        $this->request = Request::capture();
    }


    function datos($empresa_id, $codigo_entrada, $codigo_transaccion){

        $dato_entrada = FormRequest::data_formulario($this->request->input('campo'));
        $dato_entrada['empresa_id'] = $empresa_id;
        $dato_entrada['codigo'] = $codigo_entrada + 1;

        $datos_transaciones = FormRequest::array_filter_dos_dimenciones($this->request->input('transacciones'));

        $transaciones = $this->asignar_signo($datos_transaciones,$empresa_id,$codigo_transaccion +1);

        return [$dato_entrada,$transaciones];
    }

    function asignar_signo($transaciones,$empresa_id,$codigo){

        foreach($transaciones as $key=>$transacion){
            $signos = new Cuentas;
            //$transaciones[$key] = $signos->signoCuenta($transacion);
            $transaciones[$key]['conciliacion_id'] = 0;
            $transaciones[$key]['balance_verificado'] = 0;
            $transaciones[$key]['empresa_id'] = $empresa_id;
            $transaciones[$key]['codigo'] =  GenerarCodigo::setCodigo('TR'.Carbon::now()->format('y'), $codigo + $key);
        }

        return $transaciones;
    }

    public function save($entrada, $transaccion)
    {
        $entrada_manual = EntradaManual::create($entrada);
        $formato = new TransformTransaccion;
        $relacion = $formato->crearInstancia($transaccion);
        $entrada_manual->transaccion()->saveMany($relacion);
        return $entrada_manual;
    }


}
