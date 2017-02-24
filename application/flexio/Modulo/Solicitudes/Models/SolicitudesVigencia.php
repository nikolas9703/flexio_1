<?php
namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Ramos\Models\CatalogoTipoPoliza;
use Flexio\Modulo\Solicitudes\Models\Solicitudes;

class SolicitudesVigencia extends Model
{
    protected $table        = 'seg_solicitudes_vigencia';    
    protected $fillable     = ['id_solicitudes', 'vigencia_desde', 'vigencia_hasta', 'suma_asegurada', 'tipo_pagador', 'pagador', 'poliza_declarativa'];
    protected $guarded      = ['id'];
    
    //scopes
    public function solicitudes() {
        return $this->hasOne(Solicitudes::class, 'id', 'id_solicitudes');
    }
    
}