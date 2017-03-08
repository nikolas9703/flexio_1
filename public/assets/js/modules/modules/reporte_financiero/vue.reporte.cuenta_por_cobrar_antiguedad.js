var reporteCuentaPorCobrarAntiguedad = Vue.extend({
  template:'#tablelizer-cuenta-por-pagar-antiguedad',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Cuentas por cobrar por antigüedad",
      nombreColumna:"Cliente",
      iconObject: {
         'fa fa-caret-down fa-rotate-270 fa-lg': true,
         'fa fa-caret-down fa-lg': false
  },
      cuentas:[]
    };
  },
  ready:function(){
    this.cuentas = this.info[0].cuentas_antiguedad;
    var context = this;
    this.cuentas.forEach(function(item,a){
      context.cuentas[a].icono = "fa fa-caret-down fa-rotate-270 fa-lg";
      context.cuentas.$set(a,context.cuentas[a]);
    });

  },
  methods:{
    hijosClass:function(item){
      return item.tipo==='factura'? 'collapse row'+item.padre_id:'';
    },
    open:function(item){
      return item.tipo ==='factura'?'':'collapse';
    },
    target:function(item){
      return item.tipo ==='cliente'?'.row'+item.id:'';
    },
    getId:function(item){
      return item.tipo ==='cliente'?'row'+item.id:'';
    },
    toggleCollapse:function(item,e,index){
      var target = e.target;
      var clase = $(target).prop('class');
      $('.row'+item.id).collapse('toggle');
      if(clase =='fa fa-caret-down fa-rotate-270 fa-lg'){
        $(target).removeClass('fa fa-caret-down fa-rotate-270 fa-lg');
        $(target).addClass('fa fa-caret-down fa-lg');
        this.cuentas[index].icono = "fa fa-caret-down fa-lg";
        this.cuentas.$set(index,  this.cuentas[index]);

      }else{
        $(target).removeClass('fa fa-caret-down fa-lg');
        $(target).addClass('fa fa-caret-down fa-rotate-270 fa-lg');
        this.cuentas[index].icono = "fa fa-caret-down fa-rotate-270 fa-lg";
        this.cuentas.$set(index,  this.cuentas[index]);

      }

    }
  }
});
Vue.component('reporte-cuenta-por-cobrar-antiguedad', reporteCuentaPorCobrarAntiguedad);
