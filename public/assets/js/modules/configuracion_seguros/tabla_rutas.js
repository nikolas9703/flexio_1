//Modulo
var tablaRutas = (function(){
	var url = 'configuracion_seguros/ajax_listar_rutas';
	var grid_id = "RutasGrid";
	var grid_obj = $("#RutasGrid");
	var opcionesModal = $('#opcionesModal');
	var opcionesModalEstado = $('#opcionesModalEstado');
	var crearContactoForm = $("#crearContactoForm");	
	
	var botones = {
		opciones: ".viewOptions",
		editar: "",	
		crearContacto: ".agregarContacto",		
		buscar: "#searchBtn",
		limpiar: "#clearBtn",
		cambioGrupal: "#cambiarEstadoAseguradoraLnk",
		modalstate : "span.estadoRutas",
		exportar: "#exportarBtn",
		verdetalle:".verdetalle",
	};
	
	var tabla = function(){		
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'',
		   	    'Nombre de ruta',
				'Provincia',
				'Distrito',
				'Corregimiento',
				'Nombre mensajero',
				'Estado',				
				'',
				'',
				'',
				'',
				'',
				''
			],
		   	colModel:[
				{name:'id', index:'id', width:30, hidedlg:false, hidden: true},
		   	    {name:'nombre', index:'nombre_ruta', width: 50},
				{name:'provincia', index:'provincia', width:70},
				{name:'distrito', index:'distrito', width:40},
				{name:'corregimiento', index:'corregimiento', width:50},
				{name:'mensajero', index:'nombre_mensajero', width: 60 },
				{name:'estado', index: 'estado', align:"center", width: 45,editable: true, stype:"select", 
				searchoptions: { 
				value: "'':Todo;Activo:Activo;Inactivo:Inactivo", 
								}},
				{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center", search:false},
				{name:'options', index:'options', hidedlg:true, sortable:false, hidden: true, search:false},
				{name:'modalstate', index:'modalstate', align:"center", hidedlg:true, hidden: true, search:false},
				{name:'provincia_id', index:'provincia_id', width:30, hidedlg:false, hidden: true},
				{name:'distrito_id', index:'distrito_id', width:30, hidedlg:false, hidden: true},
				{name:'corregimiento_id', index:'corregimiento_id', width:30, hidedlg:false, hidden: true},
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
		    sortname: 'nombre_ruta1',
		    sortorder: "ASC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {
				$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#RutasGrid_cb, #jqgh_RutasGrid_link").css("text-align", "center");
				
				$("#jqgh_RutasGrid_cb span").removeClass("s-ico");
				$('#jqgh_RutasGrid_options span').removeClass("s-ico");
				$('#jqgh_RutasGrid_link span').removeClass("s-ico");
			}, 
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				
				/*//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).show();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron rutas.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
				}*/
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
		});
		
		//Al redimensionar ventana
		$(window).resizeEnd(function() {
			tablaRutas.redimensionar();
		});
		
		grid_obj.jqGrid('navGrid',grid_id,{del:false,add:false,edit:false,search:true});
		grid_obj.jqGrid('filterToolbar',{searchOnEnter : false});
	};
	
	//Inicializar Eventos de Botones
	var eventos = function(){		
		
		//Boton Opciones
		grid_obj.on("click", botones.opciones, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(id);  
			//alert(rowINFO);			
		    var option = rowINFO["options"];
		    //evento para boton collapse sub-menu Accion Personal
		    opcionesModal.on('click', 'a[href="#collapse'+ id +'"]', function(){
		    	opcionesModal.find('#collapse'+ id ).collapse();
		    });

	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["nombre"] +'');
		    opcionesModal.find('.modal-body').empty().append(option);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});
		
		 //Bnoton de estado
        grid_obj.on("click", botones.modalstate, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");
            var rowINFO = $.extend({}, grid_obj.getRowData(id));
            var options = rowINFO.modalstate;

            var estado = $(this).attr("data-rutaEstado");
			opcionesModal.modal('show');
			
			//Init Modal
			opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.nombre_ruta).text() + '');
			opcionesModal.find('.modal-body').empty().append(options);
			opcionesModal.find('.modal-footer').empty();
			opcionesModal.modal('show');
			
			var contador=0;
			
			opcionesModal.on('click', '.activarRuta', function (e) {
				var datos = {estado:'Activo',id:id};
				
				if(contador==0)
				{
					var cambio = moduloRutas.ajaxcambiarEstados(datos);
					cambio.done(function(response){
						recargar();
						opcionesModal.modal('hide');
						$("#mensaje_info_ruta").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>¡Éxito! La actualización de estado</div>');
					});
					
					contador++;
				}
			});
			
			opcionesModal.on('click', '.inactivarRuta', function (e) {
				var datos = {estado:'Inactivo',id:id};
				if(contador==0)
				{
					var cambio = moduloRutas.ajaxcambiarEstados(datos);
					cambio.done(function(response){
						recargar();
						opcionesModal.modal('hide');
						$("#mensaje_info_ruta").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>¡Éxito! La actualización de estado</div>');
					});
					contador++;
				}
			});
            
        });
		
		opcionesModal.on('click', '.verdetalle', function (e) {
			e.preventDefault();
            var id = $(this).attr("data-id");
			var rowINFO = $.extend({}, grid_obj.getRowData(id));
			var nombre_ruta=rowINFO.nombre;
			var provincia_id=rowINFO.provincia_id;
			var distrito_id=rowINFO.distrito_id;
			var corregimiento_id=rowINFO.corregimiento_id;
			var mensajero=rowINFO.mensajero;
			opcionesModal.modal('hide');
			
			$('#id_ruta').val(id);
			$('#nombre1_ruta').val(nombre_ruta);
			$('#provincia_ruta').val(provincia_id);
			$('#nombremensajero_ruta').val(mensajero);
			
			formularioCrear.getObtenerProvinciasDetalle(provincia_id,distrito_id,corregimiento_id);
			$('#distrito_ruta').val(distrito_id);
			formularioCrear.getObtenerCorregimientosDetalle(distrito_id,corregimiento_id);
			$('#corregimiento_ruta').val(corregimiento_id);
			
			$("#mensaje_info_ruta").empty().html('');
			
			$('#boton_actualizar').show();
			$('#boton_guardar').hide();
			
		});
	};
	
		
	//Reload al jQgrid
	var recargar = function(){
		
		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				nombre_ruta: '',
				provincia: '',
				distrito: '',
				corregimiento: '',
				mensajero: '',
			}
		}).trigger('reloadGrid');
	};
	
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#crearRutasForm').find('input[type="text"]').prop("value", "");
		$('#crearRutasForm').find('input[type="hidden"]').prop("value", "");
		$('#crearRutasForm').find('input[type="select"]').prop("value", "");
		$('#crearRutasForm').find('.chosen-select').val('').trigger('chosen:updated');
	};
	
	//Boton de Exportar aseguradores
	$(botones.exportar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();                        
		if($('#tab_rutas').is(':visible') == true){

			if ($('#tab_rutas').hasClass('active')){
				//Exportar Seleccionados del jQgrid
				var ids = [];
				ids = grid_obj.jqGrid('getGridParam','selarrrow');
				
				//Verificar si hay seleccionados
				if(ids.length > 0){	
				$('#id_rutas').val(ids);
					$('form#exportarRutas').submit();
					$('body').trigger('click');
					
					if($("#cb_"+grid_id).is(':checked')) {
						$("#cb_"+grid_id).trigger('click');
					}
					else
					{
						$("#cb_"+grid_id).trigger('click');
						$("#cb_"+grid_id).trigger('click');
					}
				}
			}
		}
	});
	
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
tablaRutas.init();