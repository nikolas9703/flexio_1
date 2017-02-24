<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Builder as Builder;
use Illuminate\Database\Capsule\Manager as Capsule;

class Roles_menu_orm extends Model{

    protected $table = 'roles_has_menu';
    protected $fillable = ['id_empresa','id_rol','nombre_menu'];
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Instancia de CodeIgniter
     */


}
