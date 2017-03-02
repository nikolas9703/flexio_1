<?php 

/**
 * Intereses Asegurados
 *
 * Modulo para administrar la creacion, edicion de Intereses Asegurados
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Flexio 
 * @link       http://www.quasarbi.com
 * @copyright  27/02/2017
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Flexio\Library\Util\GenerarCodigo as GenerarCodigo;
use Dompdf\Dompdf;
use Carbon\Carbon;

	class Hello_word extends CRM_Controller
	{
		
		function __construct(argument)
		{
			parent::__construct();
		}

		public function listar()
		{
			# code...
		}
	}