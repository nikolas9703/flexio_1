<template>

  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 item-listing lists_opciones"  v-for="metodo_pago in detalle.metodos_pago">

      <div class="lists_opciones">
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <label>Forma de Pago <span required="" aria-required="true">*</span></label>
          </div>
          <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <select name="metodo_pago[{{$index}}][tipo_pago]" aria-required="true" data-rule-required="true" v-select2="metodo_pago.tipo_pago" :config="config.select2" :disabled="config.disableDetalle">
                  <option value="">Seleccione</option>
                  <option :value="ele.etiqueta" v-for="ele in catalogos.metodos_pago">{{{ele.valor}}}</option>
              </select>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <label>Total Pagado <span required="" aria-required="true">*</span></label>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <div class="row">
                  <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                      <div class="input-group"><span class="input-group-addon">$</span>
                          <input type="text" name="metodo_pago[{{$index}}][total_pagado]" class="form-control" style="text-align: right;" aria-required="true" data-rule-required="true" placeholder="0.00" v-model="metodo_pago.total_pagado | currencyDisplay" :disabled="config.disableDetalle"/>
                      </div>
                      <label id="total_pagado{{$index}}-error" class="error" for="total_pagado{{$index}}"></label>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="display: none;">
                      <button type="button" class="btn btn-default btn-block" agrupador="opciones"><i></i></button>
                  </div>
              </div>
          </div>
      </div>

      <!-- Si el tipo de pago es ACH -->
      <div v-if="metodo_pago.tipo_pago == 'ach'">
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <label>Banco del proveedor</label>
          </div>
          <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <select name="metodo_pago[{{$index}}][referencia][nombre_banco_ach]" aria-required="true" data-rule-required="true" v-select2="metodo_pago.referencia.nombre_banco_ach" :config="config.select2" :disabled="config.disableDetalle">
                  <option value="">Seleccione</option>
                  <option :value="banco.id" v-for="banco in catalogos.bancos">{{banco.nombre}}</option>
              </select>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <label>Número de cuenta del Proveedor</label>
          </div>
          <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <input type="text" name="metodo_pago[{{$index}}][referencia][cuenta_proveedor]" class="form-control" aria-required="true" data-rule-required="true" v-model="metodo_pago.referencia.cuenta_proveedor" :disabled="config.disableDetalle"/>
          </div>
      </div>

      <!-- Si el tipo de pago es Cheque -->
      <div v-if="metodo_pago.tipo_pago == 'cheque'">
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <label>Número Cheque</label>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <input type="text" name="metodo_pago[{{$index}}][referencia][numero_cheque]" class="form-control" disabled="" v-model="metodo_pago.referencia.numero_cheque"/>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
              <label>Cuenta de Banco</label>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
              <input type="text" name="metodo_pago[{{$index}}][referencia][nombre_banco_cheque]" class="form-control" disabled="" v-model="metodo_pago.referencia.nombre_banco_cheque"/>
          </div>
      </div>

      <!-- Si el tipo de pago es Tarjeta de credito -->
      <div v-if="metodo_pago.tipo_pago == 'tarjeta_credito'">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <label>Número de tarjeta</label>
        </div>
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <input type="text" name="metodo_pago[{{$index}}][referencia][numero_tarjeta]" class="form-control" aria-required="true" data-rule-required="true" v-model="metodo_pago.referencia.numero_tarjeta" :disabled="config.disableDetalle"/>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <label>Número de recibo</label>
        </div>
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
          <input type="text" name="metodo_pago[{{$index}}][referencia][numero_recibo]" class="form-control" aria-required="true" data-rule-required="true" v-model="metodo_pago.referencia.numero_recibo" :disabled="config.disableDetalle"/>
        </div>
      </div>


      <div style="clear:both"></script>
      <br><br>
      <!-- Sumatoria de metodos de pago-->
      <div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9">
          <div class="input-group"><span class="input-group-addon">$</span>
              <input type="text" name="campo[total_pagado]" class="form-control" style="text-align: right;" disabled value="{{getTotal | currency ''}}"/>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9">
          <label class="label-success-text" style="border:#0076BE solid 1px; background-color: #0076BE;">Total</label>
          <label id="totals-error" class="error"></label>
        </div>
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

    },

    computed:{

            getTotal:function(){

                var context = this;
                var metodo  = context.detalle.metodos_pago[0];
                if(context.config.vista == 'crear' && metodo.tipo_pago == 'credito_favor' && metodo.total_pagado > context.detalle.credito_proveedor)
                {
                  metodo.total_pagado = context.detalle.credito_proveedor;
                }
                return _.sumBy(context.detalle.metodos_pago, function(pagable){
                  return pagable.total_pagado;
                });

            }

      }

}

</script>
