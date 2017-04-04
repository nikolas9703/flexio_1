<?php
class Hashgenerator {
 
    // blowfish
    private static $algo = '$2a';
 
    // cost parameter
    private static $cost = '$10';
    
    //qr parameter
    private static $_controller_url 		= '/verified_by_flexio/';
    private static $_method_url 		= '';
   // private $_form_attributes 		= array();

    public  $data 			= array();
    
    
	
        
    public static function generar_qr($numero_documento, $fecha_documento, $cantidad_articulos, $monto_documento) {
        
        $CI =& get_instance();
        
        
        $CI->load->helper(array('form', 'url', 'file', 'string','database'));
        $CI->load->library(array('ci_qr_code','encrypt'));
        $CI->config->load('qr_code');
        
       
        //$this->_method_url = $this->_controller_url . 'index';  
       
        self::$_method_url =  self::$_controller_url  . 'index/'; 
       
       
        $qr_code_config = array(); 
        $qr_code_config['cacheable'] 	= $CI->config->item('cacheable');
        $qr_code_config['cachedir'] 	= $CI->config->item('cachedir');
        $qr_code_config['imagedir'] 	= $CI->config->item('imagedir');
        $qr_code_config['errorlog'] 	= $CI->config->item('errorlog');
        $qr_code_config['ciqrcodelib'] 	= $CI->config->item('ciqrcodelib');
        $qr_code_config['quality'] 		= $CI->config->item('quality');
        $qr_code_config['size'] 		= $CI->config->item('size');
        $qr_code_config['black'] 		= $CI->config->item('black');
        $qr_code_config['white'] 		= $CI->config->item('white');
   
       
        $CI->ci_qr_code->initialize($qr_code_config);
  
        // nombre imagen
        $string = date('dhis'). random_string('alnum', 10);
        $CI->encrypt->set_cipher(MCRYPT_RIJNDAEL_128);
        $token = $CI->encrypt->encode($string);
        $token = preg_replace('/[^\p{L}\p{N}\s]/u', '', $token);
        $image_name = $token.".png";
        
        //$image_name = 'qr_code_test.png';

       // $mensaje = $this->generar_codigo();
       
        // Generar hash
        $valor_hash = $this->generar_hash($numero_documento, $fecha_documento, $cantidad_articulos, $monto_documento);

        $fieldset = array(
            'link_hash'        => $token,
            'document_hash'        => $valor_hash,
            'created_at'   => date('Y-m-d H-i-s'),
            'updated_at'       => date('Y-m-d H-i-s')        
        );
        
        $this->db->insert('sec_verified_by_flexio', $fieldset);
        
        
        
        $params['data'] = site_url() . self::$_method_url.$image_name;
        $params['level'] =  'H';
        $params['size'] = 10;

        
        $params['savename'] = FCPATH.$qr_code_config['imagedir'].$image_name;
        

        
        $CI->ci_qr_code->generate($params); 
        
       // print_r("llegue");
        //$this->data['qr_code_image_url'] = base_url().$qr_code_config['imagedir'].$image_name;
        $qr_code_image_url = base_url().$qr_code_config['imagedir'].$image_name;

        //  print_r($qr_code_image_url);
        // Display the QR Code here on browser uncomment the below line
        //echo '<img src="'.base_url().$qr_code_config['imagedir'].$image_name.'" />'; 
       // $this->load->view('qr_code', $this->data); 
     
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
 
        $valor = trim($numero_documento).trim($fecha_documento).trim($cantidad_articulos).trim($monto_documento);
        
        return crypt($valor,
                    self::$algo .
                    self::$cost .
                    '$' . self::unique_salt($valor));
 
    }
    
    
    // this will be used to compare a password against a hash
    public static function validador_hash($numero_documento, $fecha_documento, $cantidad_articulos, $monto_documento) {
 
       // $full_salt = substr($hash, 0, 29);
         $valor = trim($numero_documento).trim($fecha_documento).trim($cantidad_articulos).trim($monto_documento);
        $full_salt =  self::$algo.self::$cost.'$'.self::unique_salt($valor);
        
        $new_hash = crypt($valor, $full_salt);
        
 //$hash select base de datos
        return ($hash == $new_hash);
 
    }
 
}

?>