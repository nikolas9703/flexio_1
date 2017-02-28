<template>

    <div class="row">
        <div class="col-md-6">
            <label>Narración</label>
            <input type="text" name="campo[nombre]" class="form-control" aria-required="true" data-rule-required="true" v-model="detalle.nombre" :disabled="config.disableDetalle">
        </div>
        <div class="col-md-3" id="data_1">
            <label>Fecha de la entrada manual</label>
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
                    <strong>Incluir narraci&oacute;n a la descripci&oacute;n de la entrada manual</strong>
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
        incluir_narracion:function(){
            var context = this;
            _.forEach(context.detalle.transacciones, function(trans){
                trans.nombre = context.detalle.incluir_narracion ? context.detalle.nombre : '';
            });
        }
    }


}


</script>
