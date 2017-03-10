var tablaComponenteDepreciacion = Vue.extend({
  template:'#depreciacion_item',
  props:['productos','boton'],
  data:function(){
    return{
      botonDisabled:this.boton,
      data_subtotales: 0,
      data_impuestos : 0,
      tablaError: this.error,
    };
  },
  computed:{
      valor_actual:function(){
          return 0;
      },
      monto_depreciado:function(){
        return 0;
      }
  },
  methods:{
    calculoDepreciacion:function(porcentaje, index){
      var valor_inicial = parseFloat(this.productos[index].valor_inicial);
      var depreciacion = parseFloat(porcentaje) / 100;
      var monto_depreciado = valor_inicial * depreciacion;
      var valor_actual = valor_inicial - monto_depreciado;

      this.productos[index].valor_actual = valor_actual;
      this.productos[index].monto_depreciado = monto_depreciado;
      this.productos.$set(index,this.productos[index]);
    }
  }
});
