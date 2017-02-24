<?php

namespace Flexio\Modulo\Contabilidad\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Validation;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Contabilidad\Models\Cuentas;



class GuardarCuenta{

    protected $request;
    protected $session;

    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
    }


    function guardar(){

         $cuenta = FormRequest::data_formulario($this->request->only('id','nombre','codigo','descripcion','padre_id'));

         $this->cuentaExiste($cuenta['codigo']);

         if(isset($cuenta['id'])){
             return $this->actualizar($cuenta);
         }
         $tipo_cuenta = $this->findPadre($cuenta['padre_id']);
         $cuenta['tipo_cuenta_id'] = $tipo_cuenta->tipo_cuenta_id;
         $cuenta['usuario_id'] = $this->session->usuarioId();
         $cuenta['empresa_id']= $this->session->empresaId();
         return $this->crear($cuenta);

    }


    function crear($campos){
        return  Capsule::transaction(function() use($campos){
            $cuenta = Cuentas::create($campos);
            return $cuenta;
        });
    }

    protected function findPadre($padre_id){
        $cuenta = Cuentas::find($padre_id);
        if(is_null($cuenta)){
            throw new \Exception("La cuenta no tiene Padre");
        }
        return $cuenta;
    }

    protected function cuentaExiste($codigo){
       if(Cuentas::where('codigo',$codigo)->count('id')){
           throw new \Exception("La cuenta con ese codigo ya existe");;
       }
    }


    function actualizar($campos){

        return  Capsule::transaction(function() use($campos){
            $cuenta = Cuentas::find($campos['id']);
            $cuenta->update($campos);
            return $cuenta;
        });
    }
}
