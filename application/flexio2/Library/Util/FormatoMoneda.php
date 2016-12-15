<?php
namespace Flexio\Library\Util;

class FormatoMoneda{
  static function numero($numero=0){
    return number_format($numero, 2, '.', ',');
  }
}
