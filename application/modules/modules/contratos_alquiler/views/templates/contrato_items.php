
<template id="contrato_items">
    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <!-- <div style="display:table-cell;"> -->
        <div id="contratosAlquilerItemsErros" class="error"></div>
        <table class="table" id="contratosAlquilerItems">
            <thead>
                <tr>
                    <th width="12%" colspan="2">Categor&iacute;a del item</th>
                    <th width="30%">Item para alquiler</th>
                    <th width="12%" >Atributo</th>
                    <th width="8%"  >Cantidad</th>
                    <!-- <th width="7%" v-show="vista!='crear'"  class="hide">Contratado</th>
                    <th width="7%" v-show="vista!='crear'"  class="hide">Entregado</th>
                    <th width="7%" v-show="vista!='crear'"  class="hide">Devuelto</th>
                    <th width="7%" v-show="vista!='crear'"  class="hide">En alquiler</th> -->
                    <th width="8%">Periodo tarifario</th>
                    <th width="8%">Tarifa pactada</th>
                    <th width="1%" style="background-color: white;"></th>
                </tr>
            </thead>
            <tbody v-for="articulo in articulos"    track-by="$index" >
                 <tr class="item-listing" :id="'contrato_item' + $index">
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
                        <select data-placeholder="Seleccione" name="articulos[{{$index}}][item_id]" class="form-control chosen-select" data-rule-required="true" v-model="articulo.item_id" @change="cambiarItemAlquiler(articulo, $index)"  cambiarItemAlquiler :disabled="disabledEditar || disabledEditarTabla">
                            <option value="">Seleccione</option>
                            <option value="{{item.id}}" v-for="item in articulo.items | orderBy 'nombre'">{{item.nombre}}</option>
                        </select>
                    </td>
                     <td>
                        <select data-placeholder="Seleccione" name="articulos[{{$index}}][atributo_id]" class="form-control chosen-select"   v-model="articulo.atributo_id" :disabled="disabledEditar || disabledEditarTabla">
                            <option value="">Seleccione</option>
                            <option value="{{atributo.id}}" v-for="atributo in articulo.atributos | orderBy 'nombre'">{{atributo.nombre}}</option>
                        </select>
                    </td>
                    <td >
                        <input type="text" name="articulos[{{$index}}][cantidad]" class="form-control" data-rule-required="true" data-inputmask="'mask':'9{1,4}','greedy':false" v-model="articulo.cantidad" :disabled="disabledEditar || disabledEditarTabla">
                    </td>
                    <!-- <td v-show="vista!='crear'"  class="hide" >
                        <input type="text" class="form-control" disabled="" v-model="articulo.cantidad" >
                    </td>
                    <td v-show="vista!='crear'"  class="hide" >
                        <input type="text" class="form-control" disabled="" v-model="articulo.entregado" >
                    </td>
                    <td v-show="vista!='crear'"  class="hide">
                        <input type="text" class="form-control" disabled="" v-model="articulo.devuelto" >
                    </td>
                    <td v-show="vista!='crear'"  class="hide">
                        <input type="text" class="form-control" disabled="" v-model="articulo.en_alquiler" >
                    </td>-->
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
                        <input type="hidden" name="articulos[{{$index}}][id]" :value="articulo.id">
                    </td>
                </tr>
                
                 <tr v-show="articulo.caret == 'fa-caret-down'" >
                    <td style="border:1px solid white"></td>
                    <td  colspan="6"> 
                        <item_extrainfo v-bind:parent_index="$index"  v-bind:parent_articulo="articulo"></item_extrainfo>
                    </td>
                     
                    <td></td>
                </tr>
            </tbody>

        </table>
        
    </div>
</template>
