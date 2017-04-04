<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Grid Class
 *
 * Esta clase genera el codigo html apara la vista de grid.
 *
 * @package    PensaApp
 * @category   Libraries
 * @author     Pensanomica Dev Team
 * @version    1.0 - 16/11/2015
 * @link       http://www.pensanomca.com
 * @since      16/11/2015
 *
 */
class Grid
{
	protected $ci;

	/**
	 * Nombre del Modulo Actual
	 *
	 * @var $modulo
	 */
	private static $modulo;


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(){

    	//Instancia del core de CI
    	$this->ci =& get_instance();

    	//Nombre del Modulo (HMVC)
    	self::$modulo = $this->ci->router->fetch_module();

    }

    /*
     * Esta function retorna el HTML
     * de grupo de subpanel en elemento Tabs
     */
    public static function set()
    {
    	$instance = new Grid();
    	return $instance;
    }
    
    public function html()
    {
    	Assets::agregarjs(array(
    		'public/assets/js/plugins/jquery/jquery.matchHeight.js',
    		'public/assets/js/default/grid.angular.js'
    	));

    	$html = '<div class="row grid-container" ng-cloak="" ng-controller="gridCtrl">
            	<div infinite-scroll-disabled="grid.busy">
    			<div class="col-lg-4 grid-item" ng-repeat="item in grid.items track by $index">
                <div class="contact-box">
                    <div class="col-sm-4">
                        <div class="text-center">
                            <div ng-repeat="(key, value) in item.perfil">
    							<img src="{{value}}" class="img-circle m-t-xs img-responsive" alt="image" ng-if="key == \'imagen\'">
    							<div class="m-t-xs font-bold" ng-show="key != \'imagen\'">{{value}}</div>
    						</div>
                        </div>
                    </div>
                    <div class="col-sm-8">
    					<div ng-repeat="(key, value) in item.datos">
				            <strong>{{key}}</strong>: {{value}}<br>
				        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
    		<div class="col-lg-12">
    			<button class="btn btn-block btn-outline btn-primary" type="button" ng-model="boton" ng-click="grid.nextPage()" ng-hide="grid.error == true"><span ng-show="grid.busy == false">Cargar Mas</span> <span ng-show="grid.busy"><i class="fa fa-circle-o-notch fa-spin"></i> Cargando...</span></button>
    			</div>
    		</div>
        </div>';
    	
    	return $html;
    }
    
    /*
     * Esta function retorna el HTML
    * de grupo de subpanel en elemento Tabs
    */
    public static function visualizar_grid($registros=NULL,$modulo=NULL)
    {
    	 
    	if($registros==NULL){
    		return false;
    	}
    	$options = '';
    
    	foreach ($registros AS $key => $registro){
    		if(!empty( $registro["titulo"]["name"] ))
    			$options .= "<option value='".$registro["titulo"]["name"]."' >".$registro["titulo"]["name"]."</option>";
    		if(!empty(  $registro["subtitulo"]["name"] ))
    			$options .= "<option value='".$registro["subtitulo"]["name"]."' >".$registro["subtitulo"]["name"]."</option>";
    		if(!empty( $registro["subtitulo2"]["name"] ))
    			$options .= "<option value='".$registro["subtitulo2"]["name"]."' >".$registro["subtitulo2"]["name"]."</option>";
    		/*if(preg_match("/clientes/i", $_SERVER['HTTP_REFERER'])){
    		 $options .= "<option value='C&eacute;dula' >C&eacute;dula</option>";
    		}*/
    		//aqui se arman los elementos que se me muestran en el formulario de
    		//busqueda de la vista tipo grid
    		foreach ($registro["info"] AS $key2 => $info)
    		{
    			if(!empty($info['name']))
    			{
    				$value      = "v".md5($info['name']);
    				$options   .="<option value='".$value."' >".$info['name']."</option>";
    			}
    		}
    
    		break;
    	}
    
    	$claseDeCol="col-xs-12 col-sm-12 col-md-6 col-lg-6";
    
    	$html = '
        <div class="col-md-2">
            <div class="ibox-content text-center fadeInLeft">
                <h2>Buscar</h2>
                <div class="m-b-sm">
                    <select id="selectBuscar" class="form-control" >'.$options.'</select>
                    <br>
                    <div class="bootstrap-tagsinput" id="busquedaGrid" data-role="tags"></div>
                </div>
            </div>
        </div>
        <div class="col-md-10">
            <div class="wrapper wrapper-content animated fadeInRight" id="iconGrid" style="padding-top: 0px;">
        	<div class="row"><ul id="myList">';
    
    	$x = 0;
    	foreach ($registros AS $key => $registro){
    		$infosHtml="";
    		//Ciclo para armar el HTML de infos mÃƒÂºltiples
    		foreach($registro["info"] AS $key => $registroInfo){
                        if(strtolower(str_replace(' ', '_', $registroInfo['name']))=='e-mail_'){
                            $claseDeCol="col-sm-12";
                        }else{
                            $claseDeCol="col-xs-12 col-sm-12 col-md-12 col-lg-6";
                        }
    			$infosHtml.='<div class="'.$claseDeCol.'">';
    			$value     = "v".md5($registroInfo['name']);
    			$infosHtml.='<span class="'.$value.'" data-propiedad="'.strtolower(str_replace(' ', '_', $registroInfo['name'])).'" data-value="'.strtolower($registroInfo['value']).'">'.trim(implode(": ",$registroInfo),':').'&nbsp;</span>';
    			$infosHtml.='</div>';
    		}
    		if ($x % 2 == 0) {
    			$html .= '<li>';
    		}
    
    		$html .= '
            <div class="col-lg-6 vcard">
                <div class="contact-box">
                    <div class="chat-element" style="padding-bottom: 0px;">';
    
    		$class_col_titulo_sub_boton = "col-sm-12";
    
    		if(isset($registro["imagen"]) && !empty($registro["imagen"])){
    
    			if (! is_array ( @getimagesize($registro["imagen"] ) )) {
    				$img = '<img   alt="image" class="img m-t-xs img-responsive" src="' . base_url ( 'public/themes/crmbase/images/no_disponible.jpg' ) . '">';
    			} else {
    				$img = '<img   alt="image" class="img m-t-xs img-responsive" src="'.$registro["imagen"].'">';
    			}
    
    			$html .= '<div class="col-sm-3"><div class="text-center">'.$img.'</div></div>';
    
    			$class_col_titulo_sub_boton = "col-sm-9";
    		}
    		$html .= '<div class="'. $class_col_titulo_sub_boton .'">
                            
                            
                            <input name="registro[]"  data-nombre="'.$registro['titulo']['value'].'" type="checkbox" data-modulo="'.$modulo.'" data-uuid="'.$registro["uuid"].'" class="cbox pull-right cbgrid" id="'.$modulo.'Grid_'.$registro["uuid"].'" value="'.$registro["uuid"].'">
                            <h3><strong><span class="'.$registro["titulo"]["name"].'" data-propiedad="'.strtolower(str_replace(' ', '_', $registro["titulo"]["name"])).'" data-value="'.strtolower($registro["titulo"]['value']).'">'.$registro["titulo"]["value"].'</span></strong></h3>
                            <p><span  class="'.$registro["subtitulo"]["name"].'" data-propiedad="'.strtolower(str_replace(' ', '_', $registro["subtitulo"]["name"])).'" data-value="'.strtolower($registro["subtitulo"]['value']).'">'.implode(": ",$registro["subtitulo"]).'</span></p>';
    
    		if(isset($registro["subtitulo2"]) && !empty($registro["subtitulo2"])){
    			$html .= '<p><span  class="'.$registro["subtitulo2"]["name"].'" data-propiedad="'.strtolower(str_replace(' ', '_', $registro["subtitulo2"]["name"])).'" data-value="'.strtolower($registro["subtitulo2"]['value']).'">'.implode(": ",$registro["subtitulo2"]).'</span></p>';
    		}
                
    		$html .= '</div>
                        <div class="col-sm-12"  style="text-align: right;">
                            <button type="button"  data-nombre="'.$registro['titulo']['value'].'"  id="'.$registro['id'].'" class="viewOptionsGrid  btn btn-success btn-sm" value="Opciones"  ><i class="fa fa-cog"></i> Opciones</button>
                         </div>
                    </div>
                    <hr>
                    <div class="chat-element">
                        '.$infosHtml.'
                    </div>
                     <div id="menu'.$registro['id'].'" style="display:none">
                                <span>'.$registro['opcion'].'</span>
                            </div>
                    <div class="clearfix"></div>
                </div>
            </div>';
    		if ($x % 2 != 0) {
    			$html .= '</li>';
    		}
    		$x++;
    
    	}
    
    	$html .= '</ul>
        		<button id="loadMore" class="btn btn-primary btn-block m-t"><i class="fa fa-arrow-down"></i> Mostrar Más</button>
        		<button id="showLess" class="btn btn-default btn-block m-t"><i class="fa fa-arrow-up"></i> Mostrar Menos</button>
     
                         </div>
                    </div>
                </div>
        ';
    
    
    	echo $html;
    }
    
    public static function documents_grid($documentos, $showMore=TRUE ){
    	$box = '';
    	if( count($documentos) > 0){
    		foreach ($documentos as $doc)
    		{
    			$classImagen ="";
    			//$info = new SplFileInfo($doc["nombre_random"]);
    
    			$src = base_url($doc["ruta"]."/".$doc["nombre_random"]);
    
    			if(isset($src) && !empty($src) ){
    
    				if (file_exists( $doc["ruta"]."/".$doc["nombre_random"] )) {
    
    					if (! is_array ( @getimagesize ( $src ) )) {
    
    						if (preg_match('/doc|docx/',$doc["nombre"])){
    							$classImagen = '<div class="icon"><i class="fa fa-file-word-o"></i></div>';
    						}elseif (preg_match('/.xls|.xlsx/',$doc["nombre"])){
    							$classImagen = '<div class="icon"><i class="fa fa-bar-chart-o"></i></div>';
    						}elseif (preg_match('/.ppt|.pptx/',$doc["nombre"])){
    							$classImagen = '<div class="icon"><i class="fa fa-file-powerpoint-o"></i></div>';
    						}elseif (preg_match('/.pdf/',$doc["nombre"])){
    							$classImagen = '<div class="icon"><i class="fa fa-file-pdf-o"></i></div>';
    						}elseif (preg_match('/.zip|.rar/',$doc["nombre"])){
    							$classImagen = '<div class="icon"><i class="fa fa-file-zip-o"></i></div>';}
    							else{
    								$classImagen = '<div class="icon"><i class="fa fa-file"></i></div>';
    							}
    					}
    					else{
    						$classImagen = '<div class="image">
                            <img id="'.$doc["id_archivo"].'" alt="image" class="img-responsive ver-imagenes" src="'.$src.'">
                            </div>';
    					}
    				} else {
    
    					$classImagen = '<div class="image">
                         <img id="'.$doc["id_archivo"].'" alt="image" class="img-responsive" src="' . base_url ( 'public/themes/crmbase/images/no_existe.jpg' ) . '">
                        </div>';
    
    					//fa-times-circle-o
    
    				}
    
    			}
    
    			$box .= '<div class="file-box documentos-box-view" data-documentosUid= "'.$doc['uuid_documento'].'">
                                <div class="file">
                                    <span class="corner"></span>'.$classImagen.'
                                        <div class="file-name">
                                            <b>'. Util::truncate($doc['nombre'],20).'</b>
                                            <input type="checkbox" name="checkdownload[]" value="'.$doc['id_archivo'].'" class="pull-right"/>
                                            <br>
                                            <small>Fecha: '.date("d/m/Y", strtotime($doc['fecha_creacion'])).'</small>
                                            <br>
                                            <small>Creador Por: '.Util::truncate($doc['usuario'],14).'</small>
                                            <a href="'.$src.'" download target="_blank" class="pull-right" ><i class="fa fa-download"></i></a>
                                        </div>
                                </div>
                            </div>';
    		}
    
    	}
    	else{
    		$box .='<div class="container documentos-message"><div id="documentos-resultados" class="alert alert-warning text-center no-resultados">Todavia no tienes ning&uacute;n documento para este registro.</div></div>';
    	}
    	if ($showMore) $box .= '<button id="loadMore-documentos" class="btn btn-primary btn-block m-t"><i class="fa fa-arrow-down"></i> Mostrar M&aacute;s</button>';
    	echo $box;
    }
    
    
    /*
     * Recibe una cadena y la retona sin tildes
    *@input int cadena required
    *
    *@return string $cadena
    */
    public function quitar_tildes($cadena)
    {
    	if(empty($cadena))
    	{
    		return false;
    	}
    
    	$no_permitidas= array ("ÃƒÂ¡","ÃƒÂ©","ÃƒÂ­","ÃƒÂ³","ÃƒÂº","Ãƒï¿½","Ãƒâ€°","Ãƒï¿½","Ãƒâ€œ","ÃƒÅ¡","ÃƒÂ±","Ãƒâ‚¬","ÃƒÆ’","ÃƒÅ’","Ãƒâ€™","Ãƒâ„¢","ÃƒÆ’Ã¢â€žÂ¢","ÃƒÆ’ ","ÃƒÆ’Ã‚Â¨","ÃƒÆ’Ã‚Â¬","ÃƒÆ’Ã‚Â²","ÃƒÆ’Ã‚Â¹","ÃƒÂ§","Ãƒâ€¡","ÃƒÆ’Ã‚Â¢","ÃƒÂª","ÃƒÆ’Ã‚Â®","ÃƒÆ’Ã‚Â´","ÃƒÆ’Ã‚Â»","ÃƒÆ’Ã¢â‚¬Å¡","ÃƒÆ’Ã…Â ","ÃƒÆ’Ã…Â½","ÃƒÆ’Ã¢â‚¬ï¿½","ÃƒÆ’Ã¢â‚¬Âº","ÃƒÂ¼","ÃƒÆ’Ã‚Â¶","ÃƒÆ’Ã¢â‚¬â€œ","ÃƒÆ’Ã‚Â¯","ÃƒÆ’Ã‚Â¤","Ã‚Â«","Ãƒâ€™","ÃƒÆ’Ã‚ï¿½","ÃƒÆ’Ã¢â‚¬Å¾","ÃƒÆ’Ã¢â‚¬Â¹");
    	$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
    
    	$texto = str_replace($no_permitidas, $permitidas ,$cadena);
    
    	return $texto;
    }

}
