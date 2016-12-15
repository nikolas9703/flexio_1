<?php
namespace Flexio\Modulo\Base\Services;

class Html
{
    protected $type;
    protected $attrs;
    protected $html;

    private $simpleFactory;

    public function __construct(HtmlTypeFactory $simpleFactory)
    {
        $this->simpleFactory = $simpleFactory;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setAttrs($attrs)
    {
        $this->attrs = $attrs;
        return $this;
    }

    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    public function getAttrs()
    {
        $attrs = "";

        foreach($this->attrs as $key => $values)
        {
            $attrs .= " $key='$values' ";
        }

        return $attrs;
    }

    public function getSeriales()
    {
        return $this->attrs;
    }

    public function getHtml()
    {
        return $this->html;
    }


    public function getSalida()
    {
        $htmlType = $this->simpleFactory->getHtmlType($this->type);

        return $htmlType->getSalida($this);
    }
}
