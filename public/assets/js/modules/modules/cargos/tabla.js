//Tabla Accion de Personal
var tablaAccionPersonal = (function(){

	var url = 'cargos/ajax-listar';
	var grid_id = "tablaCargosGrid";
	var grid_obj = $("#tablaCargosGrid");
	var opcionesModal = $('#opcionesModal');
	var opcionesModalGrupal = $("#opcionesModalGrupal");
	var formulario = $('#buscarCargosForm');
	var formularioExportar = $('#exportarOrdenesForm');
	var formularioEstados = $('#cambiarEstadoEnGrupo');

	var botones = {
		buscar: "#searchBtn",
		limpiar: "#clearBtn",
		exportar: "#exportarLnk",
		opciones: ".cambiarEstado",
		cambioGrupal: "#cambiarEstadoGrupal"
	};

	var equipoid = "";

	if(typeof equipoID != "undefined"){
		equipoid = equipoID;
	}

	var tabla = function(){

		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
		   	'No. Cargo',
				'Item',
				'Fecha',
				'Cantidad',
				'Contrato',
				'Tarifa pactada',
				'Periodo tarifario',
				'Monto de cargo',
				'Estado',
				''
			],
		  colModel:[
		   	{name:'No. Cargo', index:'numero', width: 30},
				{name:'Item', index:'cliente', width:30},
				{name:'Fecha', index:'fecha_inicio', width:18},
				{name:'Cantidad', index:'centro_id', width:10 },
				{name:'Contrato', index:'estado', width:18, align: 'center' },
				{name:'Tarifa pactada', index:'estado', width:20, align: 'center' },
				{name:'Periodo tarifario', index:'estado', width:18, align: 'center' },
				{name:'Monto de cargo', index:'estado', width:20, align: 'center' },
				{name:'Estado', index:'estado', width:20, align: 'center' },
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
	    sortorder: "DESC",
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
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron datos de Cargos.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
			tablaAccionPersonal.redimensionar();
		});
	};

	//Inicializar Eventos de Botones
	var eventos = function(){
		//Boton de Buscar
		formulario.on("click", botones.buscar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			buscar();
		});

		//Boton de Reiniciar jQgrid
		formulario.on("click", botones.limpiar, function(e){
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

			//Exportar Seleccionados del jQgrid
			var ids = [];
				ids = grid_obj.jqGrid('getGridParam','selarrrow');

			//Verificar si hay seleccionados
			if(ids.length > 0){

				$('#ids').val(ids);
				formularioExportar.submit();
		        $('body').trigger('click');
			}
		});

		//Boton de cambio de estado grupal
		$(botones.cambioGrupal).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			//Seleccionados del jQgrid
			var ids = [];
				ids = grid_obj.jqGrid('getGridParam','selarrrow');
			//Verificar si hay seleccionados
			if(ids.length > 0){
				var rowINFO = grid_obj.getRowData(ids);
				var options = '<a href="#" class="btn btn-block btn-outline btn-success porFacturar">Por facturar</a><a href="#" class="btn btn-block btn-outline btn-success facturado">Facturado</a><a href="#" class="btn btn-block btn-outline btn-success anulado">Anulado</a>';
				$("#opcionesModalGrupal").on("click", ".porFacturar", function(e){
					var estado = "por_facturar";
					$('#ids').val(ids);
					$('#estadoGrupal').val(estado);
				 formularioEstados.submit();
			    $('body').trigger('click');
				});
				$("#opcionesModalGrupal").on("click", ".facturado", function(e){
					var estado = "facturado";
					$('#ids').val(ids);
					$('#estadoGrupal').val(estado);			 	;
				 formularioEstados.submit();
			    $('body').trigger('click');
				});
				$("#opcionesModalGrupal").off("click", ".anulado").on("click", ".anulado", function(e){
					cambiarEstado(ids, "anulado");
				});
						//Init Modal
				opcionesModalGrupal.find('.modal-title').empty().append('Cambiar estado');
				opcionesModalGrupal.find('.modal-body').empty().append(options);
				opcionesModalGrupal.find('.modal-footer').empty();
				opcionesModalGrupal.modal('show');
			}
		});
		//Boton de Opciones
		grid_obj.on("click", botones.opciones, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			var id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(id);
				var options = rowINFO["options"];
		 $("#opcionesModal").on("click", ".porFacturar", por_facturar);
		 $("#opcionesModal").on("click", ".facturado", facturado);
		 $("#opcionesModal").off("click", ".anulado").on("click", ".anulado", anulado);
				//Init Modal
				opcionesModal.find('.modal-title').empty().append('Cambiar estado');
				opcionesModal.find('.modal-body').empty().append(options);
				opcionesModal.find('.modal-footer').empty();
				opcionesModal.modal('show');
		});
		function por_facturar(e){
				var self = $(this);
				var id = self.data("id");
				var estado = "por_facturar";
				cambiarEstado(id, estado);
		};
		function facturado(e){
				var self = $(this);
				var id = self.data("id");
				var estado = "facturado";
				cambiarEstado(id, estado);
		};
		function anulado(e){
				var self = $(this);
				var id = self.data("id");
				var estado = "anulado";
				cambiarEstado(id, estado);
		};

		function cambiarEstado(id, estado){
			$.ajax({
					url: phost() + "cargos/ajax-cambiar-estado",
					type:"POST",
					data:{
							erptkn:tkn,
							id: id,//array or integer
							estado: estado
					},
					dataType:"json",
					success: function(data){
							if(data.success === true)
							{
								opcionesModal.modal("hide");
								opcionesModalGrupal.modal("hide");
								grid_obj.trigger("reloadGrid");
								_.forEach(data.mensaje.split('<br>'), function(mensaje){
									var tipo = mensaje.indexOf('fa-check') > -1 ? 'success' : 'error';
									if(mensaje.length > 4){toastr[tipo](mensaje);}
								});

							}
					}

			});

		};

	};

	//Reload al jQgrid
	var recargar = function(){

		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				numero: '',
				item: '',
				estado: '',
				fecha_desde: '',
				fecha_hasta: '',
				periodo: '',
				contrato: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');

	};

	//Buscar cargo en jQgrid
	var buscar = function(){

		var numero 			= formulario.find('#numero').val();
		var item				= formulario.find('#item').val();
		var fecha_desde = formulario.find('#fecha_desde').val();
		var fecha_hasta = formulario.find('#fecha_hasta').val();
		var contrato		= formulario.find('#contrato').val();
		var periodo			= formulario.find('#periodo').val();
		var estado 			= formulario.find('#estado').val();

		if(numero != "" || item  != "" || fecha_desde != "" || fecha_hasta != ""
		|| estado != "" || contrato != "" || periodo != "")
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					numero: numero,
					item: item,
					estado: estado,
					fecha_desde: fecha_desde,
					fecha_hasta: fecha_hasta,
					periodo: periodo,
					contrato: contrato,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};

	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		formulario.find('input[type="text"]').prop("value", "");
		formulario.find('select').find('option:eq(0)').prop("selected", "selected");
	};

	var actualizar_chosen = function() {
		//refresh chosen
		setTimeout(function(){
			formulario.find('select.chosen-select').trigger('chosen:updated');
		}, 50);
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

tablaAccionPersonal.init();
