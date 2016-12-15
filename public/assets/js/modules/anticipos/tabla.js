var tablaAnticipos = (function(){
  var tablaUrl = phost() + 'anticipos/ajax-listar';
	var gridId = "tablaAnticiposGrid";
	var gridObj = $("#tablaAnticiposGrid");
	var opcionesModal = $('#optionsModal');
	var formularioBuscar = '';
	var multiselect = window.location.pathname.match(/anticipos/g) ? true : false;

	var botones = {
		opciones: ".viewOptions",
		buscar: "#searchBtn",
		limpiar: "#clearBtn",
    exportar: "#exportarListaAnticipo",
    rowCambioEstado: ".cambiarEstado",
		cambioGrupal: "#cambiarEstadoAnticipo"
	};

	var tabla = function(){
	 //var proveedorId = "";
	// var ordenId = "";
		var moduloId = "";
		var campo = {};
	 if(typeof proveedor_id != "undefined"){
		 moduloId = proveedor_id;
		  campo={proveedor:proveedor_id};
		}
	if(typeof orden_id != "undefined"){
		 moduloId = orden_id;
			campo={orden_compra:orden_id};
		}
     var localstorage = window.localStorage;
     var moduloPadre = _.isEmpty(localstorage.getItem("ms-selected"))?'compras':localstorage.getItem("ms-selected");
     var anticipable = moduloPadre ==='compras'?'Proveedor':'Cliente';
		 gridObj.jqGrid({
			 url: tablaUrl,
			 mtype: "POST",
			 datatype: "json",
			 colNames:['','No. Anticipo',anticipable,'Fecha de anticipo','Monto total','No. Documento','Método de anticipo','Estado','', ''],
			 colModel:[
			 {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
			 {name:'codigo', index:'codigo', width:30, sortable:true},
			 {name:'proveedor', index:'proveedor', width:70, sortable:true},
			 {name:'fecha_anticipo', index:'fecha_anticipo', width:30,  sortable:false, },
			 {name:'total', index:'total', width: 30,  sortable:false},
			 {name:'documento', index:'documento', width: 50,  sortable:false},
             {name:'metodo_anticipo', index:'metodo_anticipo', width: 50,  sortable:false},
			 {name:'estado', index:'estado', width: 30,  sortable:false},
			 {name:'options', index:'options',width: 40},
			 {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
			 ],
	 	   postData: {
	 	   		erptkn: tkn,
			    campo: campo,
			    modulo_id : moduloId
	 	   	},
				height: "auto",
		 		autowidth: true,
		 		rowList: [10, 20,50,100],
		 		rowNum: 10,
		 		page: 1,
		 		pager: gridId+"Pager",
		 		loadtext: '<p>Cargando...</p>',
		 		hoverrows: false,
		 	  viewrecords: true,
		 	  refresh: true,
		 	  gridview: true,
			  multiselect: multiselect,
		 	  sortname: 'codigo',
		 	  sortorder: "DESC",

			  beforeProcessing: function(data, status, xhr){
					if( $.isEmptyObject(data.session) === false){
						window.location = phost() + "login?expired";
					}
		       },
			loadBeforeSend: function () {//propiedadesGrid_cb
		    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
		        $(this).closest("div.ui-jqgrid-view").find("#"+gridId+"_cb, #jqgh_"+gridId+"_link").css("text-align", "center");
		      },
			loadComplete: function(data, status, xhr){

					//check if isset data
					if(data.total === 0){
						$('#gbox_'+ gridId).hide();
						$('#'+gridId+'NoRecords').empty().append('No se encontraron Anticipos.').css({"color":"#868686","padding":"30px 0 0"}).show();
					}
					else{
						$('#'+ gridId +'NoRecords').hide();
						$('#gbox_'+ gridId).show();
					}
				if(multiselect === true){
					//---------
					// Cargar plugin jquery Sticky Objects
					//----------
					//add class to headers
					gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

					//floating headers
					$('#gridHeader').sticky({
						getWidthFrom: '.ui-jqgrid-view',
						className:'jqgridHeader'
					});

					//Arreglar tamaño de TD de los checkboxes
					//$("#"+gridId + "_cb").css("width","20px");
					//$("#"+gridId + " tbody tr").children().first("td").css("width","20px");

				}

	      },
				onSelectRow: function(id){
	        $(this).find('tr#'+ id).removeClass('ui-state-highlight');
	      }
		  });
		$(window).resizeEnd(function() {
			redimencionar_tabla();
		});
	  };

		 var eventos = function(){
	 		//Bnoton de Opciones
	 		gridObj.on("click", botones.opciones, function(e){
	 			e.preventDefault();
	 			e.returnValue=false;
	 			e.stopPropagation();

	 			var id = $(this).attr("data-id");

	 			var rowINFO = $.extend({}, gridObj.getRowData(id));

	       var options = rowINFO.link;
	 				//Init Modal
	 				opcionesModal.find('.modal-title').empty().append('Opciones: '+ $(rowINFO.codigo).text() +'');
	 				opcionesModal.find('.modal-body').empty().append(options);
	 				opcionesModal.find('.modal-footer').empty();
	 				opcionesModal.modal('show');
	 		});

			gridObj.on("click", botones.rowCambioEstado, function(e){

				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				var self = _.head($(this));
				var id = self.id;
				console.log(id);
				$("#optionsModal").on("click", ".aprobado", aprobado);
			  $("#optionsModal").on("click", ".anulado", anulado);
					var options = '<a href="#" data-id='+id+' class="btn btn-block btn-outline btn-success aprobado">Aprobado</a><a href="#" data-id='+id+' class="btn btn-block btn-outline btn-success anulado">Anulado</a>';
				//Init Modal
				opcionesModal.find('.modal-title').empty().append('Cambiar estado');
				opcionesModal.find('.modal-body').empty().append(options);
				opcionesModal.find('.modal-footer').empty();
				opcionesModal.modal('show');
			});
	 	};
			$(botones.cambioGrupal).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

				//Seleccionados del jQgrid
				var ids = [];
				ids = gridObj.jqGrid('getGridParam','selarrrow');
				console.log(ids);
				if(ids.length > 0){
				//var rowINFO = gridObj.getRowData(ids);
				//console.dir(rowINFO);
				var ids_aprobados = _.filter(ids,function(fila){
					var infoFila = $.extend({}, gridObj.getRowData(fila));
					if($(infoFila.estado).text() == 'Por aprobar'){
						return infoFila.uuid;
					}
				});

				var options = '<a href="#" class="btn btn-block btn-outline btn-success aprobado">Aprobado</a><a href="#" class="btn btn-block btn-outline btn-success anulado">Anulado</a>';
				$("#optionsModal").on("click", ".aprobado", function(){
					var datos = {campo:{estado:'aprobado',ids:ids_aprobados}};
					if(ids_aprobados.length > 0){
					var cambio = moduloAnticipo.ajaxcambiarEstados(datos);
					cambio.done(function(response){
						var anticipos =response;
						_.map(anticipos,function(ant){
							$("#tablaAnticiposGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
							$("#tablaAnticiposGrid").jqGrid('setCell', ant.id, 'total', ant.monto);
						});
						  opcionesModal.modal('hide');
					});
				}
				});
			  $("#optionsModal").on("click", ".anulado", function(){
						if(ids_aprobados.length > 0){
					var datos = {campo:{estado:'anulado',ids:ids_aprobados}};
					var cambio = moduloAnticipo.ajaxcambiarEstados(datos);
					cambio.done(function(response){
						var anticipos =response;
						_.map(anticipos,function(ant){
							$("#tablaAnticiposGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
							$("#tablaAnticiposGrid").jqGrid('setCell', ant.id, 'total', ant.monto);
						});
						  opcionesModal.modal('hide');
					});
				}
				});

				//Init Modal
					if(ids_aprobados.length > 0){
				    opcionesModal.find('.modal-title').empty().append('Cambiar estado');
				    opcionesModal.find('.modal-body').empty().append(options);
				    opcionesModal.find('.modal-footer').empty();
				    opcionesModal.modal('show');
          }
			  }
			});


		function aprobado(e){
				var self = $(this);
				var id = self.data("id");
			  var datos = {campo:{estado:'aprobado',id:id}};
				var cambio = moduloAnticipo.ajaxcambiarEstado(datos);
			    cambio.done(function(response){
			        $("#tablaAnticiposGrid").jqGrid('setCell', id, 'estado', response.estado);
			        $("#tablaAnticiposGrid").jqGrid('setCell', id, 'total', response.monto);
			        opcionesModal.modal('hide');
			    });
		}
		function anulado(e){
				var self = $(this);
				var id = self.data("id");
				var estado = "anulado";
				var datos = {campo:{estado:'anulado',id:id}};
				var cambio = moduloAnticipo.ajaxcambiarEstado(datos);
			    cambio.done(function(response){
			        $("#tablaAnticiposGrid").jqGrid('setCell', id, 'estado', response.estado);
			        $("#tablaAnticiposGrid").jqGrid('setCell', id, 'total', response.monto);
			        opcionesModal.modal('hide');
			    });
		}

		$(botones.limpiar).click(function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			$('#buscarAnticipoForm').find('input[type="text"]').prop("value", "");
			$('#buscarAnticipoForm').find('select.chosen-select').prop("value", "");
			$('#buscarAnticipoForm').find('select').prop("value", "");
			$(".select2").select2();

			recargar();
		});

		$(botones.buscar).click(function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var codigo = $('#codigo').val();
			var proveedor = $('#proveedor').val();
			var fecha_min = $('#fecha_min').val();
			var fecha_max = $('#fecha_max').val();
			var monto_min = $('#monto_min').val();
			var monto_max = $('#monto_max').val();
      var documento = $('#documento1').val();
      var cliente = $('#cliente').val();
      var metodo_anticipo = $('#metodo_anticipo').val();
      var estado = $('#estado').val();
      var anticipable_type = $('#anticipable_type').val();
      if(anticipable_type =='proveedor'){
        $("#tablaAnticiposGrid").jqGrid('setLabel', 'proveedor', 'Proveedor');
      }else if(anticipable_type =='cliente'){
        $("#tablaAnticiposGrid").jqGrid('setLabel', 'proveedor', 'Cliente');
      }else if(anticipable_type ===''){
				 var localstorage = window.localStorage;
				 var moduloPadre = _.isEmpty(localstorage.getItem("ms-selected"))?'compras':localstorage.getItem("ms-selected");
				 var anticipable = moduloPadre ==='compras'?'Proveedor':'Cliente';
				 $("#tablaAnticiposGrid").jqGrid('setLabel', 'proveedor', anticipable);
			}

			if (codigo !== "" || fecha_min !== "" || fecha_max !== "" || monto_min !== "" || monto_max !== "" || documento !== "" || metodo_anticipo !== "" || estado !== "" || proveedor !== "" || anticipable_type !=="" || cliente !== "") {
				//Reload Grid
				gridObj.setGridParam({
					url: tablaUrl,
					datatype: "json",
					postData: {
            campo:{codigo: codigo,proveedor: proveedor,fecha_min: fecha_min,fecha_max: fecha_max,monto_min: monto_min,
													monto_max: monto_max,documento: documento,metodo_anticipo: metodo_anticipo,estado: estado, anticipable_type:anticipable_type,cliente:cliente},
						erptkn: tkn
					}
				}).trigger('reloadGrid');
			}else{
                this.recargar();
            }
		});

        $(botones.exportar).on("click", function(e){
            e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

            if($('#tabla').is(':visible') === true){

                var ids = [];

                ids = gridObj.jqGrid('getGridParam','selarrrow');
                //Verificar si hay seleccionados
				if(ids.length > 0){

					$('#ids').val(ids);
			        $('form#exportar').submit();
			        $('body').trigger('click');
				}else{
                    //exportar todos
                    $('#ids').val([]);
			        $('form#exportar').submit();
			        $('body').trigger('click');
                }
            }

        });

        //Documentos Modal
    $("#optionsModal").on("click", ".subirArchivoBtn", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            //Cerrar modal de opciones
            $("#optionsModal").modal('hide');
            var anticipo_id = $(this).attr("data-id");

            //Inicializar opciones del Modal
            $('#documentosModal').modal({
                    backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                    show: false
            });

            
            var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();

        scope.safeApply(function(){
            scope.campos.anticipo_id = anticipo_id;
        });
            $('#documentosModal').modal('show');
    });

		var recargar = function(){

			//Reload Grid
			gridObj.setGridParam({
				url: tablaUrl,
				datatype: "json",
				postData: {
                    campo:{codigo: '',proveedor: '',fecha_min: '', fecha_max: '', monto_min: '',monto_max: '',documento: '', metodo_anticipo: '', estado: ''},
					erptkn: tkn
				}
			}).trigger('reloadGrid');
	};
	var redimencionar_tabla = function(){
		$(window).resizeEnd(function() {
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
			});
	};
 return{
	 init:function(){
		 tabla();
		 eventos();
		 redimencionar_tabla();
	 }
};

})();

$(function(){
   tablaAnticipos.init();
});
