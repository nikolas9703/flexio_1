<template id="items_alquiler_adicionales">

    <table class="table table-noline tabla-dinamica">
        <thead>
            <tr>
                <th v-for="columna in columnas" width="{{columna.width}}%" colspan="{{columna.colspan}}">{{{columna.nombre}}}</th>
            </tr>
        </thead>
        <tbody :id="'itemventa' + $index" v-for="item in items" track-by="$index">
            <tr>
                <td style="width: 1%;">
                    <h3><a hrfe="#" @click="toggle($event)"><i class="fa fa-caret-right"></i></a></h3>
                </td>
                <td>
                    <select name="items_adicionales[{{$index}}][categoria_id]" id="categoriaadicional_id{{$index}}" :data-rule-required="cargos_adicionales_checked=='true' ? true: false" v-model="item.categoria_id" class="chosen-select form-control" @change="popularItems($event, $index, item)" :disabled="item.id != ''">
                            <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in categorias">{{option.nombre}}</option>
                    </select>
                </td>
                <td>
                    <input  type="hidden"  id="comentario{{$index}}" name="items_adicionales[{{$index}}][comentario]" value="{{item.comentario}}">
                    <div class="input-group">
                        <select id="itemadicional_id{{$index}}" name="items_adicionales[{{$index}}][item_id]" :data-rule-required="cargos_adicionales_checked=='true' ? true: false" v-model="item.item_id" class="form-control" @change="popularUnidadAtributo($event, item, $index)" :disabled="item.id != '' || item.categoria_id == '' && item.itemsList.length == 0">
                                <option value="">Seleccione</option>
                            <option value="{{option.id}}" v-for="option in item.itemsList">{{option.nombre}}</option>
                        </select>
                        <span class="input-group-btn">
                            <a id="botonadicional{{$index}}" type="button" class="btn btn-default" rel=popover v-item-comentario="item.comentario"  :i="$index" :comentado="item.comentario"> <span class="fa fa-comment"></span></a>
                        </span>
                    </div>
                </td>
                <td>
                    <input type="text" name="items_adicionales[{{$index}}][atributo_text]" class="form-control atributo" id="atributoadicional_text{{$index}}" v-if="item.atributos.length == 0" v-model="item.atributo_text" :disabled="item.id != ''">
                    <select id="atributoadicional_id{{$index}}" name="items_adicionales[{{$index}}][atributo_id]" v-model="item.atributo_id" class="form-control" v-if="item.atributos.length > 0" :disabled="item.id != '' || item.atributos.length==0">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in item.atributos">{{option.nombre}}</option>
                    </select>
                </td>
                <td>
                    <input type="text" id="cantidadadicional{{$index}}" name="items_adicionales[{{$index}}][cantidad]" class="cantidad-item form-control" v-model="item.cantidad" @blur="calcularPrecioTotal($event, $index)" :data-rule-required="cargos_adicionales_checked=='true' ? true: false" :disabled="item.id != ''" @keyup="calcularPrecioTotal($index)" v-inputmask="item.cantidad" :config="{'mask':'9{1,8}[.9{0,4}]','greedy':false}"/>
                </td>
                <td>
                    <select id="unidadadicional_id{{$index}}" name="items_adicionales[{{$index}}][unidad_id]" :data-rule-required="cargos_adicionales_checked=='true' ? true: false" class="unidad-item form-control" v-model="item.unidad_id" :disabled="item.id !='' || item.item_id=='' && item.unidades.length==0" @change.prevent="calcularPrecioSegunUnidad($event, $index, item)">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in item.unidades">{{option.nombre}}</option>
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" id="precio_unidadadicional{{$index}}" name="items_adicionales[{{$index}}][precio_unidad]" v-model="item.precio_unidad" class="form-control precio_unidad" disabled="disabled"/>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" id="precio_totaladicional{{$index}}" name="items_adicionales[{{$index}}][precio_total]" v-model="item.precio_total" class="form-control precio_total" disabled="disabled"/>
                    </div>
                </td>
                <td v-show="$index>0 && factura.id=='' && item.id=='' || factura.id !='' && factura.uuid_venta!=''">
                    <button agrupador="items" class="btn btn-default btn-block eliminarItemBtn" type="button" @click="eliminarItemOrden($index, $event)">
                        <i class="fa fa-trash"></i> <span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp; Eliminar</span>
                    </button>
                </td>
                <td v-show="$index==0">
                    <button class="btn btn-default btn-block agregarItemBtn" type="button" @click="agregarItemOrden($event)" :disabled="item.id != ''">
                        <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp; Agregar</span>
                    </button>
                </td>
            </tr>
            <tr id="itemsDatos{{$index}}">
                <td colspan="9" class="hide">
                    <table style="width: 100%;background-color: #A2C0DA">
                        <tbody>
                            <tr>
                                <td style="padding: 15px !important;" width="33%">
                                    <b>Impuesto</b>
                                    <!-- change="impuestoSeleccionado(item.impuesto,$index)" -->
                                    <select id="impuesto_id{{$index}}" name="items_adicionales[{{$index}}][impuesto_id]" v-model="item.impuesto_uuid" class="form-control item-impuesto" :data-rule-required="cargos_adicionales_checked=='true' ? true: false" :disabled="item.id != ''">
                                            <option value="">Seleccione</option>
                                        <option value="{{option.uuid}}" v-for="option in item.impuestos">{{option.nombre}}</option>
                                    </select>
                                </td>
                                <td style="padding: 15px !important;" width="33%">
                                    <b>Descuento</b>
                                    <div class="input-group">
                                        <!-- ng-blur="descuentoCambio(item.descuento,$index)" -->
                                        <input type="text" id="descuento{{$index}}" v-model="item.descuento" name="items_adicionales[{{$index}}][descuento]" class="form-control item-descuent" :data-rule-required="cargos_adicionales_checked=='true' ? true: false" data-inputmask="'mask':'9{1,3}[.*{1,2}]'" data-rule-range="[0,100]" :disabled="item.id != ''">
                                               <span class="input-group-addon">%</span>
                                    </div>
                                </td>
                                <td style="padding: 15px !important;" width="33%">
                                    <b>Cuenta</b>
                                    <select id="cuenta_id0" name="items_adicionales[{{$index}}][cuenta_id]" :data-rule-required="cargos_adicionales_checked=='true' ? true: false" v-model="item.cuenta_uuid" class="form-control" :disabled="item.id != ''">
                                            <option value="">Seleccione</option>
                                        <option value="{{option.uuid}}" v-for="option in item.cuenta_transaccionales">{{option.nombre}}</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        <input type="hidden" id="item_id{{$index}}" name="items_adicionales[{{$index}}][factura_item_id]" :disable="factura_id===''" v-model="item.id" />
        </tbody>
    </table>

</template>
