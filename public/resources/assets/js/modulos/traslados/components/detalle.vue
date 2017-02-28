<template>

    <div class="row" style="margin-right: 0px;">
        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="fecha_solicitud">Fecha de solicitud <span required="" aria-required="true">*</span></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="campo[fecha_creacion]" class="form-control" aria-required="true" data-rule-required="true" v-datepicker="detalle.fecha_creacion" :config="config.datepicker2" :disabled="config.disableDetalle">
            </div>
            <label id="fecha_solicitud-error" class="error" for="fecha_solicitud"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="de_bodega">De bodega <span required="" aria-required="true">*</span></label>
            <select name="campo[uuid_lugar_anterior]" class="" aria-required="true" data-rule-required="true" v-select2="detalle.uuid_lugar_anterior" :config="config.select2" :disabled="config.disableDetalle">
              <option value="">Seleccione</option>
              <option :value="row.id" v-for="row in getBodegasOrigen" v-html="row.nombre"></option>
            </select>
            <label id="de_bodega-error" class="error" for="de_bodega"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="a_bodega">A bodega <span required="" aria-required="true">*</span></label>
            <select name="campo[uuid_lugar]" class="" aria-required="true" data-rule-required="true" v-select2="detalle.uuid_lugar" :config="config.select2" :disabled="config.disableDetalle || empezable.id != ''">
              <option value="">Seleccione</option>
              <option :value="row.id" v-for="row in getBodegasDestino" v-html="row.nombre"></option>
            </select>
            <label id="a_bodega-error" class="error" for="a_bodega"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="fecha_entrega">Fecha de entrega</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="campo[fecha_entrega]" class="form-control" v-model="detalle.fecha_entrega" :disabled="true">
            </div>
            <label id="fecha_solicitud-error" class="error" for="fecha_solicitud"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3" style="clear:both;">
            <label>Estado <span aria-required="true" required="">*</span></label>
            <select name="campo[id_estado]" class="estado" aria-required="true" data-rule-required="true" v-select2="detalle.id_estado" :config="config.select2" :disabled="config.vista == 'crear' || config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="estado.id_cat" v-for="estado in catalogos.estados">{{estado.etiqueta}}</option>
            </select>
        </div>

    </div>

</template>

<script>

export default {

  props:{

        config: Object,
        empezable: Object,
        detalle: Object,
        catalogos: Object

    },

    data:function(){

        return {};

    },

    computed:{

        getBodegasOrigen: function(){

            var context = this;
            return _.filter(context.catalogos.bodegas, function(o){
                return o.id != context.detalle.uuid_lugar;
            });

        },

        getBodegasDestino: function(){

            var context = this;
            return _.filter(context.catalogos.bodegas, function(o){
                return o.id != context.detalle.uuid_lugar_anterior;
            });

        }

    }
}

</script>
