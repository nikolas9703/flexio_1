<?php
namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Ramos\Models\CatalogoTipoPoliza;
use Flexio\Modulo\Solicitudes\Models\Solicitudes;

class SolicitudesPrima extends Model
{
    protected $table        = 'seg_solicitudes_prima';    
    protected $fillable     = ['id_solicitudes', 'prima_anual', 'impuesto', 'otros', 'descuentos', 'total', 'frecuencia_pago', 'metodo_pago', 'fecha_primer_pago', 'cantidad_pagos', 'sitio_pago', 'centro_facturacion', 'direccion_pago'];
    protected $guarded      = ['id'];
    
    //scopes
    public function solicitudes() {
        return $this->hasOne(Solicitudes::class, 'id', 'id_solicitudes');
    }
    
}