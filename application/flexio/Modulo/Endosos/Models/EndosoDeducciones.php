<?php 

namespace Flexio\Modulo\Endosos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;



class EndosoDeducciones extends Model
{
  protected $table = 'end_endosos_deduccion';
  protected $fillable = ['id','deduccion','valor_deduccion','id_endoso','id_endoso_interes']; 
  protected $guarded = ['id'];
  public $timestamps = false;


  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'updated_at' => date('Y-m-d H:s:i'),
      'created_at' => date('Y-m-d H:s:i')
    )), true);
    parent::__construct($attributes);
  }

}
