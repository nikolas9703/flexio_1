<?php ?><template id="tabla_series"> 
 	<div class="table-responsive">
		<table style="width:100%; background-color:#A2C0DA" class="table">
 			<tbody>
				 <tr class="item-listing" v-for="articulo in articulos" track-by="$index" :id="'series_item' + $index">
				
				<td width="30%" v-show="articulo.serializable == true">
                    <div class="form-group" style="margin-bottom: 5px !important">
                        <label class="control-label">Serie:</label>
                        <input  :disabled="estadoDevuelto"   type="text" class="form-control" name="items[{{parent_index}}][detalles][{{$index}}][serie]" id="serie{{$index}}"  value="{{articulo.serie}}" />
                    </div>
                </td>
                <td width="30%" v-show="articulo.serializable != true">
                    <div class="form-group" style="margin-bottom: 5px !important">
                        <label class="control-label">Cantidad</label> {{cantidad_validar}}
                        <input :disabled="estadoDevuelto" max="{{articulo.cantidad_validacion}}" data-bind="value:replyNumber" type="text" name="items[{{parent_index}}][detalles][{{$index}}][cantidad]" class="form-control" data-rule-required="true"  id="cantidad{{$index}}"  v-model="articulo.cantidad"  >
                    </div>
                </td>
                <td width="30%">
                    <div class="form-group" style="margin-bottom: 5px !important">
                        <label class="control-label">Bodega de retorno: </label>
                        <select :disabled="estadoDevuelto"  data-placeholder="Seleccione" name="items[{{parent_index}}][detalles][{{$index}}][bodega_id]" id="bodega_id{{$index}}" class="form-control chosen-select" data-rule-required="true" v-model="articulo.bodega_id" >
                            <option value="">Seleccione</option>
                            <option value="{{bodega.id}}" v-for="bodega in bodegas | orderBy 'nombre'" v-if="articulo.serializable == false || (articulo.serializable == true && articulo.ubicacion_id == bodega.id)">{{bodega.nombre}}</option>
                        </select>
                    </div>
                </td>
					<td width="30%">
					    <div class="form-group" style="margin-bottom: 5px !important">
                        <label class="control-label">Estado del item devuelto </label>
                        <select :disabled="estadoDevuelto"  data-placeholder="Seleccione" name="items[{{parent_index}}][detalles][{{$index}}][estado_item_devuelto]"   id="estado_item_devuelto{{$index}}" class="form-control" data-rule-required="true" v-model="articulo.estado_item_devuelto" >
                          <option value="" >Seleccione</option>
                           <option value="buen_estado" >Buen estado</option>
                            <option value="danado" >Da&ntilde;ado</option>
                        </select>
                    </div>
					</td>
 					 <td width="1%" style="text-align: center;padding-top: 30px;">
                    <button class="btn btn-default" v-if="$index===0" @click="addRow($event)" :disabled="true"><li class="fa fa-plus"></li></button>
                    <button class="btn btn-default" v-if="$index!==0" @click="removeRow($index, $event)" :disabled="disabledEditar || disabledEditarTabla"><li class="fa fa-trash"></li></button>
                </td>
					<td v-show="$index==0">
						 					
						 
					</td>
						<input type="hidden" name="items[{{parent_index}}][detalles][{{$index}}][id]" v-model="articulo.id" /> 
				</tr>
			</tbody>
		</table>
	</div>

</template>