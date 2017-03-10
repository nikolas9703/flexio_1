var tablaComponenteDevoluciones = Vue.extend({
  template:'#devolucion-items',
  props:['productos','boton','error','vista'],
  data:function(){
    return{
      botonDisabled:this.boton,
      data_subtotales: 0,
      data_impuestos : 0,
      tablaError: this.error,
    };
  },
  computed:{
      totales:function(){
          return this.subtotales + this.impuestos;
      },
      camposDisable:function(){
        return this.$parent.$data.disableDevolucion;
      },
      impuestos:function(){
        var impuestos = this.data_impuestos;
        if(this.vista ==='ver') impuestos  =  _.sumBy(this.productos,function(o) {return parseFloat(o.impuesto_total);});
        return impuestos;
      },
      subtotales:function(){
        var subtotales = this.data_subtotales;
      if(this.vista ==='ver')  subtotales = _.sumBy(this.productos,function(o) {return parseFloat(o.precio_total * o.cantidad_devolucion);});
        return subtotales;
      }
  },
  methods:{
    validarCantidad:function(valor,index,event){

        //refactorizar function
        var elementId = event.target.id;
        var cantidad = parseInt(valor);
        if(_.isNaN(cantidad)){
            $('#'+elementId).addClass('has-error error');
            $('#'+elementId).popover({placement:'right',title:'Cantidad',content:"Datos Invalido", show: true});
            $('#'+elementId).popover('show');
            this.boton = true;
            return false;

        }

        if(cantidad === 0 || cantidad > parseInt(this.productos[index].cantidad)){
        this.boton = true;
        $('#'+elementId).addClass('has-error error');
        $('#'+elementId).popover({placement:'right',title:'Cantidad',content:"el numero no puede ser mayor a la cantidad actual.", show: true});
        $('#'+elementId).popover('show');
     }else{
        $('#'+elementId).removeClass('has-error error');
        $('#'+elementId).popover('destroy');
        this.boton = false;
        this.calcularTotales(cantidad, index);
     }
   },
    calcularTotales:function(valor, index){
        this.productos[index].precio_total = this.productos[index].precio_unidad * valor;
        this.productos[index].impuesto_total =(parseFloat(this.productos[index].impuesto.impuesto) / 100) * this.productos[index].precio_total;
        this.data_subtotales = _.sumBy(this.productos,function(o) {return parseFloat(o.precio_total);}) ;
        this.data_impuestos = _.sumBy(this.productos,function(o) {return parseFloat(o.impuesto_total);}) ;
    }
  }
});
