//Modulo
var tablaContactos = (function(){
  var url = 'aseguradoras/ajax_listar_contacto';
  var uuid_aseguradora=$('input[name="campo[uuid]').val();
  var grid_id = "tablaContactosGrid";
  var grid_obj = $("#tablaContactosGrid");
  var opcionesModal = $('#opcionesModal');
  
  var botones = {
    opciones: ".aseguradoraopciones", 
	exportarcontacto: ".botonexportardetalles",
	verdetalle: ".detallecontacto",
	verdetallenombre:".verdetallenombre",
	cambiarestadoseparado:".cambiarestadoseparado",
	verdetalleestado:".verdetalleestado",
	datosaseguradora:"#datosAseguradoraBtn",
  };
  
  var tabla = function(){	
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'',
				'Nombre',
				'Cargo',
				'Correo eléctronico',
				'Celular',
				'Teléfono',
				'Estado',  
				'',				
				'',				
				'',
				'',
			],
		   	colModel:[
				{name:'id', index:'id', width:50,  hidedlg:true, hidden: true},
				{name:'nombre', index:'nombre', width: 50},
				{name:'cargo', index:'cargo', width:50},
				{name:'email', index:'email', width:50},
				{name:'celular', index:'celular', width:50},
				{name:'telefono', index:'telefono', width: 50 },
				{name:'estado', index: 'estado', align:"center", width: 50,editable: true, stype:"select", 
				searchoptions: { 
				value: "'':Todo;Activo:Activo;Inactivo:Inactivo", 
								}}, 
				{name:'estadoestado', index:'estadoestado', width:50,  hidedlg:true, hidden: true},
				{name:'principal', index:'principal', width:50,  hidedlg:true, hidden: true},
				{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center", search:false},
				{name:'options', index:'options', hidedlg:true, hidden: true}  			
		   	],
			mtype: "POST",
		   	postData: {
		   		erptkn: tkn,
				uuid_aseguradora:uuid_aseguradora
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
			search:true,
		    sortname: 'nombre',
		    sortorder: "ASC",
			//caption: "Toolbar Searching",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {
				$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
				grid_obj.find('input[type="text"]').css("width", "95% !important");
                $(this).closest("div.ui-jqgrid-view").find("#tablaContactosGrid_cb, #jqgh_tablaContactosGrid_link").css("text-align", "center");
				
			}, 
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				
				$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
		});
		
		//Al redimensionar ventana
		$(window).resizeEnd(function() {
			tablaContactos.redimensionar();
		});
		
		grid_obj.jqGrid('navGrid',grid_id,{del:false,add:false,edit:false,search:true});
		grid_obj.jqGrid('filterToolbar',{searchOnEnter : false});
	};
	var eventos = function(){
		//Boton Opciones
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
			
			if(rowINFO["estadoestado"]=='Activo')
			{
				if(rowINFO["principal"]==1)
				{
					option = '<a href="" data-id="'+id+'" class="btn btn-block btn-outline btn-success detallecontacto">Ver detalle</a><a href="" data-id="'+id+'" class="btn btn-block btn-outline btn-success cambiarestadoseparado">Inactivar</a>';
				}
				else
				{
					option = '<a href="" data-id="'+id+'" class="btn btn-block btn-outline btn-success detallecontacto">Ver detalle</a><a href="" data-id="'+id+'" class="btn btn-block btn-outline btn-success cambiarestadoseparado">Inactivar</a><a href="" data-id="'+id+'" class="btn btn-block btn-outline btn-success contactoprincipal">Contacto Principal</a>';
				}
				
			}	
			else
			{
				if(rowINFO["principal"]==1)
				{
					option = '<a href="" data-id="'+id+'" class="btn btn-block btn-outline btn-success detallecontacto">Ver detalle</a><a href="" data-id="'+id+'" class="btn btn-block btn-outline btn-success cambiarestadoseparado">Activar</a>';
				}
				else
				{
					option = '<a href="" data-id="'+id+'" class="btn btn-block btn-outline btn-success detallecontacto">Ver detalle</a><a href="" data-id="'+id+'" class="btn btn-block btn-outline btn-success cambiarestadoseparado">Activar</a>';
				}
			}

	 	    ///Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["nombre"] +'');
		    opcionesModal.find('.modal-body').empty().append(option);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});
		
		//Boton de Exportar aseguradores
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
				$('#dato_aseguradora').val(uuid_aseguradora);
			        $('form#exportarContactos').submit();
			        $('body').trigger('click');
				}
	        }
		});
		
		$(opcionesModal).on("click", botones.verdetalle, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();	
			
			$("#formulariocontacto").show();
			$("#vistaCliente").hide();
			
			$('input[name="campo[opt]"').val(2);
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var datoscontacto=moduloContactos.cargardatoscontacto($(this).attr("data-id"));
			
			var datos=datoscontacto.success(function (data) {
				$.each(data, function(i,filename) {
					$('input[name="campo[uuid]"').val(filename.uuid_contacto);
					$('input[name="campo[nombre]"').val(filename.nombre);
					$('input[name="campo[email]"').val(filename.email);
					$('input[name="campo[cargo]"').val(filename.cargo);
					$('input[name="campo[celular]"').val(filename.celular);
					$('input[name="campo[direccion]"').val(filename.direccion);
					$('input[name="campo[comentarios]"').val(filename.comentarios);
					$('select[name="campo[estado]"').val(filename.estado);
					
					 $('#impresioncontacto').show();
					
					$(".breadcrumb").html('<li><a href="../../">Seguros</a></li><li><a href="../listar">Aseguradoras</a></li><li><a href="">'+filename.nombre_aseguradora+'</a></li><li class="Active">'+filename.nombre+'</li>');
				});
			});	
		});
		
		grid_obj.on("click", botones.verdetallenombre, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();	
			
			$("#formulariocontacto").show();
			$("#vistaCliente").hide();
			
			$("#exportarBtnTab").attr("value","Exportar Contactos");
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var datoscontacto=moduloContactos.cargardatoscontacto($(this).attr("data-id"));
			
			var datos=datoscontacto.success(function (data) {
				$.each(data, function(i,filename) {
					$('input[name="campo[uuid]"').val(filename.uuid_contacto);
					$('input[name="campo[nombre]"').val(filename.nombre);
					$('input[name="campo[email]"').val(filename.email);
					$('input[name="campo[cargo]"').val(filename.cargo);
					$('input[name="campo[celular]"').val(filename.celular);
					$('input[name="campo[direccion]"').val(filename.direccion);
					$('input[name="campo[comentarios]"').val(filename.comentarios);
					$('select[name="campo[estado]"').val(filename.estado);
					
					$(".breadcrumb").html('<li><a href="../../">Seguros</a></li><li><a href="../listar">Aseguradoras</a></li><li><a href="">'+filename.nombre_aseguradora+'</a></li><li class="Active">'+filename.nombre+'</li>');
				});
			});	
		});
		
		$(opcionesModal).on("click", botones.cambiarestadoseparado, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();	
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var datoscontacto=moduloContactos.cambiarestadocontacto($(this).attr("data-id"));
			
			var datos=datoscontacto.success(function (data) {
				$.each(data, function(i,filename) {
					
					console.log(filename.estadoestado);
					$("#tablaContactosGrid").jqGrid('setCell', filename.id, 'estado', '<label class="'+filename.labelestado+' verdetalleestado cambiarestadoseparado" data-id="'+$(this).attr("data-id")+'">'+filename.estado+'</label>');
					
					$("#tablaContactosGrid").jqGrid('setCell', filename.id, 'estadoestado', filename.estadoestado);
				});
			});	
		});
		
		$("#opcionesModal").on("click", ".contactoprincipal", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();	
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var datoscontacto=moduloContactos.cambiarContactoPrincipal($(this).attr("data-id"));
			
			var datos=datoscontacto.success(function (data) {
				$.each(data, function(i,filename) {
					
					console.log(filename.estadoestado);
					
					$("#tablaContactosGrid").jqGrid('setCell', filename.id, 'nombre', '<a href="" class="verdetallenombre" data-id="'+$(this).attr("data-id")+'"><span>'+filename.nombre+'</span></a><label class="label label-warning">Principal</label>');
					
					$("#tablaContactosGrid").jqGrid('setCell', filename.id, 'principal', filename.principal);
					
					recargar();
					
				});
			});	
		});
		
		//Boton de Exportar contacto
		$(botones.exportarcontacto).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();                        
			if($('#contactos').is(':visible') == true){			
				//Exportar Seleccionados del jQgrid
				var ids = [];
				ids = grid_obj.jqGrid('getGridParam','selarrrow');
				//Verificar si hay seleccionados
				if(ids.length > 0){
				console.log(ids);	
					$('#ids').val(ids);
			        $('form#exportarContactos').submit();
			        $('body').trigger('click');
				}
	        }
		});
		
		//Boton de Exportar contacto
		$(botones.datosaseguradora).on("click", function(e){
			$("#formulariocontacto").hide();
			$("#vistaCliente").show();
		});
	}
	
  
  //Reload al jQgrid
  var recargar = function(){
    
    //Reload Grid
    grid_obj.setGridParam({
      url: phost() + url,
      datatype: "json",
      postData: {
        nombre: '',
        cargo: '',
        email: '',
        celular: '',
        telefono: '',
        estado: ''
      }
    }).trigger('reloadGrid');
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
tablaContactos.init();

var moduloContactos = (function() {
	return {
		cargardatoscontacto:function(parametros){
			return $.post(phost() + 'aseguradoras/ajax_cargar_contacto', $.extend({
				erptkn: tkn,
				id:parametros,
			}, parametros));
		},
		cambiarestadocontacto:function(parametros){
			return $.post(phost() + 'aseguradoras/ajax_cambiar_estado_contacto', $.extend({
				erptkn: tkn,
				id:parametros,
			}, parametros));
		},
		cambiarContactoPrincipal:function(parametros){
			return $.post(phost() + 'aseguradoras/ajax_cambiar_contacto_principal', $.extend({
				erptkn: tkn,
				id:parametros,
			}, parametros));
		},
	};
})();

