<?php
class Home extends CRM_Controller
{
    function __construct()
    {
		parent::__construct();
    }

    public function index()
    {
        $this->template->agregar_titulo_header('Dashboard');
        $this->template->agregar_breadcrumb(array(
        	"path" => array(
        		0 =>  array(
        			"name" => '<b>Inicio</b>',
        			"active" => true
        		)
        	)
        ));
        
       
       /* $this->load->model('tests');
        print_r(Tests::all());*/
        $this->template->visualizar();
    }
}
?>
