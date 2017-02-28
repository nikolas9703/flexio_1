// jshint esversion:6
// usado en anticipos y cobros,facturas ventas
const guardarForm = {
  methods:{
      guardar(){
          var self = this;

          $(".vue-formulario").validate({
              ignore: '',
              invalidHandler: function(){},
              submitHandler: function(form) {
                  $('#guardarBtn').html('<i class="fa fa-spinner fa-spin fa-lg fa-fw"></i><span>Guardando...</span>');
                  self.$data.campoDisabled.estadoDisabled = false;
                  
                  self.$data.campoDisabled.botonDisabled = true;
                  self.campoDisabled.botonDisabled = true;

                  $('.select2').prop("disabled",false);
                  $('.select2').prop("disabled",false);
                  $('.select2').removeAttr("readonly");
                  $('#cliente_id').prop("disabled",false);

                  Vue.nextTick(function(){
                    self.$root.$data.campoDisabled.estadoDisabled = false;
					console.log(self.$root.$data);
                    form.submit();
                  });
              }
          });
      }
  }
};

export default guardarForm;
