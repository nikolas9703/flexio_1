<?php
namespace Flexio\Modulo\Base\Services;

class HtmlTypeFactory
{
    
    public function getHtmlType($htmlType)
    {
        $className = "Flexio\\Modulo\\Base\\Services\\" . ucfirst($htmlType);

        if( ! class_exists($className)) {
            throw new \RuntimeException('la clase html no existe');
        }

        return new $className;
    }
}
