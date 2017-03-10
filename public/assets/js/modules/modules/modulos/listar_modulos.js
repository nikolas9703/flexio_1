$(function() {
	
	//-------------------------
	// Inicializar jqGrid
	//-------------------------
	$("#modulosGrid").jqGrid({
	   	url: phost() + 'modulos/ajax-listar',
	   	datatype: "json",
	   	colNames:[
			'Nombre',
			'Version',
			'Descripcion',
			'Estado',
			'Accion',
			''
		],
	   	colModel:[
			{name:'nombre', index:'nombre', width:80, align:"left"},
			{name:'versión', index:'version', width:80, align:"left"},
			{name:'descripción', index:'descripcion', width:200, align:"left"},
			{name:'status', index:'status', width:45, align:"left"},
			{name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidedlg:true},
			{name:'options', index:'options', hidedlg:true, hidden:true},
	   	],
		mtype: "POST",
	   	postData: {
	   		erptkn: tkn
	   	},       
		height: "auto",
		autowidth: true,
		rowList: [10,20,30,50],
		rowNum: 10,
		page: 1,
		pager: "#pager",
		loadtext: '<p>Cargando...',
		hoverrows: false,
	    viewrecords: true,
	    refresh: true,
	    gridview: true,
	    sortname: 'nombre',
	    sortorder: "ASC",
	    beforeProcessing: function(data, status, xhr){
	    	//Check Session
			if( $.isEmptyObject(data.session) == false){
				window.location = phost() + "login?expired";
			}
	    },
	    loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	        $(this).closest("div.ui-jqgrid-view").find("#jqgh_modulosGrid_link").css("text-align", "center");
	    },
		loadComplete: function(data){
			
			//check if isset data
			if( data['total'] == 0 ){
				$('#gbox_modulosGrid').hide();
				$('.NoRecords').empty().append('No se encontraron modulos.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('.NoRecords').hide();
				$('#gbox_modulosGrid').show();
			}

			//add class to headers
			$("#modulosGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
			
			//floating headers
			$('#gridHeader').sticky({
		    	getWidthFrom: '.ui-jqgrid-view', 
		    	className:'jqgridHeader'
		    });
		},
		onSelectRow: function(id){
			$(this).find('tr#'+ id).removeClass('ui-state-highlight');
		},
	});
	$("#modulosGrid").jqGrid('columnToggle');
	

	//-------------------------
	// Boton de opciones
	//-------------------------
	$("#modulosGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_modulo = $(this).attr("data-rol");
		var rowINFO = $("#modulosGrid").getRowData(id_modulo);
	    var options = rowINFO["options"];

	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Opciones: '+$(this).attr("data-nombre"));
		$('#optionsModal').find('.modal-body').empty().append(options);
		$('#optionsModal').find('.modal-footer').empty();
		$('#optionsModal').modal('show');
	});
	
	//-------------------------
	// Opciones: Activar Modulo
	//-------------------------
	$('#optionsModal').on("click", "#activar_modulo", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var id_modulo = $(this).attr("data-modulo");
		
		//HTML de la Barra de Progreso
		var progressbar = ['<p class="proceso"></p>',
		  '<div class="progress progress-striped active">',
          '<div class="progress-bar" role="progressbar" data-transitiongoal=""></div>',
          '</div>'
   		].join('\n');
		
		//Poner Titulo e Insertar la Barra
		$('#optionsModal').find('.modal-title').empty().append('Activar Modulo');
		$('#optionsModal').find('.modal-body').empty().append(progressbar);

		//Iniciando barra de progreso en 0%
		$('.progress .progress-bar').attr('data-transitiongoal', 0).progressbar({display_text: 'center',});
		$('.proceso').empty().append('Activando modulo...');
		
		var jqXHR1 = $.ajaxQueue({
			url: phost() + 'modulos/ajax-instalar-1',
			data: {
				erptkn: tkn,
				id_modulo: id_modulo
			},
			type: "POST",
			dataType: "json",
			cache: false,
		}).fail(function(jqXHR, textStatus, errorThrown) {
			
			//Problema de conexion a Internet
			if(jqXHR.readyState == 0 && jqXHR.status == 0 && jqXHR.statusText == 'error'){
				console.log('<b>Error:</b> Parece que se ha perdido la conexion a Internet.');
			}
			
			//Internal Server error
			if(jqXHR.readyState == 4 && jqXHR.status == 500){ /* mensaje aqui */ }
			jqXHR.abort();
		});
		
		//Verificar Paso 1
		//De lo contrario mostrar mensaje de error.
		jqXHR1.done(function(json, textStatus, jqXHR) {
			
			//Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}
			
			//If json object is empty.
			if($.isEmptyObject(json.results[0]) == true){
				return false;
			}
			
			//Verificar si la respuesta fue correcta.
			if(json.results[0]['respuesta'] == true)
			{	
				//Actualizar 100%
				$('.progress .progress-bar').attr('data-transitiongoal', 100).progressbar({display_text: 'center'});
				$('.proceso').empty().append('Activaci&oacute;n completa!');
				
				//Mensaje
				toastr.success('Se ha activado el m&oacute;dulo satisfactoriamente!');
				$('#optionsModal').modal('hide');
				
				//Recargar Grid
				$("#modulosGrid").jqGrid({
				   	url: phost() + 'modulos/ajax-listar',
					datatype: "json",
					postData: {
						nombre: '',
						descripcion: '',
						erptkn: tkn
					}
				}).trigger('reloadGrid');
				
				//window.location = phost() + 'modulos';

			}else{
				
				//Mensaje
				toastr.error(json.results[0]['mensaje']);
				
				$('#optionsModal').modal('hide');
			}
		});
		
		
	});
	
	//-------------------------
	// Opciones: Desactivar Modulo
	//-------------------------
	$('#optionsModal').on("click", "#desactivar_modulo", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_modulo = $(this).attr("data-modulo");
		
	    var footer_buttons = ['<div class="row">',
		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
		   '</div>',
		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
		   		'<button id="desactivarModulo" data-modulo="'+ id_modulo +'" class="btn btn-w-m btn-danger btn-block" type="button">Desactivar</button>',
		   '</div>',
		   '</div>'
		].join('\n');
	    
	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Confirme');
		$('#optionsModal').find('.modal-body').empty().append('&#191;Esta seguro que desea desactivar este modulo?');
		$('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
	});
	//Duplicar Rol
	$('#optionsModal').on("click", "#desactivarModulo", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var id_modulo = $(this).attr("data-modulo");
		
	    $.ajax({
			url: phost() + 'modulos/ajax-desactivar-modulo',
			data: {
				erptkn: tkn,
				id_modulo: id_modulo
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
			
			var class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
			
			//Mostrar Mensaje
			//mensaje_alerta(json.results[0]['mensaje'], class_mensaje);
			
			//Mensaje
			toastr.success(json.results[0]['mensaje']);
			
			//Recargar grid si la respuesta es true
			if(json.results[0]['respuesta'] == true)
			{
				//Recargar Grid
				$("#modulosGrid").jqGrid({
				   	url: phost() + 'modulos/ajax-listar',
					datatype: "json",
					postData: {
						nombre: '',
						descripcion: '',
						erptkn: tkn
					}
				}).trigger('reloadGrid');
			}
		});
	    
	    //Ocultar ventana
	    $('#optionsModal').modal('hide');
	});
	

	//-------------------------
	// Redimensioanr Grid al cambiar segun de la ventanas.
	//-------------------------
	$(window).resizeEnd(function() {
		$(".ui-jqgrid").each(function(){
			var w = parseInt( $(this).parent().width()) - 6;
			var tmpId = $(this).attr("id");
			var gId = tmpId.replace("gbox_","");
			$("#"+gId).setGridWidth(w);
		});
	});

	//-------------------------
	// Botones de formulario de Busqueda
	//-------------------------
	$('#searchBtn').bind('click', searchBtnHlr);
	$('#clearBtn').click(function(e){
		e.preventDefault();
		
		$("#modulosGrid").setGridParam({
		   	url: phost() + 'modulos/ajax-listar',
		   	datatype: "json",
			postData: {
				nombre: '',
				descripcion: '',
				estado: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		//Reset Fields
		$('#nombre, #descripcion').val('');
		$('#estado option:eq(0)').prop("selected", "selected");
	});
	
});

function searchBtnHlr(e) {
	e.preventDefault();
	$('#searchBtn').unbind('click', searchBtnHlr);

	var nombre 		= $('#nombre').val();
	var estado 		= $('#estado').val();
	var descripcion = $('#descripcion').val();

	if(nombre != "" || estado != "" || descripcion != "")
	{
		$("#modulosGrid").setGridParam({
			url: phost() + 'modulos/ajax-listar',
			datatype: "json",
			postData: {
				nombre: nombre,
				descripcion: descripcion,
				estado: estado,
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		$('#searchBtn').bind('click', searchBtnHlr);
	}else{
		$('#searchBtn').bind('click', searchBtnHlr);
	}
}