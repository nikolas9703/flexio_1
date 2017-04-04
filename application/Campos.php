<?php
class Campos{
	
	protected $campos = array();
	protected $ci;

	function __construct($classname=NULL)
	{
		$this->ci =& get_instance();
		
		//Armar Ruta del Namespace de Modulo Campos a Cargar;
		//$class = 'Flexio\\Modulo\\Documentos\\lib\campos\\'. $classname;
		//$this->class_campos = new $class();
		
		if(!empty($classname)){
			//Cargar Modulo
			$this->ci->load->module($classname);
			
			//Cargar Campos
			$this->agregar($this->ci->$classname->documentos_campos());
		}
	}
	
	public function agregar($campos=array())
	{
		if(!is_array($campos) || empty($campos)){
			return false;
		}

		foreach ($campos AS $campo)
		{
			if(!isset($campo['name'])) {
				$campo['name'] = str_replace(" ", "_", $name);
			}
			
			$label = "";
			if(!empty($campo['label'])) {
				$label = $campo['label'];
				unset($campo['label']);
			}

			switch ($campo['type']) {
				case 'select':
					$field = self::select($campo);
					break;
			
				default:
					$field = self::input($campo);
					break;
			}
			//Cargar campos al arreglo
			$this->campos[$label] = $field;
		}
    }
    
    /**
     * Generate a generic <input> tag.
     *
     * @param array $options An array of options to be applied as attributes to
     * the input.
     *
     * @return string HTML for the input field
     */
    public static function input($attributes)
    {
    	return '<input ' . self::attributesToString($attributes) . ' />';
    }
    
    /**
     * Generate a generic <select> tag.
     *
     * @param array $options An array of options to be applied as attributes to
     * the select.
     *
     * @return string HTML for the select field
     */
    public static function select($attributes)
    {
    	$options = $attributes["options"];
    	unset($attributes["options"]);
    	
    	$field  = '<select ' . self::attributesToString($attributes) . '>';
    	$field  .= '<option value="">Seleccione</option>';
    	foreach($options AS $option){
    		$field  .= '<option value="'. $option["id"] .'">'. $option["nombre"] .'</option>';
    	}
    	$field  .= '</select>';
    	return $field;
    }
    
    /**
     * Prepare the value for display in the form.
     *
     * @param string $value The value to prepare.
     *
     * @return string
     */
    public static function prepValue($value)
    {
    	$value = htmlspecialchars($value);
    
    	return str_replace(array("'", '"'), array("&#39;", "&quot;"), $value);
    }
    
    /**
     * Turn an array of attributes into a string for an input.
     *
     * @param array $attr Attributes for a field
     *
     * @return string
     */
    private static function attributesToString($attr)
    {
    	if (! is_array($attr)) {
    		$attr = (array) $attr;
    	}
    
    	$attributes = array();
    	foreach ($attr as $property => $value) {
    		if ($property == 'label') {
    			continue;
    		}
    
    		if ($property == 'value') {
    			$value = self::prepValue($value);
    		}
    
    		$attributes[] = "{$property}='{$value}'";
    	}
    
    	return implode(' ', $attributes);
    }
    
    public function get(){
    	return $this->campos;
    }
}