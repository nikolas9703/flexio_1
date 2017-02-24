<?php
use Flexio\Library\Util\AuthUser;
trait Refactory{

    function creando()
    {
        $data = [];
    	$mensaje = [];
    	$breadcrumb = [];
    	$titulo 		= '<i class="fa fa-line-chart"></i> Factura: Crear';
    	$titulo_header 	= 'Crear Factura';
        $acceso = 1;
    	if (!$this->auth->has_permission('acceso')) {
    		$acceso = 0;
    		$mensaje = array('estado' => 500, 'mensaje' => '<b>Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
    		$this->session->set_flashdata('mensaje', $mensaje);
    	}
    	if (!$this->empresaObj->tieneCuentaCobro()) {
    		$mensaje = array('estado' => 500, 'mensaje' => 'No hay cuenta de cobro asociada', 'clase' => 'alert-danger');
    		$this->session->set_flashdata('mensaje', $mensaje);
    		redirect(base_url('facturas/listar'));
    	}

        $editar_precio = 1;

        //if(!$this->auth->has_permission('crear__editarPrecio') && $usuario->roles->sum('superuser') == 0){
        //has_permissions check for super user
        if(!$this->auth->has_permission('crear__editarPrecio', 'facturas/crear')){
            $editar_precio= 0;
        }

    	$this->_Css();
    	$this->assets->agregar_css(array(
    		'public/assets/css/plugins/jquery/switchery.min.css',
    		'public/assets/css/modules/stylesheets/animacion.css'
    	));
    	$this->_js();
    	$this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/default/vue/directives/new-select2.js',//usado en los empezables
            'public/resources/compile/modulos/facturas/formulario1.js'
    	));

        $usuario_id = $this->id_usuario;

        $this->assets->agregar_var_js(array(
            "vista" => "crear",
            "acceso" => $acceso,
            "usuario_id" => (string)AuthUser::getId()
        ));
        $data['mensaje'] = $mensaje;
        $breadcrumb = $this->navegacionFacturasCrear();
    	$breadcrumb["titulo"] = $titulo;
    	$this->template->agregar_titulo_header($titulo_header);
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    function navegacionFacturasCrear(){
        return  array(
            "ruta" => array(
                  0 => [
                      "nombre" => "Ventas",
                      "activo" => false,
                  ],
                1 => [
                    "nombre" => "Facturas",
                    "activo" => false,
                    "url" => 'facturas/listar'
                ],
                2=> [
                    "nombre" => '<b>Crear</b>',
                    "activo" => true
                ]
            ),
        );
    }

    function ajax_formulario_catalogos()
    {
        if(!$this->input->is_ajax_request()) {
          return false;
        }
        $catalogoFormulario = new Flexio\Modulo\FacturasVentas\Catalogo\CatalogoFormularioFacturaVenta($this->empresa_id);
        $catalogos = ["clientes","cuentas","termino_pago","vendedor","lista_precio", "lista_precio_alquiler", "centros_contables", "estados", "categorias", "impuestos"];

        $catalogoForm = $catalogoFormulario->catalogos($catalogos);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
          ->set_output(collect($catalogoForm))->_display();
        exit;
    }

    function ajax_default_values()
    {
        if(!$this->input->is_ajax_request()) {
          return false;
        }
        $precios_venta_id_default = (new Flexio\Modulo\Inventarios\Repository\PreciosRepository)->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "venta", "principal" => 1));

        $response = [
          'precios_venta_id' => !empty(collect($precios_venta_id_default)->toArray()) ? $precios_venta_id_default[0]["id"] : "",
        ];

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
          ->set_output(collect($response))->_display();
        exit;
    }

    function editar($uuid = null){
        $data = [];
    	$mensaje = [];
    	$breadcrumb = [];
    	$titulo 		= '<i class="fa fa-line-chart"></i> Factura: Editar';
    	$titulo_header 	= 'Editar Factura';
        $acceso = 1;
    	if (!$this->auth->has_permission('facturas/editar/(:any)')) {
    		$acceso = 0;
    		$mensaje = array('estado' => 500, 'mensaje' => '<b>Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
    		$this->session->set_flashdata('mensaje', $mensaje);
    	}
    	if (!$this->empresaObj->tieneCuentaCobro()) {
    		$mensaje = array('estado' => 500, 'mensaje' => 'No hay cuenta de cobro asociada', 'clase' => 'alert-danger');
    		$this->session->set_flashdata('mensaje', $mensaje);
    		redirect(base_url('facturas/listar'));
    	}

        $factura = $this->facturaVentaRepository->findByUuid($uuid);

        if (is_null($uuid) || is_null($factura)) {
            $mensaje = array('estado' => 500, 'mensaje' => '<strong>Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('facturas/listar'));
        }

        $editar_precio = 1;

        //if(!$this->auth->has_permission('crear__editarPrecio') && $usuario->roles->sum('superuser') == 0){
        //has_permissions check for super user
        if(!$this->auth->has_permission('crear__editarPrecio', 'facturas/crear')){
            $editar_precio= 0;
        }

    	$this->_Css();
    	$this->assets->agregar_css(array(
    		'public/assets/css/modules/stylesheets/animacion.css'
    	));
    	$this->_js();
    	$this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/default/vue/directives/new-select2.js',
            'public/resources/compile/modulos/facturas_seguros/formulario1.js'
    	));

        $usuario_id = $this->id_usuario;

        $this->assets->agregar_var_js(array(
            "vista" => "editar",
            "acceso" => $acceso,
            "hex_factura" => $factura->uuid_factura
        ));
        $data['mensaje'] = $mensaje;
        $breadcrumb = $this->navegacionFacturasCrear();
    	$breadcrumb["titulo"] = $titulo;
    	$this->template->agregar_titulo_header($titulo_header);
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

}
