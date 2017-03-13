$(function(){
	 $("#id_proyectoBtn").on("click", function(){
	        $('#busquedaPoyectoModal').modal('toggle');
	    });
	 
	 $('#busquedaPoyectoModal').on('shown.bs.modal', function (e) {
	        $(".ui-jqgrid").each(function(){
          var w = parseInt( $(this).parent().width()) - 6;
          var tmpId = $(this).attr("id");
          var gId = tmpId.replace("gbox_","");
          $("#"+gId).setGridWidth(w);
      });
  });
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
	   			'Opciones' 
	   		],
	  	   	colModel:[
	  			{name:'Nombre', index:'pry.nombre', width:70,  hidden: true},
	  			{name:'nombre_clear', index:'pry.nombre',  width:70  },
	  			{name:'Ubicacion', index:'pry.ubicacion', width:70, hidden: true},
	  			{name:'Tipo', index:'ccat.etiqueta', width:70},
	   	   		{name:'Fase', index:'ccat2.etiqueta', width: 50 },
	  	   		{name:'Disponibles', index:'pry.no_disponibles', width: 50, hidden: true },
	  	   		{name:'Propiedades Disponibles',   width: 50, hidden: true },
	   			{name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidedlg:true, hidden: true},
	   			{name:'options', index:'options', hidedlg:true, hidden: true},
	  			{name:'link_seleccionar', index:'link_seleccionar', width: 50 }
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
			pager: "#pager2",
			loadtext: '<p>Cargando...',
			hoverrows: false,
		    viewrecords: true,
		    refresh: true,
		    gridview: true,
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
		        $(this).closest("div.ui-jqgrid-view").find("#proyectosGrid_cb, #jqgh_proyectosGrid_link").css("text-align", "center");
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
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
	     
	});
	  $("#proyectosGrid").on("click", ".viewOptions", function(){
	 	     var uuid_proyecto= $(this).attr("data-proyecto");
  	 	     
	 	    if(typeof $('#crearPropiedad').html()  === 'undefined'){
 	 	       $('#editarPropiedad ').find('select[name*="proyecto[id_proyecto]"] option[value="'+ uuid_proyecto +'"]').prop('selected', 'selected');
 	 	     }
	 	    else{
	 		       $('#crearPropiedad ').find('select[name*="proyecto[id_proyecto]"] option[value="'+ uuid_proyecto +'"]').prop('selected', 'selected');
	 		}
	 	   //Actualizar chosen
		        setTimeout(function(){
		            $(".chosen-select").chosen({
		                width: '100%'
		            }).trigger('chosen:updated');
		        }, 500); 
	 	     
		        popular_tipo_transaccion(uuid_proyecto);
	         $('#busquedaPoyectoModal').modal('hide');
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
		
		$("#proyectosGrid").setGridParam({
			url: phost() + 'proyectos/ajax-listar-proyectos',
			datatype: "json",
			postData: {
				nombre: '',
 				id_tipo: '',
				id_fase: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		//Reset Fields
		$('#nombre,  #id_tipo, #id_fase').val('');
	});
	
	
});

function searchBtnHlr(e) {
	//console.log("Hola undoi");
	e.preventDefault();
	$('#searchBtn').unbind('click', searchBtnHlr);

	var nombre 			= $('#nombre').val();
 	var id_tipo 		= $('#id_tipo').val();
	var id_fase 		= $('#id_fase').val();
  
	if( nombre != "" ||    id_tipo != "" || id_fase != ""  )
	{
		$("#proyectosGrid").setGridParam({
			url: phost() + 'proyectos/ajax-listar-proyectos',
			datatype: "json",
			postData: {
				nombre: nombre,
 				id_tipo: id_tipo,
				id_fase: id_fase,
 				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		$('#searchBtn').bind('click', searchBtnHlr);
	}else{
		$('#searchBtn').bind('click', searchBtnHlr);
	}
}