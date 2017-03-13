var tablaComponente = Vue.extend({
  template:'#tempcuentas-montos',
  props:['lista'],
  methods:{
    addRow:function(event){
      this.lista.push({cuenta_id:'',descripcion:'',monto:'0.00'});
      setTimeout(function() {
          $(".select2").select2({
             theme: "bootstrap",
             width:"100%"
          });
    }, 300);
    },
    deleteRow:function(fila){
      this.lista.$remove(fila);
    }
  }
});
