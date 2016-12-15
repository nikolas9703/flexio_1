<?php
namespace Flexio\Modulo\Inventarios\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class ItemsComentario extends Model
{
    protected $table        = 'lines_items_comentario';
    protected $fillable     = ['uuid_comentario', 'lines_items_id', 'comentario'];
    protected $guarded      = ['id'];
    public $timestamps      = false;
    
    //GETS
    public function getUuidComentarioAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    
    
}
