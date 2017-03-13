$(document).ready(function(){

  $('.actividades-icono').click(function(){
    $('.actividades-icono').removeClass('active');
    $(this).addClass('active');
    if($(this).hasClass('active')) {
      $('#icono').val($(this).data('icono'));
    }
  });

  $('#guardarBtn').click(function(){
    $("#formularioTipoActividad").submit();
  });

  $('#formularioTipoActividad').validate({
    focusInvalid: true,
    debug: true,
    ignore: '',
    wrapper: '',
    submitHandler: function(form) {
      var nombre = $('#nombre').val();
      var puntaje = $('#puntaje').val();
      var icono =  $('#icono').val();
      var id = $('#id').val();
      var etiqueta = _.snakeCase(nombre);
      $("#formularioTipoActividad").find('input:hidden').removeAttr('disabled');
      var parametros = {nombre:nombre, puntaje:puntaje, icono:icono, etiqueta:etiqueta,id:id};
      var formularioTipoActividad = moduloConfiguracion.guardarTipoActividad(parametros);
      formularioTipoActividad.done(function(data){
        var respuesta = $.parseJSON(data);
        if(respuesta.exito){
          $('#generalModal').modal('hide');
          $('#formularioTipoActividad').trigger("reset");
          $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.mensaje+'</div>');
          $("#tablaGrid").trigger('reloadGrid');
          $("#success-alert").fadeTo(5000, 500).slideUp(1500, function(){
               $("#success-alert").alert('close');
          });
        }
      });
    }
  });
});
