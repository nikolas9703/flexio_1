<?php
namespace Flexio\Library\HTML;

class HtmlRender{

    protected $content;
    protected $backgroundColor;

    private function getContent()
    {
        return $this->content;
    }

    private function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    public function setContent($value)
    {
        $this->content = $value;
        return $this;
    }

    public function setBackgroundColor($value)
    {
        $this->backgroundColor = $value;
        return $this;
    }

    public function label()
    {
        return '
            <span
                class="label label-primary"
                style="display: block;padding: 4px;font-size: 12px;margin-left: 12%;margin-right: 12%;background-color:'.$this->getBackgroundColor().';"
            >'.$this->getContent().'</span>';
    }
}
