$(document).ready(function(){
  $("#perfilActualizar").validate({
  focusInvalid: true,
        ignore: '',
        wrapper: '',
        submitHandler: function(form) {

            //Enviar el formulario
            form.submit();
        }
});
});
