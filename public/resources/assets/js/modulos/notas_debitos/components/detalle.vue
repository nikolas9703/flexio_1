<template>

    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="proveedor_id">Proveedor <span required="" aria-required="true">*</span></label>
            <select name="campo[proveedor_id]" class="form-control" data-rule-required="true" v-select2="detalle.proveedor_id" :config="config.select2" :disabled="config.disableDetalle || empezable.type != ''">
              <option value="">Seleccione</option>
              <option :value="proveedor.proveedor_id" v-for="proveedor in catalogos.proveedores">{{proveedor.nombre}}</option>
            </select>
            <label id="proveedor_id-error" class="error" for="proveedor_id"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
          <label for="monto_factura">Monto de la factura</label>
          <div class="input-group">
              <span class="input-group-addon">$</span>
              <input type="input" disabled name="campo[monto_factura]" :value="detalle.monto_factura | currency ''" class="form-control">
          </div>
          <label id="termino_pago-error" class="error" for="termino_pago"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3"><label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="input-left-addon" disabled value="{{detalle.saldo_proveedor | currency ''}}"   class="form-control debito">
             </div>
             <label class="label-danger-text">Saldo por pagar</label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="input-left-addon" disabled  value="{{detalle.credito_proveedor | currency ''}}" class="form-control debito">
            </div>
            <label class="label-success-text">Crédito a favor</label>
        </div>

    </div>

    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="fecha_desde">Fecha de factura <span required="" aria-required="true">*</span></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="campo[fecha_factura]" class="form-control" data-rule-required="true" v-model="detalle.fecha_factura" disabled>
            </div>
            <label id="fecha_desde-error" class="error" for="fecha_desde"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
          <label for="fecha">Fecha de nota de d&eacute;bito <span required="" aria-required="true">*</span></label>
          <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
              <input type="text" name="campo[fecha]" class="form-control" data-rule-required="true" v-datepicker="detalle.fecha" :config="config.datepicker2" :disabled="config.disableDetalle">
          </div>
          <label id="fecha-error" class="error" for="fecha"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
          <label for="centro_contable_id">Centro Contable <span required="" aria-required="true">*</span></label>
          <select name="campo[centro_contable_id]" class="form-control" data-rule-required="true" v-select2="detalle.centro_contable_id" :config="config.select2" :disabled="config.disableDetalle">
              <option value="">Seleccione</option>
              <option :value="centro_contable.id" v-for="centro_contable in catalogos.centros_contables">{{centro_contable.nombre}}</option>
          </select>
          <label id="item_precio_id-error" class="error" for="item_precio_id"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
          <label>Creado por <span required="" aria-required="true">*</span></label>
          <select name="campo[creado_por]" class="form-control" data-rule-required="true" v-select2="detalle.creado_por" :config="config.select2" :disabled="true">
            <option value="">Seleccione</option>
            <option :value="usuario.id" v-for="usuario in catalogos.usuarios">{{{ usuario.nombre +" "+ usuario.apellido}}}</option>
          </select>
          <label id="vendedor-error" class="error" for="vendedor"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 " style="clear:both;">
            <label>No. de Nota de cr&eacute;dito <span required="" aria-required="true">*</span></label>
            <input type="text" data-rule-required="true" name="campo[no_nota_credito]" class="form-control no_nota_credito" v-model="detalle.no_nota_credito" :disabled="config.disableDetalle">
            <label id="no_nota_credito-error" class="error" for="no_nota_credito"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
              <label>Estado <span required="" aria-required="true">*</span></label>
              <select name="campo[estado]" class="form-control" data-rule-required="true" v-select2="detalle.estado" :config="config.select2" :disabled="config.vista == 'crear' || config.disableDetalle">
                  <option value="">Seleccione</option>
                  <option :value="estado.etiqueta" v-for="estado in getEstados">{{estado.valor}}</option>
              </select>
              <label id="estado-error" class="error" for="estado"></label>
        </div>

    </div>

</template>

<script>

export default {

  props:{

        config: Object,
        detalle: Object,
        catalogos: Object,
        empezable: Object

    },

    data:function(){

        return {};

    },

    computed:{

          getEstados: function (){

              var context = this;
              if(context.config.vista == 'editar')
              {
                  var nota_debito = JSON.parse(JSON.stringify(window.nota_debito));
                  if(pago.estado == 'por_aprobar')
                  {
                      return _.filter(context.catalogos.estados, function(estado){return estado.etiqueta == 'por_aprobar' || estado.etiqueta == 'aprobado' || estado.etiqueta == 'anulado';});
                  }
                  else if(pago.estado == 'aprobado')
                  {
                      return _.filter(context.catalogos.estados, function(estado){return estado.etiqueta == 'aprobado' || estado.etiqueta == 'anulado';});
                  }
              }
              return context.catalogos.estados;

          }

    },

    watch:{

          'detalle.proveedor_id':function(val, oldVal){


              if(val == ''){
                  this.detalle.saldo_proveedor = 0;
                  this.detalle.credito_proveedor = 0;
                  return '';
              }

              var context = this;
              var datos = $.extend({erptkn: tkn},{proveedor_id:val});
              this.$http.post({
                  url: window.phost() + "proveedores/ajax-get-montos",
                  method:'POST',
                  data:datos
              }).then(function(response){

                  if(_.has(response.data, 'session')){
                      window.location.assign(window.phost());
                      return;
                  }
                  if(!_.isEmpty(response.data)){

                      context.detalle.saldo_proveedor = response.data.saldo;
                      context.detalle.credito_proveedor = response.data.credito;

                  }
              });

          }

      },

}

</script>
