<?php
namespace Flexio\Modulo\Contratos\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;

class Pagos extends Model
{
    protected $table = 'pag_pagos';
    protected $fillable = [''];
    protected $guarded = ['id'];
    protected $appends      = ['icono','enlace'];

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function pagos_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("pagos_contratos/ver/".$this->uuid_pago);
    }
    public function getIconoAttribute(){
        return 'fa fa-shopping-cart';
    }
    public function getCodigoAttribute(){
        return $this->codigo;
    }
}
