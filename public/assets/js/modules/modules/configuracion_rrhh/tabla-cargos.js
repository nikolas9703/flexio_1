//Modulo Tabla de Cargos
var tablaCargos = (function(){

	var url = 'configuracion_rrhh/ajax-listar-cargos';
	var grid_id = "tablaCargosGrid";
	var grid_obj = $("#tablaCargosGrid");
	var opcionesModal = $('#opcionesModal');
	
	var botones = {
		opciones: ".viewOptions",
		editar: ".editarCargoBtn",
		duplicar: ".duplicarCargoBtn",
		desactivar: ".desactivarCargoBtn",
		activar: ".activarCargoBtn",
		buscar: "#searchBtn",
		limpiar: "#clearBtn"
	};
	
	var tabla = function(){
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'ID Cargo',
				'Cargo',
				'Descripci&oacute;n',
				'Tipo de Rata',
				'Rata',
				'C&oacute;digo',
				'Estados',
				'Acci&oacute;n',
				'',

			],
		   	colModel:[
				{name:'idcargo', index:'id_cargo', hidedlg:true, hidden:true},
				{name:'Cargo', index:'cargo', width:70, sortable:false},
				{name:'Descripcion', index:'descripcion', width:70, sortable:false},
				{name:'Tipo Rata', index:'tipo_rata', width:70, sortable:false},
				{name:'Rata', index:'rata', width:70, sortable:false},
				{name:'Codigo', index:'codigo', width:70, sortable:false},
		   	{name:'Estado', index:'estado', width: 50, sortable:false, align:"center"},
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
			pager: grid_id +"Pager",
			loadtext: '<p>Cargando...</p>',
			hoverrows: false,
		    viewrecords: true,
				loadonce: true,
		    refresh: true,
		    gridview: true,
		    multiselect: false,
		    sortname: 'nombre',
		    sortorder: "ASC",
		    treeGrid: false,
		    //treeGridModel: 'adjacency',
		    //treedatatype: "json",
		    //ExpandColumn: 'departamento',
		    beforeProcessing: function(data, status, xhr)
				{
		    	if(typeof data == 'undefined' || typeof data == '')
					{
						console.log('Vacio');
						return false;
					}

			    	//Check Session
					if( $.isEmptyObject(data.session) == false)
					{
						window.location = phost() + "login?expired";
					}
		    },
		    loadBeforeSend: function () {}, 
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				
				if(typeof data == 'undefined'){
					return false;
				}
				
				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron cargos.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
			tablaCargos.redimensionar();
		});
	};
	
	//Inicializar Eventos de Botones
	var eventos = function(){
		
		//Bnoton de Opciones
		grid_obj.on("click", botones.opciones, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(id);
		    var options = rowINFO["options"];
		    
	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ $(rowINFO["Departamento"]).text() +'');
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});

		//Boton de Duplicar Cargo
		opcionesModal.on("click", botones.editar, function(e)
		{
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var cargo_id = $(this).attr("data-id");
			//56-0
			var rowINFO = grid_obj.getRowData(cargo_id);
		    console.log(rowINFO);
			//	var departamento_id = rowINFO["departamento_id"];
				//var idcargo = rowINFO["idcargo"];
		    var cargo = rowINFO["Cargo"];
		    var descripcion = rowINFO["Descripcion"];
		    var tipo_rata = rowINFO["Tipo Rata"];
				var rata = rowINFO["Rata"];
		    var codigo = rowINFO["Codigo"];
		    var estado = rowINFO["Estado"];
				//alert(cargo+ ' '+descripcion+' '+tipo_rata+' '+rata+' '+codigo+' '+estado);
		    var scope = angular.element('[ng-controller="configCargosCtrl"]').scope();

		    var selected_index = $('#crearCargoForm').find('#nombre').find('option[value="'+ nombre +'"]').eq();
				//alert('ID CARGOss: '+cargo_id);
		    scope.$apply(function(){
		    	scope.cargo.id = cargo_id;
		    	//scope.cargo.departamento_id = departamento_id;
		    	scope.cargo.nombre = cargo;
		    	scope.cargo.descripcion = descripcion;
		      scope.cargo.tipo_rata = tipo_rata;
		      scope.cargo.rata = rata;
		    });
		    setTimeout(function(){
                    $('#departamento_id').trigger('chosen:updated')
                    }, 300);
		    //Ocultar modal
			opcionesModal.modal('hide');
		});
		
		//Boton de Duplicar Cargo
		opcionesModal.on("click", botones.duplicar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var cargo_id = $(this).attr("data-id");

			//duplcar cargo
			duplicarCargo({
				cargo_id: cargo_id
			}).done(function(json){

	            //Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
	            
				//If json object is empty.
				if($.isEmptyObject(json) == true){
					return false;
				}
				
				//verificar si existe el id
				if(json.id == false && json.id == undefined){
					
					//mensaje error
					toastr.error(json.mensaje);
				}else{
					
					//mensaje success
					toastr.success(json.mensaje);
					
					//recargar jqgrid
					recargar();
				}
	        });
			
		    //Ocultar modal
			opcionesModal.modal('hide');
		});
		
		//Boton de Desactivar Cargo
		opcionesModal.on("click", [botones.desactivar, botones.activar], function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var cargo_id = $(e.target).attr("data-id");
			var estado_id = $(e.target).attr("data-estado") == 0 ? '0' : 1;

			//toggle cargo
			toggleCargo({
				cargo_id: cargo_id,
				estado_id: estado_id
			}).done(function(json){

	            //Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
	            
				//If json object is empty.
				if($.isEmptyObject(json) == true){
					return false;
				}
				
				//verificar si existe el id
				if(json.id == false && json.id == undefined){
					
					//mensaje error
					toastr.error(json.mensaje);
				}else{
					
					//mensaje success
					toastr.success(json.mensaje);
					
					//recargar jqgrid
					recargar();
				}
	        });
			
		    //Ocultar modal
			opcionesModal.modal('hide');
		});
	};
	
	//Boton de Buscar Cargo
	$(botones.buscar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		buscarCargo();
	});
	
	//Boton de Reiniciar jQgrid
	$(botones.limpiar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		recargar();
		limpiarCampos();
	});
	
	//Reload al jQgrid
	var recargar = function(){
		
		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				departamento: '',
				cargo: '',
				rata: '',
				codigo: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
	};
	
	//Buscar cargo en jQgrid
	var buscarCargo = function(){

		var departamento 	= $('#departamento').val();
		var cargo 			= $('#cargo').val();
		var rata 			= $('#rata_valor').val();
		var codigo 			= $('#codigo').val();

		if(cargo != "" /*|| departamento != "" ||  rata != "" || codigo != ""*/ )
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					departamento: departamento,
					cargo: cargo,
					rata: rata,
					codigo: codigo,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
	
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarCargoForm').find('input[type="text"]').prop("value", "");
	};
	
	//Funcion Ajax duplicar cargo
	var duplicarCargo = function(parametros){
		return $.ajax({
			url: phost() + 'configuracion_rrhh/ajax-duplicar-cargo',
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		});
	};
	
	//Funcion Ajax desactivar/activar cargo
	var toggleCargo = function(parametros){
		return $.ajax({
			url: phost() + 'configuracion_rrhh/ajax-toggle-cargo',
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		});
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

tablaCargos.init();

