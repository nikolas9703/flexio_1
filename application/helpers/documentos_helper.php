<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('documentos')){
	
	function documentos($usuarios, $usuarios_propiedad, $tipo='', $modulo='', $uuid_documento=''){
		
		
			$clause_documento="";
			$clause_tipo="";
			$clause_usuario="";
			$clause_usuario_propiedad="";
			if(!empty($usuarios['uuid_usuario'])){ 
				
				$uuid = "'".implode("','", $usuarios['uuid_usuario'])."'";
				$uuid_usuarios_propiedad = "'".implode("','", $usuarios_propiedad['uuid_usuario'])."'";
				
				$clause_usuario = " AND HEX(uuid_usuario) in ($uuid)";
				$clause_usuario_propiedad = " AND HEX(uuid_usuario) in ($uuid_usuarios_propiedad)";
			}
			
			if(!empty($tipo)&& $tipo!='todos' ) $clause_tipo = " AND ".documentosTipoArchivo($tipo);
			if(!empty($modulo)) $clause_modulo = $modulo;
			if(!empty($uuid_documento)){ 
				$string_uuid_documentos = "'".implode("','", $uuid_documento)."'";
				$clause_documento = " AND HEX(uuid_relacion) in ($string_uuid_documentos)";
			}
			
			if($tipo =='todos' && empty($modulo)){
			$clause = "id_modulo IN (2,3,5,9) OR (id_modulo in (8,12) $clause_usuario) OR (id_modulo = 10 $clause_usuario_propiedad)";
			}elseif($tipo != 'todos' && empty($modulo)){
				$clause = "(id_modulo IN (2,3,5,9) OR (id_modulo in (8,12) $clause_usuario) OR (id_modulo = 10 $clause_usuario_propiedad)) ".$clause_tipo;
			}elseif(!empty($modulo)){
			
				switch ($modulo){
					case 2:
					case 3:
					case 5:
					case 9:
						$clause = "id_modulo IN ($modulo) " .$clause_tipo.$clause_documento;
					break;
					case 8:
					case 12:
						$clause = "id_modulo IN ($modulo)  $clause_usuario ".$clause_tipo.$clause_documento;
					break;	
					case 10:
						$clause = "id_modulo IN ($modulo) $clause_usuario_propiedad ".$clause_tipo.$clause_documento;
					break;	
					
				}
			}
		
		return $clause;
	}
	
}


if(!function_exists('documentosTipoArchivo'))
{
	function documentosTipoArchivo($tipo){
		$clause_tipo="";
		switch($tipo) {
			case 'documentos':
				$clause_tipo = "(mime NOT LIKE 'image%' AND mime NOT LIKE 'audio%' AND mime NOT LIKE 'video%')";
				break;
			case 'imagenes':

				$clause_tipo = "mime LIKE 'image%'";
				break;
			case 'audio':
				$clause_tipo = "(mime LIKE 'audio%' OR mime LIKE 'video%')";
				break;
		}
		return $clause_tipo;
	}
}	