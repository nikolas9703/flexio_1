<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Assets Class.
 *
 * Clase PHP para cargar recuros (js, css).
 * + Implementacion de Clase para minificar los archivos css y js
 * para optimizar la aplicacion y reducir el tiempo de carga.
 *
 *
 * @category   Libraries
 *
 * @author     Pensanomica Team
 *
 * @link       http://www.pensanomica.com
 * @since     Version 1.0
 */
class Assets
{
    /**
     * CodeIgniter global.
     *
     * @var object
     */
    protected $ci;

    public static $css = array();

    public static $js = array();

    public static $vars = array();

    /**
     * Variables for
     * Minify LibraryClass.
     *
     ************************
     * Css files array.
     *
     * @var array
     */
    protected static $css_array = array();

    /**
     * Js files array.
     *
     * @var array
     */
    protected static $js_array = array();

    /**
     * Assets dir.
     *
     * @var string
     */
    public static $assets_dir = 'public/assets';

    /**
     * Css dir.
     *
     * @var string
     */
    public static $css_dir = 'css';

    /**
     * Js dir.
     *
     * @var string
     */
    public static $js_dir = 'js';

    /**
     * Output css file name.
     *
     * @var string
     */
    public static $css_file = 'styles.css';

    /**
     * Output js file name.
     *
     * @var string
     */
    public static $js_file = 'scripts.js';

    /**
     * Automatic file names.
     *
     * @var bool
     */
    public static $auto_names = false;

    /**
     * Compress files or not.
     *
     * @var bool
     */
    public static $compress = true;

    /**
     * Compression engines.
     *
     * @var array
     */
    public static $compression_engine = array('css' => 'minify', 'js' => 'closurecompiler');

    /**
     * Css file name with path.
     *
     * @var string
     */
    private static $_css_file = '';

    /**
     * Js file name with path.
     *
     * @var string
     */
    private static $_js_file = '';

    /**
     * Last modification.
     *
     * @var array
     */
    private static $_lmod = array('css' => 0, 'js' => 0);

    /**
     * Constructor.
     */

    private static $commit = "";


    public function __construct()
    {
        /*
    	 * Instanciar codeigniter
    	 */
        $this->ci = &get_instance();

        /*
    	 * Cragar helper url
    	 */
        $this->ci->load->helper('url');

        /*
    	 * Cargar variable global CSRF
    	 */
        self::agregar_var_js(array(
            'tkn' => $this->ci->security->get_csrf_hash(),
            'uuid_empresa' => $this->ci->session->userdata('uuid_empresa'),
        ));

        $config = $this->ci->load->config('minify', true, true);

        /*
    	 * Inicializar
    	 * Minify LibraryClass.
    	 */
        // user specified settings from config file
        self::$assets_dir = $this->ci->config->item('assets_dir', 'minify') ?: self::$assets_dir;
        self::$css_dir = $this->ci->config->item('css_dir', 'minify') ?: self::$css_dir;
        self::$js_dir = $this->ci->config->item('js_dir', 'minify') ?: self::$js_dir;
        self::$css_file = $this->ci->config->item('css_file', 'minify') ?: self::$css_file;
        self::$js_file = $this->ci->config->item('js_file', 'minify') ?: self::$js_file;
        self::$auto_names = $this->ci->config->item('auto_names', 'minify') ?: self::$auto_names;
        self::$compress = $this->ci->config->item('compress', 'minify') ?: self::$compress;
        self::$compression_engine = $this->ci->config->item('compression_engine', 'minify') ?: self::$compression_engine;
        //$this->closurecompiler = $this->ci->config->item('closurecompiler', 'minify');

        if (count($config) > 0) {
            // custom config array
            foreach ($config as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }

        // perform checks
        $this->_config_checks();

        self::$commit = exec('git log --pretty="%H" -n1 HEAD');

    }

    /**
     * Cargar los archivos css de los modulos en el arreglo.
     *
     * @param string $files Arreglo de los archivos con sus rutas
     */
    public function agregar_css($files = array())
    {
        self::$css = array_merge(self::$css, $files);

        // add css files
        self::declare_css(self::$css);
    }

   /**
    * Cargar los archivos css de los modulos en el template.
    */
   public static function css()
   {
       //Verificar que el arreglo de archivos css no sea vacio
        if (!empty(self::$css)) {
            foreach (self::$css as $file) {
                //Verificar que un archivo exista antes de incluirlo
                if (file_exists(realpath($file))) {
                    echo '<link href="'.base_url($file).'" rel="stylesheet">'."\n";
                }
            }
// bool argument for rebuild css (false means skip rebuilding).
//echo self::deploy_css(TRUE);
        }
   }

    /**
     * Cargar los archivos js de los modulos en el arreglo.
     *
     * @param string $files Arreglo de los archivos js con sus rutas
     */
    public function agregar_js($files = array())
    {
        self::$js = array_merge(self::$js, $files);

        // add css files
        //self::declare_js(self::$js);
    }

    /**
     * Cargar los archivos js de los modulos en el arreglo.
     *
     * @param string $files Arreglo de los archivos js con sus rutas
     */
    public static function agregarjs($files = array())
    {
        self::$js = array_merge(self::$js, $files);
    }

   /**
    * Cargar los archivos css de los modulos en el template.
    */
    public static function js()
    {
        //Verificar que el arreglo de archivos js no sea vacio
        if(!empty(self::$js)) {
            foreach (self::$js as $file){
                //Verificar que un archivo exista antes de incluirlo
                if (file_exists(realpath($file))) {
                    echo '<script src="'.base_url($file).'?rev='.self::$commit.'" type="text/javascript"></script>'."\n";
                }
            }
        }
    }

    /**
     * Cargar las variable js en el arreglo.
     *
     * @param string $vars Arreglo de variables
     */
    public function agregar_var_js($vars = array())
    {
        self::$vars = array_merge(self::$vars, $vars);
    }

   /**
    * Cargar las variables js en el template.
    */
   public static function js_vars()
   {
       if (!empty(self::$vars)) {
           echo '<script type="text/javascript">'."\n";
           foreach (self::$vars as $varname => $value) {
               if (is_string($value)) {
                   $value = "'".$value."'";
               }
               echo "var $varname = $value;"."\n";
           }
           echo '</script>'."\n";
       }
   }

    /**
     * Minify Library Functions.
     *
     * PHP Version 5.3
     *
     * @category  PHP
     *
     * @author    Slawomir Jasinski <slav123@gmail.com>
     * @copyright 2015 All Rights Reserved SpiderSoft
     * @license   Copyright 2015 All Rights Reserved SpiderSoft
     *
     * @link      Location: http://github.com/slav123/CodeIgniter-Minify
     */

    /**
     * Declare css files list.
     *
     * @param mixed $css   File or files names
     * @param bool  $group Set group for files
     */
    public function declare_css($css, $group = 'default')
    {
        if (is_array($css)) {
            self::$css_array[$group] = $css;
        } else {
            self::$css_array[$group] = array_map('trim', explode(',', $css));
        }

        return $this;
    }

    /**
     * Declare js files list.
     *
     * @param mixed $js    File or files names
     * @param bool  $group Set group for files
     */
    public static function declare_js($js, $group = 'default')
    {
        if (is_array($js)) {
            self::$js_array[$group] = $js;
        } else {
            self::$js_array[$group] = array_map('trim', explode(',', $js));
        }

        //return self;
    }

    //--------------------------------------------------------------------

    /**
     * Declare css files list.
     *
     * @param mixed $css   File or files names
     * @param bool  $group Set group for files
     */
    public function add_css($css, $group = 'default')
    {
        if (!isset(self::$css_array[$group])) {
            self::$css_array[$group] = array();
        }

        if (is_array($css)) {
            self::$css_array[$group] = array_unique(array_merge(self::$css_array[$group], $css));
        } else {
            self::$css_array[$group] = array_unique(array_merge(self::$css_array[$group], array_map('trim', explode(',', $css))));
        }

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Declare js files list.
     *
     * @param mixed $js    File or files names
     * @param bool  $group Set group for files
     */
    public function add_js($js, $group = 'default')
    {
        if (!isset(self::$js_array[$group])) {
            self::$js_array[$group] = array();
        }

        if (is_array($js)) {
            self::$js_array[$group] = array_unique(array_merge(self::$js_array[$group], $js));
        } else {
            self::$js_array[$group] = array_unique(array_merge(self::$js_array[$group], array_map('trim', explode(',', $js))));
        }

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Deploy and minify CSS.
     *
     * @param bool $force     Force to rewrite file
     * @param null $file_name File name to create
     * @param null $group     Group name
     *
     * @return string
     */
    public function deploy_css($force = true, $file_name = null, $group = null)
    {
        $return = '';

        if (is_null($file_name)) {
            $file_name = self::$css_file;
        }

        if (is_null($group)) {
            foreach (self::$css_array as $group_name => $group_array) {
                $return .= self::_deploy_css($force, $file_name, $group_name).PHP_EOL;
            }
        } else {
            $return .= self::_deploy_css($force, $file_name, $group);
        }

        return $return;
    }

    //--------------------------------------------------------------------

    /**
     * Deploy and minify js.
     *
     * @param bool $force     Force rewriting js file
     * @param null $file_name File name
     * @param null $group     Group name
     *
     * @return string
     */
    public function deploy_js($force = false, $file_name = null, $group = null)
    {
        $return = '';

        if (is_null($file_name)) {
            $file_name = self::$js_file;
        }

        if (is_null($group)) {
            foreach (self::$js_array as $group_name => $group_array) {
                $return .= self::_deploy_js($force, $file_name, $group_name).PHP_EOL;
            }
        } else {
            $return .= self::_deploy_js($force, $file_name, $group);
        }

        return $return;
    }

    //--------------------------------------------------------------------

    /**
     * Build and minify CSS.
     *
     * @param bool $force     Force to rewrite file
     * @param null $file_name File name to create
     * @param null $group     Group name
     *
     * @return string
     */
    private function _deploy_css($force = true, $file_name = null, $group = null)
    {
        if (self::$auto_names) {
            $file_name = md5(serialize(self::$css_array[$group])).'.css';
        } else {
            $file_name = ($group === 'default') ? $file_name : $group.'_'.$file_name;
        }

        self::_set('css_file', $file_name);

        self::_scan_files('css', $force, $group);

        return '<link href="'.base_url(self::$_css_file).'" rel="stylesheet" type="text/css" />';
    }

    //--------------------------------------------------------------------

    /**
     * Build and minify js.
     *
     * @param bool $force     Force rewriting js file
     * @param null $file_name File name
     * @param null $group     Group name
     *
     * @return string
     */
    private function _deploy_js($force = false, $file_name = null, $group = null)
    {
        if (self::$auto_names) {
            $file_name = md5(serialize(self::$js_array[$group])).'.js';
        } else {
            $file_name = ($group === 'default') ? $file_name : $group.'_'.$file_name;
        }

        self::_set('js_file', $file_name);

        self::_scan_files('js', $force, $group);

        return '<script type="text/javascript" src="'.base_url(self::$_js_file).'"></script>';
    }

    //--------------------------------------------------------------------

    /**
     * construct js_file and css_file.
     *
     * @param string $name  File type
     * @param string $value File name
     */
    private function _set($name, $value)
    {
        switch ($name) {
            case 'js_file':

                if (self::$compress) {
                    if (!preg_match("/\.min\.js$/", $value)) {
                        $value = str_replace('.js', '.min.js', $value);
                    }

                    self::$js_file = $value;
                }

                self::$_js_file = self::$assets_dir.'/'.$value;

                if (!file_exists(self::$_js_file) && !touch(self::$_js_file)) {
                    throw new Exception('Can not create file '.self::$_js_file);
                } else {
                    self::$_lmod['js'] = filemtime(self::$_js_file);
                }

                break;
            case 'css_file':

                if (self::$compress) {
                    if (!preg_match("/\.min\.css$/", $value)) {
                        $value = str_replace('.css', '.min.css', $value);
                    }

                    self::$css_file = $value;
                }

                self::$_css_file = self::$assets_dir.'/'.$value;

                if (!file_exists(self::$_css_file) && !touch(self::$_css_file)) {
                    throw new Exception('Can not create file '.self::$_css_file);
                } else {
                    self::$_lmod['css'] = filemtime(self::$_css_file);
                }

                break;
        }
    }

    /**
     * scan CSS directory and look for changes.
     *
     * @param string $type  Type (css | js)
     * @param bool   $force Rewrite no mather what
     * @param string $group Group name
     */
    private function _scan_files($type, $force, $group)
    {
        switch ($type) {
            case 'css':
                $files_array = self::$css_array[$group];
                $directory = self::$css_dir;
                $out_file = self::$_css_file;
                break;
            case 'js':
                $files_array = self::$js_array[$group];
                $directory = self::$js_dir;
                $out_file = self::$_js_file;
        }

        // if multiple files
        if (is_array($files_array)) {
            $compile = false;
            foreach ($files_array as $file) {
                $filename = /*$directory . '/' .*/ $file;

                if (file_exists($filename)) {
                    if (filemtime($filename) > self::$_lmod[$type]) {
                        $compile = true;
                    }
                } else {
                    throw new Exception('File '.$filename.' is missing');
                }
            }

            // check if this is init build
            if (file_exists($out_file) && filesize($out_file) === 0) {
                $force = true;
            }

            if ($compile or $force) {
                self::_concat_files($files_array, $directory, $out_file);
            }
        }
    }

    //--------------------------------------------------------------------

    /**
     * add merge files.
     *
     * @param string $file_array Input file array
     * @param string $directory  Directory
     * @param string $out_file   Output file
     */
    private function _concat_files($file_array, $directory, $out_file)
    {
        if ($fh = fopen($out_file, 'w')) {
            foreach ($file_array as $file_name) {
                $file_name = /*$directory . '/' .*/ $file_name;
                $handle = fopen($file_name, 'r');
                $contents = fread($handle, filesize($file_name));
                fclose($handle);

                fwrite($fh, $contents);
            }
            fclose($fh);
        } else {
            throw new Exception('Can\'t write to '.$out_file);
        }

        if (self::$compress) {
            // read output file contest (already concated)
            $handle = fopen($out_file, 'r');
            $contents = fread($handle, filesize($out_file));
            fclose($handle);

            // recreate file
            $handle = fopen($out_file, 'w');

            if (preg_match('/.css$/i', $out_file)) {
                $engine = '_'.self::$compression_engine['css'];
            }

            if (preg_match('/.js$/i', $out_file)) {
                $engine = '_'.self::$compression_engine['js'];
            }

            // call function name to compress file
            fwrite($handle, call_user_func(array(new self(), $engine), $contents));
            fclose($handle);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Compress javascript using closure compiler service.
     *
     * @param string $data Source to compress
     *
     * @return mixed
     */
    private function _closurecompiler($data)
    {

        /*$config = self::closurecompiler;


    	$ch = curl_init('http://closure-compiler.appspot.com/compile');

    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=' . $config['compilation_level'].'&js_code=' . urlencode($data));
    	$output = curl_exec($ch);
    	curl_close($ch);

    	return $output;*/
    }

    //--------------------------------------------------------------------

    /**
     * Implements jsmin as alternative to closure compiler.
     *
     * @param string $data Source to compress
     *
     * @return string
     */
    private function _jsmin($data)
    {
        require_once APPPATH.'libraries/minify/JSMin.php';

        return JSMin::minify($data);
    }

    //--------------------------------------------------------------------

    /**
     * Implements jsminplus as alternative to closure compiler.
     *
     * @param string $data Source to compress
     *
     * @return string
     */
    private function _jsminplus($data)
    {
        require_once APPPATH.'libraries/minify/JSMinPlus.php';

        return JSMinPlus::minify($data);
    }

    //--------------------------------------------------------------------

    /**
     * Implements cssmin compression engine.
     *
     * @param string $data Source to compress
     *
     * @return string
     */
    private function _cssmin($data)
    {
        require_once APPPATH.'libraries/minify/cssmin-v3.0.1.php';

        return CssMin::minify($data);
    }

    //--------------------------------------------------------------------

    /**
     * Implements cssminify compression engine.
     *
     * @param string $data Source to compress
     *
     * @return string
     */
    private function _minify($data)
    {
        require_once APPPATH.'libraries/minify/cssminify.php';
        $cssminify = new cssminify();

        return $cssminify->compress($data);
    }

    //--------------------------------------------------------------------

    /**
     * Perform config checks.
     */
    private function _config_checks()
    {
        if (!is_writable(self::$assets_dir)) {
            throw new Exception('Assets directory '.self::$assets_dir.' is not writable');
        }

        if (empty(self::$css_dir)) {
            throw new Exception('CSS directory must be set');
        }

        if (empty(self::$js_dir)) {
            throw new Exception('JS directory must be set');
        }

        if (!self::$auto_names) {
            if (empty(self::$css_file)) {
                throw new Exception('CSS file name can\'t be empty');
            }

            if (empty(self::$js_file)) {
                throw new Exception('JS file name can\'t be empty');
            }
        }

        if (self::$compress) {
            if (!isset(self::$compression_engine['css']) or empty(self::$compression_engine['css'])) {
                throw new Exception('Compression engine for CSS is required');
            }

            if (!isset(self::$compression_engine['js']) or empty(self::$compression_engine['js'])) {
                throw new Exception('Compression engine for JS is required');
            }
        }
    }
}
