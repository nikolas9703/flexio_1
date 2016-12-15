<?php

namespace Flexio\Library\Util;


class FlexioAssets
{

  protected $codeigniter;
  protected $tipos = ['vars' => 'agregar_var_js', 'js' => 'agregar_js', 'css' => 'agregar_css'];

  public function __construct()
  {
    $this->codeigniter = & get_instance();
  }

  public function run()
  {
    $this->_css();
    $this->_js();
  }

  public function add($tipo = 'vars', $vars = [])
  {
    if( ! array_key_exists($tipo, $this->tipos))
    {
      throw new \RuntimeException('No existe el tipo de operacion, use vars, js o css');
    }

    $aux = $this->tipos[$tipo];
    $this->codeigniter->assets->$aux($vars);
  }

  private function _css()
  {
    $this->codeigniter->assets->agregar_css([
      'public/assets/css/default/ui/base/jquery-ui.css',
      'public/assets/css/default/ui/base/jquery-ui.theme.css',
      'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
      'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
      'public/assets/css/plugins/jquery/switchery.min.css',
      //select2
      'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
      'public/assets/css/plugins/bootstrap/select2.min.css'
    ]);

  }


  private function _js()
  {
      $this->codeigniter->assets->agregar_js([
          //datepicker y otros
          'public/assets/js/default/jquery-ui.min.js',
          //jqgrid y complementos
          'public/assets/js/plugins/jquery/jquery.sticky.js',
          'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
          'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
          'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
          //select2
          'public/assets/js/plugins/bootstrap/select2/select2.min.js',
          'public/assets/js/plugins/bootstrap/select2/es.js',
          //jquery validate spanish
          'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
          'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
          'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
          //moment
          'public/assets/js/plugins/jquery/combodate/momentjs.js',
          //ckeditor
          'public/assets/js/plugins/ckeditor/ckeditor.js',
          'public/assets/js/plugins/ckeditor/adapters/jquery.js',
          //nueva version de inputmask
          'public/assets/js/default/jquery.inputmask.bundle.min.js',
      ]);
  }

}
