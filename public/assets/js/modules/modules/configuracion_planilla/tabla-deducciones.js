 $(function(){
 	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		DeduccionesGrid.redimensionar();
	});
});
 
 //Modulo Tabla de Cargos
var DeduccionesGrid = (function(){
 	var url 		= 'configuracion_planilla/ajax-listar-deducciones';
 	var grid_id 	= "tablaDeduccionesGrid";
	var grid_obj 	=  $("#tablaDeduccionesGrid");
	var opcionesModal = $('#opcionesModal');
	var formulario = $('#crearDeduccionesForm');
  	
	var botones = {
			opciones: ".viewOptions",
			cancelar: "#cancelarDeduccionBtn" ,
			guardar: "#guardarDeduccionBtn",
    };
 	
  	var tabla = function(){
 		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
		   	   'Id',
		   	   'Nombre',
		   	   'Cuenta de pasivo',
   	           'Rata de colaborador',
   	           'Rata patronal',
   	           'Estado',
   	           'Estado Id',
   	           'Descripcion',
   	           '',
   	           '',
   	           '',
   	           '',
   	           '',
   	           '',
 			   '',
  			],
		   	colModel:[
		   	    {name:'Id', index:'id',hidden: true  },
		   	    {name:'Nombre', index:'nombre',  sortable:false },
		   	    {name:'Cuenta_Pasivo', index:'cuenta_pasivo',  sortable:false},
  				{name:'Rata_de_colaborador', index:'rata_de_colaborador',  sortable:false},
  				{name:'Rata Patronal', index:'Rata_patronal', sortable:false, resizable:false, hidedlg:true },
  				{name:'Estado', index:'estado', sortable:false, resizable:false, hidedlg:true },
  				{name:'Estado_id', index:'Estado_id', sortable:false, resizable:false, hidedlg:true, hidden: true  },
  				{name:'Descripcion', index:'Descripcion', sortable:false, resizable:false, hidedlg:true, hidden: true  },
  				{name:'Cuenta_pasivo_id', index:'Cuenta Pasivo ID', sortable:false, resizable:false, hidedlg:true, hidden: true  },
  				{name:'options', index:'options', hidedlg:true, align:"center" },
  				{name:'options', index:'options', hidedlg:true, hidden: true},
  				{name:'rata_colaborador_tipo', index:'rata_colaborador_tipo',  hidden: true},
  				{name:'rata_colaborador', index:'rata_colaborador',   hidden: true},
  				{name:'rata_patrono', index:'rata_patrono',   hidden: true},
   				{name:'rata_patrono_tipo', index:'rata_patrono_tipo',  hidden: true},
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
		    multiselect: false,
		    sortname: 'justificacion',
		    sortorder: "ASC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {//propiedadesGrid_cb
	 	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
		        $(this).closest("div.ui-jqgrid-view").find("#jqgh_tablaDeduccionesGrid_cb, #jqgh_tablaDeduccionesGrid_link").css("text-align", "center");
		    }, 
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				
				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron deducciones.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
				}
 				$('#'+ grid_id +'Pager_right').empty();
 				
  				//$('button[data-id="1"]').prop( "disabled", true );
			},
			  
			  onSelectRow: function(id){
				  $(this).find('tr#'+ id).removeClass('ui-state-highlight');
    		},
 		});
	};
 
	
	var campos = function(){
		$(formulario).validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
 		});
		
		$(formulario).find('#nombre').rules("add",{ required: true});
		$(formulario).find('#rata_patrono').rules("add",{ required: true});
		$(formulario).find('#rata_colaborador').rules("add",{ required: true});
		$(formulario).find('#cuenta_pasivo_id').rules("add",{ required: true});
		
		
		formulario.find('#rata_colaborador').prop('readonly',true);
		formulario.find('#rata_patrono').prop('readonly',true);
		
		 formulario.find("#rata_colaborador_ul li a").click(function(){
			 formulario.find('#rata_colaborador').prop('readonly',false);
 			 var name = this.name;
			 if(name == 'monto' ){
				 formulario.find("#rata_simbolo").text('$');
			 }
			 else{
				 formulario.find("#rata_simbolo").text('%');
			 }
						
			 $("#div_rata_colaborador").find(".btn:first-child").text($(this).text());
			 formulario.find("#rata_colaborador_tipo").val($(this).text());
			 
		 }); 
		 
		 formulario.find("#rata_patrono_ul li a").click(function(){
			 
			 formulario.find('#rata_patrono').prop('readonly',false);
 			 var name = this.name;
 			 
 			 if(name == 'monto' ){
				 formulario.find("#rata_simbolo_patrono").text('$');
			 }
			 else{
				 formulario.find("#rata_simbolo_patrono").text('%');
			 }
						
			 $("#div_rata_patrono").find(".btn:first-child").text($(this).text());
			 formulario.find("#rata_patrono_tipo").val($(this).text());

		 });
		
   };
	//Limpiar campos de busqueda
	var limpiarDeduccion = function(){
		formulario.find('input[type="text"]').prop("value", "");
 		
		formulario.find("#div_rata_colaborador").find(".btn:first-child").text('Seleccione');
		formulario.find("#div_rata_patrono").find(".btn:first-child").text('Seleccione');
		
		formulario.find('#rata_colaborador').prop('readonly',true);
		formulario.find('#rata_patrono').prop('readonly',true);
		
		formulario.find('#rata_simbolo').text('_');
		formulario.find('#rata_simbolo_patrono').text('_');
 		
 		formulario.find('select').val("");
   		formulario.find('#estado_id option[value="1"]').prop('selected', 'selected');
   		
		formulario.find('input[type="input-left-addon"]').prop("value", "");
		formulario.find('#id_deduccion').val("0");
 		formulario.find('#titulo_form_deduccion').text("Datos generales");
 	
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
			    opcionesModal.find('.modal-title').empty().append('Opciones: '+rowINFO['Nombre']);
			    opcionesModal.find('.modal-body').empty().append(options);
			    opcionesModal.find('.modal-footer').empty();
			    opcionesModal.modal('show');
			}); 
			opcionesModal.on("click", ".editarDeduccionBtn", function(e){
				 
 		 		e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				
				formulario.find('#id_deduccion').prop("value", "0");
				formulario.find('#cuenta_pasivo_id').val("");
     			   	 
				var id = $(this).attr("data-id");
		  		var rowINFO 	= grid_obj.getRowData(id);
		  		
		  		
		 		$('#titulo_form_deduccion').text("Editando deducción:  "+ rowINFO['Nombre']);

  		  	    var nombre 				= rowINFO['Nombre'];
  		  	    var cuenta_pasivo_id	= rowINFO['Cuenta_pasivo_id'];
   		  	    var rata_colaborador_tipo	= rowINFO['rata_colaborador_tipo'];
  		  	    var rata_colaborador		= rowINFO['rata_colaborador'];
  		  	    var rata_patrono_tipo		= rowINFO['rata_patrono_tipo'];
  		  	    var rata_patrono			= rowINFO['rata_patrono'];
   		  	    var descripcion 		= rowINFO['Descripcion'];
  		  	    var estado_id 			= rowINFO['Estado_id'];
  		  	    
  		  	    formulario.find("#rata_simbolo, #rata_simbolo_patrono").text('_');

  		  	    if(rata_colaborador_tipo == 'Monto' ){
  		  	    	formulario.find("#rata_simbolo").text('$');
  		  	    }
  		  	    else if(rata_colaborador_tipo == 'Porcentual' ){
  		  	    	formulario.find("#rata_simbolo").text('%');
  		  	    }
  		  	    
  		  	    if(rata_patrono_tipo == 'Monto' ){
		  	    	formulario.find("#rata_simbolo_patrono").text('$');
		  	    }
		  	    else if(rata_patrono_tipo == 'Porcentual' ){
		  	    	formulario.find("#rata_simbolo_patrono").text('%');
		  	    }
  		  	    
  		  	    $("#div_rata_colaborador").find(".btn:first-child").text(rata_colaborador_tipo);
  		  	    $("#div_rata_patrono").find(".btn:first-child").text(rata_patrono_tipo);
  		  	 
  		  	 
  		  	    formulario.find('.ibox-content:not(:visible)').prev().find('a').trigger('click');
   				formulario.find('#nombre').prop("value", nombre);
   				formulario.find('#cuenta_pasivo_id option[value="'+ cuenta_pasivo_id +'"]').prop('selected', 'selected');
    			
   				formulario.find('#rata_colaborador_tipo').prop("value", rata_colaborador_tipo);
   				formulario.find('#rata_colaborador').prop("value", rata_colaborador);
   				formulario.find('#rata_colaborador').prop('readonly',false);
   				
   				formulario.find('#rata_patrono_tipo').prop("value", rata_patrono_tipo);
   				formulario.find('#rata_patrono').prop("value", rata_patrono);
   				formulario.find('#rata_patrono').prop('readonly',false);
   				
   				formulario.find('#descripcion').prop("value", descripcion);
   				formulario.find('#id_deduccion').prop("value", id);
  				formulario.find('#estado_id option[value="'+ estado_id +'"]').prop('selected', 'selected');
  				
   				opcionesModal.modal('hide');
		 	});
			
			$(botones.cancelar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
			 	limpiarDeduccion();
 			});
			$(botones.guardar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
		 		 
				agregarDeduccion();
			});
			  
  
 	};
	 
	
 	//Reload al jQgrid
	var recargar = function(){
   
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
  				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
	};
  
 	//Buscar cargo en jQgrid
	var agregarDeduccion = function(){

		if(formulario.validate().form() == true )
		{
			$.ajax({
				url: phost() + 'configuracion_planilla/ajax-crear-deduccion',
				data: formulario.serialize(),
					type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json) {
	 
				//Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				if(json.response == true){
						 toastr.success(json.mensaje);
						 limpiarDeduccion();
						 recargar();
					}else{
					toastr.error(json.mensaje);
				}
			 
				 
			});
			$('#opcionesModal').modal('hide');
		}
		
	};
	return{	    
		init: function() {
			tabla();
 			eventos();
 			campos();
		},
		recargar: function(){
			recargar();
		},
		eventos: function(){
			eventos();
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

DeduccionesGrid.init();





