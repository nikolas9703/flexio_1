<?php
namespace Flexio\Modulo\Comisiones\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;


class ComisionEstado extends Model
{
  protected $table = 'com_comisiones_cat';
  protected $fillable = ['id_campo', 'valor', 'etiqueta','identificador'];
  protected $guarded = ['id_cat'];
  public $timestamps = false;
  protected $primaryKey = 'id_cat';
  protected $appends     = ['color_estado'];


  public function getColorEstadoAttribute()
  {
      if($this->id_cat == 20) //Abierto, amarillo
         $color = '#F8AC59';
      else if($this->id_cat == 14) //Turquesa
        $color = '#1AB394';
      else if($this->id_cat == 16) //Anulada
        $color = '#000000';
      /*else if($this->id_cat == 16) //Turquesa
        $color = '#23C6C8';*/
      else if($this->id_cat == 29) //Celeste
        $color = '#4fd4f2';

      else if($this->id_cat == 19) //Rojo
        $color = '#ED5565';
        else if($this->id_cat == 30)
        $color = '#fd996b';
      else if($this->id_cat == 31)
        $color = '#23C6C8';
      else if($this->id_cat == 32)
        $color = '#ED5565';
        else {
          $color = '#ffffff';
        }
        return $color;
  }
}
/* Planilla
if($this->id_cat == 13) //Abierto
   $color = '#F8AC59';
else if($this->id_cat == 14)
  $color = '#1AB394';
else if($this->id_cat == 15)
  $color = '#000000';
else if($this->id_cat == 16)
  $color = '#23C6C8';
else if($this->id_cat == 29)
  $color = '#4fd4f2';
else {
  $color = '#ffffff';
}*/
