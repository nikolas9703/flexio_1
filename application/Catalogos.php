<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Catalogos Class
 *
 * Clase para administracion de catalogos de modulos.
 *
 *
 * @package    PensaApp
 * @subpackage Library
 * @category   Libraries
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @since     Version 1.0
 */
class Catalogos
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

    /**
     * Retorna formulario de catalogo
     * para el modulo indicado.
     *
     * @param string $id_grid
     * @param string $modulo
     * @param string $campo
     *
     * @return string
     */
    public static function formulario($id_grid=NULL, $modulo=NULL, $campo=NULL)
    {
		if($id_grid==NULL || $modulo==NULL || $campo==NULL){
			return false;
		}

		$nombre_formulario = $id_grid ."Form";
		$id_campo_valor = $id_grid ."Valor";

    	$formAttr = array(
    		'method'       => 'POST',
    		'id'           => $nombre_formulario,
    		'autocomplete' => 'off',
    		'class'		   => 'catalogoForm'
    	);
    	$html = form_open_multipart("", $formAttr);
        
    	$html .= '<div class="row">
    		<div class="col-md-6">
    			<label>Buscar</label>
    			<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-search"></i></span>
				  	<input name="buscarValorCatalogo" class="form-control buscarValorCatalogoCampo" type="text" />
    				<div class="input-group-btn">
			            <button class="btn btn-default limpiarValorCatalogoBtn" type="button">Limpiar</button>
			            <button class="btn btn-default buscarValorCatalogoBtn" type="button">Buscar</button>
		          	</div>
				</div>

    			<div class="m-t-md"></div>';

    	$html .= Jqgrid::cargar_jqgrid($id_grid);

    	$html .= '</div>
    		<div class="col-md-6">
    			<div>
    				<label>Nombre Valor</label>
    				<input id="'. $id_campo_valor .'" name="valor" class="form-control" type="text" data-rule-required="true" />
    				<input name="id_cat" class="form-control" type="hidden">
    			</div>
    			<div class="m-t-md">
    				<button type="button" data-modulo="'. $modulo .'" data-campo="'. $campo .'" class="btn btn-sm btn-primary pull-right m-t-n-xs guardarCatalogoBtn"><strong>Guardar</strong></button>
    				<button type="button" class="btn btn-sm btn-primary pull-right m-t-n-xs cancelarCatalogoBtn"><strong>Limpiar</strong></button>
    			</div>
    		</div>
    	</div>';
    	$html .= form_close();

    	echo $html;
    }
}
