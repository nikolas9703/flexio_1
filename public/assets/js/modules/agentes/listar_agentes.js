$(document).ready(function(){

	$('#searchBtn').bind('click');


	$(function(){

		var grid = $("#AgentesGrid");
		grid.jqGrid({
			url: phost() + 'agentes/ajax-listar',
			datatype: "json",
			colNames: ['','Nombre','Cédula','Teléfono','E-mail','Participación ','',''],
			colModel: [
				{name:'id', index:'id', hidedlg:true,key: true, hidden: true},
				{name:'nombre', index:'nombre',sorttype:"text",sortable:true,width:150},
				{name:'identificacion', index:'identificacion',sorttype:"identificacion",sortable:true,width:150},
				{name:'telefono', index:'telefono',sorttype:"text",sortable:true,width:150},
				{name:'correo', index:'correo',sorttype:"text",sortable:true,width:150},
				{name:'porcentaje_parcipacion', index:'porcentaje_parcipacion',sorttype:"text",sortable:true,width:150},
				{name:'opciones', index:'opciones', sortable:false, align:'center'},
				{name:'link', index:'link', hidedlg:true, hidden: true}
			],
			mtype: "POST",
			postData: { erptkn:tkn},
			height: "auto",
			autowidth: true,
			rowList: [10, 20,50, 100],
			rowNum: 10,
			page: 1,
			pager: "#pager_agentes",
			loadtext: '<p>Cargando...</p>',
			hoverrows: false,
                        viewrecords: true,
                        refresh: true,
                        gridview: true,
                        multiselect: true,
                        sortname: 'nombre',
                        sortorder: "ASC",
			beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){

				//check if isset data
				if(data.total == 0 ){
					$('#gbox_AgentesGrid').hide();
					$('.NoRecordsAgente').empty().append('No se encontraron Agentes.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('.NoRecordsAgente').hide();
					$('#gbox_AgentesGrid').show();
				}

				//---------
				// Cargar plugin jquery Sticky Objects
				//----------
				//add class to headers
				$("#AgentesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
				$("#AgentesGrid").find('div.tree-wrap').children().removeClass('ui-icon');
				//floating headers
				$('#gridHeader').sticky({
					getWidthFrom: '.ui-jqgrid-view',
					className:'jqgridHeader'
				});
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
                        loadBeforeSend: function () {//propiedadesGrid_cb
                        $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                        $(this).closest("div.ui-jqgrid-view").find("#AgentesGrid_cb, #jqgh_AgentesGrid_cb").css("text-align", "center");
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

		$("#AgentesGrid").on("click", ".viewOptions", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var id = $(this).attr("data-id");
			console.log("id -"+id);
			var rowINFO = $("#AgentesGrid").getRowData(id);
			console.log(rowINFO);
			var options = rowINFO["link"];
			//Init boton de opciones
			$('#opcionesModal').find('.modal-title').empty().html('Opciones: '+ rowINFO["nombre"] +'');
			$('#opcionesModal').find('.modal-body').empty().html(options);
			$('#opcionesModal').find('.modal-footer').empty();
			$('#opcionesModal').modal('show');
		});
               
            $("#exportarBtn").on("click", function(e){             
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();                        
			if($('#tabla').is(':visible') == true){				
				//Exportar Seleccionados del jQgrid
				var ids = [];
				ids = grid.jqGrid('getGridParam','selarrrow');
				
				//Verificar si hay seleccionados
				if(ids.length > 0){
				console.log(ids);	
				$('#ids').val(ids);
			        $('form#exportarAgentes').submit();
			        $('body').trigger('click');
				}
	        }
		});


	});



	$('#searchBtn').on("click",function(e) {

		var nombre 		= $('#nombre').val();
		var apellido 	= $('#apellido').val();
		var identificacion 		= $('#identificacion').val();
		var telefono 	= $('#telefono').val();
		var correo 		= $('#correo').val();
		if(nombre != "" || apellido != "" || identificacion != "" || telefono != "" || correo != "")
		{
			//Reload Grid
			$("#AgentesGrid").setGridParam({
				url: phost() + 'agentes/ajax-listar',
				datatype: "json",
				postData: {
					nombre: nombre,
					apellido: apellido,
					identificacion: identificacion,
					telefono: telefono,
					correo: correo,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
		else{
			$("#AgentesGrid").setGridParam({
				url: phost() + 'agentes/ajax-listar',
				datatype: "json",
				postData: {
					nombre: "",
					apellido: "",
					identificacion: "",
					telefono: "",
					correo: "",
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	});


});



$('#clearBtn').on("click",function(e){
	e.preventDefault();

	$("#AgentesGrid").setGridParam({
		url: phost() + 'agentes/ajax-listar',
		datatype: "json",
		postData: {
			nombre: '',
			apellido: '',
			identificacion: '',
			telefono: '',
			correo: '',
			erptkn: tkn
		}
	}).trigger('reloadGrid');

	//Reset Fields
	$('#nombre, #apellido, #identificacion, #telefono, #correo').val('');
});

