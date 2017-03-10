var tablelizerGananciasPerdidas = Vue.extend({
  template:'#tablelizer-ganancias-perdidas',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Estado de Ganancias y P\u00E9rdidas",
      ingresos:{header:[], filas:[], sumatorias:[],footer_titulo:"Total de ingresos",ganancia_bruta:"Ganancia bruta"},
      costos:{header:[], filas:[], sumatorias:[],footer_titulo:"Total de costos de venta",ganancia_bruta:"Ganancia bruta menos costos de venta"},
      gastos:{header:[], filas:[], sumatorias:[],footer_titulo:"Total de gastos", ganancia_neta:"Ganancia neta"},
      seeTotal:seeTotal
    };
  },
  ready:function(){
    //ingresos
    this.ingresos.header = _.keys(this.info[0].ingreso[0]); // obtiene los nombre de la cabecera de la columnas
    this.ingresos.filas = this.info[0].ingreso;
    this.ingresos.sumatorias = this.ingresos.filas[0];
    //costos
    this.costos.header = _.keys(this.info[0].costo[0]); // obtiene los nombre de la cabecera de la columnas
    this.costos.filas = this.info[0].costo;
    this.costos.sumatorias = this.costos.filas[0];
    //gastos
    this.gastos.header = _.keys(this.info[0].gasto[0]); // obtiene los nombre de la cabecera de la columnas
    this.gastos.filas = this.info[0].gasto;
    this.gastos.sumatorias = this.gastos.filas[0];

    this.$nextTick(function(){
          var tr = document.querySelectorAll('[data-level="1"]');
          $('table.cuentas').tabelize({
            fullRowClickable : true,
        		onReady : function(){
              _.forEach(tr,function(item){
                $(item).trigger('click');
              });
        		},
          });
    });

  },
  methods:{
    nivel:function(codigo){
      return  _.split(codigo,".").length - 1;
    },
    nivel_hijos:function(codigo){
      var level =  _.split(codigo,".").length - 2;
      return level;

  },
  totalCosto:function(ingresos,costos){
      console.log(ingresos);
      return 0;
  }
  }
});
Vue.component('tablelizer-ganancias-perdidas', tablelizerGananciasPerdidas);
