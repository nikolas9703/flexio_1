<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Sistema
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */

class Sistema extends CRM_Controller
{
	function __construct(){
    parent::__construct();
    $this->load->model('sistema_orm');
  }
}
