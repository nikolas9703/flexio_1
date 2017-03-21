<?php
namespace Flexio\Modulo\HonorariosSeguros\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as Model;

class SegHonorariosPart extends Model
{
    protected $table        = 'seg_honorarios_part';    
    protected $fillable     = ['id_honorario', 'id_comision_part'];
    protected $guarded      = ['id'];
}   