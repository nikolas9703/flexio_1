
$(function(){
	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		tablaColaboradores.redimensionar();
	});
});

//Modulo Tabla de Comisiones
var tablaColaboradores = (function(){
	var formulario =  $('#editarComision');
	var url = 'comisiones/ajax-listar-colaboradores-detalle';
	var url_editar = 'comisiones/ajax-editar-monto';
	var grid_id = "tablaColaboradoresGrid";
	var grid_obj = $("#tablaColaboradoresGrid");
	var opcionesModal = $('#opcionesModal');

	var lista_colaboradores = $('#lista_colaboradores');
	var campo_colaborador= $(formulario).find('#colaborador');
	var campo_departamento= $(formulario).find('#departamento');
	var campo_centro_contable= $(formulario).find('#centro_contable_id');
    var filesList = [], filesNames = [], paramNames = [];

	var botones = {
		guardar: "#guardarFormBtn",
		cancelar: "#cancelarFormBtn" ,
		opciones: ".viewOptions",
 		eliminar: "#EliminarBtnComisionColaborador",
 		agregarColaborador: "#agregarColaborador"
 	};

 		var lastsel;
		var tabla = function(){
	 		grid_obj.jqGrid({
			   	url: phost() + url,
			   	datatype: "json",
			   	colNames:[
			   	    'id',
					'Nombre',
	 				'C&#233;dula',
	 				'Centro contable',
	 				'Detalle',
 					'Monto',
	 			],
			   	colModel:[
			   	    {name:'id', index:'id', hidden: true},
	 				{name:'Nombre', index:'nombre', width:40},
	 				{name:'Cedula', index:'Departamento', width:40},
	 				{name:'Centro Contable', index:'centro_contable',  sortable:false,width: 40},
	 		   		{name:'Detalle', index:'detalle',editable:true,  sortable:false,width: 70},
 	 		   		{name:'Monto', index:'monto',editable:true, width: 50, editrules:{required:true}},
	  		   	],
				mtype: "POST",
			   	postData: {
			   		comision_id: comision_id,
					columna_centro: columna_centro,
 			   		erptkn: tkn
			   	},
				height: "auto",
				autowidth: true,
				rowList: [10, 20,50, 100],
				rowNum: 100,
				page: 1,
				pager: "#"+ grid_id +"Pager",
				loadtext: '<p>Cargando...</p>',
				hoverrows: false,
			    viewrecords: true,
			    refresh: true,
			    gridview: true,
			    multiselect: true,
			    sortname: 'id',
			    sortorder: "ASC",

			    onSelectRow: function(id){
 			    	var parameter = {erptkn: tkn};
					if(id && id!==lastsel){
						grid_obj.jqGrid('restoreRow',lastsel);
						grid_obj.jqGrid('editRow', id, true, false,  false , false, parameter);
 						lastsel=id;
					}
			 		},

 				editurl: phost() + url_editar,
 			    beforeProcessing: function(data, status, xhr){
			    	//Check Session
					if( $.isEmptyObject(data.session) == false){
						window.location = phost() + "login?expired";
					}
			    },

			    loadBeforeSend: function () {//propiedadesGrid_cb
			    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
			    	$(this).closest("div.ui-jqgrid-view").find("#tablaColaboradoresGrid_cb, #jqgh_tablaColaboradoresGrid_link").css("text-align", "center");
	 		    },
	  		    beforeRequest: function(data, status, xhr){},
				loadComplete: function(data){

					$("#gbox_tablaColaboradoresGrid input:checkbox").each(function() {
					    this.checked = true;
					});

					 //$("#cb_" + this.id).click();
					//check if isset data
					if( data['total'] == 0 ){
						$('#boton_guardar').hide();
						$('#gbox_'+ grid_id).hide();
						$('#'+ grid_id +'NoRecords').empty().append('No se encontraron colaboradores').css({"color":"#868686","padding":"30px 0 0"}).show();
					}
					else{
						//$('#boton_guardar').show();
						$('#'+ grid_id +'NoRecords').hide();
						$('#gbox_'+ grid_id).show();
					}
				},
	 		});
		};

 	//Inicializacion de Campos de Busqueda
	var campos = function(){
		$(formulario).validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
		});

 			$(formulario).find('.fecha-programada-pago').daterangepicker({
					singleDatePicker: true,
					showDropdowns: true,
					opens: "left",
					locale: {
						 format: 'DD/MM/YYYY',
						 applyLabel: 'Seleccionar',
						 cancelLabel: 'Cancelar',
						 daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
						 monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
						 firstDay: 1
					},
 			});

		lista_colaboradores.multiselect({
	        search: {
	            left: '<input type="text" id="buscador_colaborador" name="q" class="form-control" placeholder="Search..." />',
	            right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
	        }
	    });

		$(formulario).find('select[name="acumulados[acumulados][]"], select[name="deducciones[deducciones][]"]').find('option').removeAttr("selected");

		$.each(JSON.parse(acumulados_id), function(i,acumulado) {
				 $(formulario).find('select[name="acumulados[acumulados][]"] option[value="'+acumulado.acumulado_id +'"]') .prop('selected', 'selected');
		});


		$.each(JSON.parse(deduccion_id), function(i,deduccion) {
			 $(formulario).find('select[name="deducciones[deducciones][]"] option[value="'+deduccion.deduccion_id +'"]') .prop('selected', 'selected');
	    });

  		$(formulario).find('input[name="campo[numero]"]').prop("disabled",true);
		$(formulario).find('#centro_contable_id').prop( "disabled", true );
		$(formulario).find('#area_negocio_id').prop( "disabled", true );
		$(formulario).find('select[name="campo[centro_contable_id]"], select[name="campo[area_negocio_id]"], select[name="deducciones[deducciones][]"], select[name="acumulados[acumulados][]"]').chosen({width: '100%'}).trigger('chosen:updated');


		if(permiso_editar == 0 )
		{
		    	$(formulario).find('select, input, button, textarea').prop("disabled", "disabled");
		    	$(formulario).find('select[name="campo[centro_contable_id]"], select[name="campo[uuid_cuenta_activo]"], select[name="deducciones[deducciones][]"], select[name="acumulados[acumulados][]"]').chosen({width: '100%'}).trigger('chosen:updated');
		}
   	};


	$(botones.eliminar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		 removerColaboradoresComision();
	});

  	//Reacarga la Tabla Principal de Comisiones
	var recargar = function(){
		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				comision_id: comision_id,
				columna_centro: columna_centro,
				erptkn: tkn
			}
		}).trigger('reloadGrid');

	};

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
			    opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["Codigo"] +'');
			    opcionesModal.find('.modal-body').empty().append(options);
			    opcionesModal.find('.modal-footer').empty();
			    opcionesModal.modal('show');
			});


		$('#opcionesModal').on("click", "#eliminarColaboradorBtn", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var colaboradoresComision = [];

			colaboradoresComision = grid_obj.jqGrid('getGridParam','selarrrow');


			$.ajax({
				url: phost() + 'comisiones/ajax-eliminar-colaborador',
				data: {
					erptkn: tkn,
					colaboradoresComision: colaboradoresComision
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
					if($.isEmptyObject(json) == true){
						return false;
					}

					//Mostrar Mensaje
					if(json.response == "" || json.response == undefined || json.response == 0){

						toastr.error(json.mensaje);

					}else{
						toastr.success(json.mensaje);
						location.reload();
					}

	 				opcionesModal.modal('hide');
					recargar();

			});

		    //Ocultar ventana
		    $('#opcionesModal').modal('hide');
		});

		if(typeof agregarColaborador !== "undefined" ){
			$(agregarColaborador).on("click", function(e){
		   	 	$("#pantallaAgregarColaborador").modal('show');
		  	});
		}

			$("#confimrarAgregarColaborador").on("click", function(e){
		 		var colaboradores =  $('#lista_colaboradores_to').val();

		 		 $.ajax({
		  	         url: phost() + 'comisiones/ajax-agregar-colaborador',
		  	         data: {
						erptkn: tkn,
						comision_id:comision_id,
						colaboradores: colaboradores
					},
		   	         type: "POST",
		  	         dataType: "json",
		  	         cache: false
		  	     }).done(function(data) {

						 if(data.response == true){

							toastr.success(data.mensaje);

							location.reload();
							//recargar();

						}else{
							toastr.error(data.mensaje);

						}
						$("#pantallaAgregarColaborador").modal('hide');
		   	   });
		 	});
         initFileUpload();

	};

	$(botones.cancelar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		window.location.href = phost()+'comisiones/listar';
	});

	$(botones.guardar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		//editarComision();

        //Submit datos
        //$scope.uploadform();
        if(formulario.validate().form() != true) {
            return;
        }
        $(botones.guardar).attr("disabled", "disabled").html("<i class='fa fa-spin fa-cog'></i> Guardando..");

        if (filesList.length > 0) {

            $('#documento').fileupload('send', {
                files: filesList,
                paramName: paramNames,
                formData: {
                    erptkn: tkn,
                    comision_id: comision_id
                }
            });
        } else {
            editarComision();
        }
	});
	var removerColaboradoresComision = function(){

		var colaboradoresComision = [];

		colaboradoresComision = grid_obj.jqGrid('getGridParam','selarrrow');


		if(colaboradoresComision.length==0){
			return false;
		}
	 var mensaje = (colaboradoresComision.length > 1)?'Esta seguro que desea eliminar estos Colaboradores de esta comisi&oacute;n?':'Esta seguro que desea eliminar este colaborador?';

	 var footer_buttons = ['<div class="row">',
		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
		   '</div>',
		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
		   		'<button id="eliminarColaboradorBtn" class="btn btn-w-m btn-success btn-block" type="button">Confirmar</button>',
		   '</div>',
		   '</div>'
		].join('\n');
	 opcionesModal.find('.modal-title').empty().append('Confirme');
	 opcionesModal.find('.modal-body').empty().append(mensaje);
	 opcionesModal.find('.modal-footer').empty().append(footer_buttons);
	 opcionesModal.modal('show');
};
 	var editarComision = function(){
		if(formulario.validate().form() == true )
		{



			$.ajax({
				url: phost() + 'comisiones/ajax-editar-comision',
				data: formulario.serialize()+'&comision_id='+comision_id,
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json) {

				//Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				//Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				if(json.response == true){
						// toastr.success(json.mensaje);
						 window.location.href = phost()+'comisiones/listar';
 						 //recargar();
				}else{
					toastr.error(json.mensaje);
				}

			});
		}

	};
	var listar_colaboradores = function(parametros){
 		if(parametros == ""){
			return false;
		}
 		return $.ajax({
			url: phost() + 'comisiones/ajax-listar-departamento-x-centro',
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		});
	};

  	var listar_colaboradores_departamento = function(parametros){

		if(parametros == ""){
			return false;
		}
		 //$('#departamento').chosen({width: '100%'}).trigger('chosen:updated');
		 return $.ajax({
			url: phost() + 'comisiones/ajax-listar-colaboradores-x-centro',
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		});
  	};

    var initFileUpload = function()
    {

		var file = $('#documento');
		file.parent("span").addClass("btn-block");

        file.fileupload({
            url: phost() + 'comisiones/ajax-guardar-documento',
            type: 'POST',
            dataType: 'json',
            autoUpload: false,
            singleFileUploads: false,
           // dropZone: document.getElementById('dropTarget'),
            //acceptFileTypes: /(\.|\/)(gif|jpe?g|png|mp4|mp3)$/i,
            add: function (e, data) {
                filesList = []; filesNames = []; paramNames = [];
                $.each(data.files, function (index, file) {
                    //verificar si existe el archivo en el arreglo
                    var found = filesNames.indexOf(file.name);
                    filesNames.push(file.name);

                    //para evitar duplicidad de archivos
                    if(found<0){
                        var fieldname = $(e.delegatedEvent.currentTarget).find('input').attr('name') !== undefined ? $(e.delegatedEvent.currentTarget).find('input').attr('name') : $(e.delegatedEvent.currentTarget).attr('name');
                        filesList.push(file);
                        paramNames.push(fieldname);
                    }
                });

                $(".fileinput-button label").html('<i class="fa fa-upload"></i> 1 archivo seleccionado');
                $("input:checkbox").attr("checked", true);
            },
            done: function(e, json) {

                //mostrar mensaje
                //toastr.success(data.result.mensaje);


                //Check Session
                if( $.isEmptyObject(json.session) == false){
                    window.location = phost() + "login?expired";
                }

                editarComision();
            }
        });
    };

    var destroyFileUpload = function()
    {
        $('#documento').fileupload('destroy');
        filesList = [], filesNames = [], paramNames = [];
    };

    //filesList = [], filesNames = [], paramNames
    var getFilesList = function(){
        return filesList;
    };




	return{
		init: function() {
			tabla();
			eventos();
			campos();
 		},
		recargar: function(){
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

tablaColaboradores.init();
