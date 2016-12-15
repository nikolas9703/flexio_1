<?php
namespace Flexio\Modulo\NotaCredito\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class CatalogoNotaCredito extends Model{
  protected $table = 'venta_nota_credito_catalogo';

  protected $fillable = ['key','valor','etiqueta','tipo','orden'];

  protected $guarded = ['id'];

  public function scopeEstados($query)
  {
      return $query->where("tipo", "estado");
  }
}
