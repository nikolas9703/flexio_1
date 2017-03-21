
<template id="entrega_item">


    <table class="table" :id="'entrega_item_detalles' + parent_index" style="background: #DEEBF7">

        <tbody style="border: 1px solid white;">
            <tr class="item-listing" v-for="articulo in articulos" track-by="$index" :id="'entrega_item' + $index">
                <td width="14%" v-show="articulo.serializable == true">
                    <lista-seriales :subfila.sync="articulo" :parent_articulo.sync="parent_articulo" :parent_articulos.sync="parent_articulos" :parent_index="parent_index" :subparent_index="$index"></lista-seriales>
                </td>
                <td width="14%" v-show="articulo.serializable != true">
                    <div class="form-group" style="margin-bottom: 5px !important">
                        <label class="control-label">Cantidad: </label>
                        <input type="text" name="articulos[{{parent_index}}][detalles][{{$index}}][cantidad]" class="form-control" data-rule-required="true" v-model="articulo.cantidad" :disabled="disabledEditar || disabledEditarTabla" @change="cambiarCantidad">
                    </div>
                </td>
                <td width="14%">
                    <div class="form-group" style="margin-bottom: 5px !important">
                        <label class="control-label">Bodega de entrega: </label>
                        <select data-placeholder="Seleccione" name="articulos[{{parent_index}}][detalles][{{$index}}][bodega_id]" class="form-control chosen-select" data-rule-required="true" v-model="articulo.bodega_id" :disabled="disabledEditar || disabledEditarTabla">
                            <option value="">Seleccione</option>
                            <option value="{{bodega.id}}" v-for="bodega in bodegas | orderBy 'nombre'" v-if="articulo.serializable == false || articulo.serializable == true || vista=='editar'">{{bodega.nombre}}</option>
                        </select>
                    </div>
                </td>
                <td width="14%">
                    <div class="form-group" style="margin-bottom: 5px !important">
                        <label class="control-label">Fecha estimada de devoluci&oacute;n: </label>
                        <div class="input-group" style="width: 100%;">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="articulos[{{parent_index}}][detalles][{{$index}}][fecha]" class="form-control fecha" data-rule-required="true" v-model="articulo.fecha_devolucion_estimada" :disabled="disabledEditar || disabledEditarTabla">
                        </div>
                    </div>
                </td>
                <td width="1%" style="text-align: center;padding-top: 30px;">
                    <button class="btn btn-default" v-if="$index===0" @click="addRow($event)" :disabled="disabledEditar || disabledEditarTabla || articulo.disabledAddRow"><li class="fa fa-plus"></li></button>
                    <button class="btn btn-default" v-if="$index!==0" @click="removeRow($index, $event)" :disabled="disabledEditar || disabledEditarTabla"><li class="fa fa-trash"></li></button>
                </td>
            </tr>

        </tbody>

    </table>


</template>
