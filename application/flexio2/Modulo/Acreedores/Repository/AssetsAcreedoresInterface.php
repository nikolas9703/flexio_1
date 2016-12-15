<?php
namespace Flexio\Modulo\Acreedores\Repository;

interface AssetsAcreedoresInterface
{
    public function agregar_css_principal();
    public function agregar_js_principal();
    
    //listar
    public function agregar_css_listar();
    public function agregar_js_listar();
    public function agregar_var_js_listar($data);
    
    //ocultotabla
    public function agregar_js_ocultotabla();
    public function agregar_js_ocultotablaColaboradores();
    
    //ocultoformulario
    public function agregar_js_ocultoformulario();
}
