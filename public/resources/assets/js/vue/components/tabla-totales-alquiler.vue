<template>

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">

                  <table id="itemsTable" class="table table-noline tabla-dinamica itemsTable">
                    <tbody>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">Subtotal:</td>
                        <td width="5%" style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{getSubTotal | currency}}</td>
                    </tr>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">Descuento:</td>
                        <td width="5%" style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{getDescuentoTotal | currency}}</td>
                    </tr>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">Impuesto:</td>
                        <td width="5%" style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{getImpuestoTotal | currency}}</td>
                    </tr>
                    <tr>
                        <td width="75%"><label id="items-errores" class="text-red"></label></td>
                        <td width="20%" style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">Total:</td>
                        <td width="5%" style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{getTotal | currency}}
                          <input type="hidden" name="campo[subtotal]" :value="getSubTotal">
                          <input type="hidden" name="campo[descuento]" :value="getDescuentoTotal">
                          <input type="hidden" name="campo[impuestos]" :value="getImpuestoTotal">
                          <input type="hidden" name="campo[total]" :value="getTotal">
                          <input type="hidden" name="campo[monto]" :value="getTotal">
                        </td>
                    </tr>
                    <tr v-if="showRetenido">
                        <td width="75%"></td>
                        <td width="20%" style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            <span class="label label-warning">Retenido </span>
                        </td>
                        <td width="5%" style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{getRetenidoTotal | currency}}</td>
                    </tr>
                    <tr v-if="showPagos()">
                        <td width="75%"></td>
                        <td width="20%" style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            <span class="label label-successful">Pagos </span>
                        </td>
                        <td width="5%" style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{detalle.pagos | currency}}</td>
                    </tr>
                    <tr v-if="showSaldo()">
                        <td width="75%"></td>
                        <td width="20%" style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                            <span class="label label-danger">Saldo </span>
                        </td>
                        <td width="5%" style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{detalle.saldo | currency}}</td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>


</template>
<script>

export default {
    props:[
      'config',
      'catalogos',
      'detalle'
    ],
    data: function(){
       return {
         HedearColumnas: [],
         articulo:'articulo',
         mostrar:{icono_pop_over:false,icono_comentario:true},
         desabilitar:{selectItem:true}
       };
     },
      computed:{
          getSubTotal:function(){

            var context = this;
            var subtotal_alquiler = _.sumBy(context.detalle.articulos_alquiler, function(articulo){
                return context.getSubtotalArticulo(articulo);
            });

            //sumar sutotal si existen articulos tabla de items adicionales
            if(typeof context.detalle.articulos != 'undefined'){

              var subtotal = _.sumBy(context.detalle.articulos, function(articulo){
                  return context.getSubtotalArticulo(articulo, true);
              });
              subtotal_alquiler = parseFloat(subtotal) + parseFloat(subtotal_alquiler);
            }
            return subtotal_alquiler;
         },

         getImpuestoTotal:function(){

             var context = this;
             var impuesto_alquiler =  _.sumBy(context.detalle.articulos_alquiler, function(articulo){
                 return context.getImpuestoArticulo(articulo);
             });

             //sumar impuesto si existen articulos tabla de items adicionales
             if(typeof context.detalle.articulos != 'undefined') {
               var impuesto = _.sumBy(context.detalle.articulos, function(articulo){
                   return context.getImpuestoArticulo(articulo);
               });

               impuesto_alquiler = parseFloat(impuesto) + parseFloat(impuesto_alquiler);
             }

             return impuesto_alquiler;
         },
         getRetenidoTotal:function(){

               var context = this;
               return _.sumBy(context.detalle.articulos_alquiler, function(articulo){
                   return context.getRetenidoArticulo(articulo);
               });
               return 0;
          },

          getDescuentoTotal:function (){

               var context = this;
               var descuento_alquiler = _.sumBy(context.detalle.articulos_alquiler, function(articulo){
                   return context.getDescuentoArticulo(articulo);
               });

               //sumar descuento si existen articulos tabla de items adicionales
               if(typeof context.detalle.articulos != 'undefined'){
                 var descuento = _.sumBy(context.detalle.articulos, function(articulo){
                     return context.getDescuentoArticulo(articulo, true);
                 });

                 descuento_alquiler = parseFloat(descuento) + parseFloat(descuento_alquiler);
               }

               return descuento_alquiler;
          },

          getTotal:function (){
              return parseFloat(this.getSubTotal) + parseFloat(this.getImpuestoTotal) - parseFloat(this.getDescuentoTotal);
          }
      },

      methods:{
          setArticulosItems(){
              var self = this;
              //setTimeout(function(){
                  self.$set('articulos',Articulos.items);
              //},1500);
          },
          getArticulosCatalogos(){
            var self = this;
            var filtros = {tipo_item:'alquiler',tipo_cuenta:'activo'};
            var datos = $.extend({erptkn: tkn},filtros);
            var urlCatalogo = 'ajax_catalogo/articulos_catalogos';
            var catalogo = this.ajaxPost(urlCatalogo,datos);

            catalogo.then((response)=>{
                self.$set('catalogos',response.data);

                Vue.nextTick(function(){
                     setTimeout(function(){
                         self.$set('detalle.articulos_alquiler',Articulos.items);
                     },500);
                });
            });
          },
          ajaxPost(ajaxUrl,datos){
              return this.$http.post({url: window.phost() + ajaxUrl, method:'POST',data:datos});
          },
          getPrecioUnidad:function(articulo){

              var context = this;
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
          getSubtotalArticulo: function (articulo, adicional) {
              if(typeof adicional != 'undefined' && adicional == false){
                  return articulo.cantidad * this.getPrecioUnidad(articulo);
              }else{
                  return articulo.cantidad * articulo.precio_unidad;
              }
          },
          getDescuentoArticulo:function(articulo, adicional){
              if(typeof adicional != 'undefined' && adicional != ''){
                  return (this.getSubtotalArticulo(articulo, true) * articulo.descuento)/100;
              }else{
                  return (this.getSubtotalArticulo(articulo) * articulo.descuento)/100;
              }
          },
          getImpuestoArticulo:function(articulo){

              var context = this;
              var impuesto = _.find(context.catalogos.impuestos,function(impuesto){
                  return impuesto.id==articulo.impuesto_id;
              });
              var aux = (!_.isEmpty(impuesto)) ? parseFloat(impuesto.impuesto) : 0;
              return ((context.getSubtotalArticulo(articulo) - context.getDescuentoArticulo(articulo)) * aux)/100;

          },

          getRetenidoArticulo:function(articulo){

              var context = this;
              var impuesto = _.find(context.catalogos.impuestos,function(impuesto){
                  return impuesto.id==articulo.impuesto_id;
              });
              var aux = (!_.isEmpty(impuesto)) ? parseFloat(impuesto.porcentaje_retenido) : 0;
              return ((context.getImpuestoArticulo(articulo)) * aux)/100;

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
