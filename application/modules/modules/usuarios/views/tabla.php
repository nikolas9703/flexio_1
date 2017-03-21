<!-- jqgrid -->
<?php echo Jqgrid::cargar("usuariosGrid")  ?>
<!-- /jqgrid -->
<?php

echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

?>
