<?php
namespace Flexio\Modulo\Cobros_seguros\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros as cobro;


class CobroAnular extends Model{

    protected $guarded = ['id'];
    protected $fillable = ['cobro_id','motivo','otros'];
    protected $table = 'cob_cobro_anulado';

    function cobros(){
      return $this->hasOne(Cobro::class,'id','cobro_id');
    }
	
}
