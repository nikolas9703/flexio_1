$(function(){
	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		tablaPlanillaDetallesColaboradores.redimensionar();
	});
});

  var tablaPlanillaDetallesColaboradores = (function(){
	var formulario = '#crearPlanilla';
	var formularioComentario = '#formularioComentario';
	var vacacionModal = '#vacacionModal';
	var planillaRegularModal = $('#planillaRegularModal');
	var url = 'planilla/ajax-listar-planilla-colaboradores';
	var url_seleccionando_gastos = 'planilla/ajax-seleccionar-cuenta-gastos';
	var url_horas = 'planilla/ajax-seleccionar-ingreso-horas';
	var url_horas_eliminar = 'planilla/ajax-eliminar-ingreso-horas';
	var url_horas_editar = 'planilla/ajax-guardar-entrar-horas';
	var url_creandoColumnas = 'planilla/ajax-seleccionar-informacion-columnas';
	var grid_id = "tablaPlanillaDetallesColaboradoresGrid";
	var grid_obj = $("#tablaPlanillaDetallesColaboradoresGrid");
	var subgrid_obj = $("#subgrid_table_id");
	var opcionesModal = $('#opcionesModal');
	var lista_colaboradores = $('#lista_colaboradores');
	var botones = {

		opciones: ".viewOptions",
 		cancelar: "#cancelarBtnPlanilla",
 		cerrarPlanillaModal: "#pagarPlanilla",
 		confirmarPagar: "#confimrarPagar",
		agregarColaborador: "#agregarColaborador",
		agregarComentario: ".agregarBtnComentario",
		guardarComentario: "#GuardarComentario"
 	};

	var tabla = function(){

 		var lastsel_2;
   		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
		   	    'No. Colab.',
		   	    'Nombre',
		   	    'Centro Contable',
				'C&eacute;dula',
				'Tipo salario',
				'Estado',
 				'Total (Hrs.)',
				'',
				'',
			],
		   	colModel:[
		   	    {name:'No. Colab.', index:'numero', width: 50,  sortable:false},
				{name:'Nombre', index:'nombre', width:70},
				{name:'Centro Contable', index:'centro_contable', width: 60 },
				{name:'C&eacute;dula', index:'cedula', width:40},
				{name:'Tipo salario', index:'tipo_salario', width: 60 },
		   		{name:'Estado', index:'estado_id', width: 40,  sortable:false},
 		   		{name:'Total horas', sortable:false,  index:'total_horas', width: 40, hidden: false},//(tipo_planilla_id==79)?false:true},
 				{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
				{name:'options', index:'options', hidedlg:true, hidden: true},
		   	],
			mtype: "POST",
		   	postData: {
		   		planilla_id: planilla_id,
		   	//	cantidad_semanas:cantidad_semanas,
		   		tipo_planilla: tipo_planilla_id,
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
		    subGrid: 	true,    //(permiso_editar==1 && tipo_planilla_id==79)?true:false,
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
		    	$(this).closest("div.ui-jqgrid-view").find(grid_id+"_cb, #jqgh_"+grid_id+"_link").css("text-align", "center");
 		    },
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){

  				if( data['total'] == 0 ){
 					$('#gbox_'+ grid_id).hide();
 					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron colaboradores.').css({"color":"#868686","padding":"30px 0 0"}).show();
 				}
 				else{
 					$('#'+ grid_id +'NoRecords').hide();
 					$('#gbox_'+ grid_id).show();

 					  var rows = jQuery(grid_obj).jqGrid('getRowData');
 					  var grid = $(grid_obj);
 	 				  for (var i = 0; i < rows.length; i++) {
 						  var row = rows[i];
 						  var subGridCells = $("td.sgcollapsed",grid[0]);
 						  if (row['Tipo salario'] == 'Mensual'){
  							 // $(subGridCells[i]).unbind('click').html('');
 						  }
 	 	                }


 				}

 			},
 			 onSelectRow: function(id){
			    var parameter = {erptkn: tkn};
				if(id && id!==lastsel_2){
 					grid_obj.jqGrid('restoreRow',lastsel_2);
					grid_obj.jqGrid('editRow', id, true, false,  false , false, parameter);
					lastsel_2=id;
				}
 				 return false;
		 	 },

			subGridOptions: {
				"plusicon"  : "ui-icon-plus",
				"minusicon" : "ui-icon-triangle-1-s",
				"openicon"  : "ui-icon-arrowreturn-1-e"
			},

            subGridRowColapsed: function(subgrid_id, row_id){
                $("#"+subgrid_id).closest("tr").prev().removeClass("ui-state-highlight").removeClass("borde_azul");
			},
  			subGridRowExpanded: function(subgrid_id, row_id){


                $("#"+subgrid_id).closest("tr").prev().addClass("ui-state-highlight").addClass("borde_azul");

   				var subgrid_table_id, pager_id;
				var lastsel;
				subgrid_table_id = subgrid_id+"_t";
				pager_id = "p_"+subgrid_table_id;
				$("#"+subgrid_id).html("<div class='table-responsive' style='overflow: auto; width: 100%;'><table id='"+subgrid_table_id+"' class='scroll'></table></div><div id='"+pager_id+"' class='scroll'></div>");
				var lastData=null;
				$.ajax(
					    {
					       type: "POST",
					       url: phost() +url_creandoColumnas,
					       data: {
					    	   colaborador_id: row_id,
					    	   planilla_id: planilla_id,
					    	   erptkn: tkn,
			 	 			},
					       dataType: "json",
					       success: function(result)
					       {
                               $("#"+subgrid_id).closest("tr").prev().addClass("ui-state-highlight").addClass("borde_azul");
									 	  var button_estado = true;//(result.estado == 3)?false:true; //Si esta validado que deshabilte todo (Add, edit y del)
 					            colD = result.colData;
					            colN = result.colNombres;
					            colM = result.colModel;
					            console.log("result; :", result);

					            var ColModel1 = [];

					            ColModel1.push({name:"erptkn",index:"erptkn",width:140,editable: true, hidden:true,
 					            	formatter: function () {
					                    return tkn;
					                }
 					            });
					           ColModel1.push({name:"Centro contable",index:"centro_contable",width:100,  editable: true,
					        	   edittype:"select",
											 width: "150px",
					            	editoptions:
						            {
						            	value: selectCentroContable(),
													dataInit: function (elem) {
														  select2Apply(elem);
													}
	  					            },
  					            });
					            ColModel1.push({name:"Recargo",index:"Recargo",width:100,  editable: true, edittype:"select",
					            	editoptions:
						            {
 								            	value: selectRecargos(),
															width: "150px",
															dataInit: function (elem) {
																var select=select2Apply(elem);
                                                                if(lastData !=null && typeof lastData.rows != "undefined"  && lastData.rows.length > 0){
                                                                	select.val(lastData.rows[0].ids.recargo_id).trigger('change');
																}
                                                            }
 	  					            },
  					            });
					            ColModel1.push({name:"Cuenta_Costo",index:"cuenta_costo",width:120, editable: true,edittype:"select",editoptions:
				            		{
				            			value:selectCuentaCosto(),
													width: "150px",
													dataInit: function (elem) {
                                                        var select=select2Apply(elem);
                                                        if(lastData !=null && typeof lastData.rows != "undefined"  && lastData.rows.length > 0){
                                                            select.val(lastData.rows[0].ids.cuenta_costo_id).trigger('change');
                                                        }
													}
				            		},
												editrules:{required:true}
				            	});
					            ColModel1.push({name:"Beneficio",index:"unit",width:140,editable: true,edittype:"select",editoptions:
					            	{
					            		value:selectBeneficios(),
													width: "150px",
													dataInit: function (elem) {
															select2Apply(elem);
													},
					            		 dataEvents: [{
					            		       type: "change",
					            		       fn: function(e) {

					            		    	   var v = parseInt($(e.target).val(), 10);

					            		    	   if(isNaN(v)) {
					            		    		   			 var row = $(e.target).closest("tr.jqgrow");
				            		                   var rowId = row.attr("id");
				            		                   $("select#" + rowId + "_CuentaGasto").val("");
				            		                   $("select#" + rowId + "_CuentaGasto").empty();
				            		                   $("select#" + rowId + "_CuentaGasto").append('<option value="">Seleccione</option>').removeAttr('disabled');
																					 $("select#" + rowId + "_CuentaGasto").select2();
																					 $(".select2-container").width(374);
 					            		    	   }else{

 					            		    		  var pasivos = $("#cuenta_costo_id").html();
 					            		    		  var row = $(e.target).closest("tr.jqgrow");
				            		            var rowId = row.attr("id");
 				            		            $("select#" + rowId + "_CuentaGasto", row[0]).html(pasivos);
																		$("select#" + rowId + "_CuentaGasto  option:eq(1)").attr('selected', 'selected');
																		$("select#" + rowId + "_CuentaGasto  option:eq(0)").remove();
																		$("select#" + rowId + "_CuentaGasto").select2();

 					            		    	   }
 					            		     }
					            		  }]
					            	}
					            });
					            ColModel1.push({name:"CuentaGasto",index:"gasto",width:160,editable: true,edittype:"select",editoptions:
				            	{
				            			value:selectCuentaCosto(),
												 dataInit: function (elem) {
														 select2Apply(elem);
												 },
				            	},
				            });
 					            $.each(colM, function(i,name) {
 					            	 ColModel1.push({name:name+i,index:'WEEK'+i, align:'center', width:60, editable: true,
 					            		formatter: function (cellvalue, options, rowObject) {

 					            			if(isNaN(options.rowId) == false){
 					            				if(cellvalue!=''){
 	 					            				valor = cellvalue.split("->",3);
 	 					            				if(valor[1] == 1){
 	 	 					            				iconComents = '<a href="#"  data-index="'+options.rowId+'" data-fecha="'+valor[2]+'" class="agregarBtnComentario"><i class="fa fa-comment"></i></a>';
 	 	 					            			}else{
 	 	 					            				iconComents = '<a href="#"  data-index="'+options.rowId+'" data-fecha="'+valor[2]+'"  class="agregarBtnComentario"><i class="fa fa-comment-o"></i></a>';
 	 	 					            			}

 	 	 					            	 	    var cellPrefix = '';

 	 	 					            	 	    return valor[0] +"<br />"+iconComents;
 	 					            			}
 	 					            			 return '';
 					            			}
 					            			else{
 					            				 return '';
 					            			}
  						                },
 						                unformat: function (cellvalue, options, rowObject) {

 						            	   return cellvalue;

						                },


 					            	 });
 				   			   	});

  					    jQuery("#"+subgrid_table_id).jqGrid({
				  			url: phost() + url_horas,
									datatype: "json",
									colNames:colN,
 									colModel: ColModel1,
									mtype: "POST",
									postData: {
										colaborador_id: row_id,
										planilla_id: planilla_id,
				 		 		   		erptkn: tkn
								   },
								   rowNum:12,
								   pager: pager_id,
								   sortname: 'id',
								   sortorder: "asc",
								   hoverrows: false,
								   height: "auto",
 				 				   footerrow: false,

   				 			 	 editurl: phost() + url_horas_editar,
	   				   			 loadBeforeSend: function () {//propiedadesGrid_cb
  				 					    $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
				 				    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable").css("background-color", "#a2c0da");
				 				    	$(this).closest("div.ui-jqgrid-view").find(grid_id+"_cb, #jqgh_"+grid_id+"_link").css("text-align", "center");
   				 		 		    },
                            loadComplete: function (data) {
								lastData=data;
                                //check if isset data
                                if (data['total'] == 0) {
                                    $('#gbox_' + pager_id).hide();
                                    $('#' + pager_id + 'NoRecords').empty().append('No se encontraron horas.').css({
                                        "color": "#868686",
                                        "padding": "30px 0 0"
                                    }).show();
                                }
                                else {
                                    $('#' + pager_id + 'NoRecords').hide();
                                    $('#gbox_' + pager_id).show();
                                }
                            },
                        });


 					            jQuery("#"+subgrid_table_id).jqGrid(
  					            		'navGrid',
  					            		"#"+pager_id,
  					            		{
  					            			edit:false,
															add:false,
															del:button_estado,
															search:false,
															refresh:false,
															view:false
  					            		},{


  					            		},{},
  					            		{
  					            			url: phost() + url_horas_eliminar,
  					            			onclickSubmit: function () {
  					            				return {
  										    		erptkn:  tkn
  										    	};
  					            			},
   					            			 afterComplete: function (response, postdata) {
																	 informacionTotalHoras(row_id, planilla_id);
  					            			 },
															 afterShowForm: function ($form) {

																 $form.closest('div.ui-jqdialog').position({
																		 my: "center",
																		  at: "center center",
																		 of: jQuery("#"+subgrid_table_id).closest('div.ui-jqgrid')
																 });

															},
   					            		}
      					        ),

   								jQuery("#"+subgrid_table_id).jqGrid("inlineNav","#"+pager_id,
   										{

   											edit: button_estado,
   											editicon: "ui-icon-pencil",
   											add:  button_estado,
   										 	addicon:"ui-icon-plus",
    										addParams:{
    												position: "last",
    												addRowParams: {
    													 extraparam: {
    														    erptkn: function () {
    											                    return tkn;
    											                },
    											                colaborador_id: function () {
    											                    return row_id;
    											                },
    											                planilla_id: function () {
    											                    return planilla_id;
    											                },
     											        },
     											       aftersavefunc: function() {

																	 		$("#"+subgrid_table_id).trigger("reloadGrid");
																			  informacionTotalHoras(row_id, planilla_id);

      										         },
    												},

      										},
     										editParams: {
     											aftersavefunc: function() { //Comando que se ejecuta cuando se guarda la linea
 																		$("#"+subgrid_table_id).trigger("reloadGrid");
																		informacionTotalHoras(row_id, planilla_id);
      											},

     								    },
   										}
   								);
					       }
					    }
				);
 			},
		});
 	};

 	var formatter = function (cellvalue, options, rowObject) {

 		iconAlert = '<span class="ui-state-error" style="border:0">' +
 	    '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;">' +
 	    '</span></span>';

 	    var cellPrefix = '';
 	    if (rowObject.Category === 'Science') {
 	        cellPrefix = iconAlert;
 	    }
 	    return cellPrefix + '<a href="http://en.wikipedia.org/wiki/' + cellvalue + '">' +
 	           cellvalue + '</a>';
 	}

/*var selectCuentaDeGasto = function(){

		var output = '';
		 $("#cuenta_gasto_id option").each(function(){

			 	output +=$(this).attr('value') + ': ' + $(this).text() +';';
		 });
		 output = output.slice(0,-1)
 		 return  output;
	};*/
	var selectCuentaCosto = function(){

		var output = '';
		 $("#cuenta_costo_id option").each(function(){

			 	output +=$(this).attr('value') + ': ' + $(this).text() +';';
		 });
		 output = output.slice(0,-1)
 		 return  output;
	};
	 var selectRecargos = function(){
		 var output = '';
		 $("#recargos_id option").each(function(){

 			 	output +=$(this).attr('value') + ': ' + $(this).text() +';';
		 });
		 output = output.slice(0,-1)
  		 return  output;
	};

	var selectBeneficios = function(){
		 var output = '';
		 $("#beneficios_id option").each(function(){

			 	output +=$(this).attr('value') + ': ' + $(this).text() +';';
		 });
		 output = output.slice(0,-1)
 		 return  output;
	};
	var selectCentroContable = function(){
 		var output = '';
		$("#centro_contable_id option").each(function(){
			output +=$(this).attr('value') + ': ' + $(this).text() +';';
		});
		output = output.slice(0,-1)
		return output;

	};
	var select2Apply = function (elem) {
          var select = $(elem).select2();
          var centrocontable = $("#centro_contable_id").val();
          if (centrocontable != null && centrocontable.length > 0) {
              select.val(centrocontable[0]).trigger("change");
          }

          setTimeout(function () {
              $(".select2-container").width(374);
          }, 0);
          return select;
    };

    var select2SetSearch =  function select2_search ($el, term) {
          $el.select2('open');

          // Get the search box within the dropdown or the selection
          var $search = $el.data('select2').dropdown.$search || $el.data('select2').selection.$search;

          $search.val(term);
          $search.trigger('keyup');
			window.sele=$el;
          console.log("drop", $el.data('select2').dropdown);
          setTimeout(function(){
              var data = $el.data('data');
              console.log("data",data);
              $search.trigger('select', {
                  data: data
              });
              $search.trigger('close');
		  },250);




      };

	var campos = function(){

		var fecha1 = $(formulario).find('#rango_fecha1');
		var fecha2 = $(formulario).find('#rango_fecha2');

		lista_colaboradores.multiselect({
	        search: {
	            left: '<input type="text" id="buscador_colaborador" name="q" class="form-control" placeholder="Search..." />',
	            right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
	        }
	    });

		fecha1.daterangepicker({
	      	  singleDatePicker: true,
	          showDropdowns: true,
	          opens: "left",
	          startDate: rango1,
	          locale: {
	          	 format: 'DD/MM/YYYY',
	          	 applyLabel: 'Seleccionar',
	             cancelLabel: 'Cancelar',
	          	 daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
	             monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	             firstDay: 1
	          }
	      });
		fecha2.daterangepicker({
	      	  singleDatePicker: true,
	          showDropdowns: true,
	          opens: "left",
	          startDate: rango2,
	          locale: {
	          	 format: 'DD/MM/YYYY',
	          	 applyLabel: 'Seleccionar',
	             cancelLabel: 'Cancelar',
	          	 daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
	             monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	             firstDay: 1
	          }
	      });

		$(formulario).find('#tipo_id').attr( "disabled", true );



 		if(tipo_planilla_id==80) //VACACIONES
 		{
 				$(fecha1).prop("disabled", "disabled");
 				$(fecha2).prop("disabled", "disabled");
 				$(formulario).find('select[name="tipo_id"], select[name="ciclo_id"]').prop("disabled", "disabled");
	    	 $(formulario).find('select[name="tipo_id"], select[name="ciclo_id"]').chosen({width: '100%'}).trigger('chosen:updated');

 		}
		$(formulario).find('select, input, button, textarea').attr("disabled",true);
 	 if(permiso_editar == 1 && estado_planilla == 'abierta'  || permiso_editar == 1 && estado_planilla == 'validada')
		{
				$(formulario).find('select, input, button, textarea').attr("disabled", false);
				if(estado_planilla == 'validada'){
						$(formulario).find('select[name="tipo_id"], select[name="ciclo_id"], #centro_contable_id, #area_negocio_id').prop("disabled", "disabled");
				}
		}
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
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["Nombre"] +'');
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});

		//Eliminar colaborador de la planilla
		opcionesModal.on("click", '#confirmValidar', function(e){
	 		e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var id_colaborador = $(this).attr("data-id");

			var rowINFO = grid_obj.getRowData(id_colaborador);
			var disabled = '';

			if(rowINFO["Tipo salario"] == 'Hora' &&  rowINFO["Total horas"]==0){
				  disabled = "disabled='disabled'";
			}
		    /*disabled="disabled"
			console.log(options);*/
		    //Init boton de opciones
			opcionesModal.find('.modal-title').empty().append('Confirme');
			opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea validar este colaborador?');
			opcionesModal.find('.modal-footer')
				.empty()
				.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
				.append('<button id="validarConfirmacion" data-id="'+ id_colaborador +'" class="btn btn-w-m btn-danger" '+disabled+' type="button" >Confirmar</button>');
		});

		//Opcion: Desactivar Usuario
		opcionesModal.on("click", "#validarConfirmacion", function(e){
 			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var colaborador_id = $(this).attr('data-id');
 			   	//Guardar Formulario
			$.ajax({
				url: phost() + 'planilla/ajax-validar-colaborador-planilla',
				data: {
					planilla_id: planilla_id,
					colaborador_id: colaborador_id,
	                erptkn: tkn
				},
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json) {

				 //Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				//If json object is empty.
				if($.isEmptyObject(json) == true){
					return false;
				}

				//Mostrar Mensaje
				if(json.response == "" || json.response == undefined || json.response == 0){
					toastr.error(json.mensaje);
				}else{

					toastr.success(json.mensaje);
				}

					//Ocultar Modal
				opcionesModal.modal('hide');
				recargar();

			});
		});
		if(tipo_planilla_id == 79){

			if (typeof agregarColaborador !== "undefined") {
				$(agregarColaborador).on("click", function(e){
						$("#pantallaAgregarColaborador").modal('show');
					});
			}


		}


		$("#confimrarAgregarColaborador").on("click", function(e){


			 $("#pantallaAgregarColaborador").find("#lista_colaboradores_to").find("option").prop("selected", true);
			 var colaboradores =  $('#lista_colaboradores_to').val();

	 		 $.ajax({
	  	         url: phost() + 'planilla/ajax-agregar-colaborador-planilla',
	  	         data: {
					erptkn: tkn,
					planilla_id:planilla_id,
					colaboradores: colaboradores
				},
	   	         type: "POST",
	  	         dataType: "json",
	  	         cache: false
	  	     }).done(function(data) {

					 if(data.response == true){

						toastr.success(data.mensaje);

						location.reload();
						//recargar();

					}else{
						toastr.error(data.mensaje);

					}
					$("#pantallaAgregarColaborador").modal('hide');
	   	   });
	 	});


	};
	//KIMI
	$("#tablaPlanillaDetallesColaboradoresGrid").on('click', "a.agregarBtnComentario", function() {
		var fecha = $(this).attr("data-fecha");
		var ingreso_horas_id = $(this).attr("data-index");
		var arr = fecha.split('-');
		var fecha_impresion = arr[2] +'/'+  arr[1] +'/'+  arr[0];

		$("#fecha_imprimir").text(fecha_impresion);
		 $.ajax({
  	         url: phost() + 'planilla/ajax-seleccionar-comentario',
  	         data: {
				erptkn: tkn,
				ingreso_horas_id: ingreso_horas_id,
				fecha: fecha
			},
   	         type: "POST",
  	         dataType: "json",
  	         cache: false
  	     }).done(function(data) {

				 if(data.response == true){
					 $("#ingresohoras_dias_id").val(data.id);
 					 $("#comentario").val(data.comentario);
 				}else{
 					$("#ingresohoras_dias_id").val("0");
 					 $("#comentario").val("");
 				}
    	   });

		 $("#pantallaAgregarComentario").modal('show');
	});

	 $(botones.guardarComentario).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		guardarComentario();
	});

	/*$(botones.guardar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
 		editarPlanilla();
	});*/

	$(botones.cerrarPlanillaModal).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

 		opcionesModal.modal('hide');

 		$.ajax({
              url: phost() + 'planilla/ajax-detalles-pago',
              data: {
            	  	planilla_id: planilla_id,
            	  	//cantidad_semanas:cantidad_semanas,
					erptkn: tkn,
	 			},
              type: "POST",
              dataType: "json",
              cache: false,
          }).done(function(json) {
              //Check Session

               if( $.isEmptyObject(json.session) == false){
                  window.location = phost() + "login?expired";
              }
              if(json.response == true){
            	  planillaRegularModal.find('#total_colaboradores').text(json.calculos.cantidad_colaboradores);
            	  planillaRegularModal.find('#salario_bruto').text(json.calculos.salario_bruto);

            	  planillaRegularModal.find('#salario_neto').text(json.calculos.salario_neto);
            	  planillaRegularModal.find('#salario_neto_porcentaje').text(json.calculos.salario_neto_porcentaje);
            	  planillaRegularModal.find('#salario_neto_progress_bar').width(json.calculos.salario_neto_progress_bar);


            	  planillaRegularModal.find('#bonificaciones').text(json.calculos.bonificaciones);
            	  planillaRegularModal.find('#bonificaciones_porcentaje').text(json.calculos.bonificaciones_porcentaje);
            	  planillaRegularModal.find('#bonificaciones_progress_bar').width(json.calculos.bonificaciones_progress_bar);

            	  planillaRegularModal.find('#descuentos').text(json.calculos.descuentos);
            	  planillaRegularModal.find('#descuentos_porcentaje').text(json.calculos.descuentos_porcentaje);
            	  planillaRegularModal.find('#descuentos_progress_bar').width(json.calculos.descuentos_progress_bar);
               }else{
                  toastr.error(json.mensaje);
               }
           });

		planillaRegularModal.modal('show');

	});


	$(botones.cancelar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		window.location.href = host+'planilla/listar';
	});


	$(botones.confirmarPagar).on("click", function(e){

                $("div#planillaRegularModal").find('#confimrarPagar').attr('disabled', true);
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

               $.ajax({
                 url: phost() + 'planilla/ajax-pagar-planilla',
                 data: {
                	planilla_id: planilla_id,
  					erptkn: tkn,
  	 			},
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

                     $("#pantallaPagar").modal('hide');
                     window.location.href = phost() + "planilla/listar";
                     //recargar();
                     //limpiarFormulario();

                 }else{
                     toastr.error(json.mensaje);

                 }
             });

 	});


	var informacionTotalHoras = function(row_id, planilla_id){
var total_horas = 0;
               $.ajax({
                 url: phost() + 'planilla/ajax-informacion-total-horas',
								 data: {
									 	planilla_id: planilla_id,
									 	colaborador_id: row_id,
									 	erptkn: tkn
								 },
                 type: "POST",
                 dataType: "json",
                 cache: false,
             }).done(function(json) {
                 //Check Session

                  if( $.isEmptyObject(json.session) == false){
                     window.location = phost() + "login?expired";
                 }

                 if(json.response == true){
									   total_horas = json.totalHoras;


                 }else{
									 total_horas = 0;

                 }
								 grid_obj.jqGrid('setCell',row_id,'Total horas',total_horas);
              });
 	};
	//Reload al jQgrid
	/*var editarPlanilla = function(){
		$(formulario).find('input, select').attr( "disabled", false );

  		 if ( $(formulario).valid() != false) {
              $.ajax({
                 url: phost() + 'planilla/ajax-editar-planilla',
                 data: $(formulario).serialize()+'&planilla_id='+planilla_id,
                 type: "POST",
                 dataType: "json",
                 cache: false,
             }).done(function(json) {
                 //Check Session
                   if( $.isEmptyObject(json.session) == false){
                     window.location = phost() + "login?expired";
                 }
                  if(json.response == true){
                      window.location.href = phost() + 'planilla/listar';

                 }else{
                     toastr.error(json.mensaje);

                 }
             });
         }
	};*/

	//Reload al jQgrid
	 var guardarComentario = function(){

  		 if ( $(formularioComentario).valid() != false) {
              $.ajax({
                 url: phost() + 'planilla/ajax-guardar-comentario',
                 data: $(formularioComentario).serialize(),
                 type: "POST",
                 dataType: "json",
                 cache: false,
             }).done(function(json) {

                  if( $.isEmptyObject(json.session) == false){
                     window.location = phost() + "login?expired";
                 }
                  if(json.response == true){
                	  var mensaje = "�&Eacute;xito! Se ha creado correctamente el comentario";
                   	  toastr.success(json.mensaje);
                 }else{
                      toastr.error(json.mensaje);
                  }
                 $("#pantallaAgregarComentario").modal('hide');
                 recargar();
             });
         }
	};

	var recargar = function(){

		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				planilla_id: planilla_id,
				//cantidad_semanas:cantidad_semanas,
				tipo_planilla: tipo_planilla_id,
				erptkn: tkn
			}
		}).trigger('reloadGrid');

	};

	var recargarSubGrid = function(){

		subgrid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
 					erptkn: tkn
			}
		}).trigger('reloadGrid');

	};

 	//Eliminar colaborador de la planilla
	opcionesModal.on("click", '#confirmEliminar', function(e){
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		var id_colaborador = $(this).attr('data-id');

	    //Init boton de opciones
		opcionesModal.find('.modal-title').empty().append('Confirme');
		opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea eliminar este colaborador?');
		opcionesModal.find('.modal-footer')
			.empty()
			.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
			.append('<button id="eliminarConfirmacion" data-id="'+ id_colaborador +'" class="btn btn-w-m btn-danger" type="button">Confirmar</button>');
	 	});

	 	//Opcion: Desactivar Usuario
		opcionesModal.on("click", "#eliminarConfirmacion", function(e){
 			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var id_colaborador = $(this).attr('data-id');
 			   	//Guardar Formulario
			$.ajax({
				url: phost() + 'planilla/ajax-eliminar-colaborador-planilla',
				data: {
					planilla_id: planilla_id,
					colaborador_id: id_colaborador,
	                erptkn: tkn
				},
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json) {

				 //Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				//If json object is empty.
				if($.isEmptyObject(json) == true){
					return false;
				}

				//Mostrar Mensaje
				if(json.response == "" || json.response == undefined || json.response == 0){
					toastr.error(json.mensaje);
				}else{

					toastr.success(json.mensaje);

					//location.reload();
				}

					//Ocultar Modal
				opcionesModal.modal('hide');
				recargar();

			});
		});


	return{
		init: function() {
			tabla();
			campos();
			eventos();
		},

		informacionTotalHoras: function(){
 			informacionTotalHoras();
		},
		recargar: function(){
			//reload jqgrid
			recargar();
		},
 		recargarSubGrid: function(){
 			recargarSubGrid();
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

tablaPlanillaDetallesColaboradores.init();
