$(function(){
	
 	var $this = $('a[data-modulo="Clientes Potenciales"]'); 
	$this.addClass('active');
 	$("#notificacionesGrid").jqGrid({
 	   	url: phost() + 'notificaciones/ajax-listar-notificaciones',
	   	datatype: "json",
	   	colNames:[
 			'Descripción de la Notificación',
			'Habilitado',
			'Aviso',
			'Rol',
			'Usuario',
			'Opciones',
			'' ,
 		],
	   	colModel:[
 			{name:'descripcion', index:'ntf.descripcion', width: 40 },
			{name:'habilitado', index:'ntf.habilitado',  width: 10 },
			{name:'id_aviso', index:'pry.ubicacion', width: 10    },
			{name:'id_rol', index:'ccat.etiqueta', width: 10 ,  },
 	   		{name:'usuario', index:'ccat2.etiqueta', width: 10 , editable: true },
 	   		{name:'link', index:'link', width:10, align:"center", sortable:false, resizable:false, hidedlg:true},
 	   		{name:'options', index:'options', hidedlg:true, hidden: true},
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
		pager: "#pager_notificaciones",
		loadtext: '<p>Cargando...',
		hoverrows: false,
	    viewrecords: true,
	    refresh: true,
	    gridview: true,
	    multiselect: false,
	    sortname: 'ntf.id_notificacion',
	    sortorder: "ASC",
	    beforeProcessing: function(data, status, xhr){
	    	//Check Session
 			if( $.isEmptyObject(data.session) == false){
				window.location = phost() + "login?expired";
			}
	    },
	    loadBeforeSend: function () {//propiedadesGrid_cb
 	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	       // $(this).closest("div.ui-jqgrid-view").find("#notificacionesGrid_cb, #notificacionesGrid_link").css("text-align", "center");
	    }, 
	    beforeRequest: function(data, status, xhr){},
		loadComplete: function(data){
  			//check if isset data
			if( data['total'] == 0 ){
				$('#gbox_notificacionesGrid').hide();
				$('.NoRecords').empty().append('No se encontraron notificaciones.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('.NoRecords').hide();
				$('#gbox_notificacionesGrid').show();
			}

			//---------
			// Cargar plugin jquery Sticky Objects
			//----------
			//add class to headers
			$("#notificacionesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
			
			//floating headers
			 $('#gridHeader').sticky({
		    	getWidthFrom: '.ui-jqgrid-view', 
		    	className:'jqgridHeader'
		    }); 
			
			//Arreglar tamaño de TD de los checkboxes
			//$("#notificacionesGrid_cb").css("width","50px");
			//$("#notificacionesGrid tbody tr").children().first("td").css("width","50px");
		},
		onSelectRow: function(id){
			$(this).find('tr#'+ id).removeClass('ui-state-highlight');
  		},
 		/*	onSelectRow: function(id){
		if(id && id!==lastsel2){
			jQuery('#rowed5').jqGrid('restoreRow',lastsel2);
			jQuery('#rowed5').jqGrid('editRow',id,true);
			lastsel2=id;
		}
	},
	editurl: "server.php",
	caption: "Input Types"
*/
	});
	
	 $('#barra').on("click", ".btn-bitbucket", function(e){
		 
	 		e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
		 
			$('.btn-bitbucket').removeClass('active');
			
			var modulo	= $(this).attr("data-modulo");
			var $this = $(this);
			$this.addClass('active');  
			$("#notificacionesGrid").setGridParam({
					url: phost() + 'notificaciones/ajax-listar-notificaciones',
					datatype: "json",
					postData: {
		                modulo: modulo,
						erptkn: tkn
					}
				}).trigger('reloadGrid');
				
				 
			
	 });
	 
	 
	 
	$("#notificacionesGrid").jqGrid('columnToggle');
	
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
	$("#notificacionesGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_notificacion = $(this).attr("data-notificacion");
		var rowINFO = $("#notificacionesGrid").getRowData(id_notificacion);
	    var options = rowINFO["options"];

	    //Init boton de opciones
		//$('#optionsModal').find('.modal-title').empty().append('Opciones:'+ rowINFO['nombre_clear'] );
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
		
		$("#notificacionesGrid").setGridParam({
			url: phost() + 'notificaciones/ajax-listar-notificaciones',
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
		$("#notificacionesGrid").setGridParam({
			url: phost() + 'notificaciones/ajax-listar-notificaciones',
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