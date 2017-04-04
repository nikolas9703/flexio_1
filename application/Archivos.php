<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Documentos Class
 *
 * Construye el Documentos de los módulos.
 * 
 *
 * @package    PensaApp
 * @subpackage Library
 * @category   Libraries
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @since     Version 1.0
 */
class Archivos
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
    public static function visualizar_documentos($documentos=array())
    {
    	 
    	$html = '';
    	
     
     	$box = '	<div class="col-lg-12 parent-container">';
       if( count($documentos) > 0){
	       	 foreach ($documentos as $doc)
	       	 {
 	       	 	$classImagen ="";
	       	 	//$info = new SplFileInfo($doc["nombre_random"]);
	       	 	
   	       	 	$src = base_url($doc["ruta"]."/".$doc["nombre_random"]);
  	       	 	 
 	       	 	if(isset($src) && !empty($src) ){
 	       	 		 
  	       	 		if (file_exists( $doc["ruta"]."/".$doc["nombre_random"] )) {
  	       	 			
  	       	 			if (! is_array ( @getimagesize ( $src ) )) {
  	       	 				
  	       	 				$classImagen = '<div class="icon">
                            <i class="fa fa-file"></i>
                            </div>';
  	       	 				//echo $info->getExtension();
  	       	 				//echo "<BR>";
  	       	 			}  
  	       	 			else{
  	       	 				$classImagen = '<div class="image">
                            <img alt="image" class="img-responsive" src="'.$src.'">
                            </div>';
  	       	 			}
  	       	 		} else {
 	       	 			 
 	       	 			 $classImagen = '<div class="image">
                         <img alt="image" class="img-responsive" src="' . base_url ( 'public/themes/erp/images/no_existe.jpg' ) . '">
                        </div>'; 
 	       	 			
 	       	 			//fa-times-circle-o
 	       	 			
 	       	 		}
 	       	 		 
 	       	 		$html .= '<div class="col-sm-2"><div class="text-center">'.$classImagen.'</div></div>';
 	       	 		 
  	       	 	}
  	       	 	
	       	 	$box .= '<div class="file-box ">
                                <div class="file">
                                    <a  href="'.$src.'" >
                                        <span class="corner"></span>
                                        '.$classImagen.'
                                        <div class="file-name">
                                            '.$doc['nombre'].'
                                            <br>
                                            <small>Fecha: '.date("d/m/Y", strtotime($doc['fecha_creacion'])).'</small>
                                            <br>
                                            <small>Creador Por: '.$doc['creado_por'].'</small>
                                            <a href="'.$src.'" download target="_blank" ><i class="fa fa-download"></i></<>
                                        </div>
                                    </a>
                                </div>
                            </div>';
	       	 }
	       	 
       }
	   else{
	   	  $box .='<div class="alert alert-warning">
                                Todavia no tienes ningún documento para este registro.
            </div>';
	   }
	   $box .= ' </div>';
      /* $html = '
       		<div class="col-lg-12">
                            
                         
                     
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p2.jpg">
                                        </div>
                                        <div class="file-name">
                                            My feel.png
                                            <br>
                                            <small>Added: Jan 7, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                           
                          
                          
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-file"></i>
                                        </div>
                                        <div class="file-name">
                                            Document_2014.doc
                                            <br>
                                            <small>Added: Jan 11, 2014</small>
                                        </div>
                                    </a>
                                </div>

                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p1.jpg">
                                        </div>
                                        <div class="file-name">
                                            Italy street.jpg
                                            <br>
                                            <small>Added: Jan 6, 2014</small>
                                        </div>
                                    </a>

                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p2.jpg">
                                        </div>
                                        <div class="file-name">
                                            My feel.png
                                            <br>
                                            <small>Added: Jan 7, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-music"></i>
                                        </div>
                                        <div class="file-name">
                                            Michal Jackson.mp3
                                            <br>
                                            <small>Added: Jan 22, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p3.jpg">
                                        </div>
                                        <div class="file-name">
                                            Document_2014.doc
                                            <br>
                                            <small>Added: Fab 11, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="img-responsive fa fa-film"></i>
                                        </div>
                                        <div class="file-name">
                                            Monica s birthday.mpg4
                                            <br>
                                            <small>Added: Fab 18, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <a href="#">
                                    <div class="file">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-bar-chart-o"></i>
                                        </div>
                                        <div class="file-name">
                                            Annual report 2014.xls
                                            <br>
                                            <small>Added: Fab 22, 2014</small>
                                        </div>
                                    </div>
                                </a>
                            </div>

                        </div>	
        ';*/


        echo $box;
    }
    
    
    
    
}
