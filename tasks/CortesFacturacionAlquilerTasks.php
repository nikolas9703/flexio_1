<?php
if (isset($_SERVER['REMOTE_ADDR'])) die('Permission denied.');
define('ROOTPATH', str_replace("/tasks", "", __DIR__));

/**
 * PHP Crunz
 * Crunz is a framework-agnostic package to schedule periodic tasks (cron jobs) in PHP using a fluent API.
 * Crunz is capable of executing console commands, shell scripts, PHP scripts, and closures.
 *
 * @see https://github.com/lavary/crunz
 */

require_once ROOTPATH . "/vendor/autoload.php";

use Crunz\Schedule;

/*$schedule = new Schedule();
$schedule->run("/usr/bin/php ". ROOTPATH ."/index.php jobs facturar_cargos")
				->everyTwoMinutes() //->everyHour()
        ->description('crear cortes de facturacion de los cargos de alquiler.')
        ->appendOutputTo('/var/log/cron.log');
return $schedule;*/
