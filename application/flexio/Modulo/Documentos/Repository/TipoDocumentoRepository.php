<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 5/12/16
 * Time: 11:00 AM
 */

namespace Flexio\Modulo\Documentos\Repository;
use Flexio\Modulo\Documentos\Models\TipoDocumentos;

class TipoDocumentoRepository
{
    protected $ci;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        /*
         * Instanciar codeigniter
        */
        $this->ci = &get_instance();
    }

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $tipos = TipoDocumentos::where(function($query) use ($clause){
            if(isset($clause['campo']) && !empty(array_filter($clause['campo']))){$query->deFiltro($clause['campo']);}
        });

        if($sidx!=NULL && $sord!=NULL){$tipos->orderBy($sidx, $sord);}
        if($limit!=NULL){$tipos->skip($start)->take($limit);}
        return $tipos->get();
    }

    function find($id) {
        return TipoDocumentos::find($id);
    }
    public static function findByUuid($uuid) {
        return TipoDocumentos::where('uuid_tipo',hex2bin($uuid))->first();
    }
    public static function findByUuid2($uuid) {
        return TipoDocumentos::where('uuid_tipo',bin2hex($uuid))->first();
    }
    public static function exportar($clause = array()) {

        return TipoDocumentos::whereIn('uuid_tipo', $clause)->get();
    }
}
