<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Catalogo_toma_contacto_orm extends Model {

    protected $table = 'cp_clientes_potenciales_cat';
    protected $fillable = ['id_campo', 'valor', 'etiqueta'];
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Retorna listado de Estados
     */
    public static function lista() {
        return Capsule::table('cp_clientes_potenciales_campo AS desccam')
                        ->leftJoin('cp_clientes_potenciales_campo AS desccat', 'desccat.id_campo', '=', 'desccam.id_campo')
                        ->where('desccam.nombre_campo', '=', 'id_toma_contacto')
                        ->get(array('desccat.id_cat', 'desccat.etiqueta'));
        //  print_r(Capsule::getQueryLog());
    }

}
