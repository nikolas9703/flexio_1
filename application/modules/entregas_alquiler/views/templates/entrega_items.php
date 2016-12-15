
<template id="entrega_items">
    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <!-- <div style="display:table-cell;"> -->
        <div id="entregasAlquilerItemsErros" class="error"></div>
        <table class="table" id="entregasAlquilerItems" v-show="articulos.length > 0">
            <thead>
                <tr>
                    <td width="1%" style="border:1px solid white"></td>
                    <th width="14%">Categor&iacute;a del &iacute;tem</th>
                    <th width="14%">&Iacute;tem a entregar</th>
                    <th width="14%">Atributo</th>
                    <th width="14%">Periodo tarifario</th>
                    <th width="14%">Tarifa por periodo</th>
                    <th width="1%" style="background-color: white;"></th>
                </tr>
            </thead>
            <tbody v-for="articulo in articulos" track-by="$index" style="border: 1px solid white;">
                <tr class="item-listing" :id="'entrega_item' + $index">
                    <td style="border:1px solid white">
                        <span style="font-size: 30px;" @click="cambiarCaret(articulo)"><i style='width: 10px;' class="fa" :class="articulo.caret"></i></span>
                    </td>
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

                        <select v-if="articulo.atributos.length > 0" data-placeholder="Seleccione" name="articulos[{{$index}}][atributo_id]" class="form-control" v-model="articulo.atributo_id" :disabled="disabledEditar || disabledEditarTabla">
                            <option value="">Seleccione</option>
                            <option value="{{atributo.id}}" v-for="atributo in articulo.atributos | orderBy 'nombre'">{{atributo.nombre}}</option>
                        </select>

                        <input v-if="articulo.atributos.length == 0" type="text" name="articulos[{{$index}}][atributo_text]" class="form-control" v-model="articulo.atributo_text" :disabled="disabledEditar || disabledEditarTabla">

                    </td>
                    <td>
                        <select data-placeholder="Seleccione" name="articulos[{{$index}}][ciclo_id]" class="form-control chosen-select" data-rule-required="true" v-model="articulo.ciclo_id" :disabled="disabledEditar || disabledEditarTabla">
                            <option value="">Seleccione</option>
                            <option value="{{ciclo.id}}" v-for="ciclo in ciclos_tarifarios">{{{ciclo.nombre}}}</option>
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
                        <button class="btn btn-default" v-if="$index!==0" @click="removeRow($index, $event)" :disabled="disabledEditar"><li class="fa fa-trash"></li></button>
                    </td>
                </tr>
                <tr v-show="articulo.caret == 'fa-caret-down'">
                    <td style="border:1px solid white"></td>
                    <td colspan="5">
                        <entrega_item v-bind:parent_index="$index" v-bind:parent_articulo="articulo"></entrega_item>
                    </td>
                    <td></td>
                </tr>
            </tbody>

        </table>



    </div>
</template>
