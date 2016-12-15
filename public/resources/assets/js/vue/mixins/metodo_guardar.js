// jshint esversion:6
const guardarForm = {
  methods:{
      guardar(){
          var self = this;

          $(".vue-formulario").validate({
              ignore: '',
              focusCleanup: true,
              invalidHandler: function(){},
              submitHandler: function(form) {
                  $('#guardarBtn').html('<i class="fa fa-spinner fa-spin fa-lg fa-fw"></i><span>Guardando...</span>');
                  self.$data.campoDisabled.estadoDisabled = false;
                  self.$data.campoDisabled.botonDisabled = true;
                  self.campoDisabled.botonDisabled = true;

                  $('.select2').prop("disabled",false);
                  $('#cliente_id').prop("disabled",false);

                  Vue.nextTick(function(){

                    form.submit();
                  });
              }
          });
      }
  }
};

export default guardarForm;
