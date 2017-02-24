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
			<table class="table table-striped" id="tablaRenovacionesGrid"></table>

			<!-- pager definition -->
			<div id="RenovacionesPager"></div>

			<!-- /JQGRID -->
		</div>

		<div role="tabpanel" class="tab-pane" id="grid">
			<?php Grid::visualizar_grid($grid); ?>
		</div>
	</div>
</div>
	
<?php

$formAttr = array('method' => 'POST', 'id' => 'exportarRenovacionesPolizas','autocomplete'  => 'off');
echo form_open(base_url('polizas/exportarRenovacionesPolizas'), $formAttr);
?>
<input type="hidden" name="ids" id="idsrenovaciones" value="" />
<input type="hidden" name="solicitud" id="solicitud_exp" value="" />
<?php
echo form_close();

?>

<?php

echo Modal::config(array(
    "id" => "opcionesRenovacionesModal",
    "size" => "sm"
))->html();

?>