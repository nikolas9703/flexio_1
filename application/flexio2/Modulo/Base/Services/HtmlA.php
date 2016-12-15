<?php
namespace Flexio\Modulo\Base\Services;

class HtmlA
{
    public function getSalida(Html $html)
    {
        return '<a '.$html->getAttrs().' >'.$html->getHtml().'</a>';
    }
}
