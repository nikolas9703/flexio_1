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
			<table class="table table-striped" id="tablaEndosoGrid"></table>

			<!-- pager definition -->
			<div id="EndosoPager"></div>

			<!-- /JQGRID -->
		</div>

		<div role="tabpanel" class="tab-pane" id="grid">
			<?php Grid::visualizar_grid($grid); ?>
		</div>
	</div>
</div>

<?php

$formAttr = array('method' => 'POST', 'id' => 'exportarEndoso', 'autocomplete' => 'off');
    echo form_open(base_url('endosos/exportar'), $formAttr);
?>
    <input type="hidden" name="ids" id="ids_endoso" value="" />
<?php echo form_close(); ?>

<?php

echo Modal::config(array(
    "id" => "opcionesEndosoModal",
    "size" => "sm"
))->html();

?>