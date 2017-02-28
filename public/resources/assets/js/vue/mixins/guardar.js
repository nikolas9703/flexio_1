// jshint esversion:6
// usado en anticipos y cobros,facturas ventas
const guardarFormulario = {
  methods:{
      guardar(){
          var self = this;

          $(".vue-formulario").validate({
              ignore: '',
              invalidHandler: function(){},
              submitHandler: function(form) {
                  $('#guardarBtn').html('<i class="fa fa-spinner fa-spin fa-lg fa-fw"></i><span>Guardando...</span>');
                  self.$data.campoDisabled.estadoDisabled = false;
                  self.$dispatch('OnCampoDisabled',false);

                  self.$data.campoDisabled.botonDisabled = true;
                  $(form).find(':disabled').removeAttr("disabled");
                  Vue.nextTick(function(){
                    form.submit();
                  });
              }
          });
      }
  }
};

export default guardarFormulario;
