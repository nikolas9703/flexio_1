var presupuestoPeriodo = Vue.extend({
  template:'#template_presupuesto_periodo',
  props:['datosTabla','showGuardar'],
  ready:function(){
    this.popularGrid();
  },
  methods:{
    popularGrid:function(){
      var obj = new jqGridPresupuestoPeriodo(this.datosTabla);
      obj.setJqgrid();
    },
    guardarPresupuesto:function(){

      var objFrom = {
        presupuestoForm: $('#form_crear_presupuesto'),
      };

      objFrom.presupuestoForm.validate({
        ignore: '',
        wrapper: '',
      });

      var formularioPresupuesto = objFrom.presupuestoForm;
      if(formularioPresupuesto.valid() === true){
       $(".guardarPresupuesto").attr("disabled", true);   
       objFrom.presupuestoForm.submit();
     }else{
       console.error("error al guardar");
       console.log(formularioPresupuesto);
     }
   },
    abrirDialogo:function(event){
        console.log("text");
    }
  },
  watch:{
    datosTabla:function(){
      this.popularGrid();
    }
  }
});

Vue.component('presupuesto-periodo', presupuestoPeriodo);
