var tablaComponenteNotaCredito = Vue.extend({
  template:'#items_entradas',
  props:['rows','boton','error','items'],
  data:function(){
    return{
      itemDisable:true
    };
  },
  ready:function(){
    this.$nextTick(function(){
        $(".select2").select2({
           theme: "bootstrap",
           width:"100%"
        });
      });
  },
  computed:{
    total:function(){
        console.log('compute total')
      return this.subtotal + this.impuesto;
    },
    subtotal:function(){
      var subtotal = _.sumBy(this.items,function(o){
        return parseFloat(o.monto) || 0;
      });
      return subtotal;
    },
    impuesto:function(){
      var impuesto = _.sumBy(this.items,function(o){
        return parseFloat(o.impuesto_total) || 0;
      });
      return impuesto;
    }
  },
  methods:{
      /*addRow:function(event){
        var descripcion = this.$parent.$data.incluir?this.$parent.$data.datos.narracion:'';
        this.rows.push({descripcion:descripcion, cuenta_id:'',monto:'0.00'});
        this.$nextTick(function(){
            $(".select2").select2({
               theme: "bootstrap",
               width:"100%"
            });
          });
      },
      deleteRow:function(fila){
        this.rows.$remove(fila);
      },*/
      calcular:function(monto, index){
        
        var operacionPorcentaje = new Operacion(monto, this.items[index].impuesto == null ? '': this.items[index].impuesto.impuesto);
        //this.items[index].monto = monto;
        this.items[index].impuesto_total = operacionPorcentaje.porcentajeDelTotal();
        //this.items.$set(index,this.items[index]);
        var monto_factura = parseFloat(this.$parent.$data.datosFactura.total);
        factura = _.isNaN(monto_factura)?0:monto_factura;
        if(this.total > parseFloat(factura)){
          this.boton = true;
          this.error = "El total no puede ser mayor al monto de la factura";
        }else if( monto > parseFloat(this.items[index].precio_total)){
          this.boton = true;
          this.error = "El crédito no puede ser mayor al monto del Items";
        }else{
          this.boton = false;
          this.error = "";
        }
      }
  }
});
