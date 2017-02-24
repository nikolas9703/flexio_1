<?php
namespace Flexio\Modulo\Anticipos\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Anticipos\Models\Anticipo;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

//events compras
use Flexio\Modulo\Anticipos\Events\RealizarTransaccionAnticipo;
use Flexio\Modulo\Anticipos\Events\ActualizarCreditoProveedor;
use Flexio\Modulo\Anticipos\Events\CrearRegistroPago;
//events ventas
use Flexio\Modulo\Anticipos\Events\RealizarTransaccionAnticipoVentas;
use Flexio\Modulo\Anticipos\Events\ActualizarCreditoCliente;
use Flexio\Modulo\Anticipos\Events\CrearRegistroCobro;
//listener
use Flexio\Modulo\Anticipos\Listeners\AnticipoAprobado;

class GuardarAnticipo{
    protected $request;
    protected $session;
    protected $tipo_deposito;
    protected $disparador;
    protected $tipo_anticipable;
    protected $empezable;

    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->tipo_deposito = ['banco'=>'Flexio\Modulo\Contabilidad\Models\Cuentas',
                                'caja'=>'Flexio\Modulo\Cajas\Models\Cajas'];
        $this->tipo_anticipable = ['proveedor' => 'Flexio\Modulo\Proveedores\Models\Proveedores',
                                   'cliente'=>'Flexio\Modulo\Cliente\Models\Cliente',
								   'polizas' => 'Flexio\Modulo\Polizas\Models\Polizas'];
        $this->disparador = new \Illuminate\Events\Dispatcher();
		
        $this->empezable = ['orden_compra' => 'Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra',
        'subcontrato'=>'Flexio\Modulo\SubContratos\Models\SubContrato',
        'orden_venta'=>'Flexio\Modulo\OrdenesVentas\Models\OrdenVenta',
        'contrato' => 'Flexio\Modulo\Contratos\Models\Contrato',
		'polizas' => 'Flexio\Modulo\Polizas\Models\Polizas'];
    }

    function guardar(){
          $anticipo = FormRequest::data_formulario($this->request->input('campo'));
          $empezable = FormRequest::data_formulario($this->request->input('empezable'));

          $metodo_anticipo = $this->request->input('metodo_anticipo');

          if(isset($anticipo['metodo_anticipo']))$anticipo["referencia"] = FormatoReferencia::referencia($anticipo["metodo_anticipo"], $metodo_anticipo);

          if(isset($anticipo['tipo_deposito']))$anticipo['depositable_type'] = $this->tipo_deposito[$anticipo['tipo_deposito']];
          if(isset($anticipo['tipo_anticipable']))$anticipo['anticipable_type'] = $this->tipo_anticipable[$anticipo['tipo_anticipable']];
          if(isset($anticipo['id'])){
              return $this->actualizar($anticipo);
          }
          $anticipo["empresa_id"] = $this->session->empresaId();
          $anticipo["creado_por"] = $this->session->usuarioId();
          $anticipo['codigo'] = $this->getLastCodigo();
          $modelPolimorfico = $this->empezableType($empezable);
		  
          return $this->crear($anticipo,$modelPolimorfico,$empezable);
    }

    function crear($campos,$cabezera,$post_empezable){
        return Capsule::transaction(function() use($campos, $cabezera,$post_empezable){
            $anticipo = Anticipo::create($campos);
            //insertar en empezable
            if(!is_null($cabezera)){
                $relacion = $post_empezable['empezable_type'];
				if(function_exists($anticipo->{$relacion})){
					$anticipo->{$relacion}()->save($cabezera);
				}
            }
            return $anticipo;
        });
    }
	
    function actualizar($campos){

        return Capsule::transaction(function() use($campos){
            $anticipo = Anticipo::find($campos['id']);
            $estado = $anticipo->estado;
            $anticipo->update($campos);

            if($estado != $anticipo->estado){
                $cambio['estado'] = $anticipo->getOriginal('estado');
                //verefica que el metodo existe

                if(method_exists($this, "evento".$cambio['estado'])){
                    //llama el metodo aprobado o anulado
                    call_user_func_array(['Flexio\Modulo\Anticipos\FormRequest\GuardarAnticipo', "evento".$cambio['estado']],[$anticipo]);
                }
            }

            return $anticipo;
        });
    }

    function getLastCodigo(){
        $clause = ['empresa_id' => $this->session->empresaId()];
        $year = Carbon::now()->format('y');
        $anticipo = Anticipo::where($clause)->get()->last();
        $codigo = empty($anticipo)? 0 : (int)str_replace('ANT'.$year, "", $anticipo->codigo);
        return $codigo + 1;
      }

    function empezableType($metodo){
        if(empty($metodo)){
            return null;
        }
        if(array_key_exists($metodo['empezable_type'], $this->empezable)){
            $modelo = (new $this->empezable[$metodo['empezable_type']])->find($metodo['empezable_id']);
            return $modelo;
        }
    }

    function eventoaprobado($anticipo){
        //listener handle
        if($this->session->session()->userdata('modulo_padre')=='compras' || $this->session->session()->userdata('modulo_padre')=='contratos'){
        $this->disparador->listen(
        [
            CrearRegistroPago::class
        ],
         AnticipoAprobado::class);

        $this->disparador->fire(new CrearRegistroPago($anticipo));
    }else if($this->session->session()->userdata('modulo_padre')=='ventas'){
          $this->disparador->listen(
          [
              RealizarTransaccionAnticipoVentas::class,
              ActualizarCreditoCliente::class
          ],
           AnticipoAprobado::class);

           $this->disparador->fire(new RealizarTransaccionAnticipoVentas($anticipo));
           $this->disparador->fire(new ActualizarCreditoCliente($anticipo));
        }
    }

}
