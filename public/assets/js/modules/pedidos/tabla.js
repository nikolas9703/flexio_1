$(function(){

    var multiselect = window.location.pathname.match(/pedidos/g) ? true : false;
    
    var formato_moneda = {
        decimalSeparator:",",
        thousandsSeparator: ",",
        decimalPlaces: 2,
        prefix: "$ "
    };
    
    
    var confirmacion = '<a href="#" class="btn btn-block btn-outline btn-success modal_aceptar" data-url="" data-uuid>Aceptar</a>';
    confirmacion += '<a href="#" class="btn btn-block btn-outline btn-success modal_cancelar">Cancelar</a>';
	
    //Init Pedidos Grid
    $("#pedidosGrid").jqGrid({
        url: phost() + 'pedidos/ajax-listar',
        datatype: "json",
        colNames:[
            'Fechas',
            'N&uacute;mero',
            'Referencia',
            'Centro',
            'Estado',
            '',
            ''
        ],
        colModel:[
            {name:'Fecha', index:'ped_pedidos.fecha_creacion', width:70, sortable:true},
            {name:'Numero', index:'ped_pedidos.numero', width:70,  sortable:false},
            {name:'Referencia', index:'ped_pedidos.referencia', width:70,  sortable:false},
            {name:'Centro', index:'cen_centros.nombre', width: 40, sortable:false, align:'left'},
            {name:'Estado', index:'ped_pedidos_cat.etiqueta', width: 50,sortable:false, align:'left'},
            {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidedlg:true},
            {name:'options', index:'options', hidedlg:true, hidden: true},
        ],
        mtype: "POST",
        postData: {
            erptkn: tkn,
            orden_compra_id: (typeof orden_compra_id !== 'undefined') ? _.toString(orden_compra_id) : '',
            factura_compra_id: (typeof factura_compra_id !== 'undefined') ? _.toString(factura_compra_id) : ''
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50,100],
        rowNum: 10,
        page: 1,
        pager: "#pagerPedidos",
        loadtext: '<p>Cargando...',
        pgtext : "Página {0} de {1}",
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        multiselect: multiselect,
        sortname: 'ped_pedidos.numero',
        sortorder: "DESC",
        beforeProcessing: function(data, status, xhr){
            //Check Session
            if( $.isEmptyObject(data.session) == false){
                window.location = phost() + "login?expired";
            }
        },
        loadBeforeSend: function () {//propiedadesGrid_cb
            $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
            $(this).closest("div.ui-jqgrid-view").find("#pedidosGrid_cb, #jqgh_pedidosGrid_link").css("text-align", "center");
        }, 
        beforeRequest: function(data, status, xhr){},
        loadComplete: function(data){
			
            //check if isset data
            if( data['total'] == 0 ){
                $('#gbox_pedidosGrid').hide();
                $('.NoRecordsPedidos').empty().append('No se encontraron pedidos.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
                $('.NoRecordsPedidos').hide();
                $('#gbox_pedidosGrid').show();
            }

            
                if(multiselect == true)
                {
                    $("#pedidosGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                    $('#gridHeader').sticky({
                        getWidthFrom: '.ui-jqgrid-view', 
                        className:'jqgridHeader'
                    });

                    $("#pedidosGrid_cb").css("width","50px");
                    $("#pedidosGrid tbody tr").children().first("td").css("width","50px");
                }
                
                
                
            },
            onSelectRow: function(id){
                $(this).find('tr#'+ id).removeClass('ui-state-highlight');
            },
	});
	$("#pedidosGrid").jqGrid('columnToggle');
	
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
	
        
	//-------------------------
	// Boton de opciones
	//-------------------------
	$("#pedidosGrid").on("click", ".viewOptions", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            
            var nombre = '';
            var id_pedido = $(this).attr("data-pedido");
            var rowINFO = $("#pedidosGrid").getRowData(id_pedido);
	    var options = rowINFO["options"];
            

            nombre = rowINFO["Numero"];
	    //Init boton de opciones
            $('#optionsModal, #opcionesModal').find('.modal-title').empty().append('Opciones: '+ nombre);
            $('#optionsModal, #opcionesModal').find('.modal-body').empty().append(options);
            $('#optionsModal, #opcionesModal').find('.modal-footer').empty();
            $('#optionsModal, #opcionesModal').modal('show');
	});
        
        //Documentos Modal
		$("#optionsModal").on("click", ".subirDocumento", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			$("#optionsModal").modal('hide');
			var pedido_id = $(this).attr("data-id");
			
			//Inicializar opciones del Modal
			$('#documentosModal').modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
                        
                        //$('#pedido_id').val(pedido_id);
                        var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
		    scope.safeApply(function(){
		    	scope.campos.pedido_id = pedido_id;
		    });
			
	           
		    
			$('#documentosModal').modal('show');
		});
        
        //-------------------------
	// Boton de opciones - ANULAR
	//-------------------------
	$("#optionsModal").on("click", ".anular", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            
            var nombre = '';
            var uuid = $(this).attr("data-uuid");
            var rowINFO = $("#pedidosGrid").getRowData(uuid);
	    var options = confirmacion;
            

            nombre = rowINFO["Numero"];
	    //Init boton de opciones
            $('#optionsModal').find('.modal-title').empty().append('¿Seguro desea anular el pedido '+nombre+' ?');
            $('#optionsModal').find('.modal-body').empty().append(options);
            $('#optionsModal').find('.modal-footer').empty();
            $('#optionsModal').modal('show');
            
            //COLOCO LA URL A LA QUE VA A APUNTAR ACEPTAR.
            $(".modal_aceptar").data("url","pedidos/ajax-anular");
            $(".modal_aceptar").data("uuid",uuid);
	});
        
        //-------------------------
	// Boton de opciones - REABRIR
	//-------------------------
	$("#optionsModal").on("click", ".reabrir", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            
            var nombre = '';
            var uuid = $(this).attr("data-uuid");
            var rowINFO = $("#pedidosGrid").getRowData(uuid);
	    var options = confirmacion;
            

            nombre = rowINFO["numero"];
	    //Init boton de opciones
            $('#optionsModal').find('.modal-title').empty().append('¿Seguro desea reabrir el pedido '+nombre+' ?');
            $('#optionsModal').find('.modal-body').empty().append(options);
            $('#optionsModal').find('.modal-footer').empty();
            $('#optionsModal').modal('show');
            
            //COLOCO LA URL A LA QUE VA A APUNTAR ACEPTAR.
            $(".modal_aceptar").data("url","pedidos/ajax-reabrir");
            $(".modal_aceptar").data("uuid",uuid);
	});
        
        //-------------------------
	// Boton de opciones - MODAL CANCELAR CONFIRMACION
	//-------------------------
	$("#optionsModal").on("click", ".modal_cancelar", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            
            $('#optionsModal').modal('hide');
	});
        
        //-------------------------
	// Boton de opciones - MODAL ACEPTAR CONFIRMACION
	//-------------------------
	$("#optionsModal").on("click", ".modal_aceptar", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            
            var url = $(this).data("url");
            var uuid= $(this).data("uuid");
            
            
            $.ajax({
                url: phost() + url,
                type:"POST",
                data:{
                    erptkn:tkn,
                    uuid: uuid
                },
                dataType:"json",
                success: function(data){
                    if(data.success === false)
                    {
                        toastr["error"]("Error Interno. Comunicalo con el administrador del sistema.");
                    }
                    else
                    {
                        toastr["success"]("Su solicitud se ha procesado satisfactoriamente.");
                        $("#pedidosGrid").trigger("reloadGrid");
                    }
                }

            });
            
            $('#optionsModal').modal('hide');
	});
	
	
	
	
	//-------------------------
	// Botones de formulario de Busqueda
	//-------------------------
	$('#searchBtn').bind('click', searchBtnHlr);
 	
	$('#clearBtn').click(function(e){
            e.preventDefault();

            $("#pedidosGrid").setGridParam({
                url: phost() + 'pedidos/ajax-listar',
                datatype: "json",
                postData: {
                    fechas: '',
                    centro: '',
                    estado: '',
                    referencia: '',
                    numero: '',
                    erptkn: tkn
                }
            }).trigger('reloadGrid');

            //Reset Fields
            $('#fechas, #centro, #estado, #referencia, #numero').val('');
            
            //Reset Chosens
            $("#estado, #centro").trigger("chosen:updated");
	});

    
 
});
 

function searchBtnHlr(e) {

	e.preventDefault();
	$('#searchBtn').unbind('click', searchBtnHlr);

    var fecha1 = $('#fecha1').val();
    var fecha2 = $('#fecha2').val();
	var centro = $('#centro').val();
	var estado = $('#estado').val();
	var referencia = $('#referencia').val();
	var numero = $('#numero').val();

	if(fecha1 != "" ||fecha2 != "" || centro != "" || estado != ""  || referencia != "" || numero != "")
	{
            $("#pedidosGrid").setGridParam({
                url: phost() + 'pedidos/ajax-listar',
                datatype: "json",
                postData: {
                    fecha1: fecha1,
                    fecha2: fecha2,
                    centro: centro,
                    estado: estado,
                    referencia: referencia,
                    numero: numero,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
		
            $('#searchBtn').bind('click', searchBtnHlr);
	}else{
            $('#searchBtn').bind('click', searchBtnHlr);
	}
}