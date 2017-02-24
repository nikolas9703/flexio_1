<?php
namespace Flexio\Modulo\Ajustadores\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use \Illuminate\Database\Capsule\Manager as Capsule;

//utilities
use Carbon\Carbon as Carbon;

class AjustadoresContacto extends Model
{
    protected $prefijo      = 'seg';
    protected $table        = 'seg_ajustadores_contacto';
    protected $fillable     = ['nombre', 'apellido', 'cargo', 'telefono', 'celular', 'email', 'ajustador_id', 'principal'];
    protected $guarded      = ['id'];
    public $timestamps      = true;
    
    public function getCreatedAtAttribute($value) {
        return date("d/m/Y", strtotime($value));
    }
}
