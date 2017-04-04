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
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Core\Models\Descarga;
use Flexio\Jobs\Pedidos\CronDescargas;
use League\Csv\Writer as Writer;

class Jobs extends MX_Controller
{
	protected $CronCargosAlquiler;
	protected $CronCortesFacturacionAlquiler;
	protected $CronDescargas;
	protected $descargaModel;

	function __construct() {
		parent::__construct();

		$this->load->model('contabilidad/Impuestos_orm');
		//$this->CronDescargas = new CronDescargas();
		$this->descargaModel = new Descarga();
		$this->CronCargosAlquiler = new CronCargosAlquiler();
		$this->CronCortesFacturacionAlquiler = new CronCortesFacturacionAlquiler();
		$this->load->library('email');
		$config = Array(
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'wordwrap' => TRUE
			);
		
		$this->email->initialize($config);
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

	 function sendEmail ($attach,$toGetEmail){

	 	$filepath = realpath('./public/templates/email/ordenes/correo_proveedor.html');
	 	$htmlmail = read_file($filepath);
	 	$html = "<h1>Hola Test</h1>";
	 	$htmlmail = str_replace("__SITE_URL__", base_url('/'), $htmlmail);
	 	$htmlmail = str_replace("__BODY__", $html, $htmlmail);
	 	$htmlmail = str_replace("__YEAR__", date('Y'), $htmlmail);
	 	$empresa = Empresa::find($toGetEmail['empresa']);
	 	$usuario = Usuarios::find($toGetEmail['usuario']);
	 	$this->email->from("ldiaz@quasarbi.com", $empresa->no_reply_name  );
	 	$this->email->to('lcdiaz@misena.edu.co');
	 	$this->email->subject("Archivo solicitado");
	 	$this->email->message($htmlmail);
	 	print $htmlmail;
	 	$this->email->attach($attach);
	 	return $this->email->send();


	 }

	 public function createFileToAttach($query){

	 	$folder_save = $this->config->item('files_pdf');	
	 	$csv = array();
	 	$documentName ="test-" . date('i') . ".csv";
	 	if (empty($query)) {
	 		return false;
	 	}
	 	$output = fopen($folder_save.$documentName, 'w');

	 	foreach ($query as $i=> $level1) {
        		# code...
	 		foreach ($level1 as  $level2) {
	 			# code...
	 			foreach ($level2 as $key => $column) {
	 				# code...
	 				$csvdata[$i][$key] =$column;
	 				
	 			}

	 		}
	 		fputcsv($output, $csvdata[$i]);
	 	}

	 	return $folder_save.$documentName; 

	 } 
	 function ejecutar (){

	 	$queue=$this->descargaModel->where('estado',"pendiente")->get();
	 	if(count($queue)){

	 		foreach ($queue as $key => $data) {
				# code...
	 			$exportObject = new $data->descargaType;
	 			$clause['id_empresa']=$data->empresa_id;
	 			$query = $exportObject->getAllRows($clause);
	 			$fileRoute= $this->createFileToAttach($query);
	 			$toGetEmail = array(
	 				'empresa' => $data->empresa_id,
	 				'usuario' => $data->usuario	
	 				);
	 			$isSuccess=$this->sendEmail($fileRoute,$toGetEmail);
	 			$estado ="error";
	 			if($isSuccess){
	 				$estado = "enviado";
	 			}
	 			print $this->email->print_debugger();
	 			print "<h1><label class='label badge label-info'>".$estado.'</label></h1>';
	 			//$this->descargaModel->where("id",$data->id)->update(["estado" =>$estado]);
	 		}	
	 	}
	 }

	//mail('jpinilla@pensanomica.com', 'CRON CLI', 'Se esta ejecutando por CLI!', "From:no-reply@flexio.com");
	}
