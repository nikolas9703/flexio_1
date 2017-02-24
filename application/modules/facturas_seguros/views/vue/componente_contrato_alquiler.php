<template id="contrato_alquiler">

	<div id="alquiler-accordion">
		<div class="ibox-title">
			<h5>Items del Alquiler</h5>
		</div>
		<div id="alquiler-items" class="ibox-content panel-collapse collapse in">

			<items_alquiler v-ref:items :categorias="categorias" :impuestos="impuestos" :cuenta_transaccionales="cuenta_transaccionales" :factura.sync="factura"></items_alquiler>

		</div>
	</div>
	<div id="cargoadicional-accordion">
		<div class="ibox-title">
			<h5><input type="checkbox" name="campo[cargos_adicionales]" id="cargos_adicionales" class="toggle-cargoadicional" value="1" :checked="cargos_adicionales_checked=='true' ? true: false" /> Cargos adicionales</h5>
			<a href="#cargoadicional" id="togglecargoadicional" data-toggle="collapse">&nbsp;</a>
		</div>
		<div id="cargoadicional" class="ibox-content panel-collapse collapse {{cargos_adicionales_checked=='true' ? 'in' : ''}}">

			<items_alquiler_adicionales v-ref:items_adicionales :categorias="categorias" :impuestos="impuestos" :cuenta_transaccionales="cuenta_transaccionales" :factura.sync="factura" :cargos_adicionales_checked="cargos_adicionales_checked"></items_alquiler_adicionales>

		</div>
	</div>

</template>
