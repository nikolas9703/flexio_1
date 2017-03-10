$(function(){
	
	//Init Usuarios Grid
	$("#proyectosGrid").jqGrid({
	   	url: phost() + 'proyectos/ajax-listar-proyectos',
	   	datatype: "json",
	   	colNames:[
			'Nombre',
			'Nombre',
			'Ubicación',
			'Tipo',
			'Fase',
			'Nº de Prop. Disp.',
			'',
			'Opciones',
			'', 
 			'Acción' 
 		],
	   	colModel:[
			{name:'Nombre', index:'pry.nombre', width:70},
			{name:'nombre_clear', index:'nombre_clear', hidedlg:true, hidden: true},
			{name:'Ubicacion', index:'pry.ubicacion', width:70},
			{name:'Tipo', index:'ccat.etiqueta', width:70},
 	   		{name:'Fase', index:'ccat2.etiqueta', width: 50 },
 	   		{name:'Disponibles', index:'pry.no_disponibles', width: 50 },
	   		{name:'Propiedades Disponibles',   width: 50, hidden: true },
 			{name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidedlg:true},
 			{name:'options', index:'options', hidedlg:true, hidden: true},
			{name:'link_seleccionar', index:'link_seleccionar', width: 50, hidden: true }
 	   	],
		mtype: "POST",
	   	postData: {
	   		erptkn: tkn
	   	},
		height: "auto",
		autowidth: true,
		rowList: [10, 20,50,100],
		rowNum: 10,
		page: 1,
		pager: "#pager2",
		loadtext: '<p>Cargando...',
		hoverrows: false,
	    viewrecords: true,
	    refresh: true,
	    gridview: true,
	    multiselect: true,
	    sortname: 'pry.nombre',
	    sortorder: "ASC",
	    beforeProcessing: function(data, status, xhr){
	    	//Check Session
			if( $.isEmptyObject(data.session) == false){
				window.location = phost() + "login?expired";
			}
	    },
	    loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	        $(this).closest("div.ui-jqgrid-view").find("#proyectosGrid_cb, #proyectosGrid_link").css("text-align", "center");
	    }, 
	    beforeRequest: function(data, status, xhr){},
		loadComplete: function(data){
			
			//check if isset data
			if( data['total'] == 0 ){
				$('#gbox_proyectosGrid').hide();
				$('.NoRecords').empty().append('No se encontraron proyectos.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('.NoRecords').hide();
				$('#gbox_proyectosGrid').show();
			}

			//---------
			// Cargar plugin jquery Sticky Objects
			//----------
			//add class to headers
			$("#proyectosGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
			
			//floating headers
			 $('#gridHeader').sticky({
		    	getWidthFrom: '.ui-jqgrid-view', 
		    	className:'jqgridHeader'
		    }); 
			
			//Arreglar tamaño de TD de los checkboxes
			$("#proyectosGrid_cb").css("width","50px");
			$("#proyectosGrid tbody tr").children().first("td").css("width","50px");
		},
		onSelectRow: function(id){
			$(this).find('tr#'+ id).removeClass('ui-state-highlight');
		},
	});
	$("#proyectosGrid").jqGrid('columnToggle');
	
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
	$("#proyectosGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_cliente = $(this).attr("data-cliente");
		var rowINFO = $("#proyectosGrid").getRowData(id_cliente);
	    var options = rowINFO["options"];

	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Opciones:  '+ rowINFO['nombre_clear']);
		$('#optionsModal').find('.modal-body').empty().append(options);
		$('#optionsModal').find('.modal-footer').empty();
		$('#optionsModal').modal('show');
	});
	
	//-------------------------
	// Botones de formulario de Busqueda
	//-------------------------
	$('#searchBtn').bind('click', searchBtnHlr);
	$('#clearBtn').click(function(e){
		e.preventDefault();
		
		$("#proyectosGrid").setGridParam({
			url: phost() + 'proyectos/ajax-listar-proyectos',
			datatype: "json",
			postData: {
				nombre: '',
				ubicacion: '',
				id_tipo: '',
				id_fase: '',
				no_disponibles: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		//Reset Fields
		$('#nombre, #ubicacion, #id_tipo, #id_fase, #no_disponibles').val('');
	});
});

function searchBtnHlr(e) {
	e.preventDefault();
	$('#searchBtn').unbind('click', searchBtnHlr);

	var nombre 			= $('#nombre').val();
	var ubicacion 		= $('#ubicacion').val();
	var id_tipo 		= $('#id_tipo').val();
	var id_fase 		= $('#id_fase').val();
	var no_disponibles 	= $('#no_disponibles').val();
 
	if( nombre != "" || ubicacion != "" || id_tipo != "" || id_fase != ""  || no_disponibles != ""  )
	{
		$("#proyectosGrid").setGridParam({
			url: phost() + 'proyectos/ajax-listar-proyectos',
			datatype: "json",
			postData: {
				nombre: nombre,
				ubicacion: ubicacion,
				id_tipo: id_tipo,
				id_fase: id_fase,
				no_disponibles: no_disponibles,
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		$('#searchBtn').bind('click', searchBtnHlr);
	}else{
		$('#searchBtn').bind('click', searchBtnHlr);
	}
}