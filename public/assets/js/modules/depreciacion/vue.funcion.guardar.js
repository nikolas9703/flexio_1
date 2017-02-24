var guardar = {
  methods:{
    guardar:function(){
      var self = this;
      var $form = $("#form_crear_depreciaciones");
      $("#form_crear_depreciaciones").validate({
        ignore: '',
        wrapper: '',
        errorPlacement: function(error, element) {
          if($('#tablaItemDepreciacion').find('input[id*="depreciacion"]').length > 0) {
            self.tablaError = 'campo es requerido';
          }else{
            error.insertAfter(element);
          }
        },
        submitHandler:function(form){
          //self.disableDevolucion=false;
          $('.monto_depreciado').prop('disabled',false);
          depreciacionFormulario.$set('disableDevolucion',false);
          setTimeout(function() {
          form.submit();
        },200);
        }
      });
      //case #989 se actualiza el valor actual a guardar
      _.forEach(self.articulos, function(o) {
          var valor_actual = o.valor_actual;
          o.valor_actual = valor_actual - o.monto_depreciado;
      });
    }
  }
};
