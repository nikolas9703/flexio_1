<style></style>
<template src="./template/totales.html">

</template>
<script>
 export default {
      props:['listaArticulos','listaArticulosAlquiler'],
     data(){
         return{
            showPago:false,
            showSaldo:false
         };
     },
     directives:{
         'moneda':require('./../../directives/inputmask-currency.vue')
     },
     filters:{
         'dollar':require('./../../filters/currency-two-way.vue')
     },
     computed:{
        getSubtotales(){
            var subtotal = 0;
            var subtotal_ventas = _.sumBy(this.listaArticulos,(row)=> parseFloat(row.subtotal) ) || 0;
            var subtotal_alquiler = _.sumBy(this.listaArticulosAlquiler,(row)=>parseFloat((row.tarifa_cantidad_periodo * row.tarifa_monto))) || 0;
            subtotal = subtotal_ventas + subtotal_alquiler;
console.log('SUB', this.listaArticulosAlquiler);
            return subtotal;
        },
        getDescuentos(){
            var descuentos = 0;
            let descuentos_venta = _.sumBy(this.listaArticulos,(row)=>parseFloat(row.total_descuento)) || 0;
            let descuentos_alquiler = _.sumBy(this.listaArticulosAlquiler,(row)=>parseFloat(row.descuento_total)) || 0;
            descuentos = descuentos_venta + descuentos_alquiler;

            return descuentos;
        },
        getImpuestos(){
            var impuestos = 0;
            let impuestos_venta = _.sumBy(this.listaArticulos,(row)=>parseFloat(row.total_impuesto)) || 0;
            let impuestos_alquiler = _.sumBy(this.listaArticulosAlquiler,(row)=>((parseFloat(row.precio_total)*parseFloat(row.impuesto))/100)) || 0;
            impuestos = impuestos_venta + impuestos_alquiler;

            return impuestos;
        },
        getTotales(){
            let totales = (this.getSubtotales + this.getImpuestos) - this.getDescuentos;
            return totales;
        },
        getRetenidos(){
            return 0;
        }
     }
 }
</script>
