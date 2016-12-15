
<template id="cotizacion_items">
    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <!-- <div style="display:table-cell;"> -->
        <div id="cotizacionesAlquilerItemsErros" class="error"></div>
        <table class="table" id="cotizacionesAlquilerItems">
            <thead>
                <tr>
                    <th width="14%">Categor&iacute;a del &iacute;tem</th>
                    <th width="14%">&Iacute;tem a alquiler</th>
                    <th width="14%">Cantidad a contratar</th>
                    <th width="14%">Periodo tarifario</th>
                    <th width="14%">Tarifa pactada</th>
                    <th width="1%" style="background-color: white;"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="item-listing" v-for="articulo in articulos" track-by="$index" :id="'cotizacion_item' + $index">
                    <td>
                        <select data-placeholder="Seleccione" name="articulos[{{$index}}][categoria_id]" class="form-control chosen-select" data-rule-required="true" v-model="articulo.categoria_id" @change="cambiarCategoria(articulo, $index)" :disabled="disabledEditar || disabledEditarTabla">
                            <option value="">Seleccione</option>
                            <option value="{{categoria.id}}" v-for="categoria in categorias | orderBy 'nombre'">{{categoria.nombre}}</option>
                        </select>
                    </td>
                    <td>
                        <select data-placeholder="Seleccione" name="articulos[{{$index}}][item_id]" class="form-control chosen-select" data-rule-required="true" v-model="articulo.item_id" :disabled="disabledEditar || disabledEditarTabla">
                            <option value="">Seleccione</option>
                            <option value="{{item.id}}" v-for="item in articulo.items | orderBy 'nombre'">{{item.nombre}}</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="articulos[{{$index}}][cantidad]" class="form-control" data-rule-required="true" data-inputmask="'mask':'9{1,4}','greedy':false" v-model="articulo.cantidad" :disabled="disabledEditar || disabledEditarTabla">
                    </td>
                    <td>
                        <select data-placeholder="Seleccione" name="articulos[{{$index}}][ciclo_id]" class="form-control chosen-select" data-rule-required="true" v-model="articulo.ciclo_id" :disabled="disabledEditar || disabledEditarTabla">
                            <option value="">Seleccione</option>
                            <option value="{{ciclo.id}}" v-for="ciclo in ciclos_tarifarios">{{ciclo.nombre}}</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" name="articulos[{{$index}}][tarifa]" class="form-control" data-rule-required="true" data-inputmask="'mask':'9{1,8}[.*{1,2}]','greedy':false" v-model="articulo.tarifa" :disabled="disabledEditar || disabledEditarTabla">
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-default" v-if="$index===0" @click="addRow($event)" :disabled="disabledEditar || disabledEditarTabla"><li class="fa fa-plus"></li></button>
                        <button class="btn btn-default" v-if="$index!==0" @click="removeRow($index, $event)" :disabled="disabledEditar || disabledEditarTabla"><li class="fa fa-trash"></li></button>
                    </td>
                </tr>
            </tbody>

        </table>
        
    </div>
</template>
