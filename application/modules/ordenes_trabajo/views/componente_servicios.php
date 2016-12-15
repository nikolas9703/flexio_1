<template id="servicios">

	<div id="servc{{index}}">
		<div class="ibox-title">
			<h5>{{servicio.itemseleccionado}}</h5>
			<div class="ibox-tools">
				<a href="#panel{{index}}" class="btn btn-xs" data-toggle="collapse"><i class="fa fa-chevron-up"></i></a>
				<a href="#" class="btn btn-default btn-xs" v-show="index==0" @click.prevent="agregarServicio()"><i class="fa fa-plus"></i></a>
				<a href="#" class="btn btn-default btn-xs" v-show="index>0||servicio.id!=''" @click.prevent="eliminarServicio(index)"><i class="fa fa-trash"></i></a>
			</div>
		</div>
		<div id="panel{{index}}" class="ibox-content panel-collapse collapse in">

			<div class="row">
					<h4 class="m-b-xs">Prestar servicio a </h4>

		        	<div class="hr-line-dashed m-t-xs"></div>

  				<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Categor&iacute;a de item </label>
                	<select id="categoria_item_id{{index}}" name="servicios[{{index}}][categoria_id]" class="form-control" v-model="servicio.categoria_id" @change="popularItems($event, servicio)">
						<option value="">Seleccione</option>
						<option value="{{option.id}}" v-for="option in categorias" track-by="$index">{{option.nombre}}</option>
					</select>
				</div>
				<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Item </label>
                	<select class="form-control" id="item_id{{index}}" name="servicios[{{index}}][item_id]" v-model="servicio.item_id" :disabled="servicio.itemsservicio.length==0 || servicio.categoria_id==''" @change="popularSerial($event, servicio)">
						<option  value="">Seleccione</option>
						<option v-show="servicio.categoria_item_id!=''" value="{{option.id}}" v-for="option in servicio.itemsservicio | orderBy 'nombre'" track-by="$index">{{option.nombre}}</option>
					</select>
					<span class="alert-warning" v-if="servicio.categoria_id !='' && servicio.itemsservicio.length==0">La categoria seleccionada no tiene asociada items con serie.</span>
				</div>
				<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Serie </label>
                	<select class="form-control" id="serie_id{{index}}" name="servicios[{{index}}][serie_id]" v-model="servicio.serie_id" :disabled="servicio.series != undefined && servicio.series.length==0 || servicio.item_id==''">
						<option  value="">Seleccione</option>
						<option v-show="servicio.item_id!=''" value="{{option.id}}" v-for="option in servicio.series | orderBy 'nombre'" track-by="$index">{{option.nombre}}</option>
					</select>
				</div>
				<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Equipo Asignado </label>
                	<select class="form-control" id="equipo_id{{index}}" name="servicios[{{index}}][equipo_id]" v-model="servicio.equipo_id" @change.stop.prevent="validarCapacidadAtencion($event, servicio)">
						<option  value="">Seleccione</option>
						<option value="{{option.id}}" v-for="option in listaEquiposTrabajoOptions | orderBy 'nombre'" track-by="$index">{{option.nombre}}</option>
					</select>
					<span class="alert-warning" v-if="servicio.verificando_capacidad !=''">{{{servicio.verificando_capacidad}}}</span>
				</div>
            </div>

			<items v-ref:profile :categorias="categorias" :parent_index="index" :listaitems.sync="servicio.items"></items>

			<input type="hidden" name="servicios[{{index}}][id]" v-model="servicio.id" />
		</div>
	</div>

</template>
