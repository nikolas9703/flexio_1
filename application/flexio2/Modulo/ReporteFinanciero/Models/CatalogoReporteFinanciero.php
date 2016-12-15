<?php
namespace Flexio\Modulo\ReporteFinanciero\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class CatalogoReporteFinanciero extends Model
{
  protected $table = 'cat_reporte_financiero';
  protected $guarded = ['id'];
  public $timestamps = false;


  public function scopeReporte($query){
    return $query->where('tipo','=','reporte');
  }

}
