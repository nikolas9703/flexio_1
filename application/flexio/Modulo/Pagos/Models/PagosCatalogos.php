<?php
namespace Flexio\Modulo\Pagos\Models;

use Illuminate\Database\Eloquent\Model;

class PagosCatalogos extends Model
{
    protected $table    = 'pag_pagos_catalogo';//uso el mismo catalogo de cobros
    protected $guarded  = ['id'];

    //scopes
    public function scopeEtapas($query){
        return $query->where('tipo','etapa');
    }

    public function scopeTipoCobro($query)
    {
      return $query->where("tipo", "tipo_pago");
    }
    public function estado($id = null){
        return $this->where("id" , $id)->get();
    }
}
