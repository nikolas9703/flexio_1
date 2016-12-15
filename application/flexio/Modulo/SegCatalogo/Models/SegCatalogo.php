<?php
namespace Flexio\Modulo\SegCatalogo\Models;

use Illuminate\Database\Eloquent\Model as Model;

class SegCatalogo extends Model
{
    protected $table        = 'seg_catalogo';    
    protected $fillable     = ['id', 'key', 'valor', 'etiqueta', 'tipo', 'orden'];
    protected $guarded      = ['id'];
    
}