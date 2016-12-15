<template>

    <div class="row">

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Centro contable <span aria-required="true" required="">*</span></label>
            <select name="campo[centro_id]" class="centro" aria-required="true" data-rule-required="true" v-select2="detalle.centro_id" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="centro_contable.id" v-for="centro_contable in catalogos.centros_contables">{{centro_contable.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Bodega <span aria-required="true" required="">*</span></label>
            <select name="campo[uuid_bodega]" class="bodega" id="bodega" aria-required="true" data-rule-required="true" v-select2="detalle.uuid_bodega" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="bodega.id" v-for="bodega in catalogos.bodegas">{{bodega.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Tipo de ajuste <span aria-required="true" required="">*</span></label>
            <select name="campo[tipo_ajuste_id]" class="tipo_ajuste" aria-required="true" data-rule-required="true" v-select2="detalle.tipo_ajuste_id" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="tipo.id_cat" v-for="tipo in catalogos.tipos_ajustes">{{tipo.etiqueta}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Estado <span aria-required="true" required="">*</span></label>
            <select name="campo[estado_id]" class="estado" aria-required="true" data-rule-required="true" v-select2="detalle.estado_id" :config="config.select2" :disabled="config.disableDetalle || config.vista == 'crear'">
                <option value="">Seleccione</option>
                <option :value="estado.id_cat" v-for="estado in catalogos.estados">{{estado.etiqueta}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 " style="clear:both;">
            <label>Fecha <span aria-required="true" required="">*</span></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="campo[created_at]" value="{{detalle.created_at}}" class="form-control" disabled="true" aria-required="true" data-rule-required="true">
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Raz&oacute;n </label>
            <select name="campo[razon_id]" class="razon_id" v-select2="detalle.razon_id" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="razon.id" v-for="razon in catalogos.razones">{{razon.nombre}}</option>
            </select>
        </div>

    </div>

</template>

<script>

export default {

  props:{

        config: Object,
        detalle: Object,
        catalogos: Object

    },

    data:function(){

        return {};

    }

}

</script>
