
var formCuentaPorCobrarAntiguedad = Vue.extend({
  template:'#form_cuenta_por_cobrar_antiguedad',
  data:function(){
    return {
      years:[],
      meses:[],
      reporte:{mes:'',year:'',tipo:'cuenta_por_cobrar_por_antiguedad'},
      botonDisabled:false
    };
  },
  ready:function(){
    var context = this;
    this.$http.post({
      url: phost() + 'reportes_financieros/ajax-formulario-datos',
      method:'POST',
      data:{erptkn: tkn,formulario:'cuenta_por_cobrar_por_antiguedad'}
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
      this.reporte={ mes:'',year:'',tipo:"cuenta_por_cobrar_por_antiguedad"};
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
        reporteFinanciero.$set('reporte','reporte-cuenta-por-cobrar-antiguedad');
      });
    }
  }
});
