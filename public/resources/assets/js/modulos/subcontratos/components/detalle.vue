<template>

      <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
          <label for="proveedor_id">Proveedor <span required="" aria-required="true">*</span></label>
          <select name="campo[proveedor_id]" id="proveedor_id" class="form-control" data-rule-required="true" v-select2="detalle.proveedor_id" :config="config.select2" :disabled="config.disableDetalle">
              <option value="">Seleccione</option>
              <option :value="proveedor.proveedor_id" v-for="proveedor in catalogos.proveedores" v-html="proveedor.nombre"></option>
          </select>
          <label id="proveedor_id-error" class="error" for="proveedor_id"></label>
      </div>

      <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
          <label for="proveedor_id">Tipo de subcontrato <span required="" aria-required="true">*</span></label>
          <select name="campo[tipo_subcontrato_id]" id="tipo_subcontrato_id" class="form-control" data-rule-required="true" v-select2="detalle.tipo_subcontrato_id" :config="config.select2" :disabled="config.disableDetalle">
              <option value="">Seleccione</option>
              <option :value="tipo.id" v-for="tipo in catalogos.tipos_subcontratos" v-html="tipo.nombre"></option>
          </select>
          <label id="tipo_subcontrato_id-error" class="error" for="tipo_subcontrato_id"></label>
      </div>

      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
          <label for="fecha_inicio">Fecha de inicio <span required="" aria-required="true">*</span></label>
          <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
              <input type="text" name="campo[fecha_inicio]" id="fecha_inicio" class="form-control"  data-rule-required="true" v-datepicker="detalle.fecha_inicio" :config="config.fecha_inicio" :disabled="config.disableDetalle">
          </div>
          <label id="fecha_inicio-error" class="error" for="fecha_inicio"></label>
      </div>

      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
          <label for="fecha_final">Fecha de fin <span required="" aria-required="true">*</span></label>
          <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-calendar-plus-o"></i></span>
              <input type="text" name="campo[fecha_final]" id="fecha_final" class="form-control"  data-rule-required="true" v-datepicker="detalle.fecha_final" :config="config.fecha_final" :disabled="config.disableDetalle">
          </div>
          <label id="fecha_fin-error" class="error" for="fecha_final"></label>
      </div>

      <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
          <label for="centro_id">Centro Contable <span required="" aria-required="true">*</span></label>
          <select name="campo[centro_id]" class="form-control" data-rule-required="true" v-select2="detalle.centro_id" :config="config.select2" :disabled="config.disableDetalle">
              <option value="">Seleccione</option>
              <option :value="centro_contable.centro_contable_id" v-for="centro_contable in catalogos.centros_contables" v-html="centro_contable.nombre"></option>
          </select>
          <label id="centro_id-error" class="error" for="centro_id"></label>
      </div>

      <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
          <label for="referencia">Nombre de referencia <span required="" aria-required="true">*</span></label>
          <input type="text"  name="campo[referencia]"  class="form-control"  id="referencia" data-rule-required="true" v-model="detalle.referencia" :disabled="config.disableDetalle">
          <label id="referencia-error" class="error" for="referencia"></label>
      </div>

      <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
          <label for="estado">Estado <span required="" aria-required="true">*</span></label>
          <select name="campo[estado]" class="form-control" data-rule-required="true" v-select2="detalle.estado" :config="config.select2" :disabled="config.disableDetalle || config.vista == 'crear'">
              <option value="">Seleccione</option>
              <option :value="estado.etiqueta" v-for="estado in getEstados" v-html="estado.valor"></option>
          </select>
      </div>

      <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" v-show="false">
          <label for="numero_subcontrato">Número de Subcontrato <span required="" aria-required="true">*</span></label>
          <input type="text" disabled  class="form-control"  id="campo[codigo]" value="">
          <label id="numero_subcontrato-error" class="error" for="numero_subcontrato"></label>
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

    },

    computed:{

        getEstados:function(){

            var context = this;
            if(context.detalle.estado == 'por_aprobar')
            {
                return _.filter(context.catalogos.estados, function(estado){return estado.etiqueta == 'por_aprobar' || estado.etiqueta == 'vigente' || estado.etiqueta == 'anulado';});
            }
            else if(context.detalle.estado == 'vigente')
            {
                return _.filter(context.catalogos.estados, function(estado){return estado.etiqueta == 'vigente' || estado.etiqueta == 'terminado' || estado.etiqueta == 'anulado';});
            }
            return context.catalogos.estados;

        },

    }

}

</script>
