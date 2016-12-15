<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Relacion_orm extends Model{

protected $table = 'relacion';
protected $fillable = ['usuario_id', 'relacion_id', 'relacion_type'];

public function relacion(){
  return $this->morphTo();
}

}
