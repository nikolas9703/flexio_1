(function(){
    var crearCotizacionesForm = $("#crearCotizacionesForm");
  //$('.dropdown-toggle').dropdown();
  $('ul.dropdown-menu li a').on('click', function (event) {
    $('div#moduloOpciones').toggleClass('open');
});
  $("a#agregarContactoBtn").click(function(e){
    e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
    $('#crearContacto').find('input[id="campo[nombre]"]').val('');
    $('#crearContacto').find('input[id="campo[telefono]"]').val('');
    $('#crearContacto').find('input[id="campo[correo]"]').val('');
    clienteProvider.config(true);
    if(clienteProvider.showContacto){
      $("#vistaCliente").addClass('hide');
      $("#vistaFormularioContacto").removeClass('hide');
      $("input[id$='uuid_cliente]']").val(id_cliente);
    }
  });
  $("a#crearCotizacion").click(function (e) {
    console.log("crear cotizacion dese cliente");
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();

      //Limpiar formulario
     // crearCotizacionesForm.append('<input type="hidden" name="cliente_id" value="'+ cliente_id +'" />');
      $("#crearCotizacionesForm").append('<input type="hidden" name="cliente_id" value="" />');
      //Enviar formulario
      $("#crearCotizacionesForm").submit();
      $('body').trigger('click');
  });
})();
$( document ).ready(function() {
if (location.href.indexOf("#") != -1) {
    setTimeout(function () {    
    $('#crearContacto').find('input[id="campo[nombre]"]').val('');
    $('#crearContacto').find('input[id="campo[telefono]"]').val('');
    $('#crearContacto').find('input[id="campo[correo]"]').val('');   
      $("#vistaCliente").addClass('hide');
      $("#vistaFormularioContacto").removeClass('hide');
      $("input[id$='uuid_cliente]']").val(id_cliente);
  }, 300);
    }
    });


