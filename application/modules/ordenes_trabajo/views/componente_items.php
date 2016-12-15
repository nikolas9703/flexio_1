<template id="items">

	<div class="table-responsive">
		<table class="table table-bordered tabla-dinamica">
			<thead>
				<tr>
					<th width="1%" scope="colgroup">&nbsp;</th>
					<th width="15%" scope="colgroup">Categor&iacute;a de item</th>
					<th width="15%" scope="colgroup">Item</th>
					<th width="15%" scope="colgroup">Atributo</th>
					<th width="8%" scope="colgroup">Cantidad</th>
					<th width="8%" scope="colgroup">Unidad</th>
					<th width="10%" scope="colgroup">Precio unidad</th>
					<th width="10%" scope="colgroup">Precio total</th>
					<th width="5%" colspan="2" scope="colgroup">&nbsp;</th>
				</tr>
			</thead>
			<tbody :id="'items' + parent_index + $index" v-for="item in listaitems" track-by="$index">
				<tr>
					<td rowspan="2">
						<h3><a hrfe="#" @click="toggleSubTabla($event)"><i class="fa fa-caret-right"></i></a></h3>
					</td>
					<td>
						<select class="form-control m-l-sm" id="categoria_id{{parent_index}}{{$index}}" name="servicios[{{parent_index}}][items][{{$index}}][categoria_id]" v-model="item.categoria_id" @change="popularItems($event, item, $index)">
							<option value="">Seleccione</option>
							<option value="{{option.id}}" v-for="option in categorias" track-by="$index">{{option.nombre}}</option>
						</select>
					</td>
					<td>
						<select class="form-control" id="item_id{{parent_index}}{{$index}}" name="servicios[{{parent_index}}][items][{{$index}}][item_id]" v-model="item.item_id" :disabled="item.categoria_item_id=='' && item.items.length == 0" @change="popularItemDatos($event, item, $index)">
							<option value="">Seleccione</option>
							<option v-show="item.categoria_item_id!=''" value="{{option.id}}" v-for="option in item.items | orderBy 'nombre'" track-by="$index">{{option.nombre}}</option>
						</select>
					</td>
					<td>
						<select class="form-control" id="atributo_id{{parent_index}}{{$index}}" name="servicios[{{parent_index}}][items][{{$index}}][atributo_id]" v-model="item.atributo_id" :disabled="item.atributos.length==0 || item.item_id==''">
							<option value="">Seleccione</option>
							<option value="{{option.id}}" v-for="option in item.atributos" track-by="$index">{{option.nombre}}</option>
						</select>
					</td>
					<td>
						<input type="text" name="servicios[{{parent_index}}][items][{{$index}}][cantidad]" id="cantidad{{parent_index}}{{$index}}" class="form-control" :disabled="item.item_id==''" v-model="item.cantidad" @keyup="calcularPrecioTotal($index)">
					</td>
					<td>
						<select class="form-control" id="unidad_id{{parent_index}}{{$index}}" name="servicios[{{parent_index}}][items][{{$index}}][unidad_id]" v-model="item.unidad_id" :disabled="item.unidades.length==0 || item.item_id==''" @change.prevent="calcularPrecioSegunUnidad($event, $index, item)">
							<option value="">Seleccione</option>
							<option v-show="item.categoria_item_id!=''" value="{{option.id}}" v-for="option in item.unidades | orderBy 'nombre'" track-by="$index">{{option.nombre}}</option>
						</select>
					</td>
					<td>
						<div class="input-group">
	                		<span class="input-group-addon">$</span>
							<input type="text" name="servicios[{{parent_index}}][items][{{$index}}][precio_unidad]" id="precio_unidad{{parent_index}}{{$index}}" class="form-control" v-model="item.precio_unidad" disabled="disabled">
						</div>
					</td>
					<td>
						<div class="input-group">
	                		<span class="input-group-addon">$</span>
							<input type="text" name="servicios[{{parent_index}}][items][{{$index}}][precio_total]" id="precio_total{{parent_index}}{{$index}}" class="form-control" v-model="item.precio_total" disabled="disabled">
						</div>
					</td>
					<td v-if="$index>0||item.id!=''">
						<button agrupador="items" class="btn btn-default btn-block eliminarItemBtn" type="button" @click.prevent="eliminarItem($index)">
							<i class="fa fa-trash"></i> <span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp; Eliminar</span>
						</button>
					</td>
					<td v-if="$index==0">
						<button class="btn btn-default btn-block" type="button" @click="agregarItem()">
							<i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp; Agregar</span>
						</button>
					</td>
					<input type="hidden" name="servicios[{{parent_index}}][items][{{$index}}][id]" v-model="item.id" />
				</tr>
				<tr class="itemCamposExtra{{parent_index}}{{$index}}">
					<td colspan="8" class="hide">

						<table style="width: 100%;background-color: #A2C0DA">
			                <tbody>
			                    <tr>
			                        <td style="padding: 15px !important;" width="33%">
			                            <b>Impuesto</b>
																	<!-- data-rule-required="true"   -->
			                            <select id="impuesto_id{{parent_index}}{{$index}}" name="servicios[{{parent_index}}][items][{{$index}}][impuesto_id]" v-model="item.impuesto_uuid" class="form-control item-impuesto" :disabled="item.id != ''">
			                                <option value="">Seleccione</option>
			                                <option value="{{option.uuid}}" v-for="option in item.impuestos" track-by="$index">{{option.nombre}}</option>
			                            </select>
			                        </td>
			                        <td style="padding:15px !important;" width="33%">
			                            <b>Descuento</b>
			                            <div class="input-group">
																			<!-- data-rule-required="true"   -->
			                                <input type="text" id="descuento{{parent_index}}{{$index}}" v-model="item.descuento" name="servicios[{{parent_index}}][items][{{$index}}][descuento]" class="form-control" data-rule-range="[0,100]">
			                                <span class="input-group-addon">%</span>
			                            </div>
			                        </td>
			                        <td style="padding: 15px !important;" width="33%">
			                            <b>Cuenta</b>
																	<!-- data-rule-required="true" -->
			                            <select id="cuenta_id{{parent_index}}{{$index}}" name="servicios[{{parent_index}}][items][{{$index}}][cuenta_id]" v-model="item.cuenta_uuid" class="form-control" :disabled="item.id != ''">
			                                <option value="">Seleccione</option>
			                                <option value="{{option.uuid}}" v-for="option in item.cuentas" track-by="$index">{{option.nombre}}</option>
			                            </select>
			                        </td>
			                    </tr>
			                </tbody>
			            </table>

					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>
<?php ?>
