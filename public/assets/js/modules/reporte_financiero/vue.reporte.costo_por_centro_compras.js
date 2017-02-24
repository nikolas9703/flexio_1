var reporteCostoPorCentroCompras = Vue.extend({
  template:'#tablelizer-costo-por-centro-compras',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Informe de costos por centro contable y categor&iacute;a de item",
      detalle:[],
      totales: [],
      parametros: [],
      logoEmpresa: empresa_logo,
    };
  },
  ready:function(){
    this.detalle = this.info[0].detalle;
    this.totales = this.info[0].totales;
    this.parametros = this.info[0].parametros;
  },
  computed:{},
  methods:{
    esRetenido:function(codigo){
      return window.retiene_impuesto=="si"?true:false;
    },
  }
});
Vue.component('reporte-costo-por-centro-compras', reporteCostoPorCentroCompras);
