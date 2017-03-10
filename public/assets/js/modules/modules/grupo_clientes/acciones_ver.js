(function(){
  //$('.dropdown-toggle').dropdown();
  $('ul.dropdown-menu li a').on('click', function (event) {
    $('div#moduloOpciones').toggleClass('open');
});
  $("a#agregarContactoBtn").click(function(e){
    e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
    clienteProvider.config(true);
    if(clienteProvider.showContacto){
      $("#vistaCliente").addClass('hide');
      $("#vistaFormularioContacto").removeClass('hide');
      $("input[id$='uuid_cliente]']").val(id_cliente);
    }
  });
})();
