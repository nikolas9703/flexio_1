<div role="tabpanel">

	<!-- Botones -->

	<!-- /Botones -->

	<!-- Tab panes -->
	<div class="row tab-content">
		<div role="tabpanel" class="tab-pane active" id="tabla">
		
			<!-- JQGRID -->

			<!-- Opcion: Mostrar/Ocultar columnas del jQgrid -->
			<div id="jqgrid-column-togle" class="row"></div>

			<!-- Listado de Clientes -->

			<div class="NoRecordsEmpresa text-center lead"></div>
			
			<div id="gridHeader"></div>

			<!-- the grid table -->
			<table class="table table-striped" id="PolizasGrid"></table>

			<!-- pager definition -->
			<div id="pager_polizas"></div>

			<!-- /JQGRID -->
		</div>

		<div role="tabpanel" class="tab-pane" id="grid">
			<?php Grid::visualizar_grid($grid); ?>
		</div>
	</div>
</div>
	
<?php

$formAttr = array('method' => 'POST', 'id' => 'exportarPolizasIntereses','autocomplete'  => 'off');
echo form_open(base_url('intereses_asegurados/exportarPolizasIntereses'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<input type="hidden" name="interes" id="interes_exp" value="" />
<?php
echo form_close();

?>

<?php

echo Modal::config(array(
    "id" => "opcionesPolizasModal",
    "size" => "sm"
))->html();

?>