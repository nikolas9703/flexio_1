<template>

    <tr>
        <td>
            <input type="text" name="transacciones[{{parent_index}}][nombre]" class="form-control" aria-required="true" data-rule-required="true" v-model="trans.nombre" :disabled="config.disableDetalle">
        </td>
        <td>
            <select name="transacciones[{{parent_index}}][cuenta_id]" aria-required="true" data-rule-required="true" v-select2ajax="trans.cuenta_id" :config="select2cuenta" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
            </select>
        </td>
        <td>
            <select name="transacciones[{{parent_index}}][centro_id]" aria-required="true" data-rule-required="true" v-select2ajax="trans.centro_id" :config="select2centro" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
            </select>
        </td>
        <td v-show="!isMovimientoMonetario() || config.modulo == 'retiro_dinero'">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input type="text" class="form-control" name="transacciones[{{parent_index}}][debito]" aria-required="true" data-rule-required="true"
                v-model="trans.debito | currencyDisplay" :disabled="config.disableDetalle" @change="debitoChanged()">
            </div>
        </td>
        <td v-show="!isMovimientoMonetario() || config.modulo == 'recibo_dinero'">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input type="text" class="form-control" name="transacciones[{{parent_index}}][credito]" aria-required="true" data-rule-required="true"
                v-model="trans.credito | currencyDisplay" :disabled="config.disableDetalle" @change="creditoChanged()">
            </div>
        </td>
        <td>
            <button type="button" class="btn btn-default btn-block-sm" @click="removeRow(trans)" :disabled="config.disableDetalle"><i class="fa fa-trash"></i></button>
            <input type="hidden" name="transacciones[{{parent_index}}][id]" :value="trans.id">
        </td>
    </tr>

</template>

<script>

export default {

  props:{

        config: Object,
        detalle: Object,
        parent_index: Number,
		trans: Object

    },

    data:function(){

        return {
            select2cuenta:{
                ajax:{
                    url: function(params){
                        return phost() + 'contabilidad/ajax_get_cuentas';
                    },
                    data: function (params) {
                        return {
                            q: params.term
                        }
                    }
                }
            },
            select2centro:{
                ajax:{
                    url: function(params){
                        return phost() + 'contabilidad/ajax_get_centros';
                    },
                    data: function (params) {
                        return {
                            q: params.term
                        }
                    }
                }
            }
        };

    },

    methods:{
        isMovimientoMonetario: function(){
            var context = this;
            var movimientos_monetarios = ['recibo_dinero', 'retiro_dinero'];
            return movimientos_monetarios.indexOf(context.config.modulo) != -1;
        },
        debitoChanged: function(){
            var context = this;
            if(context.trans.debito != 0 && context.trans.credito != 0){
                context.trans.credito = 0;
            }
        },
        creditoChanged: function(){
            var context = this;
            if(context.trans.credito != 0 && context.trans.debito != 0){
                context.trans.debito = 0;
            }
        },
        removeRow: function(trasn){
            if(this.detalle.transacciones.length == 1){
                trasn.id = '';
                trasn.nombre = '';
                trasn.cuenta_id = '';
                trasn.centro_id = '';
                trasn.debito = 0;
                trasn.credito = 0;
            }else{
                this.detalle.transacciones.$remove(trasn);
            }
        }
    }

}

</script>
