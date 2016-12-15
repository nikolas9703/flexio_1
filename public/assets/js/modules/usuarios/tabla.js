$(function(){

	//Init Usuarios Grid
	$("#usuariosGrid").jqGrid({
	   	url: phost() + 'usuarios/ajax-listar-usuarios',
	   	datatype: "json",
	   	colNames:[
			'Nombre',
			'Correo Electr&oacute;nico',
			'Fecha Creaci&oacute;n',
			'Estado',
			'Acci&oacute;n',
			'',
			'',
			'',
			'',
			'',
		],
	   	colModel:[
			{name:'Nombre Completo', index:'nombre_completo', width:70},
			{name:'Correo', index:'correo', width:70},
			{name:'Fecha de Creacion', index:'fecha_creacion', formatter: 'date', formatoptions: { newformat: 'd-m-Y' }, width:70, align:"center"},
			{name:'Estado', index:'estado', width: 50, align:"center" },
			{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
			{name:'options', index:'options', hidedlg:true, hidden: true},
			{name:'nombre', index:'nombre', hidedlg:true, hidden: true},
			{name:'apellido', index:'apellido', hidedlg:true, hidden: true},
			{name:'rol_sistema_id', index:'rol_sistema_id', hidedlg:true, hidden: true},
			{name:'rol_id', index:'rol_id', hidedlg:true, hidden: true},
	   	],
		mtype: "POST",
	   	postData: {
	   		erptkn: tkn,
	   		uuid_empresa: uuid_empresa
	   	},
		height: "auto",
		autowidth: true,
		rowList: [10, 20,50, 100],
		rowNum: 10,
		page: 1,
		pager: "#usuariosGridPager",
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
	    loadBeforeSend: function () {

	    },
	    beforeRequest: function(data, status, xhr){},
		loadComplete: function(data){

			//check if isset data
			if( data['total'] == 0 ){
				$('#gbox_usuariosGrid').hide();
				$('.NoRecords').empty().append('No se encontraron Usuarios.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('.NoRecords').hide();
				$('#gbox_usuariosGrid').show();
			}

			//---------
			// Cargar plugin jquery Sticky Objects
			//----------
			//add class to headers
			$("#usuariosGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

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

	//-------------------------
	// Redimensioanr Grid al cambiar tamaño de la ventanas.
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
	// Boton de opciones
	//-------------------------
	$("#usuariosGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id = $(this).attr("data-id");
		var rowINFO = $("#usuariosGrid").getRowData(id);
	    var options = rowINFO["options"];

 	    //Init boton de opciones
		$('#opcionesModal').find('.modal-title').empty().append('Opciones: '+ rowINFO["Nombre Completo"] +'');
		$('#opcionesModal').find('.modal-body').empty().append(options);
		$('#opcionesModal').find('.modal-footer').empty();
		$('#opcionesModal').modal('show');
	});

	$('#opcionesModal').on("click", ".editarUsuarioBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		console.log('.editarUsuarioBtn', window.form_usuarios);
		return;
		var form_id = '#crearUsuarioForm';

		var id = $(this).attr("data-id");
		var rowINFO = $("#usuariosGrid").getRowData(id);
	    var nombre = rowINFO["nombre"];
	    var apellido = rowINFO["apellido"];
	    var email = rowINFO["Correo"];
	    var rol_sistema_id = rowINFO["rol_sistema_id"];
	    var rol_id = rowINFO["rol_id"];
	    //Seleccionar scope de controlador angular del formulario
	    var scope = angular.element('[ng-controller="crearUsuarioController"]').scope();

	    //Abrir panel de formulario
	    $(form_id).find('.ibox-content').removeAttr("style");

	    setTimeout(function () {
	    	scope.safeApply(function () {
				scope.usuario.id 		= id;
				scope.usuario.nombre 	= nombre;
				scope.usuario.apellido  = apellido;
				scope.usuario.email		= email;
				scope.usuario.rol		= rol_sistema_id;
				scope.usuario.roles		= rol_id;
	        });
	    }, 500);
		$('#opcionesModal').modal('hide');
	});

	$('#opcionesModal').on("click", ".estadoUsuarioBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var usuario_id = $(this).attr("data-id");

		$.ajax({
			url: phost() + 'usuarios/ajax-toggle-estado',
			data: {
				erptkn: tkn,
				usuario_id: usuario_id
			},
			type: "POST",
			dataType: "json",
			cache: false,
		}).done(function(json, textStatus, xhr) {

			//Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}

			if(xhr.status != 200){
				//mensaje error
				toastr.error('Hubo un error al tratar de cambiar el estado.');
			}

			//mensaje success
			toastr.success(json.mensaje);

			//cerrar modal
			$('#opcionesModal').modal('hide');

			//Recargar tabla jqgrid
			$("#usuariosGrid").setGridParam({
				url: phost() + 'usuarios/ajax-listar-usuarios',
				datatype: "json",
				postData: {
					uuid_empresa: uuid_empresa,
					erptkn: tkn
				}
			}).trigger('reloadGrid');

		}).fail(function(xhr, textStatus, errorThrown) {
			//mensaje error
			toastr.error('Hubo un error al tratar de cambiar el estado.');
		});
	});

});
