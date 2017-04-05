<?php defined('BASEPATH') || exit('No direct script access allowed');
/**
 * Modal Class
 *
 * funciones para el render de modales en las vistas.
 *
 *
 * @package    PensaApp
 * @subpackage Library
 * @category   Libraries
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @since      Version 1.5
 */
class Modal
{
	private static $id = "";
	private static $titulo = "";
	/**
	 * Size default
	 */
	private static $size = "lg";
	/**
	 * Arreglo de size para modal
	 */
	private static $sizeArray = array(
		"xs" => "modal-xs",
		"sm" => "modal-sm",
		"md" => "modal-md",
		"lg" => "modal-lg",
	);
	private static $contenido = "";
	private static $footer = "";
	private static $attr = "";
	private static $class = 'class="modal fade"';

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct(){
		self::$id = "";
		self::$attr = "";
		self::$titulo = "";
		self::$size = "";
		self::$contenido = "";
		self::$footer = "";
	}
	private static function id($valor=NULL){
		self::$id = $valor;
	}
	private static function titulo($valor=NULL){
		self::$titulo = $valor;
	}
	private static function size($size=NULL){
		self::$size = self::$sizeArray[$size];
	}
	private static function contenido($contenido=NULL){
		self::$contenido = $contenido;
	}
	private static function footer($footer=NULL){
		self::$footer = $footer;
	}
	private static function attr($attrs=NULL){
		self::$attr = "";

		//reemplazar class
		self::$class = !empty($attrs["class"]) ? 'class="modal fade '. $attrs["class"] .'"' : 'class="modal fade"';
		unset($attrs["class"]);

		//recorrer atributos
		foreach($attrs AS $index => $value){
			self::$attr .= $index.'="'. $value .'" ';
		}
	}
	public static function config($data=array()){
		$instance = new Modal();
		if(!empty($data["id"])){
			self::id($data["id"]);
		}
		if(!empty($data["titulo"])){
			self::titulo($data["titulo"]);
		}
		if(!empty($data["size"])){
			self::size($data["size"]);
		}
		if(!empty($data["contenido"])){
			self::contenido($data["contenido"]);
		}
		if(!empty($data["footer"])){
			self::footer($data["footer"]);
		}
		if(!empty($data["attr"])){
			self::attr($data["attr"]);
		}
   		return $instance;
	}
	public function html()
	{
		$modal = '';

		$modal.='<div '. self::$class .' id="'. self::$id .'" '. self::$attr .' tabindex="-1" role="dialog" aria-labelledby="'. self::$id .'" aria-hidden="true">
		 <div class="modal-dialog '. self::$size .'">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h4 class="modal-title">'. self::$titulo .'</h4>
					</div>
					<div class="modal-body">'. self::$contenido .'</div>
					<div class="modal-footer">'. self::$footer .'</div>
				</div>
			</div>
		</div>';
		return $modal;
	}
}
