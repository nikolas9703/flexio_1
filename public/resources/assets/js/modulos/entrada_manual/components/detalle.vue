<template>

    <div class="row">
        <div :class="isMovimientoMonetario() ? 'col-md-3' : 'col-md-6'">
            <label>Narración</label>
            <input type="text" name="campo[nombre]" class="form-control" aria-required="true" data-rule-required="true" v-model="detalle.nombre" :disabled="config.disableDetalle">
            <input type="hidden" name="modulo" v-model="config.modulo" v-if="isMovimientoMonetario">
        </div>
        <div class="col-md-3" v-if="isMovimientoMonetario()">
            <label v-html="config.modulo == 'recibo_dinero' ? 'Recibir a cuenta de banco' : 'Retirar de cuenta de banco'"></label>
            <select name="campo[cuenta_id]" aria-required="true" data-rule-required="true"
            v-select2ajax="detalle.cuenta_id" :config="select2cuenta" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
            </select>
        </div>
        <div class="col-md-3" id="data_1">
            <label>Fecha {{getAmbitoLabel()}}</label>
            <div class="input-group date">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="campo[fecha_entrada]" class="form-control" aria-required="true" data-rule-required="true"
                v-datepicker="detalle.fecha_entrada" :config="config.datepicker2" :disabled="config.disableDetalle">
            </div>
        </div>
        <div class="col-md-3">
            <label>Creado por</label>
            <select class="form-control" aria-required="true" data-rule-required="true"
            v-select2ajax="detalle.usuario_id" :config="select2usuario" :disabled="true">
                <option value="">Seleccione</option>
            </select>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6" v-show="config.vista=='crear'">
            <div class="checkbox">
                <label>
                    <input type="checkbox" v-model="detalle.incluir_narracion" @change="incluir_narracion()" :disabled="config.disableDetalle">
                    <strong>Incluir narraci&oacute;n a la descripci&oacute;n {{getAmbitoLabel()}}</strong>
                </label>
            </div>
        </div>
    </div>

</template>

<script>

export default {

  props:{

        config: Object,
        detalle: Object

    },

    data:function(){

        return {
            select2cuenta:{
                ajax:{
                    url: function(params){
                        return phost() + 'contabilidad/ajax_get_cuentas?depositable_type=banco';
                    },
                    data: function (params) {
                        return {
                            q: params.term
                        }
                    }
                }
            },
            select2usuario:{
                ajax:{
                    url: function(params){
                        return phost() + 'usuarios/ajax_get_usuarios';
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
        getAmbitoLabel:function(){
            var context = this;
            if(context.config.modulo == 'recibo_dinero'){
                return ' del recibo de dinero';
            }else if(context.config.modulo == 'retiro_dinero'){
                return ' del retiro de dinero';
            }
            return ' de la entrada manual';
        },
        isMovimientoMonetario: function(){
            var context = this;
            var movimientos_monetarios = ['recibo_dinero', 'retiro_dinero'];
            return movimientos_monetarios.indexOf(context.config.modulo) != -1;
        },
        incluir_narracion:function(){
            var context = this;
            _.forEach(context.detalle.transacciones, function(trans){
                trans.nombre = context.detalle.incluir_narracion ? context.detalle.nombre : '';
            });
        }
    }


}


</script>
