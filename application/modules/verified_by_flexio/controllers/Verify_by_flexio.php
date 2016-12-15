<?php
/**
 * Presupuesto
 *
 * Modulo para administrar la creacion, edicion de ajustes
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/16/2015
 */

use Endroid\QrCode\Exceptions\ImageFunctionFailedException;
use Endroid\QrCode\Exceptions\ImageFunctionUnknownException;
use Endroid\QrCode\QrCode;


class Verified_by_flexio extends CRM_Controller
{

        // blowfish
    private static $algo = '$2a';

    // cost parameter
    private static $cost = '$10';

 /**
     * @var QrCode
     */
    protected $qrCode;

    public function __construct()
    {
        parent::__construct();

            //$this->load->helper(array('form', 'url', 'file', 'string','database'));
            $this->load->helper(array('url','database'));
            $this->load->library(array('encrypt'));




    }


   public function listar(){

        $data  = array();
          $this->load->view('listar', $data);

           }

    public function index(){

        $data['mensaje_valida_codigo_existe_documento_error'] = "";
        $data['mensaje_valida_codigo_existe_documento'] = "";



      $token = $this->input->get('token', TRUE);

      if (!empty($token)){

          $result = $this->valida_codigo_existe_documento($token);


           if(empty($result)){
              // dd("prueba");
                $data['mensaje_valida_codigo_existe_documento_error'] = "No se valido el documento";
           } else {
                  $data['mensaje_valida_codigo_existe_documento'] = "Se valido el documento";
           }


      }

        //session not defined
        $session['isset_session'] = "EMPTY";







         $this->load->view('listar', $data);

     //   $this->load->view('include/header', $session);
        //$this->load->view('templates/mensaje', $data);
       // $this->load->view('valida_codigo');
       // $this->load->view('include/footer');
    }






    public  function generar_qr_prueba($numero_documento = 0, $fecha_documento = 0, $cantidad_articulos = 0, $monto_documento = 0){
        $prueba = "hola.png";
        return $fecha_documento;
    }

    public  function generar_qr($numero_documento = 0, $fecha_documento = 0, $cantidad_articulos = 0, $monto_documento = 0){

        $qr_code_config = array();
        $qr_code_config['imagedir'] 	= FCPATH."public/uploads/tmp_qr_codes/";
        $qr_code_config['controllerdir'] 	= site_url()."verified_by_flexio";
        //dd($qr_code_config);

          // nombre imagen
        $string = date('dhis'). random_string('alnum', 10);
        $this->encrypt->set_cipher(MCRYPT_RIJNDAEL_256);

        $token = $this->encrypt->encode($string);

        $token = preg_replace('/[^\p{L}\p{N}\s]/u', '', $token);
        $image_name = $token.".png";
       // $image_name = "prueba.png";
        // Generar hash

        $valor_hash = $this->generar_hash($numero_documento, $fecha_documento, $cantidad_articulos, $monto_documento);


        $fieldset = array(
            'link_hash'        => $token,
            'document_hash'        => $valor_hash,
            'created_at'   => date('Y-m-d H-i-s'),
            'updated_at'       => date('Y-m-d H-i-s')
        );

        $this->db->insert('sec_verified_by_flexio', $fieldset);


         //$url = base_url('login/recover/?email='.  $email. '&token='. $token);
        $qrCode = new QrCode();
        $qrCode
        ->setText($qr_code_config['controllerdir']."?token=".$token)
        ->setSize(300)
        ->setPadding(10)
        ->setErrorCorrection('high')
        ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
        ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
        ->setLabel('Verified by Flexio')
        ->setLabelFontSize(16)
        ->setImageType(QrCode::IMAGE_TYPE_PNG);


        $qrCode->render($qr_code_config['imagedir'].$image_name);

       // dd($image_name);

        return $image_name;

    }


      // mainly for internal use
    public static function unique_salt($texto) {

        $salt = substr(sha1($texto),0,6).substr(sha1($texto),10,6).substr(sha1($texto),20,6).substr(sha1($texto),12,4);

        //return substr(sha1(mt_rand()),0,22);

        return $salt;

    }

    // this will be used to generate a hash
    public static function generar_hash($numero_documento, $fecha_documento, $cantidad_articulos, $monto_documento) {
 ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $valor = trim($numero_documento).trim($fecha_documento).trim($cantidad_articulos).trim($monto_documento);


        $cadena = crypt($valor,
                    self::$algo .
                    self::$cost .
                    '$' . self::unique_salt($valor));

        return $cadena;
        /*
        return crypt($valor,
                    self::$algo .
                    self::$cost .
                    '$' . self::unique_salt($valor));
 */
    }


    // this will be used to compare a password against a hash
    public static function validador_hash($numero_documento, $fecha_documento, $cantidad_articulos, $monto_documento) {

       // $full_salt = substr($hash, 0, 29);
        $valor = trim($numero_documento).trim($fecha_documento).trim($cantidad_articulos).trim($monto_documento);
        $full_salt =  self::$algo.self::$cost.'$'.self::unique_salt($valor);

       // print_r("uno:".$valor);
       // print_r("dos:".$full_salt);
       //  dead();

        $new_hash = crypt($valor, $full_salt);

 //$hash select base de datos
        return ($new_hash);

    }


  public function valida_codigo_existe_documento($token)
    {
        $this->load->library('user_agent');



        $fields = array(
            "id",
            "link_validated"
        );
        $clause = array(
            "link_hash" => $token

        );

        $result = $this->db->select($fields)
                    ->from('sec_verified_by_flexio')
                    ->where($clause)
                    ->get()
                    ->result_array();



        if(!empty($result))
        {
            $ip = $this->input->server('REMOTE_ADDR');
            /*
             'ip' 		=> $this->input->server('REMOTE_ADDR'),
            'navegador'         => $this->agent->agent_string(),
            'uri' 		=> $this->input->server('REQUEST_URI'),
              */


             //Begin Transaction
            //$this->db->trans_start();

            $fieldset = array(
              'link_validated' =>   $result[0]['link_validated'] + 1,
                'last_ip' => $ip,
              'updated_at' => date('Y-m-d h:i:s')
            );
            $clause = array(
              "id" => $result[0]['id']
            );
            $this->db->where($clause)
                    ->update('sec_verified_by_flexio', $fieldset);

            //End Transaction
           // $this->db->trans_complete();

            //Managing Errors
            /*
            if($this->db->trans_status() === FALSE){
                return false;
            }else{
                return true;
            }
            */
        }
        return $result;
    }



    public function valida_codigo_no_cambia_documento(){
        //print_r($_POST);
        //dead();

        $this->session->unset_userdata('mensaje_valida_codigo_no_cambia_documento_error');
        $this->session->unset_userdata('mensaje_valida_codigo_no_cambia_documento');

          $error =0;
        $numero_documento = $this->input->post("numero_documento");
        $fecha_documento = $this->input->post("fecha_documento");
        $cantidad_articulos = $this->input->post("cantidad_articulos");
        $monto_documento = $this->input->post("monto_documento");

        $result = $this->validador_hash($numero_documento, $fecha_documento, $cantidad_articulos, $monto_documento);



        if(!empty($result)){
            $this->load->library('user_agent');



            $fields = array(
                "id",
                "document_validated"
            );
            $clause = array(
                "document_hash" => $result

            );

            $resultHash = $this->db->select($fields)
                        ->from('sec_verified_by_flexio')
                        ->where($clause)
                        ->get()
                        ->result_array();



            if(!empty($resultHash))
            {
                $ip = $this->input->server('REMOTE_ADDR');


                $cantidad_elementos = count($resultHash);
                //Begin Transaction
                //$this->db->trans_start();
                if ($cantidad_elementos == 1){
                    $fieldset = array(
                        'document_validated' =>   $resultHash[0]['document_validated'] + 1,
                        'last_ip' => $ip,
                         'updated_at' => date('Y-m-d h:i:s')
                    );
                    $clause = array(
                      "id" => $resultHash[0]['id']
                    );
                    $this->db->where($clause)
                            ->update('sec_verified_by_flexio', $fieldset);

                    //End Transaction
                    // $this->db->trans_complete();

                    //Managing Errors
                    /*
                    if($this->db->trans_status() === FALSE){
                        return false;
                    }else{
                        return true;
                    }
                    */
                } else {
                       $this->session->set_userdata('mensaje_valida_codigo_no_cambia_documento_error',"Error al validar el contenido del documento");
                       $error = 1;

                }
            } else {
                 $this->session->set_userdata('mensaje_valida_codigo_no_cambia_documento_error',"Contenido de documento no valido");
                     $error = 1;

            }
        } else {
            $this->session->set_userdata('mensaje_valida_codigo_no_cambia_documento_error',"No se pudo validar el contenido del documento");
              $error = 1;

        }

        if( $error == 0){
             $this->session->set_userdata('mensaje_valida_codigo_no_cambia_documento',"Contenido del documento validado");

        }

         redirect('verified_by_flexio/', 'refresh');
        //return $result;

    }




}
