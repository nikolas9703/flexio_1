<?php

namespace Flexio\Modulo\ConfiguracionCompras\Library;

use Flexio\Library\Util\FlexioSession;

class PDFPrint
{
    protected $module = '';
    protected $categories = [];
    protected $FlexioSession;

    public function __construct()
    {
        $this->FlexioSession = new FlexioSession;
    }

    public function setModule($value)
    {
        $this->module = $value;
        return $this;
    }

    public function setCategories($value)
    {
        $this->categories = (is_array($value) && !empty($value)) ? $value : [-1];
        return $this;
    }

    public function html()
    {
        return "<div>{$this->getContent()}<div>";
    }

    private function getContent()
    {
        $clause = ['empresa' => $this->FlexioSession->empresaId(), 'estado' => 'activo', 'modulo' => $this->module];
        if($this->applyCategories()){
            $clause['categoria'] = $this->categories;
        }
        $terminos_condiciones = \Flexio\Modulo\ConfiguracionCompras\Models\TerminoCondicion::deFiltro($clause)->get();
        return $terminos_condiciones->implode('content', "<br><br><hr>");

    }

    private function applyCategories()
    {
        return in_array($this->module, ['pedidos', 'ordenes', 'facturas_compras']);
    }
}
