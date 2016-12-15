$(function(){
	
	//Init Usuarios Grid
	$("#clientesGrid").jqGrid({
	   	url: phost() + 'clientes/ajax-listar-clientes',
	   	datatype: "json",
	   	colNames:[
			'Nombre',
			'Raz&oacute;n Social',
			'RUC/C&eacute;dula',
			'Tel&eacute;fono',
			'Correo',
			'Usuario',
			'&Uacute;ltimo Contacto',
			'# Oportunidades',
			'',
			'',
			'Acci&oacute;n',
		],
	   	colModel:[
			{name:'Nombre', index:'usr.nombre', width:70  },
			{name:'Nombre Comercial', index:'nombre_comercial', width:70, sortable:false},
			{name:'Cedula/RUC', index:'cedula_ruc', width:70, sortable:false},
	   		{name:'Telefono', index:'telefonos', width: 40,  sortable:false, hidden: true},
	   		{name:'Correos', index:'correos', width: 50,   sortable:false, hidden: true},
	   		{name:'Usuario', index:'usr.id_usuario', width: 50,  sortable:false, hidden: true},
	   		{name:'Ultimo Contacto', index:'ultimo_contacto', width: 50,  sortable:false, hidden: true},
	   		{name:'# Oportunidades', index:'oportunidades', width: 50,  sortable:false, hidden: true},
			{name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidedlg:true, hidden: true},
			{name:'options', index:'options', hidedlg:true, hidden: true},
			{name:'linkcliente', index:'linkcliente', width:50, align:"center", sortable:false, resizable:false, hidedlg:true},
	   	],
		mtype: "POST",
	   	postData: {
	   		erptkn: tkn
	   	},
		height: "auto",
		autowidth: true,
		rowList: [],
		rowNum: 5,
		page: 1,
		pager: "#pager_cliente",
		loadtext: '<p>Cargando...',
		hoverrows: false,
	    viewrecords: true,
	    refresh: true,
	    gridview: true,
	    sortname: 'usr.nombre',
	    sortorder: "ASC",
	    beforeProcessing: function(data, status, xhr){
	    	//Check Session
			if( $.isEmptyObject(data.session) == false){
				window.location = phost() + "login?expired";
			}
	    },
	    loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	        $(this).closest("div.ui-jqgrid-view").find("#jqgh_propiedadesGrid_link").css("text-align", "center");
	    }, 
	    beforeRequest: function(data, status, xhr){},
		loadComplete: function(data){
			
			//check if isset data
			if( data['total'] == 0 ){
				$('#gbox_clientesGrid').hide();
				$('.NoRecordsClientes').empty().append('No se encontraron clientes.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('.NoRecordsClientes').hide();
				$('#gbox_clientesGrid').show();
			}
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
	// Botones de formulario de Busqueda
	//-------------------------
	$('#searchBtn').bind('click', searchBtnHlr);
	$('#clearBtn').click(function(e){
		e.preventDefault();
		
		$("#clientesGrid").setGridParam({
			url: phost() + 'clientes/ajax-listar-clientes',
			datatype: "json",
			postData: {
				nombre_cliente: '',
				nombre_comercial: '',
				cedula_ruc: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		//Reset Fields
		$('#nombre_cliente, #nombre_comercial, #cedula_ruc').val('');
	});
});

function searchBtnHlr(e) {
	e.preventDefault();
	$('#searchBtn').unbind('click', searchBtnHlr);

	var nombre_cliente 	= $('#nombre_cliente').val();
	var nombre_comercial = $('#nombre_comercial').val();
	var cedula_ruc 	= $('#cedula_ruc').val();

	if(nombre_cliente != "" || nombre_comercial != "" || cedula_ruc != "")
	{
		$("#clientesGrid").setGridParam({
			url: phost() + 'clientes/ajax-listar-clientes',
			datatype: "json",
			postData: {
				nombre_cliente: nombre_cliente,
				nombre_comercial: nombre_comercial,
				cedula_ruc: cedula_ruc,
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		$('#searchBtn').bind('click', searchBtnHlr);
	}else{
		$('#searchBtn').bind('click', searchBtnHlr);
	}
}