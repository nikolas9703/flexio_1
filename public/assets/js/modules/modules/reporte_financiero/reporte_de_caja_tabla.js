var reporteDeCaja = Vue.extend({
  template:'#tablaReporteDeCaja',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Informe de caja menuda",
      datos_reporte:[],
      totalcredito:'',
      totaldebito:'',
      nombrecaja: '',
      centrocontable: '',
      responsab: '',
      rangodefechas: {
          desde: '',
          hasta: ''
      }
    };
  },
  ready:function(){
    this.nombrecaja = cajaID[cajaID.selectedIndex].text;
    this.centrocontable = centro_contable.value;
    this.responsab = responsable_id.value;
    this.rangodefechas.desde = fecha_desde.value;
    this.rangodefechas.hasta = fecha_hasta.value;
    this.datos_reporte = this.info[0];
    this.totales();
  },
  computed:{
      
  },
  methods:{
    'totales' : function() {
          var context = this;
          var totdeb = 0;
          var totcred = 0;
            _.forEach(context.info[0], function(o){
                totdeb = totdeb + parseFloat(o.debito);
                totcred = totcred + parseFloat(o.credito);
            })
          context.totalcredito = totcred.toFixed(2).toString();
          context.totaldebito = totdeb.toFixed(2).toString();
      },
      
      'showornot' : function(val) {
           if (val>0) {
               return true;
           } else {
               return false;
           }
           
          
      }
  }
});
Vue.component('reporte-caja-tabla', reporteDeCaja);



