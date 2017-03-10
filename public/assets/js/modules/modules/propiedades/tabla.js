$(function(){
	
	 var nombre_mostrar = true;
	 if(typeof uuid_proyecto === 'undefined'){
		 uuid_proyecto="";
		 nombre_mostrar = false;
	    };
	    
 	//Init Usuarios Grid
	$("#propiedadesGrid").jqGrid({
	   	url: phost() + 'propiedades/ajax-listar-propiedades',
	   	datatype: "json",
	   	colNames:[
			'Nombre',  
			'Proyecto',
			'Metraje',
			'Valor (A/V)',
			'Tipo',
			'Detalle de Ubicación',
			'Estado',
			'Opciones',
			'' ,
 			'' 
 		],
	   	colModel:[
			{name:'Nombre', index:'rpo.nombre', width:60},
			{name:'Proyecto', index:'pry.nombre', width:60,   hidden: nombre_mostrar },
			{name:'Metraje', index:'rpo.metraje', width:50},
 	   		{name:'Valor (A/V)', index:'rpo.valor_alquiler', width: 50},
 	   		{name:'Tipo', index:'cat.etiqueta', width: 60},
 	   		{name:'Detalle de Ubicacion', index:'rpo.ubicacion', width: 70 },
 	   		{name:'Estado', index:'cat2.etiqueta', width: 50},
 			{name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidedlg:true},
 			{name:'nombre_clear', hidden: true},
			{name:'options', index:'options', hidedlg:true, hidden: true},
 	   	],
		mtype: "POST",
	   	postData: {
	   		uuid_proyecto: uuid_proyecto,
	   		erptkn: tkn
	   	},
 		height: "auto",
		autowidth: true,
		rowList: [10, 20,50,100],
		rowNum: 10,
		page: 1,
		pager: "#pager",
		loadtext: '<p>Cargando...',
		hoverrows: false,
	    viewrecords: true,
	    refresh: true,
	    gridview: true,
	    multiselect: true,
	    sortname: 'rpo.nombre',
	    sortorder: "ASC",
	    beforeProcessing: function(data, status, xhr){
	    	//Check Session
			if( $.isEmptyObject(data.session) == false){
				window.location = phost() + "login?expired";
			}
	    }, 
	    loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	        $(this).closest("div.ui-jqgrid-view").find("#propiedadesGrid_cb, #jqgh_propiedadesGrid_link").css("text-align", "center");
	    }, 
	    beforeRequest: function(data, status, xhr){},
		loadComplete: function(data){
			
			//check if isset data
			if( data['total'] == 0 ){
				$('#gbox_propiedadesGrid').hide();
				$('.NoRecords').empty().append('No se encontraron propiedades.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('.NoRecords').hide();
				$('#gbox_propiedadesGrid').show();
			}

			//---------
			// Cargar plugin jquery Sticky Objects
			//----------
			//add class to headers
			$("#propiedadesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
			
			//floating headers
			$('#gridHeader').sticky({
		    	getWidthFrom: '.ui-jqgrid-view', 
		    	className:'jqgridHeader'
		    });
			
			//Arreglar tamaño de TD de los checkboxes
			$("#propiedadesGrid_cb").css("width","50px");
			$("#propiedadesGrid tbody tr").children().first("td").css("width","50px");
		},
		onSelectRow: function(id){
			$(this).find('tr#'+ id).removeClass('ui-state-highlight');
		},
	});
	$("#propiedadesGrid").jqGrid('columnToggle');
	
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
	$("#propiedadesGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_propiedad = $(this).attr("data-propiedad");
		var rowINFO = $("#propiedadesGrid").getRowData(id_propiedad);
	    var options = rowINFO["options"];

	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Opciones:'+ rowINFO['nombre_clear'] );
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
		
		$("#propiedadesGrid").setGridParam({
			url: phost() + 'propiedades/ajax-listar-propiedades',
			datatype: "json",
			postData: {
				nombre: '',
				id_proyecto: '',
				id_tipo_propiedad: '',
				ubicacion: '',
				id_estado_propiedad: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		//Reset Fields
		$('#nombre, #id_proyecto, #id_tipo_propiedad, #ubicacion, #id_estado_propiedad').val('');
	});
	
	 
	 
});

function searchBtnHlr(e) {
	e.preventDefault();
	$('#searchBtn').unbind('click', searchBtnHlr);
 
	var nombre 				= $('#nombre').val();
	var id_proyecto 		= $('#id_proyecto').val();
	var id_tipo_propiedad 	= $('#id_tipo_propiedad').val();
	var ubicacion 			= $('#ubicacion').val();
	var id_estado_propiedad = $('#id_estado_propiedad').val();
 
	if( nombre != "" || id_proyecto != "" || id_tipo_propiedad != "" || ubicacion != ""  || id_estado_propiedad != ""  )
	{
		$("#propiedadesGrid").setGridParam({
			url: phost() + 'propiedades/ajax-listar-propiedades',
			datatype: "json",
			postData: {
				nombre: nombre,
				id_proyecto: id_proyecto,
				id_tipo_propiedad: id_tipo_propiedad,
				ubicacion: ubicacion,
				id_estado_propiedad: id_estado_propiedad,
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		$('#searchBtn').bind('click', searchBtnHlr);
	}else{
		$('#searchBtn').bind('click', searchBtnHlr);
	}
}