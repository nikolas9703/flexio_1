$( document ).ready(function() {
    popular_cliente_nombre_comercial($('select[name*="campo[uuid_cliente]"]').val(),$("#hidden_uuid_sociedad").val());
    popular_cliente_contactos($('select[name*="campo[uuid_cliente]"]').val(),$("#hidden_uuid_contacto").val());
    popular_elementos_modulo($("#relacionado_con").val(),$("#hidden_uuid_relacion").val()); 
});

$(function(){

	//Datos para el Formulario Principal: Crear
	$('#crearActividad').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
	});
	//console.log( $('.modal-body form#crearActividad').find('#uuid_tipo_actividad'));
	/*if( $('.wrapper-content form#crearActividad').find('#uuid_tipo_actividad') ){
		$('.wrapper-content form#crearActividad').find('#uuid_tipo_actividad').rules("add",{ required: true, messages: { required: 'Introduzca el tipo.' } });
	}*/
	
 	//$('#crearActividad').find('#uuid_sociedad, #uuid_contacto').prop('disabled','disabled').empty().append('<option value="" selected="selected">Seleccione</option>');

 	//Por defecto asignado a debe ser la persona logeada
 	$('#uuid_asignado option[value="'+ uuid_usuario +'"]').prop('selected', uuid_usuario);
	
    //Inicializando datetimepicker en campo fecha/hora
    $('input[name*="campo[fecha]"]').datetimepicker({
        format: 'YYYY-MM-DD H:s'
    });

    //Abrir modals de clientes y contactos
    $("#uuid_clienteBtn").on("click", function(){
        $('#busquedaClienteModal').modal('toggle');
    });
    /*$("#uuid_contactoBtn").on("click", function(){
        $('#busquedaContactoModal').modal('toggle');
    });*/

    //Boton para mostrar venta de busqueda de Clientes
    $("#crearActividad").on("click", "#uuid_clienteBtn", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
    });

    //Al abrir el modal de Busqueda de Clientes
    //Redimensionar el jqgrid
    $('#busquedaClienteModal').on('shown.bs.modal', function (e) {
        $(".ui-jqgrid").each(function(){
            var w = parseInt( $(this).parent().width()) - 6;
            var tmpId = $(this).attr("id");
            var gId = tmpId.replace("gbox_","");
            $("#"+gId).setGridWidth(w);
        });
    });

   //Buscar Nombre Comercial y contacto dependiento del cliente, solo cuando se desde actividades
    $(".modal-body  form#crearActividad, .wrapper-content form#crearActividad").on("change", 'select[name*="campo[uuid_cliente]"] ', function(e){ 
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
 		popular_cliente_nombre_comercial(  $(this).val() );
		popular_cliente_contactos(  $(this).val() ); //Solo debe aplicarse para oportunidades
		 
	});
    //Pasar la Seleccion del modal al chosen
    $("#clientesGrid").on("click", ".viewOptions", function(){
     	$('form#crearActividad').find('select[name*="campo[uuid_cliente]"] option[value="'+ $(this).attr("data-cliente") +'"]').prop('selected', 'selected');
    	 setTimeout(function(){
             $(".chosen-select").chosen({
                 width: '100%'
             }).trigger('chosen:updated');
         }, 500);
 
        $('#busquedaClienteModal').modal('hide');

        //Popular select de nombre comerciales
        popular_cliente_nombre_comercial($(this).attr("data-cliente"));
        
        //Popular select de contactos
        popular_cliente_contactos($(this).attr("data-cliente")); //Solo debe aplicarse para oportunidades
    });

    //Actualizar relacion al seleccionar Relacionado con(Modulo)
    $("#relacionado_con").on("change", function(){
        var id_cat=$(this).val();
        popular_elementos_modulo(id_cat);

    });


});

//** Lista dependiente se usa en relacion con **//
function popular_elementos_modulo(id_cat,selected)
{
    $.ajax({
        url: phost() + 'actividades/ajax-listar-elementos-modulo',
        data: {
            id_cat: id_cat,
            erptkn: tkn
        },
        type: "POST",
        dataType: "json",
        cache: false,
    }).done(function(json) {
        //Check Session
        if( $.isEmptyObject(json.session) == false){
            window.location = phost() + "login?expired";
        }

        //If json object is not empty.
        if( $.isEmptyObject(json.results[0]) == false ){

            $('#uuid_relacion').empty().append('<option value="">Seleccione</option>').removeAttr('disabled');
            $.each(json.results[0], function(i, result){
                if(selected==result['uuid']){
                    $('#uuid_relacion').append('<option selected value="'+ result['uuid'] +'">'+ result['etiqueta'] +'</option>');
                }
                else{
                    $('#uuid_relacion').append('<option value="'+ result['uuid'] +'">'+ result['etiqueta'] +'</option>');
                }
            });
        }else{

            $('#uuid_relacion').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
        }

    });
}

//** Esta funcion popula el select de nombre comercial **//
 function popular_cliente_nombre_comercial(uuid_cliente,selected)
{
    if(uuid_cliente==""){
        return false;
    }

    $.ajax({
        url: phost() + 'oportunidades/ajax-seleccionar-cliente-sociedades',
        data: {
            uuid_cliente: uuid_cliente,
            erptkn: tkn
        },
        type: "POST",
        dataType: "json",
        cache: false,
    }).done(function(json) {

        //Check Session
        if( $.isEmptyObject(json.session) == false){
            window.location = phost() + "login?expired";
        }

        //If json object is not empty.
        if( $.isEmptyObject(json.results[0]) == false ){


            $('#uuid_sociedad').empty().append('<option value="">Seleccione</option>').removeAttr('disabled');
            $.each(json.results[0], function(i, result){
                if(selected==result['uuid_sociedad']){
                    $('#uuid_sociedad').append('<option selected value="'+ result['uuid_sociedad'] +'">'+ result['nombre_comercial'] +'</option>');
                }
                else{
                    $('#uuid_sociedad').append('<option value="'+ result['uuid_sociedad'] +'">'+ result['nombre_comercial'] +'</option>');
                }

            });
        }else{

            $('#uuid_sociedad').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
        }

    });
}


 
//** Esta funcion popula el select de contactos, Solo se debe usar en ciertos casos, En contactos no se usa**//
function popular_cliente_contactos(uuid_cliente,selected)
{
    if(uuid_cliente==""){
        return false;
    }

    $.ajax({
        url: phost() + 'oportunidades/ajax-seleccionar-cliente-contactos',
        data: {
            uuid_cliente: uuid_cliente,
            erptkn: tkn
        },
        type: "POST",
        dataType: "json",
        cache: false,
    }).done(function(json) {

        //Check Session
        if( $.isEmptyObject(json.session) == false){
            window.location = phost() + "login?expired";
        }

        //If json object is not empty.
        if( $.isEmptyObject(json.results[0]) == false ){

            $('#uuid_contacto').empty().append('<option value="">Seleccione</option>').removeAttr('disabled');
            $.each(json.results[0], function(i, result){
                if(selected==result['uuid_contacto']){
                    $('#uuid_contacto').append('<option selected value="'+ result['uuid_contacto'] +'">'+ result['nombre_contacto'] +'</option>');
                }
                else{
                    $('#uuid_contacto').append('<option value="'+ result['uuid_contacto'] +'">'+ result['nombre_contacto'] +'</option>');
                }
            });
        }else{

            $('#uuid_contacto').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
        }

    });
}