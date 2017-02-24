//Modulo
var tablaAseguradoras = (function(){
	var url = 'configuracion_seguros/ajax_listar';
	var grid_id = "RamosGrid";
	var grid_obj = $("#RamosGrid");
	var opcionesModal = $('#opcionesModal');	
	
	
	var botones = {
		opciones: ".viewOptions",
		editar: "",		
		buscar: "#searchBtn",
		limpiar: "#clearBtn" 
	};
	
	var botones = {
		opciones: ".viewOptions",
		editar: "",		
		buscar: "#searchBtn",
		limpiar: "#clearBtn"
	};
	
	var tabla = function(){		
		
		//inicializar jqgrid
		grid_obj.jqGrid({
			url: phost() + url,
			datatype: "json",
			colNames:[
			'Ramo',
			'Descripción',
			'Código',
			'Tipo de interes',
			'Tipo de póliza',		
			'Estado',
			'Acciones',
			],
			colModel:[
			{name:'nombre', index:'nombre', width: 50},
			{name:'ruc', index:'ruc', width:70},
			{name:'telefono', index:'telefono', width:40},
			{name:'email', index:'email', width:50},
			{name:'direccion', index:'direccion', width: 60 },
			{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
			{name:'options', index:'options', hidedlg:true, hidden: true}				
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
			pager: "#"+ grid_id +"Pager",
			loadtext: '<p>Cargando...</p>',
			hoverrows: false,
			viewrecords: true,
			refresh: true,
			gridview: true,
			multiselect: true,
			sortname: 'nombre',
			sortorder: "ASC",
			beforeProcessing: function(data, status, xhr){
		    	//Check Session
		    	if( $.isEmptyObject(data.session) == false){
		    		window.location = phost() + "login?expired";
		    	}
		    },
		    loadBeforeSend: function () {}, 
		    beforeRequest: function(data, status, xhr){},
		    loadComplete: function(data){

				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron Aseguradoras.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
				}
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
		});
		
		//Al redimensionar ventana
		$(window).resizeEnd(function() {
			tablaAseguradoras.redimensionar();
		});
	};
	
	//Inicializar Eventos de Botones
	var eventos = function(){		
		
		//Boton de Buscar Colaborador
		$(botones.buscar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			buscaraseguradoras();
		});
		
		//Boton de Reiniciar jQgrid
		$(botones.limpiar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			recargar();
			limpiarCampos();
		});
		
		//Boton de Activar Colaborador
		$(botones.activar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			if($('#tabla').is(':visible') == true){
				
				//Exportar Seleccionados del jQgrid
				var colaboradores = [];
				colaboradores = grid_obj.jqGrid('getGridParam','selarrrow');
				
				//Verificar si hay seleccionados
				if(colaboradores.length > 0){
					//Cambiar Estado
					toggleColaborador({colaboradores: colaboradores, estado_id: 1});
				}
			}
		});
	};
	
	//Reload al jQgrid
	var recargar = function(){
		
		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				nombre: '',
				ruc: '',
				telefono: '',
				email: '',
				direccion: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
	};
	
	var buscaraseguradoras = function(){

		var nombre 		= $('#nombre').val();
		var ruc 		= $('#ruc').val();
		var telefono		= $('#telefono').val();              
		var direccion                = $('#direccion').val();
		var email = $('#email').val();	

		if(nombre != "" || ruc != "" || telefono != "" || direccion != "" || email != "" )
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					nombre: nombre,
					ruc: ruc,
					telefono: telefono,
					direccion: direccion,
					email: email,				
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
	
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarAseguradoraForm').find('input[type="text"]').prop("value", "");
		$('#buscarAseguradoraForm').find('.chosen-select').val('').trigger('chosen:updated');
	};
	
	return{	    
		init: function() {
			tabla();
			eventos();
		},
		recargar: function(){
			//reload jqgrid
			recargar();
		},
		redimensionar: function(){
			//Al redimensionar ventana
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
		}
	};
})();
tablaAseguradoras.init();

