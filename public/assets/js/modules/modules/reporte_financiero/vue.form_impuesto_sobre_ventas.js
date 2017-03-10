var formularioImpuestoSobreVenta = Vue.extend({
  template:'#form_impuesto_sobre_venta',
  data:function(){
    return {
      years:[],
      meses:[],
      reporte:{mes:'',year:'',tipo:'impuestos_sobre_ventas'},
      botonDisabled:false
    };
  },
  ready:function(){
    var context = this;
    this.$http.post({
      url: phost() + 'reportes_financieros/ajax-formulario-datos',
      method:'POST',
      data:{erptkn: tkn,formulario:'impuestos_sobre_ventas'}
    }).then(function(response){
      if(_.has(response.data, 'session')){
         window.location.assign(phost());
      }
      var catalogos = response.data;
      context.$set('years',catalogos.years);
      context.$set('meses',catalogos.meses);
    });

  },
  methods:{
    limpiar:function (){
      this.$resetValidation();
      this.reporte={ mes:"",year:"",tipo:"impuesto_sobre_ventas"};
      reporteFinanciero.$set('dataReporte',[]);
    },
    generar_reporte:function(reporte){
      this.$validate(true);
      if (this.$validar.invalid) {
        return false;
      }
      reporteFinanciero.$set('dataReporte',[]);
      var data_reporte = reporte;
      var context = this;
      this.$http.post({
        url: phost() + 'reportes_financieros/ajax-generar-reporte',
        method:'POST',
        data:$.extend({erptkn: tkn}, data_reporte)
      }).then(function(response){
        if(_.has(response.data, 'session')){
           window.location.assign(phost());
        }
        var datos = response.data;
        reporteFinanciero.$set('dataReporte',[datos]);
        reporteFinanciero.$set('reporte','reporte-impuesto-sobre-venta');
      });
    }
  }
});
