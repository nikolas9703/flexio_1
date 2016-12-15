$(function() {

    if(typeof ajustadores.id === 'undefined'){
        ajustadores.id="";
    }

    //verificar si la url actual es contactos
    //de lo contrario no mostrar multiselect del jqgrid
    var multiselect = window.location.pathname.match(/contactos/g) ? true : false;

    //Init Contactos Grid
    $("#contactosGrid").jqGrid({
        url: phost() + 'ajustadores/ajax-listar-contacto',
        datatype: "json",
        colNames:[
            'Principal',
            'Nombre',
            'Cargo',
            'Correo',
            'Celular',
            'Tel&eacute;fono',
            '&Uacute;ltimo Contacto',
            '',
            ''
        ],
        colModel:[
            {name:'principal', index:'principal', width:25,align: "center",editable: true, edittype: 'checkbox', editoptions: { value: "True:False" },
            formatter:cboxFormatter, formatoptions: { disabled: false}, classes:'check'  },
            {name:'nombre', index:'nombre', width:70 },
            {name:'Cargo', index:'cargo', width:70 },
            {name:'correo', index:'correo', width: 50 , sortable:false},
            {name:'celular', index:'celular', width: 50 , sortable:false},
            {name:'telefono', index:'telefono', width: 50,  sortable:false},
            {name:'ultimo_contacto', index:'ultimo_contacto',  width: 50,   sortable:false},
            {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false},
            {name:'options', index:'options', hidden: true}
        ],
        mtype: "POST",
        postData: {
            erptkn: tkn,
            ajustador_id: ajustadores.id
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50,100],
        rowNum: 10,
        page: 1,
        pager: "#contactosGridPager",
        loadtext: '<p>Cargando Contactos...',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        //multiselect: multiselect,
        sortname: 'nombre',
        sortorder: "ASC",
        beforeProcessing: function(data, status, xhr){
            //Check Session
            if( $.isEmptyObject(data.session) === false){
                window.location = phost() + "login?expired";
            }
        },
        loadBeforeSend: function () {
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	        $(this).closest("div.ui-jqgrid-view").find("#jqgh_contactosGrid_cb, #jqgh_contactosGrid_link").css("text-align", "center");
	    },
        beforeRequest: function(data, status, xhr){
            $('.jqgrid-overlay').show();
            $('#load_contactosGrid').addClass('ui-corner-all');
        },
        loadComplete: function(data){

            $('.jqgrid-overlay').hide();

            //check if isset data
            if($("#contactosGrid").getGridParam('records') === 0 ){
              $('#gbox_contactosGrid').hide();
              $('#contactosGridNoRecords').empty().append('No se encontraron Contactos.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
              $('#gbox_contactosGrid').show();
              $('#contactosGridNoRecords').empty();
            }

            if(multiselect == true){
	            //add class to headers
	            $("#contactosGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

	            //floating headers
	            $('#gridHeader').sticky({
	                getWidthFrom: '.ui-jqgrid-view',
	                className:'jqgridHeader'
	            });
	            //Arreglar tamaño de TD de los checkboxes
                //$("#contactosGrid_cb").css("width","50px");
              //  $("#contactosGrid tbody tr").children().first("td").css("margin-left","10px");
            }
        },
        onSelectRow: function(id){
            $(this).find('tr#'+ id).removeClass('ui-state-highlight');
        },
    });

    //Boton de opciones
    $("#contactosGrid").on("click", ".viewOptions", function(e){
        
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        var id_contacto = $(this).attr("data-id");

        var rowINFO = $("#contactosGrid").getRowData(id_contacto);
        console.log(id_contacto);
        var nombre = $(this).attr("data-nombre");
        var options = rowINFO["options"];
         //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Opciones: '+ rowINFO.nombre);
        $('#optionsModal').find('.modal-body').empty().append(options);
        $('#optionsModal').find('.modal-footer').empty();
        $('#optionsModal').modal('show');
    });
    //Resize grid, on window resize end
    $(window).resizeEnd(function() {
        $(".ui-jqgrid").each(function(){
            var w = parseInt( $(this).parent().width()) - 6;
            var tmpId = $(this).attr("id");
            var gId = tmpId.replace("gbox_","");
            $("#"+gId).setGridWidth(w);
        });
    });

    $("#iconGrid").on("click", ".viewOptionsGrid", function(e){

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Opciones: '+ $(this).closest(".chat-element").find("input[type='checkbox']").data("nombre"));
    });

    //Estas funciones aplican cuando se carga la tabla de contactos
    //desde modulo de clientes.
    if(multiselect == false){

    	//-----------------------------
        // Accciones para modo: Subpanel
        //-----------------------------
        //Abrir ventana de Crear contacto
        $("#optionsModal").on("click", "#verContacto", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            if($(this).attr('href') == "#"){

            	 var id_contacto = $(this).attr('data-contacto');

                //ocultar vista de cliente
                $('.editarFormularioClientes').addClass('hide');

                //mostrar formulario de editar contacto
                $('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="editarContactos"]').trigger('click');

                //ocultar modal
                $('#optionsModal').modal('hide');

                popular_detalle_contacto(id_contacto);
            }
        });

        //Asignar un contacto como principal
$("#optionsModal").on("click", "#asignarContactoPrincipalBtn", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            var uuid_contacto = $(this).attr('data-contacto');
  $.ajax({
				url: phost() + 'contactos/ajax-asignar-contacto-principal',
				data: {
					uuid_contacto: uuid_contacto,
					uuid_cliente: id_cliente,
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

				//If json object is empty.
				if($.isEmptyObject(json.results) == true){
					return false;
				}

				//Mostrar Mensaje
				$class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
				mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);

				//Recargar tabla de contactos
				$("#contactosGrid").setGridParam({
					url: phost() + 'contactos/ajax-listar-contactos',
					datatype: "json",
					postData: {
						nombre: '',
		                cliente: '',
		                telefono: '',
		                email: '',
		                erptkn: tkn
					}
				}).trigger('reloadGrid');

			});

            $('#optionsModal').modal('hide');
        });
    }
    
    //Boton de Exportar Contactos
    $('#exportarContactos').on("click", function(e){        
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();                        
            if($('#contactosGrid').is(':visible') == true){				
                    //Exportar Seleccionados del jQgrid
                    var ids = [];
                    ids = $("#contactosGrid").jqGrid('getDataIDs');
                    console.log(ids);
                    //Verificar si hay seleccionados
                    if(ids.length > 0){
                    console.log(ids);
                    $('#ids').val(ids);
                    $('form#exportarContactos').submit();
                    $('body').trigger('click');
                    }
    }
    });
    
    //Reload al jQgrid
	var recargar = function(){
		
		//Reload Grid
		$("#contactosGrid").setGridParam({
			url: phost() + 'ajustadores/ajax-listar-contacto',
			datatype: "json",
			postData: {
				nombre: '',
				cargo: '',
				correo: '',
				celular: '',
				telefono: '',
				ultimo_contacto_desde: '',				
				ultimo_contacto_hasta: '',				
				erptkn: tkn
			}
		}).trigger('reloadGrid');
	};
   //Boton de Buscar Colaborador
    $('#searchBtn').on("click", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            //console.log("jeloooou");
            buscarContacto();
    });     
	
	//Buscar cargo en jQgrid
	var buscarContacto = function(){

		var nombre 		= $('#nombre').val();		
		var cargo 		= $('#cargo').val();
                var celular 		= $('#celular').val();
		var correo 		= $('#correo').val();
		var telefono     	= $('#telefono').val();		
		var ultimo_contacto_desde = $('#ultimo_contacto_desde').val();
		var ultimo_contacto_hasta = $('#ultimo_contacto_hasta').val();

		if(nombre != "" || cargo != "" || celular != "" || correo != "" || telefono != "" || ultimo_contacto_desde != "" || ultimo_contacto_hasta != "")
		{
			//Reload Grid
			$("#contactosGrid").setGridParam({
				url: phost() + 'ajustadores/ajax-listar-contacto',
				datatype: "json",
				postData: {
					nombre: nombre,
					cargo: cargo,
					celular: celular,
					correo: correo,
					telefono: telefono,					
					ultimo_contacto_desde: ultimo_contacto_desde,
					ultimo_contacto_hasta: ultimo_contacto_hasta,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
	
	//Limpiar campos de busqueda
	$('#clearBtn').on("click", function(e){
		$('#buscarContactosForm').find('input[type="text"]').prop("value", "");
                recargar();
            });

    function cboxFormatter(cellvalue, options, rowObject) {
      return '<input type="checkbox"' + (cellvalue == "1" ? ' checked="checked" disabled ' : '') +
      'data-rowId="' + options.rowId + '" value="' + cellvalue + '" class="principal"/>';
  }
  $('table#contactosGrid').on('click','.principal', function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    var uuid_contacto = $(this).data('rowid');
    var parametros = {ajustador_id:ajustadores.id, contacto_id:uuid_contacto, erptkn: tkn};
    $.ajax({
                data:  parametros,
                url:   phost() + 'ajustadores/ajax-contacto-principal',
                type:  'post',
                beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                        $("#contactosGrid").trigger('reloadGrid');
                }
        });
    //verificar si s
  });
});

$(function(){
	//jQuery Daterange
	$("#ultimo_contacto_desde").datepicker({
		defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#ultimo_contacto_hasta").datepicker( "option", "minDate", selectedDate );
		}
	});
	$("#ultimo_contacto_hasta").datepicker({
		defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#ultimo_contacto_desde").datepicker( "option", "maxDate", selectedDate );
	    }
	});
});
