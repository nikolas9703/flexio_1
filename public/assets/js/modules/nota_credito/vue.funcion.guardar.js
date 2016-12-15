var guardar = {
  methods:{
    guardar:function(){
      var self = this;
      var $form = $("#form_crear_notaCredito");
      $form.validate({
        ignore: '',
        wrapper: '',
        errorPlacement: function(error, element) {
          if($('#notaCreditoItemTable').find('input[id*="cuenta_id"]').length > 0 || $('#notaCreditoItemTable').find('input[id*="monto"]').length > 0) {
            self.tablaError = 'campo es requerido';
          }else{
            error.insertAfter(element);
          }
        },
        submitHandler:function(form){

          if(_.isUndefined(notaCreditoFormulario.datosFactura.id)){
            self.tablaError ="debe de seleccionar una factura";
            return false;
          }

          if( _.sumBy(notaCreditoFormulario.filas,function(o){ return o.monto;}) > notaCreditoFormulario.datosFactura.total){
            self.tablaError ="El total no puede ser mayor al monto de la factura";
            return false;
          }

          notaCreditoFormulario.$set('disabledFactura',false);
          notaCreditoFormulario.$set('defaultDisable',false);
          notaCreditoFormulario.$set('estadoDisable',false);
          notaCreditoFormulario.$set('disabledCabecera',false);
          notaCreditoFormulario.$children[0].$set('itemDisable',false);
          notaCreditoFormulario.$nextTick(function(){
            form.submit();
          });
        }
      });
    }
  }
};
