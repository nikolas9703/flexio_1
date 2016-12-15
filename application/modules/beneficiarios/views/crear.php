<?php
$info = !empty($info) ? array("info" => $info) : array();
echo modules::run('beneficiarios/formulario', $info);
?>