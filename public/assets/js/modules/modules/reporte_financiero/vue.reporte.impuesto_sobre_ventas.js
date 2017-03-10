var reporteImpuestoSobreVentas = Vue.extend({
  template:'#reporte_impuesto_sobre_ventas',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Informe de impuestos sobre las ventas",
      ventas:{header:[], filas:[],sumatorias:[],footer_titulo:"Total de impuestos en ventas"},
      compras:{header:[],filas:[], nota_debito:[], sumatorias:[],footer_titulo:"Total de impuestos en compras"},
      notas_creditos:{header:[],filas:[],footer_titulo:"Total de impuestos en compras"},
      notas_debitos:{header:[],filas:[],footer_titulo:"Total de impuestos en compras"}
    };
  },
  ready:function(){
    //ventas
    this.ventas.header = _.keys(this.info[0].ventas); // obtiene los nombre de la cabecera de la columnas
    this.ventas.filas = this.info[0].ventas;
    //notas de credito
    this.notas_creditos.header = _.keys(this.info[0].notas_creditos); // obtiene los nombre de la cabecera de la columnas
    this.notas_creditos.filas = this.info[0].notas_creditos;

    //compras
    this.compras.header = _.keys(this.info[0].compras); // obtiene los nombre de la cabecera de la columnas
    this.compras.filas = this.info[0].compras;
    //notas de debito
    this.notas_debitos.header = _.keys(this.info[0].notas_debitos);
    this.notas_debitos.filas = this.info[0].notas_debitos;
  },
  computed:{

    totales_ventas:function(){
      var ventas = this.ventas.filas;
      var notas_creditos = this.notas_creditos.filas;
      var total_notas_credito = [], obj = {};

      for(var llave in this.notas_creditos.filas){
        obj[llave]= notas_creditos[llave] + ventas[llave];
      }
      total_notas_credito.push(obj);
      return total_notas_credito[0];
     },
    totales_compras:function(){
      var compras = this.compras.filas;
      var notas_debitos = this.notas_debitos.filas;
      var total_notas_debito = [], obj = {};

      for(var llave in this.notas_debitos.filas){
        obj[llave]= notas_debitos[llave] + compras[llave];
      }
      total_notas_debito.push(obj);
      return total_notas_debito[0];
    },
    impuesto_pagar:function(){
      var obj = {};
      var impuesto_pagar;
      for(var llave in this.totales_compras){
        if(llave !=='subtotal' && llave !=='total'){
          obj[llave] =  this.totales_ventas[llave] - this.totales_compras[llave];
        }
      }
      impuesto_pagar = obj;
      return impuesto_pagar;
    }

  },
  methods:{

    header_formato:function(titulo){
      return _.startCase(titulo);
    },
    sumaTotales:function(impuestos){
      return _.reduce(impuestos, function(sum, n) {
        return sum + n;
        }, 0);
    }
  }
});
Vue.component('reporte-impuesto-sobre-venta', reporteImpuestoSobreVentas);
