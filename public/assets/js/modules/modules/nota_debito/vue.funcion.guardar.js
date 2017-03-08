var guardar = {
  methods:{
    guardar:function(){
      var self = this;
      var $form = $("#form_crear_notaDebito");
      $form.validate({
        ignore: '',
        wrapper: '',
        errorPlacement: function(error, element) {
          if($('#notaDebitoItemTable').find('input[id*="cuenta_id"]').length > 0 || $('#notaDebitoItemTable').find('input[id*="monto"]').length > 0) {
            self.tablaError = 'campo es requerido';
          }else{
            error.insertAfter(element);
          }
        },
        submitHandler:function(form){

          if(_.isUndefined(notaDebitoFormulario.datosFactura.id)){
            self.tablaError ="debe de seleccionar una factura";
            return false;
          }


           if( parseFloat(_.sumBy(notaDebitoFormulario.filas,function(o){ return o.monto;})) > parseFloat(notaDebitoFormulario.datosFactura.total)){
            self.tablaError ="El total no puede ser mayor al monto de la factura";
            return false;
          }
          notaDebitoFormulario.$set('proveedorDisable',false);
          notaDebitoFormulario.$set('disabledFactura',false);
          notaDebitoFormulario.$set('defaultDisable',false);
          notaDebitoFormulario.$set('estadoDisable',false);
          notaDebitoFormulario.$set('disabledCabecera',false);
          notaDebitoFormulario.$children[0].$set('itemDisable',false);

          //habilitado campos usando jquery
          $('form').find('input, select').prop('disabled', false);

          notaDebitoFormulario.$nextTick(function(){
            form.submit();
          });
        }
      });
    }
  }
};
