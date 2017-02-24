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

    var getParametrosFiltroInicial = function(){
      //Parametros default
      var data = {
        erptkn: tkn,
        orden_compra_id: (typeof orden_compra_id !== 'undefined') ? _.toString(orden_compra_id) : '',
        factura_compra_id: (typeof factura_compra_id !== 'undefined') ? _.toString(factura_compra_id) : '',
        item_id: (typeof sp_item_id !== 'undefined') ? _.toString(sp_item_id) : ''
      };

      //Parametros guardados en localStorage
      if (multiselect && typeof(Storage) !== "undefined") {
        if(typeof localStorage.pd_fecha1 != "undefined" && localStorage.pd_fecha1 != "null" && localStorage.pd_fecha1 !=""){
          data.fecha1 = localStorage.pd_fecha1;
        }
        if(typeof localStorage.pd_fecha2 != "undefined" && localStorage.pd_fecha2 != "null" && localStorage.pd_fecha2 !=""){
          data.fecha2 = localStorage.pd_fecha2;
        }
        if(typeof localStorage.pd_centro != "undefined" && localStorage.pd_centro != "null" && localStorage.pd_centro != ""){
          data.centro = localStorage.pd_centro;
        }
        if(typeof localStorage.pd_estado != "undefined" && localStorage.pd_estado != '' && localStorage.pd_estado != "null"){

          if(localStorage.pd_estado.match(/,/gi)){
            data.estado = [];
            $.each(localStorage.pd_estado.split(","), function(i, estado){
              data.estado[i] = estado;
            });

          }else{
            data.estado = localStorage.pd_estado;
          }
        }
        if(typeof localStorage.pd_referencia != "undefined" && localStorage.pd_referencia != "null" && localStorage.pd_referencia != ""){
          data.referencia = localStorage.pd_referencia;
        }
        if(typeof localStorage.pd_numero != "undefined" && localStorage.pd_numero != "null" && localStorage.pd_numero != ""){
          data.numero = localStorage.pd_numero;
        }
      }

      return data;
    };

    //Mostrar en los campos de busqueda los valores guardados
    //en localStorage
    var setBusquedaDeLocalStorage = function(){
      if (typeof(Storage) == "undefined") {
          return false;
      }
      var haybusqueda = 0;

      if(typeof localStorage.pd_fecha1 != "undefined" && localStorage.pd_fecha1 != ''){
        $('#fecha1').val(localStorage.pd_fecha1);
        haybusqueda += 1;
      }
      if(typeof localStorage.pd_fecha2 != "undefined" && localStorage.pd_fecha2 != ''){
        $('#fecha2').val(localStorage.pd_fecha2);
        haybusqueda += 1;
      }
      if(typeof localStorage.pd_centro != "undefined" && localStorage.pd_centro != ''){
        $('#centro').find('option[value="'+ localStorage.pd_centro +'"]').attr("selected", "selected");
        haybusqueda += 1;
      }
      if(typeof localStorage.pd_estado != "undefined" && localStorage.pd_estado != ''){
        //verificar si hay varios estados seleccionados
        if(localStorage.pd_estado.match(/,/gi)){
          $.each(localStorage.pd_estado.split(","), function(i, estado){
            $('#estado').find('option[value="'+ estado +'"]').attr("selected", "selected");
          });

        }else{
          $('#estado').find('option[value="'+ localStorage.pd_estado +'"]').attr("selected", "selected");
        }

        haybusqueda += 1;
      }
      if(typeof localStorage.pd_referencia != "undefined" && localStorage.pd_referencia != ''){
        $('#referencia').val(localStorage.pd_referencia);
        haybusqueda += 1;
      }
      if(typeof localStorage.pd_numero != "undefined" && localStorage.pd_numero != ''){
         $('#numero').val(localStorage.pd_numero);
         haybusqueda += 1;
      }
      //si existe parametros en localStorage
      //mostrar el panel de busqueda abierto.
      if(haybusqueda > 0){
        $('#centro').closest('.ibox-content').removeAttr("style");
      }

      $("#estado, #centro").trigger("chosen:updated");
    };

    var guardarBusquedaLocalStorage = function(dom) {
      localStorage.setItem("pd_fecha1", $('#fecha1').val());
      localStorage.setItem("pd_fecha2", $('#fecha2').val());
      localStorage.setItem("pd_centro", $('#centro').val());
      localStorage.setItem("pd_estado", $('#estado').val());
      localStorage.setItem("pd_referencia", $('#referencia').val());
      localStorage.setItem("pd_numero", $('#numero').val());
    };

    var limpiarBusquedaLocalStorage = function() {
      if (typeof(Storage) == "undefined") {
          return false;
      }
      localStorage.removeItem("pd_fecha1");
      localStorage.removeItem("pd_fecha2");
      localStorage.removeItem("pd_centro");
      localStorage.removeItem("pd_estado");
      localStorage.removeItem("pd_referencia");
      localStorage.removeItem("pd_numero");
    };

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
            '',
            '',
            ''
        ],
        colModel:[
            {name:'Fecha', index:'ped_pedidos.fecha_creacion', width:70, sortable:true},
            {name:'Numero', index:'ped_pedidos.numero', width:70,  sortable:true},
            {name:'Referencia', index:'ped_pedidos.referencia', width:70,  sortable:true},
            {name:'Centro', index:'uuid_centro', width: 40, sortable:true, align:'left'},
            {name:'Estado', index:'id_estado', width: 50,sortable:true, align:'left'},
            {name:'link', index:'link', width:50, align:"center", sortable:true, resizable:false, hidedlg:true},
            {name:'options', index:'options', hidedlg:true, hidden: true},
            {name:'centro_id', index:'centro_id', hidedlg:true, hidden: true},
            {name:'bodega_id', index:'bodega_id', hidedlg:true, hidden: true},
        ],
        mtype: "POST",
        postData: getParametrosFiltroInicial(),
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
        cache: false,
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
  //Se elimino por que aveces oculta el header de la tabla, un bug
	/*$(window).resizeEnd(function() {
            $(".ui-jqgrid").each(function(){
                var w = parseInt( $(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_","");
                $("#"+gId).setGridWidth(w);
            });
	});*/


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

  $("#optionsModal").on("click", ".validarPedido", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var pedido_id = $(this).attr('data-id');
		//var tipo = $(this).attr('data-tipo');

	    //Init boton de opciones
		$('#optionsModal, #opcionesModal').find('.modal-title').empty().append('Confirme');
		$('#optionsModal, #opcionesModal').find('.modal-body').empty().append('Est&aacute; seguro que desea validar este pedido?');
		$('#optionsModal, #opcionesModal').find('.modal-footer')
			.empty()
			.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
			.append('<button id="validarPedidoAccion" data-id="'+ pedido_id +'"   class="btn btn-w-m btn-primary" type="button">Corfirmar</button>');
	 });


   $('#optionsModal, #opcionesModal').on("click", "#validarPedidoAccion", function(e){
 				 	e.preventDefault();
 			 		e.returnValue=false;
 			 		e.stopPropagation();

 	 				var pedido_id = $(this).attr('data-id');

 					$.ajax({
 				          url: phost() + "pedidos/ajax-validar-pedido",
 				          data: {
 				          	pedido_id: pedido_id,
 				  					erptkn: tkn,
 				  	 			},
 				          type: "POST",
 				          dataType: "json",
 				          cache: false,
 				  }).done(function(json) {

 				        if( $.isEmptyObject(json.session) == false){
 				                     window.location = phost() + "login?expired";
 				        }

 				        if(json.response == true){
 				                 toastr.success(json.mensaje);
 												 $('#optionsModal, #opcionesModal').modal('hide');
 												 recargar();
 								}
 								 else{
 				                 toastr.error(json.mensaje);
 												 $('#optionsModal, #opcionesModal').modal('hide');
                  }
 							 });

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

    //Modal estados
    $("#optionsModal").on("click", ".cambiarEstado", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    var pedido_id = $(this).attr("data-id");
    var html = '';       
    _.each(estados, function(index) { 
        html += '<a href="#" id="por_enviar" data-id="'+ pedido_id +'" data-estado="'+ index.id_cat +'" class="btn btn-block btn-outline btn-default cambiandoEstado estadop-modal'+ index.id_cat +'">'+ index.etiqueta +'</a>';
    });
    $("#optionsModal").find('.modal-title').empty().append('Cambiar estado');
    $("#optionsModal").find('.modal-body').empty().append(html);
    $("#optionsModal").find('.modal-footer').empty();
    $("#optionsModal").modal('show');
     });
     //Cambiar estado desde LABEL
    $("#pedidosGrid").on("click", '.cambiarEstado', function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    var pedido_id = $(this).attr("data-id");
    var html = '';       
    _.each(estados, function(index) {      
        html += '<a href="#" id="por_enviar" data-id="'+ pedido_id +'" data-estado="'+ index.id_cat +'" class="btn btn-block btn-outline btn-default cambiandoEstado estadop-modal'+ index.id_cat +'">'+ index.etiqueta +'</a>';
    });
    $("#optionsModal").find('.modal-title').empty().append('Cambiar estado');
    $("#optionsModal").find('.modal-body').empty().append(html);
    $("#optionsModal").find('.modal-footer').empty();
    $("#optionsModal").modal('show');
     });

      //Cambiar estado desde menu MULTIPLES
    $("#moduloOpciones ul").on("click", '#cambiarEstado', function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    console.log("guajaaMULTI");
    var pedido_id = [];
    pedido_id = $("#pedidosGrid").jqGrid('getGridParam','selarrrow');    
    var html = '';       
    _.each(estados, function(index) {      
        html += '<a href="#" id="por_enviar" data-id="'+ pedido_id +'" data-estado="'+ index.id_cat +'" class="btn btn-block btn-outline btn-default cambiandoEstado estadop-modal'+ index.id_cat +'">'+ index.etiqueta +'</a>';
    });
    $("#optionsModal").find('.modal-title').empty().append('Cambiar estado');
    $("#optionsModal").find('.modal-body').empty().append(html);
    $("#optionsModal").find('.modal-footer').empty();
    $("#optionsModal").modal('show');
     });

    //Cambiar estado desde modal
    $("#optionsModal").on("click", ".cambiandoEstado", function(e){
        var pedido_id = $(this).attr("data-id");
        var estado_id = $(this).attr("data-estado");    
        cambiar_estado(pedido_id, estado_id);
        setTimeout(function () {
            $("#optionsModal").modal('hide');
        }, 500);
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

  var searchBtnHlr = function(e) {

  	e.preventDefault();
  	$('#searchBtn').unbind('click', searchBtnHlr);

      var fecha1 = $('#fecha1').val();
      var fecha2 = $('#fecha2').val();
    	var centro = $('#centro').val();
    	var estado = $('#estado').val();
    	var referencia = $('#referencia').val();
    	var numero = $('#numero').val();
console.log(numero);
    	if(fecha1 != "" ||fecha2 != "" || centro != "" || estado != ""  || referencia != "" || numero != "")
    	{
          if (typeof(Storage) !== "undefined") {
              guardarBusquedaLocalStorage();
          }

          $("#pedidosGrid").setGridParam({postData:null});
          setTimeout(function(){
              $("#pedidosGrid").setGridParam({
                  url: phost() + 'pedidos/ajax-listar',
                  datatype: "json",
                  cache: false,
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
            }, 1000);

              $('#searchBtn').bind('click', searchBtnHlr);
  	}else{
              $('#searchBtn').bind('click', searchBtnHlr);
  	}
  };


	//-------------------------
	// Botones de formulario de Busqueda
	//-------------------------
	$('#searchBtn').bind('click', searchBtnHlr);

  var limpiarTabla = function() {
    $("#pedidosGrid").setGridParam({
        url: phost() + 'pedidos/ajax-listar',
        datatype: "json",
        postData: {
            fecha1: '',
            fecha2: '',
            centro: '',
            estado: '',
            referencia: '',
            numero: '',
            erptkn: tkn
        }
    }).trigger('reloadGrid');
  };

	$('#clearBtn').click(function(e){
            e.preventDefault();

            limpiarTabla();

            //limpiar localStorage
            limpiarBusquedaLocalStorage();

            $("#estado").find('option').removeAttr("selected").find('option:eq(0)').attr("selected", "selected");
            var estados = $("#estado").find('option');

            //Reset Fields
            $('input[type="text"]').val('');
            $("#centro").find('option:eq(0)').prop("selected", "selected");
            $("#estado").empty();
            setTimeout(function(){
              $("#estado").append(estados).find('option:eq(0)').attr("selected", "selected");
              $("#estado").trigger("chosen:updated");;
            }, 500);


            //Reset Chosens
            $("#estado, #centro").trigger("chosen:updated");
	});

  //Al cargar, mostrar resultados guardados
  //en localStorage si existen
  setBusquedaDeLocalStorage();
});

var cambiar_estado = function (pedido_id, estado_id) {  
        $.ajax({
            url: phost() + "pedidos/ajax-cambiar-estado",
            type:"POST",
            data:{
                erptkn:tkn,
                pedido_id: pedido_id,
                estado_id: estado_id
            },
            dataType:"json",
            success: function(data){                    
    				if (!_.isEmpty(data)) {
    					toastr[data.response ? 'success' : 'error'](data.mensaje);  
              recargar();           
    				}
    			
            }

        });
        }

var recargar = function () {

//Reload Grid
$("#pedidosGrid").jqGrid({
        url:  phost() + 'pedidos/ajax-listar',
        datatype: "json",
        postData: {
          erptkn: tkn,
          orden_compra_id: '',
          factura_compra_id: '',
          item_id: ''
        }
}).trigger('reloadGrid');
};
