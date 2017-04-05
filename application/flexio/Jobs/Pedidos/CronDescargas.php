<?php
namespace Flexio\Jobs\Pedidos;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Core\Models\Descarga;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Empresa\Models\Empresa;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use League\Csv\Writer as Writer;


class CronDescargas {
	private $descargaModel;

	function __construct(){
		$this->descargaModel = new Descarga();
		$queue=$this->descargaModel->where('estado',"pendiente")->get();
	}

	function ejecutar (){

		
		if(count($queue)){

			foreach ($queue as $key => $data) {
				# code...
				$exportObject = new $data->descargaType;
				$clause['id_empresa']=$data->empresa_id;
				$query = $exportObject->getAllRows($clause);
				print_r($query);
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
				$exportObject->where('id',$data->id)->update('estado' ,$estado);
			}	
		}
	}

	function sendEmail ($attach,$toGetEmail){
		
		$filepath = realpath('./public/templates/email/ordenes/correo_proveedor.html');
		$htmlmail = read_file($filepath);
		$htmlmail = str_replace("__SITE_URL__", base_url('/'), $htmlmail);
		$htmlmail = str_replace("__BODY__", $html, $htmlmail);
		$htmlmail = str_replace("__YEAR__", date('Y'), $htmlmail);
		$empresa = Empresa::find($toGetEmail['empresa']);
		$usuario = Usuarios::find($toGetEmail['usuario']);
		$this->email->from($empresa->no_reply_email, $empresa->no_reply_name  );
		$this->email->to($usuario->email);
		$this->email->subject("Archivo solicitado");
		$this->email->message($htmlmail);
		$this->email->attach($attach);
		return $this->email->send();

	}

	public function createFileToAttach($query){
		
		$folder_save = $this->config->item('files_pdf');	
		$csv = array();
		if (empty($query)) {
			return false;
		}

		
		foreach ($query as $key => $column) {
        		# code...
			$csvdata[$key] = Util::verificar_valor($column);
		}


		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertAll($csvdata);
		$documento = $csv->output("aseguradoras-" . date('y-m-d') . ".csv");
		file_put_contents($folder_save.$documento, $output);
		return $folder_save.$documento; 

	} 
}
