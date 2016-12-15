var guardar = {
  methods:{
    guardar:function(){
      var self = this;
      var $form = $("#form_crear_devoluciones");
      if(_.isEmpty(self.articulos)){
        devolucionFormulario.$set('botonDisabled',true);
        devolucionFormulario.$set('mensajeError','No se puede guardar devolución sin items');
        return false;
      }
      $("#form_crear_devoluciones").validate({
        ignore: '',
        wrapper: '',
        errorPlacement: function(error, element) {
          if($('#devolucionDinamica').find('input[id*="cantidad_devolucion"]').length > 0 || $('#devolucionDinamica').find('input[id*="cuenta_id"]').length > 0 ) {
            self.tablaError = error;
          }else{
            error.insertAfter(element);
          }
        },
        submitHandler:function(form){
          devolucionFormulario.$set('disableDevolucion',false);
          devolucionFormulario.$set('disableCliente',false);
          setTimeout(function() {
          form.submit();
        },200);
        }
      });
    }
  }
};
