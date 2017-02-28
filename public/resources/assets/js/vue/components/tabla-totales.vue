<template>

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">

                <table id="itemsTable" class="table table-noline tabla-dinamica itemsTable">
                    <tbody>
                    <tr v-if="precioCompra() || precioVenta()">
                        <td width="75%"></td>
                        <td width="20%"
                            style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            Subtotal:
                        </td>
                        <td width="5%"
                            style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">
                            ${{getSubTotal | currencyDisplay}}
                        </td>
                    </tr>
                    <tr v-if="precioCompra() || precioVenta()">
                        <td width="75%"></td>
                        <td width="20%"
                            style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            Descuento:
                        </td>
                        <td width="5%"
                            style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">
                            ${{getDescuentoTotal | currencyDisplay}}
                        </td>
                    </tr>
                    <tr v-if="precioCompra() || precioVenta()">
                        <td width="75%"></td>
                        <td width="20%"
                            style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            Impuesto:
                        </td>
                        <td width="5%"
                            style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">
                            ${{getImpuestoTotal | currencyDisplay}}
                        </td>
                    </tr>
                    <tr v-if="config.muestroRetenidoSubContrato">
                        <td width="75%"></td>
                        <td width="20%"
                            style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            Monto retenido:
                        </td>
                        <td width="5%"
                            style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">
                            {{getRetenidoSubContratoTotal | currencyDisplay}}
                        </td>
                    </tr>
                    <tr v-if="precioCompra() || precioInventario() || precioVenta()">
                        <td width="75%"></td>
                        <td width="20%"
                            style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            Total:
                        </td>
                        <td width="5%"
                            style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">
                            ${{ getTotal | currencyDisplay}}

                            <input type="hidden" name="campo[subtotal]"
                                   value="{{showRetenido ? (getSubTotal - getRetenidoTotalMul) : getSubTotal}}">
                            <input type="hidden" name="campo[descuento]" :value="getDescuentoTotal">
                            <input type="hidden" name="campo[impuesto]" :value="getImpuestoTotal">
                            <input type="hidden" name="campo[impuestos]" :value="getImpuestoTotal">
                            <input type="hidden" name="campo[total]" value="{{getTotal}}">
                            <input type="hidden" name="campo[monto]" value="{{getTotal}}">
                            <input type="hidden" name="campo[retencion]" value="{{getRetenidoSubContratoTotal}}">
                            <input v-if="showRetenido" type="hidden" name="campo[cents]" value="{{(getRetenidoTotalMul - getRetenidoTotal)}}">
                        </td>
                    </tr>
                    <tr v-if="showRetenido">
                        <td width="75%"></td>
                        <td width="20%"
                            style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            <span class="label label-warning">Retenido </span>
                        </td>
                        <td width="5%"
                            style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">
                            ${{getRetenidoTotalMul | currencyDisplay}}
                        </td>
                    </tr>

                    <tr v-if="showCreditoAplicado">
                        <td width="75%"></td>
                        <td width="20%"
                            style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            <span class="label label-success">Cr&eacute;dito aplicado </span>
                        </td>
                        <td width="5%"
                            style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">
                            ${{detalle.creditos_aplicados | currencyDisplay}}
                        </td>
                    </tr>

                    <tr v-if="showPagos()">
                        <td width="75%"></td>
                        <td width="20%"
                            style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            <span class="label label-successful">Pagos </span>
                        </td>
                        <td width="5%"
                            style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">
                            ${{detalle.pagos | currencyDisplay}}
                        </td>
                    </tr>
                    <tr v-if="showSaldo()">
                        <td width="75%"></td>
                        <td width="20%"
                            style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            <span class="label label-danger">Saldo </span>
                        </td>
                        <td width="5%"
                            style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">
                            ${{detalle.saldo | currencyDisplay}}
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>


</template>
<script>
import redondeo from './../mixins/redondeo'
Vue.filter('currencyDisplay', require('./../../vue/filters/currency-two-way.vue'));
export default {
    mixins:[redondeo],
    data: function(){
        return {
            articulo:'articulo',
            retenido_total:0
        };
    },
    props:{
          config:Object,
          detalle:Object,
          catalogos:Object,
          empezable:Object
    },
      computed:{

          getSubTotal:function(){

              var context = this;
              var subtotal = _.sumBy(context.detalle.articulos, function(articulo){
                  return parseFloat(this.roundNumber(context.getSubtotalArticulo(articulo),2));
              });

              //sumar sutotal si existen articulos tabla
              //de items de alquiler
              if(typeof context.detalle.articulos_alquiler != 'undefined'){
                var subtotal_alquiler = _.sumBy(context.detalle.articulos_alquiler, function(articulo){
                    return parseFloat(articulo.tarifa_monto*articulo.tarifa_cantidad_periodo);
                });

                subtotal = parseFloat(subtotal) + parseFloat(subtotal_alquiler);
              }

              return subtotal;
          },

          getImpuestoTotal:function(){

              var context = this;
              var impuesto = _.sumBy(context.detalle.articulos, function(articulo){
                  return parseFloat(context.getImpuestoArticulo(articulo));
              });

              //sumar impuesto de alquiler si existen articulos tabla de items de alquiler
              if(typeof context.detalle.articulos_alquiler != 'undefined'){
                var impuesto_alquiler = _.sumBy(context.detalle.articulos_alquiler, function(articulo){
                    return context.getImpuestoArticulo(articulo, true);
                });

                impuesto = parseFloat(impuesto) + parseFloat(impuesto_alquiler);
              }

              return impuesto;
          },

          getRetenidoTotal:function(){
              var context = this;
              return _.sumBy(context.detalle.articulos, function(articulo){
                  return parseFloat(context.getRetenidoArticulo(articulo));
              });
          },

          getRetenidoTotalMul:function(){
              var context = this;
              var sum_retenido = _.sumBy(context.detalle.articulos, function(articulo){
                  return parseFloat(context.getRetenidoArticulo(articulo));
              });
              return context.round2(context.getImpuestoTotal) * 0.50;
          },

          getRetenidoSubContratoTotal:function(){
                var context = this;
                   var retenido =  (context.getSubTotal-context.getDescuentoTotal)*(context.detalle.porcentaje_retencion/100);
                   return parseFloat(this.roundNumber(retenido,2));
          },
          getDescuentoTotal:function(){

              var context = this;
              var descuento = _.sumBy(context.detalle.articulos, function(articulo){
                  return context.getDescuentoArticulo(articulo);
              });

              //sumar descuento de alquiler si existen articulos tabla de items de alquiler
              if(typeof context.detalle.articulos_alquiler != 'undefined'){
                var descuento_alquiler = _.sumBy(context.detalle.articulos_alquiler, function(articulo){
                    return context.getDescuentoArticulo(articulo, true);
                });

                descuento = parseFloat(descuento) + parseFloat(descuento_alquiler);
              }

              return descuento;
          },

          getTotal:function(){
              var context = this;
              var total = context.round2(context.getSubTotal) + context.round2(context.getImpuestoTotal) - context.round2(context.getDescuentoTotal);
                var context = this;
                if( typeof context.empezable != "undefined"  && typeof context.empezable.type != "undefined"  && context.empezable.type != "" && (context.empezable.type == "orden_compra" ) ){
                    if( window.cache_operation_total==null){
                        // para no tener que obtener el valor seleccionado nuevamente se guarda en cache
                         window.cache_operation_total= _.find(window[this.empezable.type+"s"], function(operacion){
                            return operacion.id == context.empezable.id;
                        });
                    }

                    var operacion=window.cache_operation_total;
                    if(operacion!=null && typeof operacion.monto != "undefined"  && total > 0){
                        var montoOperacion=+new Number(operacion.monto).toFixed(2);
                        var montoTotal=+new Number(total).toFixed(2);
                        if (montoOperacion < montoTotal) {
                            context.config.disableDetalle = true;
                            toastr.error("El monto total actual no puede ser mayor que la operación seleccionada" , "Mensaje");
                        }else{
                            context.config.disableDetalle = false;
                        }
                    }
                }

                return  parseFloat(total);
          },

          showRetenido:function(){

              if(this.config.modulo != 'facturas_compras'){
                  return false;
              }

              var context = this;
              var proveedor = _.find(context.catalogos.cloneProveedores, function(proveedor){
                  return proveedor.proveedor_id == context.detalle.proveedor_id;
              });

              if(context.config.modulo == 'facturas_compras' && this.getTotal > 0 && this.retieneImpuesto()){
                  return true;
              }

              return false;

          },

          showCreditoAplicado:function(){
              var context = this;
              var modulos_compras = ['facturas_compras'];
              return modulos_compras.indexOf(context.config.modulo) != -1 && context.detalle.creditos_aplicados > 0 ? true : false;
          },



      },

      methods:{

          round:function(v){
              var context = this;
              return parseFloat(roundNumber(v,4));
          },

          round2:function(v){
              return parseFloat(roundNumber(v,2));
          },

          retieneImpuesto:function(){
              var context = this;
              var proveedor = _.find(context.catalogos.cloneProveedores, function(proveedor){
                  return proveedor.proveedor_id == context.detalle.proveedor_id;
              });
              if(!_.isEmpty(proveedor) && proveedor.retiene_impuesto == 'no' && context.catalogos.empresa.retiene_impuesto == 'si'){
                  return true;
              }
              return false;
          },

          precioInventario:function(){

              var context = this;
              var modulos_compras = ['ajustes'];
              return modulos_compras.indexOf(context.config.modulo) != -1 ? true : false;

          },

          precioCompra:function(){

              var context = this;
              var modulos_compras = ['ordenes','facturas_compras','ordenes_alquiler'];
              return modulos_compras.indexOf(context.config.modulo) != -1 ? true : false;

          },

          precioVenta:function(){

              var context = this;
              var modulos_ventas = ['cotizaciones','ordenes_ventas','ordenes_alquiler'];
              return modulos_ventas.indexOf(context.config.modulo) != -1 ? true : false;

          },

          getPrecioUnidad:function(articulo){

              var context = this;
              if(context.precioCompra() || context.precioInventario() || articulo.id != '' || (typeof context.config.editarPrecio !== "undefined" && context.config.editarPrecio)){

                  return articulo.precio_unidad;

              }

              var precio = _.find(articulo.precios, function(precio){
                  return precio.id == context.detalle.item_precio_id;
              });

              if(!_.isEmpty(precio)){

                  var unidad = _.find(articulo.unidades, function(unidad){
                      return unidad.id == articulo.unidad_id;
                  });
                  if(!_.isEmpty(unidad)){

                      return (parseFloat(precio.pivot.precio) || 0) *  (parseFloat(unidad.pivot.factor_conversion) || 0);

                  }
                  return parseFloat(precio.pivot.precio) || 0;

              }

              return 0;

          },

          getSubtotalArticulo: function (articulo) {

              return articulo.cantidad * this.getPrecioUnidad(articulo);

          },

          getDescuentoArticulo:function(articulo, esalquiler){
              if(esalquiler != undefined){
                return ((articulo.precio_total * articulo.descuento)/100);
              } else{
                return (this.getSubtotalArticulo(articulo) * articulo.descuento)/100;
              }
          },
          //Segundo parametro "esalquiler" es para identificar
          //si es un articulo de contrato de alquiler
          getImpuestoArticulo:function(articulo, esalquiler){
              var context = this;
              var impuesto = _.find(context.catalogos.impuestos,function(impuesto){
                  return impuesto.id==articulo.impuesto_id;
              });

              var aux = (!_.isEmpty(impuesto)) ? context.round(impuesto.impuesto) : 0;

              if(esalquiler != undefined){
                  var imp = aux != 0 ? aux : (articulo.impuesto != undefined && articulo.impuesto.impuesto != undefined ? parseFloat(articulo.impuesto.impuesto) : 0);
                  return imp != 0 ? ((parseFloat((articulo.tarifa_monto*articulo.tarifa_cantidad_periodo)) - (parseFloat((articulo.tarifa_monto*articulo.tarifa_cantidad_periodo)) * articulo.descuento)/100) * imp)/100 : 0;
              }

              let subtotal = context.round(context.getSubtotalArticulo(articulo));
              let descuento = context.round(context.getDescuentoArticulo(articulo));
              let total_impuesto = (subtotal - descuento) * (aux / 100) ;
              return total_impuesto;
          },

          getRetenidoArticulo:function(articulo){

              var context = this;
              var impuesto = _.find(context.catalogos.impuestos,function(impuesto){
                  return impuesto.id==articulo.impuesto_id;
              });
              var aux = (!_.isEmpty(impuesto)) ? context.round(impuesto.porcentaje_retenido) : 0;

              let total_impuesto = context.detalle.articulos.length > 1 ? context.round(context.getImpuestoArticulo(articulo)) : context.round2(context.getImpuestoArticulo(articulo));
              return total_impuesto * (aux / 100);
          },

          showPagos:function(){

              if(this.config.modulo == 'facturas_compras' && this.config.vista == 'editar'){
                  return true;
              }
              return false;

          },

          showSaldo:function(){

              if(this.config.modulo == 'facturas_compras' && this.config.vista == 'editar'){
                  return true;
              }
              return false;

          },
          nuevoRedondeo(value, decimals){
              return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
          }


      },

      ready: function () {



      }
}


</script>
<style>
    .table-noline .table-noline td .totales_derecha{
        padding: 10px;
        border: 1px solid silver !important;
        border-left: 0px !important;
    }

    .table-noline .table-noline td .totales_izquierda{
        padding: 10px;
        border: 1px solid silver !important;
        border-right: 0px !important;
    }


</style>
