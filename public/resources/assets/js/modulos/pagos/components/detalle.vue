<template>

    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="fecha_pago">Fecha de pago <span required="" aria-required="true">*</span></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="campo[fecha_pago]" class="form-control" aria-required="true"
                       data-rule-required="true" v-datepicker="detalle.fecha_pago" :config="config.datepicker2"
                       :disabled="config.disableDetalle">
            </div>
            <label id="fecha_pago-error" class="error" for="fecha_pago"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="proveedor">Proveedor <span required="" aria-required="true">*</span></label>
            <select name="campo[proveedor_id]" aria-required="true" data-rule-required="true"
                    v-select2ajax="detalle.proveedor_id" :config="select2proveedor" :disabled="true">
                <option value="">Seleccione</option>
            </select>
            <label id="proveedor-error" class="error" for="proveedor"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" v-if="config.vista == 'crear'">
            <label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" disabled value="{{detalle.saldo_proveedor| currency ''}}"
                       class="form-control debito">
            </div>
            <label class="label-danger-text">Saldo por pagar</label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 " v-if="config.vista == 'crear'">
            <label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" disabled value="{{detalle.credito_proveedor | currency ''}}"
                       class="form-control debito">
            </div>
            <label class="label-success-text">Crédito a favor</label>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3" v-show="config.vista != 'crear'">
            <label>Estado <span aria-required="true" required="">*</span></label>
            <select name="campo[estado]" class="estado" aria-required="true" data-rule-required="true"
                    v-select2="detalle.estado" :config="config.select2"
                    :disabled="config.vista == 'crear' || (config.vista != 'crear' && !sePuedeCambiarEstado)">
                <option value="">Seleccione</option>
                <option :value="estado.etiqueta" v-for="estado in getEstados">{{estado.valor}}</option>
            </select>
        </div>

    </div>

</template>

<script>

export default {

  props:{

        config: Object,
        detalle: Object,
        empezable: Object,
        catalogos: Object

    },

    data:function(){

        return {
            listaProveedores:[],
            select2proveedor:{
                ajax:{
                    url: function(params){
                        return phost() + 'proveedores/ajax-get-proveedores?tipo='+ empezable.type;
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
    ready(){
        if(this.config.vista == 'editar'){
            var pago = JSON.parse(JSON.stringify(window.pago));
            this.listaProveedores = [pago.proveedor];
			
        }
    },
    computed:{

          sePuedeCambiarEstado: function (){

              var context = this;
              if(context.config.vista == 'editar')
              {
                  var pago = JSON.parse(JSON.stringify(window.pago));
                  this.listaProveedores = [pago.proveedor];
                  //this.detalle.proveedor_id = window.pago.proveedor_id;
                  //falta metodo de pago cheque
                  if(pago.estado == 'anulado' || pago.estado == 'cheque_en_transito')
                  {
                      return false;
                  }
                  return true;
              }

          },

          getEstados: function (){

              var context = this;
              if(context.config.vista == 'editar')
              {
                  var pago = JSON.parse(JSON.stringify(window.pago));
                  if(pago.estado == 'por_aprobar')
                  {
                      return _.filter(context.catalogos.estados, function(estado){return estado.etiqueta == 'por_aprobar' || estado.etiqueta == 'por_aplicar' || estado.etiqueta == 'anulado';});
                  }
                  else if(pago.estado == 'por_aplicar')
                  {
                      if(context.detalle.metodos_pago[0].tipo_pago == 'cheque')
                      {
                        //se aplica al momento de imprimir el cheque
                        return _.filter(context.catalogos.estados, function(estado){return estado.etiqueta == 'por_aplicar' || estado.etiqueta == 'anulado';});
                      }
                      return _.filter(context.catalogos.estados, function(estado){return estado.etiqueta == 'por_aplicar' || estado.etiqueta == 'aplicado' || estado.etiqueta == 'anulado';});
                  }
                  else if(pago.estado == 'aplicado')
                  {
                      return _.filter(context.catalogos.estados, function(estado){return estado.etiqueta == 'aplicado' || estado.etiqueta == 'anulado';});
                  }
              }
              return context.catalogos.estados;

          }

    },

    watch:{

        'empezable.id':function(val, oldVal){

            var context = this;
            var datos = $.extend({erptkn: tkn},{id:val, type:context.empezable.type});

            if(context.config.vista != 'crear' || val === '')return;

            this.$http.post({
                url:window.phost() + "pagos/ajax-get-empezable",
                method:'POST',
                data:datos
            }).then(function(response){
                if(!_.isEmpty(response.data)){
                    context.detalle = $.extend(context.detalle, JSON.parse(JSON.stringify(response.data)));
                    context.detalle.id = '';
                }
            });

        },

          'detalle.proveedor_id':function(val, oldVal){

              if(val == null){
                  return;
              }
              if(val == ''){
                  this.detalle.saldo_proveedor = 0;
                  this.detalle.credito_proveedor = 0;
                  return '';
              }

              var context = this;
              var datos = $.extend({erptkn: tkn},{proveedor_id:val,tipo:this.empezable.type});
              this.$http.post({
                  //url: window.phost() + "proveedores/ajax-get-montos",
                  url: window.phost() + "proveedores/ajax_get_proveedor_pago",
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
                      context.listaProveedores = [response.data];
                      var proveedor = _.find(context.listaProveedores, function(proveedor){
                        return proveedor.id == val;
                      });

                      if(!_.isEmpty(proveedor) && !_.isEmpty(context.detalle.metodos_pago[0].referencia) && context.config.vista == "crear")
                      {
                         context.detalle.metodos_pago[0].tipo_pago = proveedor.forma_pago;
                         context.detalle.metodos_pago[0].referencia.nombre_banco_ach = proveedor.banco_id;
                         context.detalle.metodos_pago[0].referencia.cuenta_proveedor = proveedor.numero_cuenta;
                      }
                      this.$nextTick(function(){
                          this.detalle.proveedor_id = val;
                      });
                  }
              });

          }

      },

}


</script>
