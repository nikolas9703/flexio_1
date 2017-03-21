$(document).ready(function(){

	$('#searchBtn').bind('click');


	$(function(){

		if( $("#ramo").length > 0){
			
			$("#ramo").select2({
				theme: "bootstrap",
				width:"100%"
			});
		}

		if(localStorage.getItem('moduloAnterior') == "cobros"){

			$('#cliente').val(localStorage.getItem('filtrarPolizaCliente'));
			$('#aseguradora').val(localStorage.getItem('filtrarPolizaAseguradora')).trigger('chosen:updated');
			$('#ramo').val(localStorage.getItem('filtrarPolizaRamo').split(',')).trigger('change');
			$('#no_poliza').val(localStorage.getItem('filtrarPolizaNumPoliza'));
	    	$('#inicio_vigencia').daterangepicker({
		    	locale: {
		    		format: 'YYYY-MM-DD'
		    	},
		    	showDropdowns: true,
		    	defaultDate: '',
		    	singleDatePicker: true
		    }).val(localStorage.getItem('filtrarPolizaIniVigencia'));
	    	$('#fin_vigencia').daterangepicker({
		    	locale: {
		    		format: 'YYYY-MM-DD'
		    	},
		    	showDropdowns: true,
		    	defaultDate: '',
		    	singleDatePicker: true
		    }).val(localStorage.getItem('filtrarPolizaFinVigencia'));
			$('#categoria').val(localStorage.getItem('filtrarPolizaCategoria')).trigger('chosen:updated');
			$('#declarativa').val(localStorage.getItem('filtrarPolizaDeclarativa')).trigger('chosen:updated');
			$('#estado').val(localStorage.getItem('filtrarPolizaEstado')).trigger('chosen:updated');
		}else{
			$('#inicio_vigencia, #fin_vigencia').daterangepicker({
				locale: {
					format: 'YYYY-MM-DD'
				},
				showDropdowns: true,
				defaultDate: '',
				singleDatePicker: true
			}).val("");
		}

		var cliente =  localStorage.getItem('moduloAnterior') == "cobros" ? localStorage.getItem('filtrarPolizaCliente') : '' ;
		var aseguradora = localStorage.getItem('moduloAnterior') == "cobros" ? localStorage.getItem('filtrarPolizaAseguradora') : '' ;
		var categoria = localStorage.getItem('moduloAnterior') == "cobros" ? localStorage.getItem('filtrarPolizaCategoria') : '' ;
		var ini_vigenci	= localStorage.getItem('moduloAnterior') == "cobros" ? localStorage.getItem('filtrarPolizaIniVigencia') : '' ;
		var fin_vigenci	= localStorage.getItem('moduloAnterior') == "cobros" ? localStorage.getItem('filtrarPolizaFinVigencia') : '' ;
		var usuario = localStorage.getItem('moduloAnterior') == "cobros" ? localStorage.getItem('filtrarPolizaUsuario') : '' ;
		var declarativa	 = localStorage.getItem('moduloAnterior') == "cobros" ? localStorage.getItem('filtrarPolizaDeclarativa') : '' ;
		var estado = localStorage.getItem('moduloAnterior') == "cobros" ? localStorage.getItem('filtrarPolizaEstado') : '' ;
		var ramo = localStorage.getItem('moduloAnterior') == "cobros" ? localStorage.getItem('filtrarPolizaRamo') : '' ;
		var no_poliza = localStorage.getItem('moduloAnterior') == "cobros" ? localStorage.getItem('filtrarPolizaNumPoliza') : '' ;
		localStorage.setItem('moduloAnterior','polizas');
		
		var grid = $("#PolizasGrid");
		grid.jqGrid({
			url: phost() + 'polizas/ajax-listar',
			datatype: "json",
			colNames: ['','No. Póliza','Cliente','Aseguradora','Ramo','Inicio de Vigencia','Fin de Vigencia','Frecuencia Facturación','Ultima Factura','Saldo','Categoria','Estado','Opciones','',''],
			colModel: [
			{name:'id', index:'id', hidedlg:true,key: true, hidden: true },
			{name:'numero', index:'numero',sorttype:"text",sortable:true,width:150},
			{name:'cliente', index:'cliente',sorttype:"text",sortable:true,width:150},
			{name:'aseguradora', index:'aseguradora_id',sorttype:"text",sortable:true,width:150},
			{name:'ramo', index:'ramo',sorttype:"text",sortable:true,width:130},
			{name:'inicio_vigencia', index:'inicio_vigencia',sorttype:"text",sortable:true,width:185},
			{name:'fin_vigencia', index:'fin_vigencia',sorttype:"text",sortable:true,width:160},
			{name:'frecuencia_facturacion', index:'frecuencia_facturacion',sorttype:"text",sortable:true,width:200},
			{name:'ultima_factura', index:'ultima_factura',sorttype:"date",sortable:true,width:150},
			{name:'saldo', index:'saldo',sorttype:"text",sortable:false,width:150},
			//{name:'usuario', index:'usuario',sorttype:"text",sortable:true,width:120},
			{name:'categoria', index:'categoria',sorttype:"text",sortable:true,width:150},
			{name:'estado', index:'estado',sorttype:"text",sortable:true,width:180},
			{name:'opciones', index:'opciones', sortable:false, align:'center',width:180},
			{name:'link', index:'link', hidedlg:true, hidden: true},
			{name:'linkEstado', index:'linkEstado', hidedlg:true, hidden: true}
			],
			mtype: "POST",
			postData: { 
				cliente		: cliente,
        		aseguradora	: aseguradora,
        		categoria	: categoria,
        		ini_vigenci	: ini_vigenci,
        		fin_vigenci	: fin_vigenci,
        		usuario		: usuario,
        		declarativa	: declarativa,
        		estado		: estado,
        		ramo		: ramo,
        		no_poliza	: no_poliza,
				erptkn:tkn
			},
			height: "auto",
			autowidth: true,
			rowList: [10, 20,50, 100],
			rowNum: 10,
			page: 1,
			pager: "#pager_polizas",
			loadtext: '<p>Cargando...',
			hoverrows: false,
			viewrecords: true,
			multiselect: true,
			refresh: true,
			gridview: true,
			beforeRequest: function(data, status, xhr){
				$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
				$(this).closest("div.ui-jqgrid-view").find("#jqgh_PolizasGrid_cb").css("text-align", "center");
			},
			loadComplete: function(data){
				
				//check if isset data
				if(data.total == 0 ){
					$('#gbox_PolizasGrid').hide();
					$('.NoRecordsEmpresa').empty().append('No se encontraron Pólizas.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('.NoRecords').hide();
					$('#gbox_PolizasGrid').show();
				}
				
				//---------
				// Cargar plugin jquery Sticky Objects
				//----------
				//add class to headers
				$("#PolizasGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
				$("#PolizasGrid").find('div.tree-wrap').children().removeClass('ui-icon');
				//floating headers
				$('#gridHeader').sticky({
					getWidthFrom: '.ui-jqgrid-view',
					className:'jqgridHeader'
				});
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			}
		});
		//-------------------------
		// Redimensioanr Grid al cambiar tamaño de la ventanas.
		//-------------------------
		$(window).resizeEnd(function() {
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
		});

		$("#PolizasGrid").on("click", ".viewOptions", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var id = $(this).attr("data-id");
			var rowINFO = $("#PolizasGrid").getRowData(id);
			var options = rowINFO["link"];
			//Init boton de opciones
			$('#opcionesModal').find('.modal-title').empty().html('Opciones: '+ rowINFO["cliente"] +'');
			$('#opcionesModal').find('.modal-body').empty().html(options);
			$('#opcionesModal').find('.modal-footer').empty();
			$('#opcionesModal').modal('show');

			/*
			$('#opcionesModal').on("click","#CambiarEstado",function(e){

				$('#opcionesModal').modal('hide');

				var options = rowINFO["linkEstado"];
				$('#opcionesModalCambioEstado').find('.modal-title').empty().html('Cambiar Estado: '+ rowINFO["cliente"] +'');
				$('#opcionesModalCambioEstado').find('.modal-body').empty().html(options);
				$('#opcionesModalCambioEstado').find('.modal-footer').empty();
				$('#opcionesModalCambioEstado').modal('show');

				$("#opcionesModalCambioEstado").on("click",".cambiaEstadoBtn",function(e){
					var id = $(this).attr("data-id");
					var estado = $(this).attr("data-estado");

					if(estado == "Renovada"){
						$("#opcionesModalCambioEstado").modal('hide');
						renovationForm.renovationModal(id);
						modalRenovation.modal("show");
					}else{
						var estado_anterior = $(this).text();
						var rowINFO = $("#PolizasGrid").getRowData(id);
						var poliza = $("tr[id='"+id+"']").find("[aria-describedby='PolizasGrid_numero']").find("a").text()
						
						var datos = {campo: {estado: estado, ids: id, estado_anterior:estado_anterior, tipo: 'Cambio de Estado', id_poliza:id, poliza:poliza }};
						var cambio = moduloPolizas.cambiarEstadoPolizas(datos);
						console.log(estado);
						cambio.done(function (response) {
							$('#opcionesModalCambioEstado').modal('hide');
							$("#mensaje").hide();
							ids = "";
							$("#PolizasGrid").trigger('reloadGrid');
							toastr.success('Se ha efectuado Cambio de Estado correctamente.');
						});
						cambio.fail(function(response){
							toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
						});
					}
				});
			});*/

		});
		
	});



$('#searchBtn').on("click",function(e) {
	$('.NoRecordsEmpresa').empty()
	var cliente		= $('#cliente').val();
	var aseguradora	= $('#aseguradora').val();
	var categoria	= $('#categoria').val();
	var ini_vigenci	= $('#inicio_vigencia').val();
	var fin_vigenci	= $('#fin_vigencia').val();
	var usuario		= $('#usuario').val();
	var declarativa	= $('#declarativa').val();
	var estado		= $('#estado').val();
	var ramo 		= [];
	ramo 			= $('#ramo').val();
	var no_poliza	= $('#no_poliza').val();
	

	if(cliente != "" || aseguradora != "" || categoria != "" || ini_vigenci != "" || fin_vigenci != "" || usuario != "" || declarativa != "" || estado != "" || ramo!="" || no_poliza!="")
	{
            //Reload Grid
            localStorage.setItem('filtrarPolizaCliente',cliente);
            localStorage.setItem('filtrarPolizaAseguradora',aseguradora);
            localStorage.setItem('filtrarPolizaCategoria',categoria);
            localStorage.setItem('filtrarPolizaIniVigencia',ini_vigenci);
            localStorage.setItem('filtrarPolizaFinVigencia',fin_vigenci);
            localStorage.setItem('filtrarPolizaUsuario',usuario);
            localStorage.setItem('filtrarPolizaDeclarativa',declarativa);
            localStorage.setItem('filtrarPolizaEstado',estado);
            localStorage.setItem('filtrarPolizaRamo', ramo);
            localStorage.setItem('filtrarPolizaNumPoliza',no_poliza);

            $("#PolizasGrid").setGridParam({
            	url: phost() + 'polizas/ajax-listar',
            	datatype: "json",
            	postData: {
            		cliente		: cliente,
            		aseguradora	: aseguradora,
            		categoria	: categoria,
            		ini_vigenci	: ini_vigenci,
            		fin_vigenci	: fin_vigenci,
            		usuario		: usuario,
            		declarativa	: declarativa,
            		estado		: estado,
            		ramo		: ramo,
            		no_poliza	: no_poliza,
            		erptkn		: tkn
            	}
            }).trigger('reloadGrid');
        }else{
        	$("#PolizasGrid").setGridParam({
        		url: phost() + 'polizas/ajax-listar',
        		datatype: "json",
        		postData: {
        			cliente		: "",
        			aseguradora	: "",
        			categoria	: "",
        			ini_vigenci	: "",
        			fin_vigenci	: "",
        			usuario		: "",
        			declarativa	: "",
        			estado		: "",
        			ramo		: "",
        			no_poliza	: "",
        			erptkn		: tkn
        		}
        	}).trigger('reloadGrid');
        }
    });

$("#cliente").keyup(function(e){
	var cli = $(this).val();
	var len = cli.length;
	if(len>0){
		$("#searchBtn").click();
	}else{
		$("#searchBtn").click();
		var grid = $("#PolizasGrid");
		grid.jqGrid();
	}
});





	//Cambio de estado
	$("#PolizasGrid").on("click",".estadoPoliza",function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id = $(this).attr("data-id");
		var estado = $(this).attr("data-estado");
		var rowINFO = $("#PolizasGrid").getRowData(id);
		var options = rowINFO["linkEstado"];
		console.log(estado);
		//Init boton de opciones
		if(estado == "Facturada" || estado == "No Renovada" || estado == "Renovada"  ){ //|| estado == "Expirada"
			/*$('#opcionesModal').find('.modal-title').empty().html('Mensaje');
			$('#opcionesModal').find('.modal-body').empty().html("<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>");
			$('#opcionesModal').find('.modal-footer').empty();
			$('#opcionesModal').modal('show');*/
		}else{
			$('#opcionesModal').find('.modal-title').empty().html('Cambiar Estado: '+ rowINFO["cliente"] +'');
			$('#opcionesModal').find('.modal-body').empty().html(options);
			$('#opcionesModal').find('.modal-footer').empty();
			$('#opcionesModal').modal('show');
		}
		
	});

	$("#opcionesModal").on('click',".renovationModal",function(){
		$(modalRenovation).modal({
		backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
		show: false	
	});
		opcionesModal.modal("hide");
		renovationForm.renovationModal();
		modalRenovation.modal("show");

	});

	$("#opcionesModal").on('click',".renovationModal",function(e){
		e.preventDefault();
		e.returnValue=false;
		var id = $(this).attr("data-id");
		var permiso_comision = $(this).attr("permiso_comision");
		var permiso_agente = $(this).attr("permiso_agente");
		var permiso_participacion = $(this).attr("permiso_participacion");
		$("#comision_poliza").inputmask('Regex', {regex: "^[0-9]{1,20}(\\.\\d{1,2})?$"});
		e.stopPropagation();
		$(modalRenovation).modal({
		backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
		show: false	
	});
		
		opcionesModal.modal("hide");
		renovationForm.renovationModal(id,permiso_comision,permiso_agente,permiso_participacion);
		modalRenovation.modal("show");

	});
	
	$("#opcionesModal").on("click",".cambiaEstadoBtn",function(e){
		var id = $(this).attr("data-id");
		var estado = $(this).attr("data-estado");
		var estado_anterior = $(this).text();
		var rowINFO = $("#PolizasGrid").getRowData(id);
		var poliza = $("tr[id='"+id+"']").find("[aria-describedby='PolizasGrid_numero']").find("a").text()
		
		var datos = {campo: {estado: estado, ids: id, estado_anterior:estado_anterior, tipo: 'Cambio de Estado', id_poliza:id, poliza:poliza }};
		var cambio = moduloPolizas.cambiarEstadoPolizas(datos);
		console.log(estado);
		cambio.done(function (response) {
			$('#opcionesModal').modal('hide');
			$("#mensaje").hide();
			ids = "";
			$("#PolizasGrid").trigger('reloadGrid');
			toastr.success('Se ha efectuado Cambio de Estado correctamente.');
		});
		cambio.fail(function(response){
			toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
		});
	});

	//not Renewal
	$("#opcionesModal").on("click",".notRenowal",function(e){
		var id = $(this).attr("data-id");
		var estado = "No Renovada";
		var estado_anterior = $(this).text();
		var rowINFO = $("#PolizasGrid").getRowData(id);
		var poliza = $("tr[id='"+id+"']").find("[aria-describedby='PolizasGrid_numero']").find("a").text()
		
		var datos = {campo: {estado: estado, ids: id, estado_anterior:estado_anterior, tipo: 'Cambio de Estado', id_poliza:id, poliza:poliza }};
		var cambio = moduloPolizas.cambiarEstadoPolizas(datos);
		console.log(estado);
		cambio.done(function (response) {
			$('#opcionesModal').modal('hide');
			$("#mensaje").hide();
			ids = "";
			$("#PolizasGrid").trigger('reloadGrid');
			toastr.success('Se ha efectuado Cambio de Estado correctamente.');
		});
		cambio.fail(function(response){
			toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
		});
	});
	//Boton subir documentos
	$("#opcionesModal").on('click' ,'.subir_archivos_poliza', function (e) {
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		$('#opcionesModal').modal('hide');

		var id = $(this).attr("data-id");
		console.log(id);

	    //Inicializar opciones del Modal
	    $('#documentosModal').modal({
	            backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
	            show: false
	        });
	    
	    $('#documentosModal').modal('show');
	    $('#id_poliza').val(id);
	});


	$("#opcionesModal").on("click", "#agregarReclamoBtn", function(e){     
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        //Cerrar modal de opciones
        $("#opcionesModal").modal('hide');

        var ramo_id = $(this).attr("data-ramo");
        var poliza_id  =  $(this).attr("data-id");
        //Inicializar opciones del Modal
        /*formularioSolicitudesModal.modal({
        backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
        show: false
        });*/
        $('#ReclamosForm').find('#ramo_id').val(ramo_id);
        $('#ReclamosForm').find('#poliza_id').val(poliza_id);
        $('#ReclamosForm').submit();
    });

    //Boton subir documentos
    $("#subir_documento").on('click', function (e) {
    	e.preventDefault();
    	e.returnValue=false;
    	e.stopPropagation();
    	$('#opcionesModal').modal('hide');

    	var id = $("#idPoliza").val();
    	console.log(id);

	    //Inicializar opciones del Modal
	    /*$('#documentosModal').modal({
	            backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
	            show: false
	        });*/
	    
	    $('#documentosModal').modal('show');
	    $('#id_poliza').val(id);
	});

    var counter = 2;
    $('#del_file_poliza').hide();
    $('#add_file_poliza').click(function(){
    	
    	$('#file_tools_poliza').before('<div class="file_upload_poliza row" id="fpoliza'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
    	$('#del_file_poliza').fadeIn(0);
    	counter++;
    });
    $('#del_file_poliza').click(function(){
    	if(counter == 3){
    		$('#del_file_poliza').hide();
    	}   
    	counter--;
    	$('#fpoliza'+counter).remove();
    });  
    
});

$('#clearBtn').on("click",function(e){
	e.preventDefault();

	localStorage.setItem('filtrarPolizaCliente','');
    localStorage.setItem('filtrarPolizaAseguradora','');
    localStorage.setItem('filtrarPolizaCategoria','');
    localStorage.setItem('filtrarPolizaIniVigencia','');
    localStorage.setItem('filtrarPolizaFinVigencia','');
    localStorage.setItem('filtrarPolizaUsuario','');
    localStorage.setItem('filtrarPolizaDeclarativa','');
    localStorage.setItem('filtrarPolizaEstado','');
    localStorage.setItem('filtrarPolizaRamo', '');
    localStorage.setItem('filtrarPolizaNumPoliza','');
	
	$("#PolizasGrid").setGridParam({
		url: phost() + 'polizas/ajax-listar',
		datatype: "json",
		postData: {
			cliente		: "",
			categoria	: "",
			ini_vigenci	: "",
			fin_vigenci	: "",
			usuario		: "",
			declarativa	: "",
			estado		: "",
			aseguradora	: "",
			erptkn		: tkn
		}
	}).trigger('reloadGrid');
	
	//Msg Grid clear
	$('.NoRecordsEmpresa').empty();
    //Reset Fields
    $("#buscarPolizaForm input[type=text]").val("");
    $("#buscarPolizaForm select").val("");
    $(".select2-selection__choice__remove").each(function(i){
    	$(this).trigger("click");
    });
    $(".select2-selection__choice__remove").trigger("click");
});

$('#opcionesModal').on('click','.AgendarCobro',function(e){

	e.preventDefault();
    e.returnValue=false;
   	e.stopPropagation();

   	var id_poliza = $(this).attr('data-id');
   	var datos = {id_poliza:id_poliza,modo_cobro:'individual'};
   	var cobros_agendados = moduloPolizas.AgendarCobro(datos);
	cobros_agendados.done(function (response) {
		if(response.cobro_agendado == 1){

			$('#opcionesModal').modal('hide');
			toastr.error('La poliza tiene un cobro agendado <p> número: <a href="'+phost()+'cobros_seguros/ver/'+response.datos_cobro.uuid_cobro+'?mod=polizas">'+response.datos_cobro.numero_cobro+'</a> fecha de creación '+response.datos_cobro.fecha_cobro+'</p>');
		}else if(response.cobro_agendado == 2){

			$('#opcionesModal').modal('hide');
			window.open('../cobros_seguros/crear?mod=poliza&idPoliza='+id_poliza, "_self");
		}else if(response.cobro_agendado == 3){

			$('#opcionesModal').modal('hide');
			toastr.error('La poliza no tiene facturas en estado por cobrar o cobrado parcial o el saldo por cobrar es 0.00');	
		}
	});
	/*cobros_agendados.fail(function(response){
		toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
	});*/
	//'.base_url().'cobros_seguros/crear?mod=poliza&idPoliza='.$row->id.'
});




$('#opcionesModal').on('click','#crearEndosoBtn',function(e){

	e.preventDefault();
    e.returnValue=false;
   	e.stopPropagation();
   	var id_poliza = $(this).attr('data-id');
   	$('#opcionesModal').modal('hide');
   	window.open('../endosos/crear/'+id_poliza);

});



