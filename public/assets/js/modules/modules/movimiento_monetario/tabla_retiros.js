$(document).ready(function() {

    $('.chosen-select').chosen({width: "100%"});
    $('.chosen-select').trigger("liszt:updated");

   //Plugin Datepicker
			$('#fecha_desde').daterangepicker({
				singleDatePicker: true,
				autoUpdateInput: false,
				format: 'MM-DD-YYYY',
			    showDropdowns: true,
			    opens: "left",
			    locale: {
			    	applyLabel: 'Seleccionar',
			        cancelLabel: 'Cancelar',
			    	daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
			        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			        firstDay: 1
			    }
			}).on('apply.daterangepicker', function(ev, picker) {
				$(this).val(picker.startDate.format('DD/MM/YYYY'));
			});

                        $('#fecha_hasta').daterangepicker({
				singleDatePicker: true,
				autoUpdateInput: false,
				format: 'MM-DD-YYYY',
			    showDropdowns: true,
			    opens: "left",
			    locale: {
			    	applyLabel: 'Seleccionar',
			        cancelLabel: 'Cancelar',
			    	daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
			        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			        firstDay: 1
			    }
			}).on('apply.daterangepicker', function(ev, picker) {
				$(this).val(picker.startDate.format('DD/MM/YYYY'));
			});

});

//Modulo Tabla de Entrega de Inventario
var tablaRecibos = (function(){

	//var evaluacion_id = '';
	var url = 'movimiento_monetario/ajax-listar-retiros';
	var grid_id = "retirosGrid";
	var grid_obj = $("#retirosGrid");
	var opcionesModal = $('#opcionesModal');
	var formularioBusqueda = '#buscarRetirosForm';
	var formularioInventarioModal = $('#entregaInventarioModal');
	var documentosModal = $('#documentosModal');
	var botones = {
		opciones: ".viewOptions",
		editar: ".editarInventarioBtn",
		reemplazar: ".reemplazarInventarioBtn",
		descargar: ".descargarInventarioBtn",
                exportar: "#exportarDescuentoLnk",
		limpiar: "#clearBtn",
		buscar: "#searchBtn",
		subirArchivo: ".subirArchivoBtn",
	};


	var tabla = function(){

		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[

                    'No. Retiro',
                    'Cliente/Proveedor',
                    'Nombre',
                    'Narraci&oacute;n',
                    'Fecha de recibo',
                    'D&eacute;bito',
                    '',
                    ''


			],
		   	colModel:[
		{name:'codigo',index:'codigo', sortable:true},
                {name:'cliente_proveedor',index:'cliente_proveedor', sortable:true},
                {name:'nombre',index:'nombre', sortable:true},
                {name:'narracion',index:'narracion', sortable:true, width:200},
                {name:'fecha_inicio',index:'fecha_inicio', formatter: 'date', formatoptions: { newformat: 'd/m/Y' }, sortable:true},
                {name:'debito', index:'debito', formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$ "}, sortable:true},
                {name:'link', index:'link', sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'options', index:'options', hidedlg:true, hidden: true}
		   	],
			mtype: "POST",
		   	postData: {
		   		erptkn: tkn
		   	//	colaborador_id: colaborador_id
		   	},
			height: "auto",
			autowidth: true,
			rowList: [10, 20,50, 100],
			rowNum: 10,
			page: 1,
			pager: "#"+ grid_id +"Pager",
			loadtext: '<p>Cargando...</p>',
			hoverrows: false,
		    viewrecords: true,
		    refresh: true,
		    gridview: true,
		    multiselect: true,
		    sortname: 'id',
		    sortorder: "DESC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
		        $(this).closest("div.ui-jqgrid-view").find("#descuentosgrid").css("text-align", "center");},
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){

				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron retiros.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
				}
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
		});
	};



         //Boton de Exportar Descuento
		$(botones.exportar).on("click", function(e){

			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			if($('#tabla').is(':visible') == true){

				//Exportar Seleccionados del jQgrid
				var ids = [];

				ids = grid_obj.jqGrid('getGridParam','selarrrow');

				//Verificar si hay seleccionados
				if(ids.length > 0){

					$('#ids').val(ids);
                                        console.log(ids);
			        $('form#exportarDescuentos').submit();
			        $('body').trigger('click');
				}
	        }
		});

	//Inicializar Eventos de Botones
	var eventos = function(){

		//Bnoton de Opciones
		grid_obj.on("click", botones.opciones, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(id);
		    var options = rowINFO["options"];

	 	    //Init Modal
                    opcionesModal.find('.modal-title').empty().append('Opciones: ' + rowINFO["codigo"]);
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');

            $( ".anular" ).click(function() {

                                var botones = [
                       '<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>',
                       '<button type="button" class="btn btn-w-m btn-danger" id="eliminar_retiros">Eliminar</button>'
                    ].join('\n');


                    //Init Modal
                    opcionesModal.find('.modal-title').empty().append('Confirmar');
                    opcionesModal.find('.modal-body').empty().append('&#191;Esta seguro que desea anular este retiro?');
                    opcionesModal.find('.modal-footer').empty().append(botones);
                    opcionesModal.modal('show');


                    $('#eliminar_retiros').on("click", function(e){

                   var url2 = "movimiento_monetario/ajax_eliminar_retiros";
                     $.ajax({
                    url: phost() + url2,
                    type: "post",
                    data: {id:id, erptkn: tkn} ,
                    success: function (response) {
                       grid_obj.trigger('reloadGrid');
                       opcionesModal.modal('hide');

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                       console.log(textStatus, errorThrown);
                    }


                    });
                });





                    });


		});

		//Documentos Modal
		$(opcionesModal).on("click", botones.subirArchivo, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			var retiro_id = $(this).attr("data-id");
			//Inicializar opciones del Modal
			documentosModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});

			var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
		    scope.safeApply(function(){
		    	scope.campos.retiro_id = retiro_id;
		    });
			documentosModal.modal('show');
		});

		//Boton de Descargar de Entrega de Inventario
		opcionesModal.on("click", botones.descargar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var entrega_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(entrega_id);

			var archivo_nombre = rowINFO["archivo_nombre"];
	    	var archivo_ruta = rowINFO["archivo_ruta"];
	    	var fileurl = phost() + archivo_ruta +'/'+ archivo_nombre;

	    	if(archivo_nombre == '' || archivo_nombre == undefined){
	    		return false;
	    	}

                if($('#tabla').is(':visible') == true){

				//Exportar Seleccionados del jQgrid
				var descuentos = [];
					descuentos = grid_obj.jqGrid('getGridParam','selarrrow');

				//Verificar si hay seleccionados
				if(descuentos.length > 0){
					//Cambiar Estado
					toggleColaborador({descuentos: descuentos, estado_id: 1});

                                        console.log("legggass");
				}
	        }



	    	//Descargar archivo
	    	downloadURL(fileurl, archivo_nombre);

		    //Ocultar modal
			opcionesModal.modal('hide');
		});

		//Boton de Editar Entrega de Inventario
		opcionesModal.on("click", botones.editar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			//Cerrar modal de opciones
			opcionesModal.modal('hide');

			var entrega_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(colaborador_id);
		   // var departamento_id = rowINFO["departamento_id"];

			//Inicializar opciones del Modal
			formularioInventarioModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			formularioInventarioModal.attr("data-uuid", colaborador_uuid);
			formularioInventarioModal.attr("data-departamento-id", departamento_id);
			formularioInventarioModal.find('.modal-title').empty().append('Editar Entrega de Inventario');
			formularioInventarioModal.find('input[id="campo[colaborador_id]"]').val(colaborador_id);
			formularioInventarioModal.find('#departamento_id').find('option[value="'+ departamento_id +'"]').prop('selected', 'selected');
			formularioInventarioModal.find('input[id="campo[id]"]').val(entrega_id);
			formularioInventarioModal.find('#tipo_reemplazo_id').find('option:eq(0)').prop('selected', 'selected').closest('.form-group').addClass('hide');
			formularioInventarioModal.find('#departamento_id, #categoria_id, #item_id').removeAttr("disabled");
			formularioInventarioModal.modal('show');
		});

		//Boton de Editar Entrega de Inventario
		opcionesModal.on("click", botones.reemplazar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			//Cerrar modal de opciones
			opcionesModal.modal('hide');

			var entrega_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(colaborador_id);
		    var departamento_id = rowINFO["departamento_id"];

			//Inicializar opciones del Modal
			formularioInventarioModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			formularioInventarioModal.attr("data-uuid", colaborador_uuid);
			formularioInventarioModal.attr("data-departamento-id", departamento_id);
			formularioInventarioModal.find('.modal-title').empty().append('Reemplazo Inventario');
			formularioInventarioModal.find('input[id="campo[colaborador_id]"]').val(colaborador_id);
			formularioInventarioModal.find('#departamento_id').find('option[value="'+ departamento_id +'"]').prop('selected', 'selected');
			formularioInventarioModal.find('input[id="campo[id]"]').val(entrega_id);
			formularioInventarioModal.find('#tipo_reemplazo_id').find('option:eq(0)').prop('selected', 'selected').closest('.form-group').removeClass('hide');
			formularioInventarioModal.find('#departamento_id, #bodega_uuid, #categoria_id, #item_id').attr("disabled", "disabled");
			formularioInventarioModal.modal('show');
		});
	};

	//Boton de Buscar Evaluacion
	$(botones.buscar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		buscarRecibos();
	});

	//Boton de Reiniciar jQgrid
	$(botones.limpiar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		recargar();
		limpiarCampos();
	});

	//Reload al jQgrid
	var recargar = function(){

		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				cliente: '',
				nombre: '',
				narracion: '',
                                monto_desde: '',
                                monto_hasta: '',
				fecha_desde: '',
                                fecha_hasta: '',


			}
		}).trigger('reloadGrid');

	};

	//Buscar Entrega de Inventario en jQgrid
	var buscarRecibos = function(){

           // console.log("llegaste");

		var cliente 	        = $('#cliente').val();
		var nombre              = $('#cliente_proveedor').val();
		var narracion    	= $('#narracion').val();
		var monto_desde         = $('#monto_desde').val();
		var monto_hasta         = $('#monto_hasta').val();
		var fecha_desde         = $('#fecha_desde').val();
		var fecha_hasta         = $('#fecha_hasta').val();

		if(cliente != "" || nombre != "" || narracion != "" || monto_desde != "" || monto_hasta != "" || fecha_desde != "" || fecha_hasta != "")
		{
                    //console.log(tipo_descuento);
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					cliente: cliente,
					nombre: nombre,
					narracion: narracion,
					monto_desde: monto_desde,
					monto_hasta: monto_hasta,
                                        fecha_desde: fecha_desde,
                                        fecha_hasta: fecha_hasta

				}
			}).trigger('reloadGrid');
		}
	};

	//Limpiar campos de busqueda
	var limpiarCampos = function(){

           // console.log("llegastelimpiando");
		$('input[type="text"]').prop("value", "");
		$('#cliente').val('').trigger('chosen:updated');
                $('#cliente_proveedor').val('').trigger('change');

	};

	return{
		init: function() {
			tabla();
		//	init();
			eventos();
		},
		recargar: function(){
			//reload jqgrid
			recargar();
		},
		redimensionar: function(){
			//Al redimensionar ventana
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
		}
	};
})();

$("#cliente2").change(function() {
var cliente_proveedor = $('#cliente').val();

  $.ajax({
    url: phost() + "movimiento_monetario/ajax-cliente-proveedor",
    type:"POST",
    data:{
    erptkn:tkn,
    cliente_proveedor:cliente_proveedor
    },
    dataType:"json",
    success: function(data){

         $.each(data.result, function(index, element) {
         $('#nombre').empty();
         setTimeout(function(){
	 $('#nombre').append('<option value='+ element.id +'>'+ element.nombre +'</option>').trigger('chosen:updated');
		}, 50);



        });

    }

    });



});

 $('#cliente_proveedor').removeClass("chosen-filtro").addClass("form-control").select2({
        ajax: {
            url: phost() + "movimiento_monetario/ajax-cliente-proveedor",
            method: 'POST',
            dataType: 'json',
            delay: 200,
            cache: true,
            data: function (params) {
                return {
                    cliente_proveedor: $('#cliente').val(),
                    q: params.term, // search term
                    page: params.page,
                    limit: 10,
                    erptkn: window.tkn
                };
            },
            processResults: function (data, params) {


                let resultsReturn = data.map(resp=> [{
                    'id': resp['id'],
                    'text': resp['nombre']
                }]).reduce((a, b) => a.concat(b), []);

                return {results: resultsReturn};
            },
            escapeMarkup: function (markup) {
                return markup;
            },
        }
    }).on("change", function () {
        $('.id_cliente_proveedor').val($(this).val());
        $('.guardar1').removeAttr('disabled');
    });


tablaRecibos.init();
