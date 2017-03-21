<?php
	$info = !empty($info) ? $info : array();
	Template::cargar_formulario($info);
?>

<!-- Tabla Disponibilidad de Items -->
<div id="itemsInventarioDisponible" class="hide" style="display:none;">
	<table class="table table-bordered no-margins">
		<thead>
        <tr>
			<th>Disp.</th>
			<th>No Disponible</th>
			<th>Total</th>
		</tr>
		</thead>
		<tbody>
			<tr>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
</div>