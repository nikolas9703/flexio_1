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

			<!-- the grid table -->
			<table class="table table-striped" id="tablaInteresesAseguradosGrid"></table>

			<!-- pager definition -->
			<div id="InteresesPager"></div>

			<!-- /JQGRID -->
		</div>

		<div role="tabpanel" class="tab-pane" id="grid">
			<?php Grid::visualizar_grid($grid); ?>
		</div>
	</div>
</div>
	
<?php

$formAttrInt = array('method' => 'POST', 'id' => 'exportarInteresesPolizas','autocomplete'  => 'off');
echo form_open(base_url('polizas/exportarInteresesPolizas'), $formAttrInt);
?>
<input type="hidden" name="ids" id="ids_intereses" value="" />
<input type="hidden" name="solicitud" id="solicitud_exp" value="" />
<?php
echo form_close();

?>

<?php

echo Modal::config(array(
    "id" => "opcionesInteresesModal",
    "size" => "sm"
))->html();

?>