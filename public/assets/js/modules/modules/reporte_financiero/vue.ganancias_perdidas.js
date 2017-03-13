var seeTotal = { totales:false };
var reporteGananciasPerdidas = Vue.extend({
  template:'#ganancias_perdidas',
  data:function(){
    return {
      periodo_comparar:[1,2,3,4,5,6,7,8,9,10,11],
      rango_perido:[{etiqueta:"mensual", valor:"Mensual"},{etiqueta:"trimestral", valor:"Trimestral"},{etiqueta:"semestral", valor:"Semestral"},{etiqueta:"anual", valor:"Anual"}],
      years:[],
      meses:[],
      centros:[],
      reporte:{mes:'',year:'',rango:'',periodo:'',tipo:'ganancias_perdidas',centro_contable:'todos',seeTotal:seeTotal},
      botonDisabled:false
    };
  },
  ready:function(){
    var context = this;
    this.$http.post({
      url: phost() + 'reportes_financieros/ajax-formulario-datos',
      method:'POST',
      data:{erptkn: tkn,formulario:'ganancias_perdidas'}
    }).then(function(response){
      var catalogos = response.data;
      context.$set('years',catalogos.years);
      context.$set('meses',catalogos.meses);
      context.$set('centros',catalogos.centros_contable);
    });

  },
  methods:{
    limpiar:function (){
      this.$resetValidation();
      this.reporte={ mes:"",year:"",periodo:"",rango:"",tipo:"ganancias_perdidas",centro_contable:'todos'};
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
        reporteFinanciero.$set('reporte','tablelizer-ganancias-perdidas');
      });
    }
  }
});
