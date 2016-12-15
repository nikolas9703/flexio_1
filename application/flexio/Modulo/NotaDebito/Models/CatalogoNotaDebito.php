<?php
namespace Flexio\Modulo\NotaDebito\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class CatalogoNotaDebito extends Model{
  protected $table = 'compra_nota_debito_catalogo';

  protected $fillable = ['key','valor','etiqueta','tipo','orden'];

  protected $guarded = ['id'];

  public function scopeEstados($query)
  {
      return $query->where("tipo", "estado");
  }
  public function getColorLabelAttribute()
  {
      if($this->id == 1) //Abierto
         $label = 'warning';
      else if($this->id == 2)
        $label = 'successful';
      else if($this->id == 3)
        $label = 'inverse';

      else {
        $label = '#ffffff';
      }
      return $label;
  }
 
}
