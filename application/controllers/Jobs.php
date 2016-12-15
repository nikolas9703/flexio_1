<?php //if (isset($_SERVER['REMOTE_ADDR'])) die('Permission denied.');
/**
 * Jobs
 *
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  17/08/2016
 */
use Flexio\Jobs\ContratosAlquiler\CronCargosAlquiler;
use Flexio\Jobs\ContratosAlquiler\CronCortesFacturacionAlquiler;

class Jobs extends MX_Controller
{
	protected $CronCargosAlquiler;
	protected $CronCortesFacturacionAlquiler;

	function __construct() {
		parent::__construct();

		$this->load->model('contabilidad/Impuestos_orm');

		$this->CronCargosAlquiler = new CronCargosAlquiler();
		$this->CronCortesFacturacionAlquiler = new CronCortesFacturacionAlquiler();
	}

	/**
	 * Esta funcion ejecuta los cargos de alquiler
	 * que esten por realizarse (hora/diario/semanal/mensual).
	 *
	 * @return void
	 */
	public function cargos_alquiler() {
		if($this->input->is_cli_request()){
			$this->CronCargosAlquiler->ejecutar();
		}else{
			$this->CronCargosAlquiler->ejecutar();
		}
   }

	 /**
 	 * Esta funcion crea ordenes de venta
 	 * de los cargos ejcutados que esten por facturar.
 	 *
 	 * @return void
 	 */
	public function facturar_cargos() {
		if($this->input->is_cli_request()){
			$this->CronCortesFacturacionAlquiler->ejecutar();
		}else{
			$this->CronCortesFacturacionAlquiler->ejecutar();
		}
	}

	//mail('jpinilla@pensanomica.com', 'CRON CLI', 'Se esta ejecutando por CLI!', "From:no-reply@flexio.com");
}
