export default {
 methods:{
     guardar: function () {
         var self = this;
         var $form = $("#form_crear_cotizacion_alquiler");
         var tableErrors = $("#items-errores");
         var tablaCotizacion = $("#itemsTable");

         this.$data.campoDisabled.clienteDisabled = false;
         this.$data.campoDisabled.estadoDisabled = false;
         this.$data.campoDisabled.botonDisabled = true;
          $('#guardarBtn').html('<i class="fa fa-spinner fa-spin fa-lg fa-fw"></i><span>Guardando...</span>');
    var objFrom = {
      presupuestoForm: $("#form_crear_cotizacion_alquiler"),
    };

    objFrom.presupuestoForm.validate({
      // debug: true,
      ignore: '',
      wrapper: '',
      errorPlacement: function(error, element) {
    if(tablaCotizacion.find('input[id*="item"]').length > 0 || tablaCotizacion.find('input[id*="periodo_tarifario"]').length > 0 || tablaCotizacion.find('input[id*="categoria"]').length > 0 || tablaCotizacion.find('input[id*="impuesto"]').length > 0 || tablaCotizacion.find('input[id*="cuenta"]').length > 0) {
      tableErrors.html('campo es requerido');
    }else{
      error.insertAfter(element);
    }
   }
    });

    var formularioPresupuesto = objFrom.presupuestoForm;

    if(formularioPresupuesto.valid() === true){
      objFrom.presupuestoForm.find('input:disabled, select:disabled').removeAttr('disabled');
       Vue.nextTick(function(){
          objFrom.presupuestoForm.submit();
       });
   }else{
     $('#guardarBtn').text('Guardar');
       self.$data.campoDisabled.clienteDisabled = true;
       self.$data.campoDisabled.estadoDisabled = true;
       self.$data.campoDisabled.botonDisabled = false;
   }

    }
 }
};
