//Modulo
var tablaAjustadores = (function(){

	var url = 'ajustadores/ajax-listar';
	var grid_id = "ajustadoresGrid";
	var grid_obj = $("#ajustadoresGrid");
	var opcionesModal = $('#opcionesModal');
        var crearContactoForm = $("#crearContactoForm");
	var botones = {
		opciones: ".viewOptions",
		editar: "",
		duplicar: "",
		desactivar: "",
                crearContacto: ".agregarContacto",
		activar: "#activarAjustadoresLnk",
		trasladar: "#trasladarAjustadoresLnk",
		liquidar: "#liquidarAjustadoresLnk",
		exportar: "#exportarAjustadores",
		subirArchivo: ".subirArchivoBtn",
		buscar: "#searchBtn",
		limpiar: "#clearBtn"
	};
	
	var tabla = function(){
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
                                '',
                                'Nombre',
				'RUC',
				'Tel&eacute;fono',
				'E-mail',
				'Direcci&oacute;n',				
				'',
				'',
			],
		   	colModel:[
                                {name:'id', index:'id', width:30,  hidedlg:true, hidden: true},
				{name:'Nombre', index:'nombre', width:70},
				{name:'Ruc', index:'ruc', width:40},
				{name:'Telefono', index:'telefono', width:50},
				{name:'E-mail', index:'email', width: 60 },
				{name:'Direccion', index:'direccion', width: 60 }, 
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
		    loadBeforeSend: function () {//propiedadesGrid_cb
                    $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                    $(this).closest("div.ui-jqgrid-view").find("#ajustadoresGrid_cb, #jqgh_ajustadoresGrid_link").css("text-align", "center");
                    }, 
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				
				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron ajustadores.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
			tablaAjustadores.redimensionar();
		});
	};
	
	//Inicializar Eventos de Botones
	var eventos = function(){
		
		//Boton de Opciones
		grid_obj.on("click", botones.opciones, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
                    var id = $(this).attr("data-id");
                    var rowINFO = grid_obj.getRowData(id);                        
		    var option = rowINFO["options"];
		    //evento para boton collapse sub-menu Accion Personal
		    opcionesModal.on('click', 'a[href="#collapse'+ id +'"]', function(){
		    	opcionesModal.find('#collapse'+ id ).collapse();
		    });

	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["Nombre"] +'');
		    opcionesModal.find('.modal-body').empty().append(option);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});
		
		//Boton de Buscar Colaborador
		$(botones.buscar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			buscarAjustadores();
		});
		
		//Boton de Reiniciar jQgrid
		$(botones.limpiar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			recargar();
			limpiarCampos();
		});
		//Boton de Exportar Colaborador
		$(botones.exportar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();                        
			if($('#tabla').is(':visible') == true){				
				//Exportar Seleccionados del jQgrid
				var ids = [];
				ids = grid_obj.jqGrid('getGridParam','selarrrow');
				
				//Verificar si hay seleccionados
				if(ids.length > 0){
				console.log(ids);	
				$('#ids').val(ids);
			        $('form#exportarAjustadores').submit();
			        $('body').trigger('click');
				}
	        }
		});
                $(opcionesModal).on("click", botones.crearContacto, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');			
			var ajustadores_id = $(this).attr("data-id");
			var ajustadores_uuid = $(this).attr("data-uuid");
			//Limpiar formulario
                        crearContactoForm.attr('action', phost() + 'ajustadores/ver/' + ajustadores_uuid);
			crearContactoForm.find('input[name*="ajustadores_"]').remove();
			crearContactoForm.append('<input type="hidden" name="ajustadores_id" value="'+ ajustadores_id +'" />');			
			crearContactoForm.append('<input type="hidden" name="agregar_contacto" value="1" />');			
			//Enviar formulario
			crearContactoForm.submit();
	        $('body').trigger('click');
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
	
	//Buscar cargo en jQgrid
	var buscarAjustadores = function(){
		var nombre 		= $('#nombre').val();
		var telefono 		= $('#telefono').val();
		var email 		= $('#email').val();
		if(nombre != "" || telefono != "" || email != "")
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					nombre: nombre,
					telefono: telefono,
					email: email,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
	
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarAjustadoresForm').find('input[type="text"]').prop("value", "");
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

tablaAjustadores.init();