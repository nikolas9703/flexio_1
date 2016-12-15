var reporteEstadoCuentaCliente = Vue.extend({
  template:'#tablelizer-estado-cuenta-cliente',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Estado de cuenta de cliente",
      clienteInfo:{},
      resumenEstadoCuenta:{},
      detalle:[],
      fecha_inicial:'',
      fecha_final:'',
      centro_facturable:[],
      logoEmpresa: empresa_logo,
      reporteAntiguedad: []
    };
  },
  ready:function(){
    this.clienteInfo = this.info[0].cliente;
    this.resumenEstadoCuenta = this.info[0].resumen;
    this.detalle = this.info[0].detalle;
    this.fecha_inicial = this.info[0].fecha_inicial;
    this.fecha_final = this.info[0].fecha_final;
    this.reporteAntiguedad = this.info[0].datos_antiguedad;
    this.centro_facturable = this.info[0].cliente.centro_facturable || [];
  },
  computed:{
    entre_fechas:function(){
      return this.fecha_inicial +' al '+ this.fecha_final;
    }
  },
  methods:{
    esPago:function(codigo){
      return _.startsWith(codigo, 'PAY');
    },
    esFactura:function(codigo){
      return _.startsWith(codigo, 'INV');
    }
  }
});
Vue.component('reporte-estado-cuenta-cliente', reporteEstadoCuentaCliente);
