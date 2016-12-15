<?php

namespace Flexio\Modulo\TipoIntereses\Models;



use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Support\Facades\DB;



class TipoIntereses extends Model
{
	protected $table = 'seg_ramos_tipo_interes';

	 protected $fillable = ['nombre','id'];


}