<template id="agregar_colaboradores">
	<div class="col-xs-5">
		<select name="from[]" id="lista_colaboradores" multiple="multiple" class="form-control multiselectField" size="8" >
			<template v-for="option in colaboradoresOptions" track-by="$index" >
				<option value="{{option.id}}">{{{option.nombre}}}</option>
			</template>
		</select>
	</div>
	<div class="col-xs-2">
		<button type="button" id="lista_colaboradores_rightAll" class="btn btn-block"><i class="fa fa-forward"></i></button>
		<button type="button" id="lista_colaboradores_rightSelected" class="btn btn-block"><i class="fa fa-chevron-right"></i></button>
		<button type="button" id="lista_colaboradores_leftSelected" class="btn btn-block"><i class="fa fa-chevron-left"></i></button>
		<button type="button" id="lista_colaboradores_leftAll" class="btn btn-block"><i class="fa fa-backward"></i></button>
	</div>
	<div class="col-xs-5">
		<select name="to[]" id="lista_colaboradores_to" class="form-control" size="8" multiple="multiple">
		<template v-for="option in colaboradoresSeleccionadosOptions" track-by="$index" >
				<option value="{{option.id}}">{{{option.nombre}}}</option>
			</template>
		</select>
	</div>
</template>
