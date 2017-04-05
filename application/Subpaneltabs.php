<?php
use Flexio\Library\HTML\HtmlSubPanel;

class SubpanelTabs
{
    protected $ci;
    public static $subpaneles = array();
    private static $ruta_modulos;
    private static $modulo;

    public function __construct()
    {
        //Instancia del core de CI
        $this->ci =& get_instance();
        //Nombre del Modulo (HMVC)
        self::$modulo = $this->ci->uri->segment(1);

        //Ruta donde estan los modulos
        self::$ruta_modulos = $this->ci->config->item('modules_locations');
    }

    public static function visualizar($data=null)
    {
        $self = new SubpanelTabs;
        $self->cargar_modulos();
        if (!$self->tipo_de_dato($data) || empty(self::$subpaneles)) {
            return false;
        }

        $html =  HtmlSubPanel::generarHtml(self::$subpaneles, $data, new modules);
        echo $html;
    }

    function tipo_de_dato($data){
        if(is_array($data) && !empty($data)){
            return true;
        }elseif(!is_null($data)){
            return true;
        }
        return false;
    }

    public function cargar_modulos()
    {
        $subpanel_file = realpath(self::$ruta_modulos . self::$modulo . "/config/subpanels.php");
        if(!file_exists($subpanel_file)) throw new \Exception('El Archivo subpanels.php no existe');
        include($subpanel_file);

        if(empty($config['subpanels'])) throw new \Exception('El Array de subpanels esta vacio');
        $auxpanelArr = [];
        self::$subpaneles= $config['subpanels'];
    }

}
