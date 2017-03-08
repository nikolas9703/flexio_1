$(function(){
	
	//Init Usuarios Grid
	$("#agentesGrid").jqGrid({
	   	url: phost() + 'agentes/ajax-listar-agentes',
	   	datatype: "json",
	   	colNames:[
			'Nombre',
			'Cédula',
			'Teléfono',
			'Correo',
			'Participación',
			'Estado',
			'Acci&oacute;n',
			'',
		],
	   	colModel:[
			{name:'Nombre', index:'nombre_agente', width:70},
			{name:'identificacion', index:'agt.identificacion', width:40, sortable:false},
			{name:'Telefono', index:'agt.telefono', width:40,   sortable:false},
	   		{name:'Correo', index:'agt.correo', width: 40,   sortable:false},
	   		{name:'estado', index:'agt.estado', width: 40,   sortable:false},
	   		{name:'porcentaje_participacion', index:'agt.porcentaje_participacion', width: 30 },
	   		
			{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
			{name:'options', index:'options', hidedlg:true, hidden: true},
	   	],
		mtype: "POST",
	   	postData: {
	   		erptkn: tkn
	   	},
		height: "auto",
		autowidth: true,
		rowList: [10, 20,50, 100],
		rowNum: 10,
		page: 1,
		pager: "#pager",
		loadtext: '<p>Cargando...',
		hoverrows: false,
	    viewrecords: true,
	    refresh: true,
	    gridview: true,
	    multiselect: true,
	    sortname: 'id',
	    sortorder: "ASC",
	    beforeProcessing: function(data, status, xhr){
	    	//Check Session
			if( $.isEmptyObject(data.session) == false){
				window.location = phost() + "login?expired";
			}
	    },
	    loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	        $(this).closest("div.ui-jqgrid-view").find("#agentesGrid_cb, #jqgh_agentesGrid_link").css("text-align", "center");
	    }, 
	    beforeRequest: function(data, status, xhr){},
		loadComplete: function(data){
			
			//check if isset data
			if( data['total'] == 0 ){
				$('#gbox_agentesGrid').hide();
				$('.NoRecords').empty().append('No se encontraron agentes.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('.NoRecords').hide();
				$('#gbox_agentesGrid').show();
			}

			//---------
			// Cargar plugin jquery Sticky Objects
			//----------
			//add class to headers
			$("#agentesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
			
			//floating headers
			$('#gridHeader').sticky({
		    	getWidthFrom: '.ui-jqgrid-view', 
		    	className:'jqgridHeader'
		    });
			
			//Arreglar tamaño de TD de los checkboxes
			$("#agentesGrid_cb").css("width","50px");
			$("#agentesGrid tbody tr").children().first("td").css("width","50px");
		},
		onSelectRow: function(id){
			$(this).find('tr#'+ id).removeClass('ui-state-highlight');
		},
	});
	$("#agentesGrid").jqGrid('columnToggle');
	
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
	$("#agentesGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_cliente = $(this).attr("data-cliente");
		var rowINFO = $("#agentesGrid").getRowData(id_cliente);
	    var options = rowINFO["options"];
 	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Opciones: '+ $(this).attr("data-Nombre") +'');
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
		
		$("#agentesGrid").setGridParam({
			url: phost() + 'agentes/ajax-listar-agentes',
			datatype: "json",
			postData: {
				nombre: '',
				apellido: '',
				telefono: '',
				correo: '',
				identificacion: '',
				porcentaje_participacion: '',
			}
		}).trigger('reloadGrid');
		
		//Reset Fields
		$('#nombre, #apellido, #telefono, #correo, #identificacion').val('');
	});
});

function searchBtnHlr(e) {
	e.preventDefault();
	$('#searchBtn').unbind('click', searchBtnHlr);

	var nombre 	= $('#nombre').val();
	var apellido = $('#apellido').val();
	var telefono = $('#telefono').val();
	var correo 	= $('#correo').val();
	var identificacion 	= $('#identificacion').val();

	if(nombre != "" || apellido != "" || telefono != "" || correo != "" || identificacion != "" )
	{
		$("#agentesGrid").setGridParam({
			url: phost() + 'agentes/ajax-listar-agentes',
			datatype: "json",
			postData: {
				nombre: nombre,
				apellido: apellido,
				telefono: telefono,
				correo: correo,
				cedula: cedula,
				identificacion: identificacion,
				porcentaje_participacion: porcentaje_participacion,
				estado: estado,
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		$('#searchBtn').bind('click', searchBtnHlr);
	}else{
		$('#searchBtn').bind('click', searchBtnHlr);
	}
}