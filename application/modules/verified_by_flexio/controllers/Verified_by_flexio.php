<?php
/**
 * Presupuesto.
 *
 * Modulo para administrar la creacion, edicion de ajustes
 *
 * @category   Controllers
 *
 * @author     Pensanomica Team
 *
 * @link       http://www.pensanomca.com
 *
 * @copyright  10/16/2015
 */

use Endroid\QrCode\QrCode;
use Flexio\Modulo\VerifiedByFlexio\Repository\VerifiedByFlexioRepository;

class Verified_by_flexio extends CRM_Controller
{
    private static $algo = '$2a';
    private static $cost = '$10';
    protected $qrCode;

    //qr params
    protected $qr_image_dir;
    protected $qr_controller_dir;

    //repositories
    protected $VerifiedByFlexioRepository;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper(array('url', 'string'));
        $this->load->library(array('encrypt'));

        //qr params
        $this->qr_image_dir = FCPATH.'public/uploads/tmp_qr_codes/';
        $this->qr_controller_dir = site_url().'verified_by_flexio';

        //repositories
        $this->VerifiedByFlexioRepository = new VerifiedByFlexioRepository;
    }

    public function listar()
    {
        $this->load->view('listar', []);
    }

    //this method is used for validate a link and show form of
    //document validations
    public function index()
    {

        $token = $this->input->get('token', true);
        $message = [];

        if (!empty($token))
        {
            $clause = ['campo' => ['token' => $token]];
            $success = $this->VerifiedByFlexioRepository->validate_link($clause, $this->input->server('REMOTE_ADDR'));

            if ($success)
            {
                $message = ["tipo" => 'success', "mensaje" => 'Se valid&oacute; el enlace del documento'];
            }
            else
            {
                $message = ["tipo" => 'error', "mensaje" => 'No se valid&oacute; el enlace del documento'];
            }
        }

        //session not defined
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => Collect($message),
            "uuid_empresa" => ''
        ));

        $this->load->view('listar', []);
    }

    public function valida_documento_old()
    {
        $message = [];
        $numero_documento = $this->input->post('numero_documento');
        $fecha_documento = $this->input->post('fecha_documento');
        //$cantidad_articulos = $this->input->post('cantidad_articulos');
        $monto_documento = $this->input->post('monto_documento');
        $cantidad_articulos = 0;

        $result = $this->generar_hash($numero_documento, $fecha_documento, $cantidad_articulos, $monto_documento);

        $clause = ['campo' => ['document_hash' => $result]];
        $success = $this->VerifiedByFlexioRepository->validate_document($clause, $this->input->server('REMOTE_ADDR'));

        if ($success)
        {
            $message = ["tipo" => 'success', "mensaje" => 'Se valid&oacute; el documento'];
        }
        else
        {
            $message = ["tipo" => 'error', "mensaje" => 'No se valid&oacute; el documento'];
        }

        //session not defined
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => Collect($message),
            "uuid_empresa" => ''
        ));

        $this->load->view('listar', []);

    }
    public function valida_documento() {

          $numero_documento = $this->input->post('numero_documento');
          $fecha_documento = $this->input->post('fecha_documento');
          $monto_documento = $this->input->post('monto_documento');

          $fecha = explode("/", $fecha_documento);

          $fecha_documento = $fecha[2]."-".$fecha[1]."-".$fecha[0];

          $not_allowed	=	array (",");
          $allowed		=	array ("");
        	$monto_documento		=	str_replace ($not_allowed,$allowed, $monto_documento);

          $ci =& get_instance();

          $fields = array(
              "id"
          );
          $clause = array(
            "numero" => $numero_documento,
            "DATE_FORMAT(fecha_creacion,'%Y-%m-%d')" => $fecha_documento,
            "monto" => $monto_documento,
            "id_empresa" => "1"
          );

          $result = $ci->db->select($fields)
                    ->from('ord_ordenes')
                    ->where($clause)
                    ->get()
                    ->result_array();

          $cantidad = 0;
          if(!empty($result)) {
            $cantidad = count($result[0]);
          }

            $message = [];


            if ($cantidad==1)
            {
                $message = ["tipo" => 'success', "mensaje" => 'Se valid&oacute; el documento'];
            }
            else
            {
                $message = ["tipo" => 'error', "mensaje" => 'No se valid&oacute; el documento'];
            }

            $this->assets->agregar_var_js(array(
                "flexio_mensaje" => Collect($message),
                "uuid_empresa" => ''
            ));

            $this->load->view('listar', []);

        }
    private function _get_token()
    {
        $string = date('dhis').random_string('alnum', 10);
        $this->encrypt->set_cipher(MCRYPT_RIJNDAEL_256);

        $token = $this->encrypt->encode($string);

        return preg_replace('/[^\p{L}\p{N}\s]/u', '', $token);
    }

    private function _create_qr_image($token, $image_name)
    {
        //create qr image
        $qrCode = new QrCode();
        $qrCode
        ->setText($this->qr_controller_dir.'?token='.$token)
        ->setSize(300)
        ->setPadding(10)
        ->setErrorCorrection('high')
        ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
        ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
        ->setLabel('Verified by Flexio')
        ->setLabelFontSize(16)
        ->setImageType(QrCode::IMAGE_TYPE_PNG);

        $qrCode->render($this->qr_image_dir.$image_name);
    }

    public function generar_qr($numero_documento = 0, $fecha_documento = 0, $cantidad_articulos = 0, $monto_documento = 0)
    {
        //Defino el nombre de la imagen
        $token = $this->_get_token();
        $image_name = $token.'.png';
        $cantidad_articulos = 0;

        //obtengo el hash del documento
        $valor_hash = $this->generar_hash($numero_documento, $fecha_documento, $cantidad_articulos, $monto_documento);

        //create database register
        $this->VerifiedByFlexioRepository->guardar(['link_hash' => $token, 'document_hash' => $valor_hash]);

        //create qr image
        $this->_create_qr_image($token, $image_name);

        return $image_name;
    }

      // mainly for internal use
    public static function unique_salt($texto)
    {
        $salt = substr(sha1($texto), 0, 6).substr(sha1($texto), 10, 6).substr(sha1($texto), 20, 6).substr(sha1($texto), 12, 4);

        return $salt;
    }

    // this will be used to generate a hash
    public static function generar_hash($numero_documento, $fecha_documento, $cantidad_articulos, $monto_documento)
    {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $fecha_documento = str_replace('-', '/', $fecha_documento);// must be d/m/Y
        $monto_documento = number_format(str_replace('$', '', str_replace(',', '', $monto_documento)),2);

        $valor = trim($numero_documento).trim($fecha_documento).trim($cantidad_articulos).trim($monto_documento);
        $cadena = crypt($valor, self::$algo.self::$cost.'$'.self::unique_salt($valor));

        return $cadena;
    }


}
