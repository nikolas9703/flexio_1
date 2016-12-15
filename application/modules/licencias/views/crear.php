<?php 
$info = !empty($info) ? array("info" => $info) : array();
echo modules::run('licencias/formulario', $info);
?>

        	