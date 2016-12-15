<?php
namespace Flexio\Modulo\Atributos\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class Atributos extends Model
{
    protected $table        = 'atr_atributos';
    protected $fillable     = ['nombre','descripcion'];
    protected $guarded      = ['id'];
    
    public function atributable(){
        
        return $this->morphTo();
        
    }
    
}
