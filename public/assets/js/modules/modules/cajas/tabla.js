//Modulo Tabla de Cajas
var tablaCajas = (function(){

	var url = 'cajas/ajax-listar';
	var grid_id = "cajasGrid";
	var grid_obj = $("#cajasGrid");
	var opcionesModal = $('#opcionesModal');
        var documentosModal = $('#documentosModal');
	
	var botones = {
		opciones: ".viewOptions",
		editar: "",
		duplicar: "",
		desactivar: "",
		buscar: "#searchBtn",
		limpiar: "#clearBtn",
                subirDocumento: ".subirArchivoBtn"
	};
	
	var tabla = function(){
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'No. Caja',
				'Nombre',
				'Centro Contable',
				'Responsable',
				'Limite',
				'Saldo',
				'',
				''
			],
		   	colModel:[
				{name:'No. Caja', index:'ca_cajas.numero', width:70, align:'left'},
				{name:'Nombre', index:'ca_cajas.nombre', width: 40, sortable:false, align:'left'},
				{name:'Centro Contable', index:'cen_centros.nombre', width: 40, sortable:false, align:'left'},
				{name:'Responsable', index:'ca_cajas.responsable', width:70,  sortable:false, align:'left'},
				{name:'Limite', index:'ca_cajas.limite', width:70,  sortable:false, align:'left'},
				{name:'Saldo', index:'ca-cajas.saldo', width: 40, sortable:false, align:'left'},
				{name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidedlg:true},
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
		    sortname: 'numero',
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
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron datos de cajas.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
			 tablaCajas.redimensionar();
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
		    options = option.replace(/0000/gi, id);
		    
		    //evento para boton collapse sub-menu Accion Personal
		    opcionesModal.on('click', 'a[href="#collapse'+ id +'"]', function(){
		    	opcionesModal.find('#collapse'+ id ).collapse();
		    });

	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["Nombre"] +'');
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});
		
		//Boton de Buscar
		$(botones.buscar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			buscar();
		});
		
		//Boton de Reiniciar jQgrid
		$(botones.limpiar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			recargar();
			limpiarCampos();
		});
	};
	
	//Buscar
	var buscar = function(){

		var nombre = $('#nombre').val();
		var centro = $('#centro').val();
		var limite = $('#limite').val();
		var responsable_id = $('#responsable_id').val();

		if(nombre!= "" || centro != "" || limite != ""  || responsable_id != "" )
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					nombre: nombre,
                    centro_id: centro,
                    limite: limite,
                    responsable_id: responsable_id,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
        
        $("#opcionesModal").on("click", ".subirDocumento", function (e) {
       
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        opcionesModal.modal('hide');
        
        var caja = $(this).attr("data-id");
        
        	//Inicializar opciones del Modal
			documentosModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			
			var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
		    scope.safeApply(function(){		    	
                scope.campos.caja_id = caja;
		    	
		    });
			documentosModal.modal('show');
    });
	
	//Reload al jQgrid
	var recargar = function(){
		
		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				nombre: '',
				centro_id: '',
                limite: '',
                responsable_id: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
	};
	

	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarCajasForm').find('input[type="text"]').prop("value", "");
		$('#buscarCajasForm').find('select').find('option:eq(0)').prop('selected', 'selected');
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

tablaCajas.init();
