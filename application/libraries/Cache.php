<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Cache Class
 *
 * Clase implementa la libreria de cache phpFastCache, para
 * acelerar la carga de la aplicacion guardando en
 * cache funciones pesadas o parte de codigo.
 * 
 * http://www.phpfastcache.com/
 * 
 *
 * @package    PensaApp
 * @subpackage Library
 * @category   Libraries
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @since     Version 1.0
 */
class Cache
{
	protected static $ci;
	
	protected static $config;

    public function __construct(){}
    
    /**
     * Inicializar phpFastCache
     */
    public static function inicializar()
    {
    	phpFastCache::setup("storage","auto");
    	phpFastCache::$config = array(
			"storage"   =>  "auto", // auto, files, sqlite, apc, cookie, memcache, memcached, predis, redis, wincache, xcache
			"default_chmod" => 0777, // For security, please use 0666 for module and 0644 for cgi.
			"htaccess"      => true,
			"path" =>  "application/cache",
			"securityKey" => "auto",
			"memcache" =>  array(
				array("127.0.0.1",11211,1),
			),
			"redis" =>  array(
				"host"  => "127.0.0.1",
				"port"  =>  6379,
				"password"  =>  "",
				"database"  =>  1,
				"timeout"   =>  ""
			),
			"extensions" =>  array(),
			"fallback" => "files",
		);
    	
		//de lo contrario iniciar cache en archivo
		return phpFastCache("auto");
    }
}
