<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;


class Grupo_cliente_agrupador_orm extends Model{
  /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'grp_grupo_clientes';

    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['cliente_id', 'grupo_id','uuid_cliente', ''];
   /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];
      public function __construct(array $attributes = array()) {
         parent::__construct($attributes);
    }
    
    public static function getIdsClientes($id=NULL){
        
         return self::select('uuid_cliente')
                 ->where('grupo_id', '=', $id)->where('deleted_at', '=', NULL)->get();
    }
    public static function getIdsClientesRelations($id=NULL){
        
         return self::select('grupo_id')
                 ->where('uuid_cliente', '=', $id)->where('deleted_at', '=', NULL)->get();
    }
    public static function guardar($id_clientes = NULL, $id_agrupador = NULL){
       // $i=0;
        //foreach ($id_clientes AS $ids){
            self::insert([
                ['uuid_cliente' => $id_clientes, 'grupo_id' => $id_agrupador]
            ]);
           // $i++;
        //}
       /* return array(
            "respuesta" => true,
            "mensaje" => "Se han agrupado " . ( count($id_clientes) > 1 ? "los clientes satisfactoriamente." : "los clientes satisfactoriamente." )
        );*/
    }
    public static function desagrupar($id_cliente = NULL) {

        //Retorna false si $id_clientes es vacio
        if (empty($id_cliente)) {
            return false;
        }
        self::where('uuid_cliente', $id_cliente)
                ->update(['deleted_at' => date('Y-m-d H-i-s')]);
        return array(
            "respuesta" => true,
            "mensaje" => "Se ha desagrupado " . ( count($id_cliente) > 1 ? "el cliente satisfactoriamente." : "el cliente satisfactoriamente." )
        );
    }

}
?>