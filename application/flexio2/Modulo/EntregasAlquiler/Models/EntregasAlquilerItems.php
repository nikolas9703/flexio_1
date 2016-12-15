<?php
namespace Flexio\Modulo\EntregasAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class EntregasAlquilerItems extends Model
{
    protected $table    = 'conalq_items';
    protected $fillable = ['empresa_id','item_id', 'serie'];
    protected $guarded  = ['id'];
    public $timestamps = false;
}
