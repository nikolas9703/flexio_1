
<template id="articulo_template">
    {{{row | json}}}
    <tr :style="(row.facturado || config.vista == 'crear') ? 'background:white;' : 'background:orange;'">

        <td style='background: white;' v-if="precioCompra() || precioVenta()">
            <i class="fa" :class="fa_caret" style="font-size: 28px;width: 10px;" @click="changeCaret"></i>
        </td>

        <td class="categoria{{parent_index}} ">
            <select name="items[{{parent_index}}][categoria]" class="categoria" id="categoria{{parent_index}}" data-rule-required="true" aria-required="true" v-model="row.categoria_id" v-select2="row.categoria_id" :config="config.select2" :disabled="config.disableArticulos || disabledArticulo">
                <option value="">Seleccione</option>
                <option :value="categoria.id" v-for="categoria in catalogos.categorias">{{categoria.nombre}}</option>
            </select>
        </td>

        <td class="item{{parent_index}} ">
            <input type="hidden" name="items[{{parent_index}}][item_hidden]" class="item_hidden" id="item_hidden{{parent_index}}" v-model="row.item_hidden_id">
            <select name="items[{{parent_index}}][item]" class="item" id="item{{parent_index}}" data-rule-required="true" aria-required="true" v-model="row.item_id" v-select2="row.item_id" :config="config.select2" :disabled="config.disableArticulos || disabledArticulo">
                <option value="">Seleccione</option>
                <option :value="item.id" v-for="item in row.items">{{item.nombre}}</option>
            </select>
        </td>

        <td class="atributo{{parent_index}} ">
            <input type="text" name="items[{{parent_index}}][atributo_text]" class="form-control atributo" id="atributo_text{{parent_index}}" v-if="row.atributos.length == 0" v-model="row.atributo_text" :disabled="config.disableArticulos || disabledArticulo">
            <select name="items[{{parent_index}}][atributo_id]" class="atributo" id="atributo_id{{parent_index}}" v-if="row.atributos.length > 0" v-model="row.atributo_id" v-select2="row.atributo_id" :config="config.select2" :disabled="config.disableArticulos || disabledArticulo">
                <option value="">Seleccione</option>
                <option :value="atributo.id" v-for="atributo in row.atributos">{{atributo.nombre}}</option>
            </select>
        </td>

        <td class="cantidad{{parent_index}}  input-group">
            <input type="text" name="items[{{parent_index}}][cantidad]" class="form-control cantidad valid" data-rule-required="true" aria-required="true" id="cantidad{{parent_index}}" aria-required="true" v-model="row.cantidad" v-inputmask="row.cantidad" :config="config.inputmask.currency2" :disabled="config.disableArticulos || disabledArticulo">
            <span class="input-group-addon cantidad_info" style="background-color:#27AAE1;color:white;border:1px solid #27AAE1" v-pop_over_cantidad=""><i class="fa fa-info"></i></span>
        </td>

        <td class="unidad{{parent_index}} ">
            <input type="hidden" name="items[{{parent_index}}][unidad_hidden]" class="unidad_hidden" id="unidad_hidden{{parent_index}}" v-model="row.unidad_hidden_id">
            <select name="items[{{parent_index}}][unidad]" class="unidad" id="unidad{{parent_index}}" data-rule-required="true" aria-required="true" v-model="row.unidad_id" v-select2="row.unidad_id" :config="config.select2" :disabled="config.disableArticulos || disabledArticulo">
                <option value="">Seleccione</option>
                <option :value="unidad.id" v-for="unidad in row.unidades">{{unidad.nombre}}</option>
            </select>
        </td>

        <td class="precio_unidad{{parent_index}} " v-if="precioCompra() || precioVenta()">
            <div class="input-group" v-if="precioCompra()">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input
                    type="text"
                    name="items[{{parent_index}}][precio_unidad]"
                    class="form-control precio_unidad valid"
                    data-rule-required="true"
                    aria-required="true"
                    id="precio_unidad{{parent_index}}"
                    agrupador="items"
                    aria-required="true"
                    aria-invalid="false"
                    v-model="row.precio_unidad"
                    v-inputmask="row.precio_unidad"
                    :config="config.inputmask.currency2"
                    :disabled="config.disableArticulos || disabledArticulo">
                <span class="input-group-addon precio_unidad_info" style="background-color:#27AAE1;color:white;border:1px solid #27AAE1" v-pop_over_precio=""><i class="fa fa-info"></i></span>
            </div>
            <div class="input-group" v-if="precioVenta()">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input
                    type="text"
                    name="items[{{parent_index}}][precio_unidad]"
                    class="form-control precio_unidad valid"
                    data-rule-required="true"
                    aria-required="true"
                    value="{{getPrecioUnidad | currency ''}}"
                    :disabled="true">
            </div>
        </td>

        <td class="precio_total{{parent_index}} " v-if="precioCompra() || precioVenta()">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input type="text" name="items[{{parent_index}}][precio_total]" value="{{getSubtotal | currency ''}}" class="form-control precio_total" disabled="true" id="precio_total{{parent_index}}" agrupador="items">
                <input type="hidden" name="items[{{parent_index}}][impuesto_total]" value="{{getImpuestoTotal | currency ''}}" class="form-control impuesto_total" id="impuesto_total{{parent_index}}">
                <input type="hidden" name="items[{{parent_index}}][descuento_total]" value="{{getDescuentoTotal | currency ''}}" class="form-control descuento_total" id="descuento_total{{parent_index}}" >
                <input type="hidden" name="items[{{parent_index}}][retenido_total]" value="{{getRetenidoTotal | currency ''}}" class="form-control retenido_total" id="retenido_total{{parent_index}}" >
            </div>
        </td>

        <td class="cuenta{{parent_index}}" v-if="!( precioCompra() || precioVenta() )">
            <select name="items[{{parent_index}}][cuenta]" class="cuenta" id="cuenta{{parent_index}}" data-rule-required="true" aria-required="true" v-model="row.cuenta_id" v-select2="row.cuenta_id" :config="config.select2" :disabled="config.disableArticulos || disabledArticulo">
                <option value="">Seleccione</option>
                <option :value="cuenta.id" v-for="cuenta in catalogos.cuentas">{{cuenta.codigo +' '+ cuenta.nombre}}</option>
            </select>
        </td>

        <td style="background: white;">
            <button type="button" class="btn btn-default btn-block agregarBtn" agrupador="items" label="<i class=&quot;fa fa-plus&quot;></i>" v-if="parent_index=='0'" @click="addRow()" :disabled="config.disableArticulos || disabledArticulo"><i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-default btn-block eliminarBtn" agrupador="items" label="<i class=&quot;fa fa-trash&quot;></i>" v-if="parent_index!='0'" @click="removeRow(row)" :disabled="config.disableArticulos || disabledArticulo"><i class="fa fa-trash"></i></button>
            <input type="hidden" name="items[{{parent_index}}][id_pedido_item]" value="{{row.id}}" class="form-control" id="id_pedido_item">
        </td>

    </tr>

    <tr v-show="fa_caret == 'fa-caret-down'" v-if="precioCompra() || precioVenta()">
        <td></td>
        <td colspan="7">
            <table style="width: 100%;background: #A2C0DA;">

                <td class="impuesto{{parent_index}}" width="33%" style="padding: 10px;">
                    <label>Impuesto</label>
                    <select name="items[{{parent_index}}][impuesto]" class="impuesto" id="impuesto{{parent_index}}" data-rule-required="true" aria-required="true" v-model="row.impuesto_id" v-select2="row.impuesto_id" :config="config.select2" :disabled="config.disableArticulos || disabledArticulo">
                        <option value="">Seleccione</option>
                        <option :value="impuesto.id" v-for="impuesto in catalogos.impuestos">{{impuesto.nombre}}</option>
                    </select>
                </td>

                <td class="descuento{{parent_index}}" width="33%" style="padding: 10px;">
                    <label>Descuento</label>
                    <div class="input-group" style="width: 100%;">
                        <input type="input-right-addon" name="items[{{parent_index}}][descuento]" class="form-control descuento" id="descuento{{parent_index}}" agrupador="items" v-model="row.descuento" v-inputmask="row.descuento" :config="config.inputmask.descuento" :disabled="config.disableArticulos || disabledArticulo">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>

                <td class="cuenta{{parent_index}}" width="33%" style="padding: 10px;">
                    <label>{{config.vista == 'crear' ? 'Cuenta' : (precioCompra() ? 'Cuenta de gastos' : 'Cuenta de ingresos')}}</label>
                    <select name="items[{{parent_index}}][cuenta]" class="cuenta" id="cuenta{{parent_index}}" data-rule-required="true" aria-required="true" v-model="row.cuenta_id" v-select2="row.cuenta_id" :config="config.select2" :disabled="config.disableArticulos || disabledArticulo">
                        <option value="">Seleccione</option>
                        <option :value="cuenta.id" v-for="cuenta in catalogos.cuentas">{{cuenta.codigo +' '+ cuenta.nombre}}</option>
                    </select>
                </td>

            </table>
        </td>
        <td></td>
    </tr>

</template>
