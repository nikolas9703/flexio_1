<?php
namespace Flexio\Modulo\Catalogos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class RamosDocumentos extends Model
{
	
	protected $table = 'seg_ramos_documentos'; 
	protected $fillable =["id","id_ramo","nombre","categoria","modulo","estado","updated_at","created_at"]; 
	public $timestamps = false;

	public static function listar_documentos_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
        $documentos = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){

            if( (isset($clause['id_ramo']) ) && (!empty($clause['id_ramo']) ) ){ 
            	$query->where('id_ramo','=' , $clause['id_ramo']);
            }else{
            	$query->where('id_ramo','=' ,0);
            }


            if($limit!=NULL) $query->skip($start)->take($limit);            
       	});
        
        if($sidx!=NULL && $sord!=NULL){ $documentos->orderBy($sidx, $sord); }

        return $documentos->get();
    }
	
}