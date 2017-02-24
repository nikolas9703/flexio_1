<?php
namespace Flexio\Modulo\InteresesAsegurados\Repository;

interface AssetsInteresesAseguradosInterface
{
    public function agregar_css_principal();
    public function agregar_js_principal();
    
    //listar
    public function agregar_css_listar();
    public function agregar_js_listar();    
    
    //ocultotabla
    public function agregar_js_ocultotabla();
}
