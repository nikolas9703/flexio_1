<?php
namespace Flexio\Modulo\Bancos\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class Bancos extends Model
{
    protected $table        = 'ban_bancos';
    protected $fillable     = ['nombre', 'ruta_transito'];
    protected $guarded      = ['id'];
    protected $primaryKey   = "id";
    public $timestamps      = false;
}
