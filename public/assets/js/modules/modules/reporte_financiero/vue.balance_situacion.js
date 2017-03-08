//filtros
Vue.filter('redondeo',{
  read:function(val){
    return accounting.toFixed(val, 2);
  },
  write: function(val,oldVal){
    return isNaN(val) ? 0 : accounting.toFixed(parseFloat(val),2);
  }
});
Vue.filter('moneda',function(val){
  return _.isNaN(val)?0 : accounting.formatMoney(val);
});

var reporteBalanceSituacion = Vue.extend({
  template:'#balance_situacion',
  data:function(){
    return {
      periodo_comparar:[1,2,3,4,5,6,7],
      rango_perido:[{etiqueta:"trimestral", valor:"Trimestral"},{etiqueta:"semestral", valor:"Semestral"},{etiqueta:"anual", valor:"Anual"}],
      years:[],
      meses:[],
      reporte:{mes:'',year:'',rango:'',periodo:'',tipo:'balance_situacion'},
      botonDisabled:false
    };
  },
  ready:function(){
    var context = this;
    this.$http.post({
      url: phost() + 'reportes_financieros/ajax-formulario-datos',
      method:'POST',
      data:{erptkn: tkn,formulario:'balance_situacion'}
    }).then(function(response){
      var catalogos = response.data;
      context.$set('years',catalogos.years);
      context.$set('meses',catalogos.meses);
    });

  },
  /*activate:function(){
     //this.$activateValidator();
     //done();
  },*/
  methods:{
    limpiar:function (){
      this.$resetValidation();
      this.reporte={ mes:"",year:"",periodo:"",rango:"",tipo:"balance_situacion"};
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
        reporteFinanciero.$set('reporte','tablelizer-balance');
      });
    }
  }
});
