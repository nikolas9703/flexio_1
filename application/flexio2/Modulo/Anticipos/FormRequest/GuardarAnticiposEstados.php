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

class GuardarAnticiposEstados{
    protected $request;
    protected $session;
    protected $disparador;


    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->disparador = new \Illuminate\Events\Dispatcher();
    }

    function guardar(){

          $campos = FormRequest::data_formulario($this->request->input('campo'));
          $ids = $campos['ids'];
          $empresa = $this->session->empresaId();
          $campo = ['estado'=>$campos['estado']];
          $anticipo = Anticipo::where(function($query)use($empresa,$ids){
              $query->where('empresa_id', $empresa)
                    ->whereIn('id',$ids);
                })->get();

            return Capsule::transaction(function() use($campo, $anticipo){
              return $anticipo->map(function($ant) use($campo){
                  $ant->update($campo);
                  if($campo['estado'] == 'aprobado'){
                    $this->eventoaprobado($ant);
                  }

                  return $ant;
              });
        });
    }

    function eventoaprobado($anticipo){

      //listener handle
      if($this->session->session()->userdata('modulo_padre')=='compras'){
            $this->disparador->listen(
            [
                RealizarTransaccionAnticipo::class,
                ActualizarCreditoProveedor::class,
                CrearRegistroPago::class
            ],
             AnticipoAprobado::class);

            $this->disparador->fire(new RealizarTransaccionAnticipo($anticipo));
            $this->disparador->fire(new ActualizarCreditoProveedor($anticipo));
            $this->disparador->fire(new CrearRegistroPago($anticipo));
    }else if($this->session->session()->userdata('modulo_padre')=='ventas'){
      $this->disparador->listen(
      [
          RealizarTransaccionAnticipoVentas::class,
          ActualizarCreditoCliente::class,
          CrearRegistroCobro::class
      ],
       AnticipoAprobado::class);

       $this->disparador->fire(new RealizarTransaccionAnticipoVentas($anticipo));
       $this->disparador->fire(new ActualizarCreditoCliente($anticipo));
       //$this->disparador->fire(new CrearRegistroCobro($anticipo));
    }
  }


}
