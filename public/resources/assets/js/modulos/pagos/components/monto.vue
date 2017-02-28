<template>

  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <label>Monto</label>
      </div>
      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
          <div class="input-group">
              <span class="input-group-addon">$</span>
              <input type="text" name="campo[monto_pagado]" class="form-control" style="text-align: right;" disabled value="{{getMonto | currency ''}}"/>
          </div>
      </div>
      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <select name="campo[depositable_type]" aria-required="true" data-rule-required="true" v-select2="detalle.depositable_type" :config="config.select2">
          <option value="">Seleccione</option>
          <option :value="tipo_pago.etiqueta" v-for="tipo_pago in catalogos.tipos_pago">{{tipo_pago.valor}}</option>
        </select>
      </div>
      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <select name="campo[depositable_id]" aria-required="true" data-rule-required="true" v-select2ajax="detalle.depositable_id" :config="depositableIdSelect">
          <option value="">Seleccione</option>
          <option :value="depositable.id" v-for="depositable in getDepositables">{{depositable.nombre}}</option>
        </select>
        <label id="cuenta_id-error" class="error" for="cuenta_id"></label>
      </div>

  </div>

</template>

<script>
    export default {

        props: {

            config: Object,
            detalle: Object,
            catalogos: Object

        },

        data: function() {

            var context = this;

            return {
                depositableIdSelect: {
                    ajax: {
                        url: function(params) {
                            return phost() + 'contabilidad/ajax-get-cuentas?depositable_type=' + context.detalle.depositable_type;
                        },
                        data: function(params) {
                            return {
                                q: params.term
                            }
                        }
                    }
                }
            };

        },

        watch: {

            'detalle.depositable_type': function(val, oldVal) {

                console.log('execute: watch -> detalle.depositable_type in monto.vue');
                this.detalle.depositable_id = '';

            }

        },

        computed: {

            getMonto: function() {

                var context = this;
                return _.sumBy(context.detalle.pagables, function(pagable) {
                    return parseFloat(pagable.monto_pagado);
                });

            },

            getDepositables: function() {

                var context = this;
                if (context.detalle.depositable_type == 'banco') {
                    return context.catalogos.cuentas; //son las cuentas de banco
                } else if (context.detalle.depositable_type == 'caja') {
                    return context.catalogos.cajas; //son las cuentas de caja
                } else if (context.detalle.depositable_type == 'cuenta_contable') {

                    return context.catalogos.cuenta_contable; //son las cuentas de caja
                }
                return [];

            }

        }

    }
</script>