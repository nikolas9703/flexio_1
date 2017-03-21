<?php
/**
 * Configuracion
 *
 * Modulo que muestra los modulos activos que tienen seccion de configuracion.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 *
 */
class Configuracion extends CRM_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
    	
    	// Build the data array
    	$this->template->agregar_titulo_header ( 'Ver Usuario' );
    	$this->template->agregar_breadcrumb ( array (
    			 "titulo" => '<i class="fa fa-gear"></i>  Administraci&oacute;n',
    			"ruta" => array (
    					0 => array (
    							"nombre" => "Administraci&oacute;n",
    							"activo" => false,
    							"url" 	=> 'configuracion'
    					),
    					 
    					1 => array (
    							"nombre" => '<b>Grupos</b>',
    							"activo" => true
    					)
    			)
    	) );
    		
    	
    	/*$this->template->agregar_titulo_header('Configuracion');
    	$this->template->agregar_breadcrumb(array(
    		"titulo" => '<i class="fa fa-cogs"></i> Administraci&oacute;n'
    	));*/
        $this->template->visualizar();
    }
    
    
    public function proveedores()
    {
    
    	// Build the data array
    	$this->template->agregar_titulo_header ( 'Ver Usuario' );
    	$this->template->agregar_breadcrumb ( array (
    			"titulo" => '<i class="fa fa-gear"></i>  Administraci&oacute;n',
    			"ruta" => array (
    					0 => array (
    							"nombre" => "Administraci&oacute;n",
    							"activo" => false,
    							"url" 	=> 'configuracion'
    					),
    
    					1 => array (
    							"nombre" => '<b>Inventario</b>',
    							"activo" => true
    					)
    			)
    	) );
    
    
    	/*$this->template->agregar_titulo_header('Configuracion');
    	 $this->template->agregar_breadcrumb(array(
    	 		"titulo" => '<i class="fa fa-cogs"></i> Administraci&oacute;n'
    	 ));*/
    	$this->template->visualizar();
    }
    public function inventario()
    {
    	 
    	// Build the data array
    	$this->template->agregar_titulo_header ( 'Ver Usuario' );
    	$this->template->agregar_breadcrumb ( array (
    			"titulo" => '<i class="fa fa-gear"></i>  Administraci&oacute;n',
    			"ruta" => array (
    					0 => array (
    							"nombre" => "Administraci&oacute;n",
    							"activo" => false,
    							"url" 	=> 'configuracion'
    					),
    
    					1 => array (
    							"nombre" => '<b>Inventario</b>',
    							"activo" => true
    					)
    			)
    	) );
    
    	 
    	/*$this->template->agregar_titulo_header('Configuracion');
    	 $this->template->agregar_breadcrumb(array(
    	 		"titulo" => '<i class="fa fa-cogs"></i> Administraci&oacute;n'
    	 ));*/
    	$this->template->visualizar();
    }
    public function grupo()
    {
    	/*$this->template->agregar_titulo_header('Configuracion  ');
    	$this->template->agregar_breadcrumb(array(
    			"titulo" => '<i class="fa fa-cogs"></i> Administraci&oacute;n: Sistema'
    	));*/
    	
    	
    	
    	$this->template->agregar_breadcrumb ( array (
    			"titulo" => '<i class="fa fa-gear"></i>  Administraci&oacute;n',
    			"ruta" => array (
    					0 => array (
    							"nombre" => "Administraci&oacute;n",
    							"activo" => false,
    							"url" 	=> 'configuracion'
    					),
    	
    					1 => array (
    							"nombre" => '<b>Configuración de Sistema</b>',
    							"activo" => true
    					)
    			)
    	) );
    	
    	
    	
    	$this->template->visualizar();
    }
    public function ventas()
    {    
    	$this->template->agregar_titulo_header('Ventas');
    	$this->template->agregar_breadcrumb(array(
    			"titulo" => '<i class="fa fa-folder-open"></i>  Ventas',
    			"ruta" => array (
    					0 => array (
    							"nombre" => "Administraci&oacute;n",
    							"activo" => false,
    							"url" => 'configuracion'
					
    					),
    					1 => array (
    							"nombre" => '<b>Ventas</b>',
    							"activo" => true
    					)
    			)
    	));
    	$this->template->visualizar();
    }
}
?>
