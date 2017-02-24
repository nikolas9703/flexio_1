var reporteImpuestoSobreItbms = Vue.extend({
  template:'#reporte_impuesto_sobre_itbms',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Reporte de retención de I.T.B.M.S. por proveedor",
      proveedor:[],
      fecha_inicial:'',
      fecha_final:'',
      resumen:[]
    };
  },
  ready:function(){
      this.proveedor = this.info[0].proveedor;
      this.fecha_inicial = this.info[0].fecha_inicial;
      this.fecha_final = this.info[0].fecha_final;
      this.resumen = this.info[0].resumen;  
      $('#imprimirReporte').css('display', 'block'); //Habilita la impresion del reporte  
  }
});
Vue.component('reporte-impuesto-sobre-itbms', reporteImpuestoSobreItbms);
