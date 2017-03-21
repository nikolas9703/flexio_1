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
			<table class="table table-striped" id="tablaDeclaracionesGrid"></table>

			<!-- pager definition -->
			<div id="DeclaracionesPager"></div>

			<!-- /JQGRID -->
		</div>

		<div role="tabpanel" class="tab-pane" id="grid">
			<?php Grid::visualizar_grid($grid); ?>
		</div>
	</div>
</div>

<?php

$formAttr = array('method' => 'POST', 'id' => 'exportarDeclaraciones', 'autocomplete' => 'off');
    echo form_open(base_url('endosos/exportar'), $formAttr);
?>
    <input type="hidden" name="ids" id="ids_declaraciones" value="" />
<?php echo form_close(); ?>

<?php

echo Modal::config(array(
    "id" => "opcionesDeclaracionesModal",
    "size" => "sm"
))->html();

?>