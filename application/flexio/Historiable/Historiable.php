<?php 
namespace Flexio\Historiable;

use Flexio\Library\Util\AuthUser;

/**
 * Class Historiable
 * @package Flexio\Historiable
 */
trait Historiable
{

	public function historial(){
        return $this->morphMany('Flexio\Modulo\Historial\Models\Historial','historiable');
    }

}