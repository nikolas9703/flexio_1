$(function() {

    //Expotar Casos a CSV
    $('#moduloOpciones ul').on("click", "#exportarCasosBtn", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        var casos = [];

        if($('#tabla').is(':visible') == true){

            //Exportar Seleccionados del jQgrid
            casos = $("#casosGrid").jqGrid('getGridParam','selarrrow');

        }else{

            var i = 0;
            $("#grid").find('input[type="checkbox"]:checked').each(function(){
                casos[i] = $(this).val();
                i++;
            });
        }



        var obj = new Object();
        obj.count = casos.length;


        if(obj.count) {

            obj.items = new Array();

            for(elem in casos) {

                if($('#tabla').is(':visible') == true){

                    var caso = $("#casosGrid").getRowData(casos[elem]);

                }else{

                    //fix - cuando todos los elementos no tienen las mismas propiedades
                    var caso = {};
                    $(".vcard").each(function() {
                        var encontrado = 0;

                        //verificio si la tarjeta hace coincide
                        $(this).find(".cbox").each(function(){
                            if($(this).val() == casos[elem]){
                                encontrado += 1;
                            }
                        });

                        if(encontrado > 0)
                        {
                            $(this).find("span").each(function(){
                                if($(this).data("propiedad"))
                                {
                                    var aux = quitar_tildes($(this).data("propiedad"));
                                    caso[aux] = $(this).data("value");
                                }
                            });

                            //inserto los titulos de las columnas
                            if(elem == 0)
                            {
                                var titulos = {};

                                $(this).find("span").each(function(){
                                    if($(this).data("propiedad"))
                                    {
                                        var aux = quitar_tildes($(this).data("propiedad"));
                                        titulos[aux] = preparar_titulo($(this).data("propiedad"));
                                    }
                                });

                                //Push to array
                                obj.items.push(titulos);
                            }
                        }
                    });
                }


                //Remove objects from associative array
                delete caso['Tipo'];
                delete caso['Con'];
                delete caso['linkcaso'];
                delete caso['options'];
                delete caso['link'];


                //Push to array
                obj.items.push(caso);
            }

            //return;
            var json = JSON.stringify(obj);
            var csvUrl = JSONToCSVConvertor(json);
            var filename = 'casos_'+ Date.now() +'.csv';

            //Ejecutar funcion para descargar archivo
            downloadURL(csvUrl, filename);
        }
    });

    //Inicializando datetimepicker en campo fecha/hora
    /*$('#rango_fecha').daterangepicker({
        format: 'DD/MM/YYYY',
        showDropdowns: true,
        separator: ' hasta ',
        locale: {
            applyLabel: 'Seleccionar',
            cancelLabel: 'Cancelar',
            fromLabel: 'Desde',
            toLabel: 'Hasta',
            customRangeLabel: 'Personalizar',
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            firstDay: 1
        }
    });*/
    
  //Alerta para eliminar proyectos
	$('#moduloOpciones ul').on("click", "#eliminarCasoBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var casos = [];
 		casos = $("#casosGrid").jqGrid('getGridParam','selarrrow');
  		if(casos.length==0){
			return false;
		}
		var mensaje = (casos.length > 1)?'Esta seguro que desea eliminar estos casos.?':'Esta seguro que desea eliminar este caso?';
  		var footer_buttons = ['<div class="row">',
   		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
   		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
   		   '</div>',
   		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
   		   		'<button id="eliminarCasoBtn" class="btn btn-w-m btn-success btn-block" type="button">Confirmar</button>',
   		   '</div>',
   		   '</div>'
   		].join('\n');
   	    //Init boton de opciones
   		$('#optionsModal').find('.modal-title').empty().append('Confirme');
   		$('#optionsModal').find('.modal-body').empty().append(mensaje);
   		$('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
   		$('#optionsModal').modal('show');
	});
	
	//Accion que elimina Proyectos
	$('#optionsModal').on("click", "#eliminarCasoBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		 
 		
		var casos = [];
		
 		casos = $("#casosGrid").jqGrid('getGridParam','selarrrow');
		 

		$.ajax({
			url: phost() + 'casos/eliminar',
			data: {
				erptkn: tkn,
				id_casos: casos
			},
			type: "POST",
			dataType: "json",
			cache: false,
		}).done(function(json) {

			//Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}
			
			//If json object is empty.
			if($.isEmptyObject(json.results[0]) == true){
				return false;
			}
			
			$class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
			
			//Mostrar Mensaje
			mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);
			
			//Recargar grid si la respuesta es true
			if(json.results[0]['respuesta'] == true)
			{
				//Recargar Grid
				$("#casosGrid").setGridParam({
					url: phost() + 'casos/ajax-listar-casos',
					datatype: "json",
					postData: {
						 
						erptkn: tkn
					}
				}).trigger('reloadGrid');
				
 			}
		});
	    
	    //Ocultar ventana
	    $('#optionsModal').modal('hide');
	});
	
	
    $('form#buscarCasosForm').find('input[id="rango_fecha"]').daterangepicker({
   	 format: 'DD-MM-YYYY',
       showDropdowns: true,
       separator: ' hasta ',
       opens: "left",
       ranges: {
           'Hoy': [moment(), moment()],
           'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Ultimos 7 Dias': [moment().subtract(6, 'days'), moment()],
           'Ultimos 30 Dias': [moment().subtract(29, 'days'), moment()],
           'Este Mes': [moment().startOf('month'), moment().endOf('month')],
           'Ultimo Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
       },
       locale: {
           applyLabel: 'Seleccionar',
           cancelLabel: 'Cancelar',
           fromLabel: 'Desde',
           toLabel: 'Hasta',
           customRangeLabel: 'Personalizar',
           daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
           monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
           firstDay: 1
       }
   }).on('apply.daterangepicker', function(ev, picker) {
   	 //do something, like clearing an input
   	$('#rango_fecha').prop("value", $('#rango_fecha').val());
   });

    $('#searchBtn').bind('click', searchBtnHlr);

	$('#clearBtn').click(function(e){
		e.preventDefault();
		
		$("#casosGrid").setGridParam({
			url: phost() + 'casos/ajax-listar-casos',
			datatype: "json",
			postData: {
				asunto: '',
                cliente: '',
                estado: '',
                fecha_creacion_inicio: '',
           	 	fecha_creacion_fin: '',
                erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		//Reset Fields
		$('#asunto, #cliente, #estado, #rango_fecha').val('');
	});
});


function searchBtnHlr(e) {
    e.preventDefault();
    $('#searchBtn').unbind('click', searchBtnHlr);

    var asunto 	= $('#asunto').val();
    var cliente = $('#cliente').val();
    var estado 	= $('#estado').val();
    //var rango_fecha 	= $('#rango_fecha').val();

    var fecha_creacion = $('#rango_fecha').val();
    
	var fecha_creacion_inicio ='';
	var fecha_creacion_fin='';
	
	if(fecha_creacion != ""){
		var dates = fecha_creacion.split(' hasta ');
		fecha_creacion_inicio = dates[0];
		fecha_creacion_fin = dates[1];
	}
	 
    if(asunto != "" || cliente != "" || estado != "" || rango_fecha != "")
    {
        $("#casosGrid").setGridParam({
            url: phost() + 'casos/ajax-listar-casos',
            datatype: "json",
            postData: {
                asunto: asunto,
                cliente: cliente,
                estado: estado,
           	 	fecha_creacion_inicio: fecha_creacion_inicio,
           	 	fecha_creacion_fin: fecha_creacion_fin,
                erptkn: tkn
            }
        }).trigger('reloadGrid');

        $('#searchBtn').bind('click', searchBtnHlr);
    }else{
        $('#searchBtn').bind('click', searchBtnHlr);
    }
}

$('#clearBtn').click(function(e){
    e.preventDefault();

    $('#estado option[value=""]').prop('selected', true);

    $("#casosGrid").setGridParam({
        url: phost() + 'casos/ajax-listar-casos',
        postData: {
            asunto: '',
            cliente: '',
            fecha_creacion_inicio: '',
       	 	fecha_creacion_fin: '',
            estado: '',
            erptkn: tkn
        }
    }).trigger('reloadGrid');

    //Reset Fields
    $('#asunto, #cliente, #fecha, #estado').val('');
});


//Expotar Casos a CSV
$("#exportarCasosBtn").on("click", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();

    exportarjQgrid();
});


function exportarjQgrid() {
    //Exportar Seleccionados del jQgrid
    var casos = [];

    casos = $("#casosGrid").jqGrid('getGridParam','selarrrow');

    var obj = new Object();
    obj.count = casos.length;

    if(obj.count) {

        obj.items = new Array();

        for(elem in casos) {
            var caso = $("#casosGrid").getRowData(casos[elem]);

            delete caso['linkcaso'];
            delete caso['options'];
            delete caso['link'];

            //Push to array
            obj.items.push(caso);
        }

        var json = JSON.stringify(obj);
        var csvUrl = JSONToCSVConvertor(json);
        var filename = 'casos_'+ Date.now() +'.csv';

        //Ejecutar funcion para descargar archivo
        downloadURL(csvUrl, filename);

        $('body').trigger('click');
    }
}
