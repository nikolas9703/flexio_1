<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| phpFastCache settings
| -------------------------------------------------------------------------
| PHP Caching Class For Database: Reduce Database Calls.
|
|	See: http://www.phpfastcache.com/
|
*/
$config = array(
	"phpfastcache" => array(
		"storage"   =>  "auto", // auto, files, sqlite, apc, cookie, memcache, memcached, predis, redis, wincache, xcache
		"default_chmod" => 0777, // For security, please use 0666 for module and 0644 for cgi.
		"htaccess"      => true,
		"path" =>  "application/cache",
		"redis" =>  array(
			"host"  => "127.0.0.1",
			"port"  =>  6379,
			"password"  =>  "",
			"database"  =>  1,
			"timeout"   =>  ""
		),
		"extensions" =>  array(),
		"fallback" => "files"
	)
);
