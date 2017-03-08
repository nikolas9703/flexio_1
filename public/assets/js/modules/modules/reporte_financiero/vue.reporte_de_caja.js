var formularioReporteCaja = Vue.extend({
  template:'#reporte_de_caja',
  data:function(){
    return {
        reporte: {
      id_caja: '',
      nombre_de_caja:'',
      caja_cuenta_id: '',
      fecha_desde:'',
      fecha_hasta:'',
      responsable:'',
      centro_contable:'',
      tipo: 'reporte_de_caja'
        },
      cats: {
          cajas:'',
          centros:'',
          responsables:''
      },
    };
  },
  ready:function(){
    var context = this;
    this.$http.post({
      url: phost() + 'reportes_financieros/ajax-formulario-datos',
      method:'POST',
      data:{erptkn: tkn,formulario:'reporte_de_caja'}
    }).then(function(response){
      var catalogos = response.data;
      context.$set('cats.cajas', catalogos.cajas);
      context.$set('cats.centros', catalogos.centros);
    });

  },
  /*activate:function(){
     //this.$activateValidator();
     //done();
  },*/
  methods:{
    limpiar:function (){
      this.$resetValidation();
      this.reporte={ mes:"",year:"",periodo:"",rango:"",tipo:"reporte_caja"};
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
        reporteFinanciero.$set('reporte','reporte-caja-tabla');
      });
    }
  },
  watch: { 
          'reporte.id_caja' : function(val) {
             
              var context = this;
              var valor = val;
              
              var caja = _.find(context.cats.cajas, function(o) {
                  return o.id == valor;
              });
              
              var responsable = caja.responsable2;
              var centrocontable = _.find(this.cats.centros, function(o) {
                  return o.id == caja.centro_id;
              })
              context.reporte.caja_cuenta_id = caja.cuenta_id;
              responsable_id.value = responsable.nombre+' '+responsable.apellido;;
              centro_contable.value =  centrocontable.nombre;
              
              
          }
      }
});


