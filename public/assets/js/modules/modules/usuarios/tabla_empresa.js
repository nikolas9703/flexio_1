$(function(){

	//Init Usuarios Grid
	$("#usuariosGrid").jqGrid({
	   	url: phost() + 'usuarios/ajax-empresa-usuario',
	   	datatype: "json",
	   	colNames:[
			'Nombre',
			'Fecha Creación',
			'Acci&oacute;n',
			'',
		],
	   	colModel:[
			{name:'Nombre', index:'nombre', width:70},
			{name:'Fecha de Creacion', index:'fecha_creacion', formatter: 'date', formatoptions: { newformat: 'd-m-Y' }, width:70},
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
				$('.NoRecords').empty().append('No se encontraron colaboradores.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
		$('#opcionesModal').find('.modal-title').empty().append('Opciones: '+ rowINFO["Nombre"] +'');
		$('#opcionesModal').find('.modal-body').empty().append(options);
		$('#opcionesModal').find('.modal-footer').empty();
		$('#opcionesModal').modal('show');
	});
});
