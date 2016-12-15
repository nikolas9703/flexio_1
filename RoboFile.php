<?php

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 * correr el archivo el windows, php robo.phar clean
 */
require_once 'vendor/autoload.php';
class RoboFile extends \Robo\Tasks {
	public function __construct() {
	}

	/**
	 * Borrar la carpeta de cache del proyecto
	 */
	public function clean() {
		$this->_cleanDir ( [
			'application/cache'
		] );
		$this->_cleanDir ( [
			'public/logs'
		] );
	}
	public function create_upload_folder() {
		$this->say('Creating folder...');
		$this->taskFilesystemStack()->mkdir('public/uploads')->stopOnFail()->run();
		$this->say("Uploads folder was created successfully.");
	}
	public function run_cron_cargos_alquiler() {
		$this->taskExec('php scheduler.php')->run();
		$this->say("The cron job was execute successfully.");
	}
	public function run_compass() {
		$this->taskExec('compass')->arg('watch')->run();
	}
	public function update_repository() {
		$this->taskGitStack()->stopOnFail()->pull('origin', 'master')->run();
	}
	public function localhost() {
		$this->taskOpenBrowser('http://localhost:88/erp')->run();
	}
	public function load_class() {
		$this->taskComposerDumpAutoload()->run();
	}

	public function deploy($env = 'produccion'){
			if($env == 'produccion'){
			 $this->say('pull master');
			 $this->taskGitStack()->stopOnFail()->checkout('.')->pull('origin', 'master')->run();
			}
			$this->taskExecStack()
           ->stopOnFail()
           ->exec('vendor/bin/phinx migrate -e '.$env)
           //->exec('npm install')
           ->exec('npm run prod')
           ->run();
	}

	/**
	 * Limpiar cache de modulos
	 */
	/*
	 * public function limpiar_cache_modulo(){ if(class_exists('Menu')){ $this->say("Existe la clase"); }else{ $this->say("Esta clase ni Existe."); } }
	 */
}
