var reporteTransaccionesPorCentroContable = Vue.extend({
  template:'#tablelizer-transacciones-por-centro-contable',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Transacciones contables por centro contable",
      transacciones:[],
      totales: [],
      parametros: [],
      logoEmpresa: empresa_logo,
    };
  },
  ready:function(){
    this.transacciones = this.info[0].transacciones;
    this.totales = this.info[0].totales;
    this.parametros = this.info[0].parametros;
  },
  computed:{},
  methods:{}
});
Vue.component('reporte-transacciones-por-centro-contable', reporteTransaccionesPorCentroContable);
