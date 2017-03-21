<?php 

use Illuminate\Database\Eloquent\Model as Model;

class Proveedores_cat_orm extends Model
{
    protected $table = 'pro_proveedores_cat';
    public $timestamps = false;	
    protected $keepRevisionOf = ['id_campo', 'valor', 'etiqueta', 'orden'];
    protected $guarded      = ['id_cat'];
}
