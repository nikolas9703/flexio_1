var ventana;
var crear = {
  settings: {
     botonCancelar : $("#cancelarBtn"),
 	   botonGuardar : $("#guardarBtn"),
     formId : $('#crearCentroForm')
   },
   init:function(){
     ventana = this.settings;
     this.inicializar_plugin();
     this.eventos();
   },
   eventos:function(){
     ventana.botonCancelar.click(function(e){
       crear.limpiarFormulario();
       centro.modal.crear.modal('hide');
     });
     centro.modal.crear.on('hidden.bs.modal', function () {
       crear.limpiarFormulario();
     });
     ventana.botonGuardar.click(function(e){
       e.preventDefault();
       var selfButton = this;
       if(ventana.formId.validate().form() === true)
       {
         //$(selfButton).unbind("click");
         $("#guardarBtn").attr("disabled", true);
         ventana.formId.find('.chosen-select').prop('disabled',false);
         var guardar = moduloContabilidad.crearCentroContable(ventana.formId);
         guardar.done(function(data){
           var respuesta = $.parseJSON(data);
           if(respuesta.estado == 200)
           {
             $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.mensaje+'</div>');
             centro.recargar();
           }
             $("#guardarBtn").attr("disabled", false);
             //$(selfButton).bind("click");
             crear.limpiarFormulario();
             centro.modal.crear.modal('hide');
         });

       }
     });
   },
   limpiarFormulario:function(){
     var validator = ventana.formId.validate();
     validator.resetForm();
     ventana.formId.trigger("reset");
   },
   inicializar_plugin:function(){
     ventana.formId.validate({
       focusInvalid: true,
       ignore: ".ignore",
       wrapper: '',
     });
   }
};

(function(){
  crear.init();
})();
