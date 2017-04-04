$(function() {
	
	//-------------------------
	// Inicializar jqGrid
	//-------------------------
	$("#rolesGrid").jqGrid({
	   	url: phost() + 'roles/ajax-listar',
	   	datatype: "json",
	   	colNames:[
			'Id',
			'Rol',
			'Descripcion',
			'Super Usuario',
			'Estado',
			'',
			'',
			'Acci&oacute;n',
			''
		],
	   	colModel:[
			{name:'id', index:'id', width:45, hidden:true},
			{name:'nombre', index:'nombre', width:80 },
			{name:'descripcion', index:'descripcion', width:80 },
			{name:'superuserV', index:'superuser', width:40},
			{name:'estado', index:'estado', width:45},
			{name:'superuser', index:'superuser', hidden:true},
			{name:'default', index:'default', hidden:true},
			{name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false},
			{name:'options', index:'options', hidden:true},
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
		pager: "#rolesGridPager",
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
	    	 $(this).closest("div.ui-jqgrid-view").find("#jqgh_rolesGrid_link").css("text-align", "center");
	    }, 
		loadComplete: function(data){
			
			//check if isset data
			if( data['total'] == 0 ){
				$('#gbox_rolesGrid').hide();
				$('#rolesGridNoRecords').empty().append('No se encontraron roles.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('#rolesGridNoRecords').hide();
				$('#gbox_rolesGrid').show();
			}

			//add class to headers
			//$("#rolesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
			
			//floating headers
			/*$('#gridHeader').sticky({
		    	getWidthFrom: '.ui-jqgrid-view', 
		    	className:'jqgridHeader'
		    });*/
		},
		onSelectRow: function(id){
			$(this).find('tr#'+ id).removeClass('ui-state-highlight');
		},
	});
	
	//-------------------------
	// Boton de opciones
	//-------------------------
	$("#rolesGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_rol = $(this).attr("data-rol");
		var rowINFO = $("#rolesGrid").getRowData(id_rol);
	    var options = rowINFO["options"];

	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Opciones: Rol');
		$('#optionsModal').find('.modal-body').empty().append(options);
		$('#optionsModal').find('.modal-footer').empty();
		$('#optionsModal').modal('show');
	});
	
	//-------------------------
	// Opciones:
	//-------------------------
	//Editar Rol
	$('#optionsModal').on("click", "#editar_rol", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var rol_id = $(this).attr("data-rol");
		
		//Obtener el nombre y descripcion del rol seleccionado
		var rowINFO = $("#rolesGrid").getRowData(rol_id);
	    var nombre = rowINFO["nombre"];
	    var descripcion = rowINFO["descripcion"];
	    var superuser = rowINFO["superuser"];
	    var defaultRol = rowINFO["default"];
	    
	   
	    //toggle switchery plugin
	    if(superuser == 1 && $("#superusuario").is(':not(:checked)')){
	    	setTimeout(function(){
	    		switchery.bindClick();
			}, 500);
	    }else{
	    	setTimeout(function(){
	    		switchery.bindClick();
			}, 500);
	    }
	    
	    //Accesar el scope de controlador del formulario
	    //y establecer los valores
	    var scope = angular.element($("#crearRolBox")).scope();
	    scope.$apply(function(){
	    	scope.rol.rol_id = rol_id;
	    	scope.rol.nombre = nombre;
	    	scope.rol.descripcion = descripcion;
	        scope.rol.superusuario = superuser;
	        scope.rol.defaultRol = defaultRol;
	    });
	    
	    //$('#superusuario').trigger('click');
	    
	    //Abrir formulario
	    $('#crearRolForm').find('.ibox-content:not(:visible)').prev().find('a').trigger('click');
	    
	    //Ocultar ventana
	    $('#optionsModal').modal('hide');
	});
	
	//Duplicar Rol - Confirmacion
	$('#optionsModal').on("click", "#duplicar_rol", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var rol_id = $(this).attr("data-rol");
		
	    var footer_buttons = ['<div class="row">',
		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
		   '</div>',
		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
		   		'<button id="duplicarRol" data-rol="'+ rol_id +'" class="btn btn-w-m btn-danger btn-block" type="button">Duplicar</button>',
		   '</div>',
		   '</div>'
		].join('\n');
	    
	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Confirme');
		$('#optionsModal').find('.modal-body').empty().append('&#191;Esta seguro que desea duplicar este rol?');
		$('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
	});
	
	//Duplicar Rol
	$('#optionsModal').on("click", "#duplicarRol", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var rol_id = $(this).attr("data-rol");
		
	    $.ajax({
			url: phost() + 'roles/ajax-duplicar-rol',
			data: {
				erptkn: tkn,
				rol_id: rol_id
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
			
			if(json.results[0]['respuesta'] == true){
				toastr.success(json.results[0]['mensaje']);
			}else{
				toastr.error(json.results[0]['mensaje']);
			}
			
			//Recargar grid si la respuesta es true
			if(json.results[0]['respuesta'] == true)
			{
				//Recargar Grid
				$("#rolesGrid").setGridParam({
					url: phost() + 'roles/ajax-listar',
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
	
	//Desactivar Rol - Confirmacion
	$('#optionsModal').on("click", "#desactivar_rol", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var id_rol = $(this).attr("data-rol");
		var estado = $(this).attr('data-status');
		var msg = $(this).attr('data-msg');
		
	    var footer_buttons = ['<div class="row">',
		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
		   '</div>',
		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
		   		'<button id="desactivarRol" data-rol="'+ id_rol +'" data-status="'+ estado +'" data-msg="'+ msg +'" class="btn btn-w-m btn-danger btn-block" type="button">Desactivar</button>',
		   '</div>',
		   '</div>'
		].join('\n');
	    
	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Confirme');
		$('#optionsModal').find('.modal-body').empty().append('&#191;Esta seguro que desea desactivar este rol?');
		$('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
	});
	
	//Desactivar Rol
	$('#optionsModal').on("click", "#desactivarRol, #activar_rol", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var rol_id = $(this).attr("data-rol");

		$.ajax({
			url: phost() + 'roles/ajax-activar-desactivar-rol',
			data: {
				erptkn: tkn,
				rol_id: rol_id,
				estado: $(this).attr('data-status'),
				estado_mensaje: $(this).attr('data-msg')
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
			
			if(json.results[0]['respuesta'] == true){
				toastr.success(json.results[0]['mensaje']);
			}else{
				toastr.error(json.results[0]['mensaje']);
			}
			
			//Recargar grid si la respuesta es true
			if(json.results[0]['respuesta'] == true)
			{
				//Recargar Grid
				$("#rolesGrid").setGridParam({
					url: phost() + 'roles/ajax-listar',
					datatype: "json",
					postData: {
						nombre: '',
						descripcion: '',
						erptkn: tkn
					}
				}).trigger('reloadGrid');
			}
			
			//Ocultar ventana
		    $('#optionsModal').modal('hide');
		});
	});
	
});