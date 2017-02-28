$(function(){


    $('#clearBtn').click(function(){
          $("#color_ojo").css("color", "#c4c4c4");
    });

$('.borrar_buscador').click(function(){
   var id = $(this).attr("data-id");
      var modulo = $('#modulo').val();
             $.ajax({
                url: phost() + "Busqueda/ajax_borrar_variables",
                type:"POST",
                data:{
                    erptkn:tkn,
                    id: id
                },
                dataType:"json",
                success: function(data){

                      if(data.response == true){
                        location.reload(phost()+modulo+'/listar');
                      }
                 }
             });
});
   $('a.boton_buscador').click(function(){

         var id = $(this).attr("data-id");

         $("#formularioBuscador").trigger('reset');
         $(this).attr("background-color","#SFERRE");
          $.ajax({
              url: phost() + "Busqueda/ajax_get_variables",
              type:"POST",
              data:{
                  erptkn:tkn,
                  id: id
              },
              dataType:"json",
              success: function(data){
                 $("#color_ojo").css("color", "#0070ba");
                    obj = JSON.parse(data.campos);
                    $.each(obj, function(index, value) {
                         $('#'+index).val(value);
                    });
                    $("select").trigger("chosen:updated");
                    $("#formularioBuscador").find('.ibox-content:not(:visible)').prev().find('a.collapse-link').trigger('click');
                    $('#searchBtn').trigger('click');
                 }
           });
   });
   $('#guardarbusqueda').click(function(){
      var disabled_text = [];

 		$('#optionsModal, #opcionesModal').find('.modal-title').empty().append('Guardar parámetros de búsqueda avanzada');
 		$('#optionsModal, #opcionesModal').find('.modal-body').empty().append('Nombre:</br> <input id="nombre_busqueda" name="busqueda" class="form-control" value="" placeholder="" type="text">');
 		$('#optionsModal, #opcionesModal').find('.modal-footer')
 			.empty().append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
 			.append('<button id="guardarBusquedaAccion" '+disabled_text+' class="btn btn-w-m btn-primary" type="button">Guardar</button>');
      $('#optionsModal, #opcionesModal').modal('show');
 	 });

 $('#optionsModal').on("click", "#guardarBusquedaAccion", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
     var nombre_busqueda = $('#nombre_busqueda').val();
     var modulo = $('#modulo').val();

     $('#guardarBusquedaAccion').attr("disabled","disabled");
    $.ajax({
       url: phost() + 'Busqueda/ajax_guardar_variables',
       data: $('form#formularioBuscador').serialize()+'&busqueda[busqueda]='+nombre_busqueda,
       type: "POST",
       dataType: "json",
       cache: false,
   }).done(function(json) {
       //Check Session
       if( $.isEmptyObject(json.session) == false){
           window.location = phost() + "login?expired";
       }
   $('#optionsModal, #opcionesModal').modal('hide');
    location.reload(phost()+modulo+'/listar');
     });
  });
 })
