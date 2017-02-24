<template id="filtro_factura">

	<div style="background-color: #D9D9D9; padding: 6px 0 39px 10px">
		<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
			<label class="m-t-xs">Empezar factura desde</label>
		</div>
		<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
			<select class="white-bg chosen-select" name="tipo" id="tipo" v-chosen="ordendesde_id" v-model="ordendesde_id" data-placeholder="Seleccione" :disabled="disabled==true">
				<option value="">Seleccione</option>
				<option v-bind:value="option.id" v-for="option in ordenDesdeOptions | orderBy 'nombre'" track-by="$index">{{{option.nombre}}}</option>
			</select>
		</div>
		<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
			<select id="tipofactura" class="white-bg chosen-select" name="tipo_factura_id" v-model="tipo_factura_id" v-chosen="tipo_factura_id" data-placeholder="Seleccione"  :disabled="ordendesde_id=='' || disabled==true">
				<option value="">Seleccione</option>
				<option v-bind:value="option.uuid" v-for="option in tipoFacturasOptions[ordendesde_id] | orderBy 'nombre'" track-by="$index">{{{option.nombre}}}</option>
			</select>
		</div>
		<div class="col-xs-12 col-sm-3 col-md-6 col-lg-5 text-left"></div>
	</div>
	<input type="hidden" name="formulario" v-model="formulario" />
	
</template>
<?php ?>