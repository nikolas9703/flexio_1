<?php ?><template id="tabla_entregas">

	<div class="table-responsive">

	<div id="devolucionesAlquilerItemsError" class="error"></div>
 		<table class="table table-bordered tabla-dinamica" id="itemsTable">
			<thead>

				<tr>
					<th scope="colgroup" width="1%" scope="mainheader">&nbsp;</th>
					<th v-if="showNoEntrega == true" width="12%" class="item" scope="colgroup">No. entrega</th>
					<th width="14%" scope="colgroup">Categor&iacute;a de item</th>
					<th width="30%" scope="colgroup">Item a entregar</th>
					<th width="12%" scope="colgroup">Cantidad en alquiler</th>

					<th width="5%" scope="colgroup" colspan="2">&nbsp;</th>
				</tr>
			</thead>
			<tbody :id="'ordenitem' + $index" v-for="entregaValor in entregas" track-by="$index">
				<tr>
					  <td rowspan="2">
						<h3><a hrfe="#" @click="toggleSubTabla($event)"><i class="fa fa-caret-right"></i></a></h3>
					</td>
					<td  v-if="showNoEntrega == true">
						<select  :disabled="true"  id="entrega_id{{$index}}" name="items[{{$index}}][entrega_id]" class="form-control" v-model="entregaValor.entrega_id">
							<option value="">Seleccione</option>
							<template v-for="option in listaEntregasOptions" track-by="$index" >
								<option v-bind:value="option.id">{{{option.codigo}}}</option>
							</template>
						</select>
					</td>
					<td>
						<select :disabled="disabledCategoria"   class="form-control" id="categoria_id{{$index}}" name="items[{{$index}}][categoria_id]" v-model="entregaValor.categoria_id"  >
							<option  value="">Seleccione</option>
							<option   value="{{option.id}}" v-for="option in listaCategoriasOptions | orderBy 'nombre'">{{option.nombre}}</option>
						</select>
					</td>
					<td>
						<select :disabled="disabledItem"  class="form-control" id="item_id{{$index}}" name="items[{{$index}}][item_id]" v-model="entregaValor.item_id"  >
							<option  value="">Seleccione</option>
							<option  value="{{option.id}}" v-for="option in entregaValor.items | orderBy 'nombre'">{{option.nombre}}</option>
						</select>
						<input type="hidden" name="items[{{$index}}][atributo_id]" v-model="entregaValor.atributo_id">
						<input type="hidden" name="items[{{$index}}][atributo_text]" v-model="entregaValor.atributo_text">
						<input type="hidden" name="items[{{$index}}][ciclo_id]" v-model="entregaValor.ciclo_id">
						<input type="hidden" name="items[{{$index}}][tarifa]" v-model="entregaValor.tarifa">
					</td>

					<td>
						<input :disabled="true"  type="text"  class="form-control" name="items[{{$index}}][cantidad_alquiler]" id="cantidad_alquiler{{$index}}" v-model="entregaValor.cantidad_alquiler" />
					</td>
					<td v-show="$index>0">
						<button agrupador="items" class="btn btn-default btn-block eliminarItemBtn" type="button" @click="eliminarItemOrden($index, $event)">
							<i class="fa fa-trash"></i> <span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp; Eliminar</span>
						</button>
					</td>
					<td v-show="$index==0">
						<button agrupador="items" class="btn btn-default btn-block agregarItemBtn" :disabled="disabledAgregar">
							<i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp; Agregar</span>
						</button>
					</td>
				</tr>
				<tr class="itemsUtilizados{{$index}}">
					<td colspan="8" class="hide">
 						<tabla_series v-bind:parent_index="$index" v-bind:parent_entregas="entregaValor" ></tabla_series>
 					</td>
				</tr>
		</table>
	</div>

</template>
