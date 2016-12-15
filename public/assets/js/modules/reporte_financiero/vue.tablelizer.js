var reporteTablelizer = Vue.extend({
  template:'#tablelizer',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Hoja de balance de situaci\u00F3n",
      activos:{header:[], filas:[], sumatorias:[],footer_titulo:"Total de activos"},
      pasivos:{header:[], filas:[], sumatorias:[],footer_titulo:"Total de pasivo",activo_neto:"Activos netos"},
      patrimonios:{header:[], filas:[], sumatorias:[],footer_titulo:"Total del patrimonio"}
    };
  },
  ready:function(){
    //Activos
    this.activos.header = _.keys(this.info[0].activo[0]); // obtiene los nombre de la cabecera de la columnas
    this.activos.filas = this.info[0].activo;
    this.activos.sumatorias = this.activos.filas[0];
    //Pasivos
    this.pasivos.header = _.keys(this.info[0].pasivo[0]); // obtiene los nombre de la cabecera de la columnas
    this.pasivos.filas = this.info[0].pasivo;
    this.pasivos.sumatorias = this.pasivos.filas[0];
    //patrimonios
    this.patrimonios.header = _.keys(this.info[0].patrimonio[0]); // obtiene los nombre de la cabecera de la columnas
    this.patrimonios.filas = this.info[0].patrimonio;
    this.patrimonios.sumatorias = this.patrimonios.filas[0];

    this.$nextTick(function(){
          $('table.cuentas').tabelize({
            fullRowClickable : true,
        		onReady : function(){
              $("table.cuentas tr#activos0").trigger('click');
              $("table.cuentas tr#pasivos0").trigger('click');
              $("table.cuentas tr#patrimonios0").trigger('click');
        		},
          });
    });
  },
  computed:{

  },
  methods:{
    nivel:function(codigo){
      return  _.split(codigo,".").length - 1;
    }
  }
});
Vue.component('tablelizer-balance', reporteTablelizer);
