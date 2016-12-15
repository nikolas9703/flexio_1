<?php

namespace Flexio\Library\Util;
use Pusher;

class FlexioPusher{
  public $pusher;
  public $options = ['encrypted' => false];

  function getPusher(){
    return $this->pusher = new Pusher('323c4c72368dae9707b9','dd49e26060569c24bd37','228324',$this->options);
  }
}
