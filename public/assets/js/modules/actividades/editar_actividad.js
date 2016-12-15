$( document ).ready(function() {
    popular_cliente_nombre_comercial($('select[name*="campo[uuid_cliente]"]').val(),$("#hidden_uuid_sociedad").val());
    popular_cliente_contactos($('select[name*="campo[uuid_cliente]"]').val(),$("#hidden_uuid_contacto").val());
    popular_elementos_modulo($("#relacionado_con").val(),$("#hidden_uuid_relacion").val());


});

 



$(function(){

    //Inicializando datetimepicker en campo fecha/hora
    $('input[name*="campo[fecha]"]').datetimepicker({
        format: 'YYYY-MM-DD H:s'
    });

    //jQuery Validate
	$('#editarActividad').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: ''
	});

    //Abrir modals de clientes y contactos
    $("#uuid_clienteBtn").on("click", function(){
        $('#busquedaClienteModal').modal('toggle');
    });
    $("#uuid_contactoBtn").on("click", function(){
        $('#busquedaContactoModal').modal('toggle');
    });

    //Boton para mostrar venta de busqueda de Clientes
    $("#editarActividad").on("click", "#uuid_clienteBtn", function(e){
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

    //Pasar la selecciÃ³n del modal al chosen
    $("#clientesGrid").on("click", ".viewOptions", function(){

    	
    	$('form#editarActividad').find('select[name*="campo[uuid_cliente]"] option[value="'+ $(this).attr("data-cliente") +'"]').prop('selected', 'selected');
    	 setTimeout(function(){
             $(".chosen-select").chosen({
                 width: '100%'
             }).trigger('chosen:updated');
         }, 500);
        
        $('#busquedaClienteModal').modal('hide');

         popular_cliente_nombre_comercial($(this).attr("data-cliente"));
          popular_cliente_contactos($(this).attr("data-cliente"));
    });
    //Buscar Nombre Comercial y contacto dependiento del cliente
    $("form#editarActividad").on("change", 'select[name*="campo[uuid_cliente]"] ', function(e){ 
    	 
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
 		  popular_cliente_nombre_comercial(  $(this).val() );
		  popular_cliente_contactos(  $(this).val() );
		 
	});
    //Pasar la selección del modal al chosen
    $("#clientesGrid").on("click", ".viewOptions", function(){

        var rowINFO = $("#clientesGrid").getRowData($(this).attr("data-cliente"));
        $('select[name*="campo[uuid_cliente]"]').empty().find('option[value=""]').remove();
        $('select[name*="campo[uuid_cliente]"]').append('<option selected="selected" value="'+ $(this).attr("data-cliente") +'">'+ rowINFO.Nombre +'</option>');

        //Actualizar chosen
        setTimeout(function(){
            $(".chosen-select").chosen({
                width: '100%'
            }).trigger('chosen:updated');
        }, 500);

        var arreglo=new Array();
        $('select[name*="campo[uuid_cliente]"]').find('option').each(function(){
            arreglo.push(this.value);
        });
        $('select[name*="campo[uuid_cliente]"]').val(arreglo);
        $('#busquedaClienteModal').modal('hide');

        //Popular select de nombre comerciales
        popular_cliente_nombre_comercial($(this).attr("data-cliente"));

        //Popular select de contactos
        popular_cliente_contactos($(this).attr("data-cliente"));
    });

    //actualizar campo Relación con la info del módulo seleccionado
    $("#relacionado_con").on("change", function(){

        var id_cat=$(this).val();
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
                    $('#uuid_relacion').append('<option value="'+ result['uuid'] +'">'+ result['etiqueta'] +'</option>');
                });
            }else{

                $('#uuid_relacion').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
            }

        });


    });


});

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

//Esta funcion popula el select de nombre comercial
//segun el cliente seleccionado.
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

//Esta funcion popula el select de contacto
//segun el cliente seleccionado.
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


if(permiso_editar_actividad == "false"){
 	 
	$('form#crearActividad').find('select[name="campo[uuid_cliente]"]').prop("disabled", "disabled");
	//Actualizar chosen
	setTimeout(function(){
		$(".chosen-select").chosen({
			width: '100%'
		}).trigger('chosen:updated');
	}, 500);
	$("#editarActividad").find('select, input, button, textarea').prop("disabled", "disabled");
}