<?php
namespace Flexio\Modulo\Base\Services;

class HtmlI
{
    public function getSalida(Html $html)
    {
        return '<i '.$html->getAttrs().' >'.$html->getHtml().'</i>';
    }
}
