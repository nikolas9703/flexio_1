<?php
namespace Flexio\Modulo\Base\Services;

class HtmlSpan
{
    public function getSalida(Html $html)
    {
        return '<span '.$html->getAttrs().' >'.$html->getHtml().'</span>';
    }
}
