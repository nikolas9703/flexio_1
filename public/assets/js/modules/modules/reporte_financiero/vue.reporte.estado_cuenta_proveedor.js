var reporteEstadoCuentaProveedor = Vue.extend({
  template:'#tablelizer-estado-cuenta-proveedor',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Estado de cuenta de proveedor",
      proveedorInfo:{},
      resumenEstadoCuenta:{},
      detalle:[],
      fecha_inicial:'',
      fecha_final:'',
      logoEmpresa: empresa_logo,
      reporteAntiguedad: []
    };
  },
  ready:function(){
    this.proveedorInfo = this.info[0].proveedor;
    this.resumenEstadoCuenta = this.info[0].resumen;
    this.detalle = this.info[0].detalle;
    this.fecha_inicial = this.info[0].fecha_inicial;
    this.fecha_final = this.info[0].fecha_final;
    this.reporteAntiguedad = this.info[0].datos_antiguedad;
  },
  computed:{
    entre_fechas:function(){
      return this.fecha_inicial +' al '+ this.fecha_final;
    }
  },
  methods:{
    esPago:function(codigo){
      return _.startsWith(codigo, 'PGO');
    },
    esFactura:function(codigo){
      return _.startsWith(codigo, 'FT');
    }
  }
});
Vue.component('reporte-estado-cuenta-proveedor', reporteEstadoCuentaProveedor);
