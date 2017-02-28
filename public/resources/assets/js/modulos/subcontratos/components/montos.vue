<template>

    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table class="table table-noline itemsTable" id="prueba">
            <thead>
                <tr>
                    <th width="23%" style="color: white;background: #0076BE;font-weight: bold;text-indent: 6px;">Cuenta <span required="" aria-required="true">*</span></th>
                    <th width="1%" style="color: white;background: #0076BE;font-weight: bold;"></th>
                    <th width="23%" style="color: white;background: #0076BE;font-weight: bold;">Descripci&oacute;n <span required="" aria-required="true">*</span></th>
                    <th width="1%" style="color: white;background: #0076BE;font-weight: bold;"></th>
                    <th width="22.5%" style="color: white;background: #0076BE;font-weight: bold;">Monto (Sin ITBMS) <span required="" aria-required="true">*</span></th>
                    <th width="1%"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in detalle.montos">
                     <td>
 
                      <select class="form-control" name="items[{{$index}}][cuenta_id]" id="items_cuenta_id{{$index}}" data-rule-required="true"  v-select2="item.cuenta_id" :config="config.select2" :disabled="!config.disablePermisoAdenda">
                        <option value="">Seleccione</option>
                        <option :value="cuenta.id" v-for="cuenta in catalogos.cuentas" v-html="cuenta.codigo +' '+cuenta.nombre"></option>
                      </select>
                    </td>
                    <td></td>
                    <td>
                      <input type="text" class="form-control" name="items[{{$index}}][descripcion]" id="items_descripcion{{$index}}" data-rule-required="true" v-model="item.descripcion" :disabled="!config.disablePermisoAdenda">
                    </td>
                    <td></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" id="monto_{{$index}}" v-model="item.monto | currencyDisplay" @change="calcularPorcentajes()" name="items[{{$index}}][monto]" id="items_subcontrato_monto{{$index}}" class="form-control subcontrato_monto" placeholder="0.00" data-rule-required="true" :disabled="!config.disablePermisoAdenda">
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-default btn-block" v-show="$index === 0"  v-on:click="addRow()" data-rule-required="true" agrupador="items" aria-required="true" :disabled="!config.disablePermisoAdenda"><i class="fa fa-plus"></i></button>
                        <button  type="button" v-show="$index !== 0" class="btn btn-default btn-block" data-rule-required="true" agrupador="items" aria-required="true" v-on:click="deleteRow(item)" :disabled="!config.disablePermisoAdenda"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>

                <tr v-if="detalle.monto_adenda > 0">
                    <td colspan="4" style="text-align: right;vertical-align: middle;padding-right: 32px;">
                      Total de adendas
                    </td>

                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" value="{{detalle.monto_adenda | currency ''}}" class="form-control" data-rule-required="true" :disabled="true">
                        </div>
                    </td>
                    <td></td>
                </tr>

                <tr>
                    <td colspan="4" style="text-align: right;vertical-align: middle;padding-right: 32px;">
                      Monto del contrato sin ITBMS
                    </td>

                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" value="{{getMontoSubcontrato | currency ''}}" name="campo[monto_subcontrato]" class="form-control"  placeholder="0.00" data-rule-required="true" :disabled="true">
                        </div>
                    </td>
                    <td></td>
                </tr>

            </tbody>
        </table>
        <div class="tabla_dinamica_error"></div>
        <div id="tablaError">

            <label class="error" style="display:block;" v-if="!validate_montos">El total del abono m&aacute;s el retenido es mayor al monto del contrato.</label>
            <label class="error" style="display:block;" v-if="!validate_porcentajes">El porcentaje total del abono m&aacute;s el retenido es mayor al 100%.</label>

        </div>
    </div>

</template>

<script>

import calcular_movimientos from '../mixins/calcular-movimientos';

export default {

  mixins: [calcular_movimientos],

  props:{

        config: Object,
        detalle: Object,
        catalogos: Object

    },

    data:function(){

        return {};

    },

    methods:{

        addRow:function(){
            this.detalle.montos.push({cuenta_id:'', descripcion: '', monto:''});
        },

        deleteRow:function(row){
            this.detalle.montos.$remove(row);
        }

    }

}

</script>
