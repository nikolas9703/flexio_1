<?php

namespace Flexio\Modulo\NotaDebito\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\NotaDebito\Models\NotaDebito;
use Flexio\Modulo\NotaDebito\Validators\NotaDebitoValidator;
use Flexio\Strategy\Transacciones\Transaccion;
//use Flexio\Modulo\Cobros\Transaccion\TransaccionCobro;


class FormGuardarNotaDebito{

    protected $request;
    protected $session;
    protected $disparador;
    protected $NotaDebitoValidator;

    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->disparador = new \Illuminate\Events\Dispatcher();
        $this->NotaDebitoValidator = new NotaDebitoValidator;
    }


    function guardar(){

        $nota_credito = FormRequest::data_formulario($this->request->input('campo','empezable_type','empezable_id'));
        $nota_credito['tipo'] = $this->request->input('empezable_type');
        $nota_credito['factura_id'] = $this->request->input('empezable_id');
        $nota_credito = FormRequest::data_formulario($nota_credito);
        $datos_items = FormRequest::array_filter_dos_dimenciones($this->request->input('items'));

        if(isset($nota_credito['id'])){
            //actualiza
            //$comentario = ['comentario'=>$datos_activos['comentario'],'usuario_id'=>$usuario->id];
            return $this->actualizar($nota_credito, $datos_items);
        }

        $nota_credito['codigo'] = $this->_generar_codigo();
        $nota_credito['empresa_id'] =  $this->session->empresaId();
        $nota_credito['creado_por'] = $this->session->usuarioId();
        return $this->crear($nota_credito, $datos_items);
    }

    function crear($nota_credito, $datos_items){
        $this->NotaDebitoValidator->post_validate($nota_credito);
        return  Capsule::transaction(function() use($nota_credito, $datos_items){
            $nota_debito = NotaDebito::create($nota_credito);
            $lineItem = $this->getLinesItems($datos_items);
            $nota_debito->items()->saveMany($lineItem);

            //$comentario = new Comentario($array_comentario);
            //$nota_debito->comentario()->save($comentario);
            return $nota_debito;
        });
    }

    function actualizar($nota_credito, $datos_items){
        return  Capsule::transaction(function() use($nota_credito, $datos_items){
            $nota_debito = NotaDebito::find($nota_credito['id']);
            $nota_debito->update($nota_credito);
            $lineItem = $this->getLinesItems($datos_items);
            $nota_debito->items()->saveMany($lineItem);
            //$comentario = new Comentario($array_comentario);
            //$nota_debito->comentario()->save($comentario);
            //eventos
            //1. de por_aprobar a aprobado
            // 1.1 realiza la transaccion
            // 1.2 actualiza estado factura o el saldo del proveedor
            //-----------------------------------------------------
            //2. de por aprobar a anulada.
            //-----------------------------------------------------
            //3.  de aprobado a anulado
            //2.1 elimina la transaccion
            //2.2 reversa el cambio en factura o en el saldo del proveedor
            return $nota_debito;
        });
    }

    function getLinesItems($items){
        $linesItems = new \Flexio\Modulo\NotaDebito\Transform\NotaDebitoItemTransformer;
        return $linesItems->crearInstancia($items);
    }

    private function _generar_codigo(){
        $clause = ['empresa_id' => $this->session->empresaId()];
        $year = Carbon::now()->format('y');
        $nota_credito = NotaDebito::where($clause)->get()->last();
        $codigo_actual = is_null($nota_credito)? 0: $nota_credito->codigo;
        $codigo = (int)str_replace('ND'.$year, "", $codigo_actual);
        return $codigo + 1;
    }

}
