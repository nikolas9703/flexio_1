<?php

namespace Flexio\Modulo\TipoPoliza\Models;



use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Support\Facades\DB;



class TipoPoliza extends Model
{
	protected $table = 'seg_ramos_tipo_poliza';

	 protected $fillable = ['nombre','id'];


}