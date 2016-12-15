<?php
namespace Flexio\Modulo\Base\Services;

class HtmlLabel
{
    public function getSalida(Html $html)
    {
        return '<label '.$html->getAttrs().' >'.$html->getHtml().'</label>';
    }
}
