var multiselect = window.location.pathname.match(/pagos/g) ? true : false;

var tablaPagos = (function(){
if (typeof uuid_cotizacion === 'undefined'){
    uuid_cotizacion = "";
}
        var tablaUrl = phost() + 'pagos/ajax-listar';
        var gridId = "tablaPagosGrid";
        var gridObj = $("#tablaPagosGrid");
        var opcionesModal = $('#optionsModal, #opcionesModal');
        var formularioBuscar = '';
        var botones = {
               opciones: ".viewOptions",
                buscar: "#searchBtn",
                limpiar: "#clearBtn",
                pagarColaborador: ".pagarColaborador",
                //pagarColaboradorExtraordinario: ".pagarColaboradorExtraordinario",
                anularColaborador: ".anularColaborador",
                aprobarPago: ".aprobarPago", //No planilla
                anularPago: ".anularPago",
                aplicarPago: ".aplicarPago",
                generarAplicadoMultiple:"#generarAplicadoMultiple"

        };
        var tabla = function(){
        var pedidosid = '';
        if (typeof pedidos_id != 'undefined') {
              pedidosid = pedidos_id;
        }
        var cajaId = "";
        if(typeof caja_id != "undefined"){
	           cajaId = $.parseJSON(caja_id);
		    }
        gridObj.jqGrid({
        url: tablaUrl,
                mtype: "POST",
                datatype: "json",
                colNames:['', 'N&uacute;mero de pago', 'Fecha', 'Proveedor', 'No. Documento', 'Forma de Pago',  'Monto', 'Estado', 'estado_etiqueta','', ''],
                colModel:[
                {name:'uuid', index:'uuid', width:30, hidedlg:true, hidden: true},
                {name:'codigo', index:'codigo', width:55, sortable:true},
                {name:'fecha', index:'fecha', width:50, sortable:false},
                {name:'Proveedor', index:'proveedor', width:70, sortable:false, },
                {name:'tipo', index:'tipo', width: 50, sortable:false},
                {name:'forma_pago', index:'forma_pago', width: 40, sortable:false},
                //{name:'banco', index:'banco', width: 60, sortable:false},
                {name:'monto', index:'monto', width: 60, sortable:false},
                {name:'estado', index:'estado', width: 45, sortable:false},
                {name:'estado_etiqueta', index:'estado_etiqueta', width: 45, hidden: true},
                {name:'options', index:'options', width: 40},
                {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidden: true, hidedlg:true},
                ],
                postData: {
                    erptkn: tkn,
                    proveedor: (typeof uuid_proveedor !== 'undefined') ? _.toString(uuid_proveedor) : '',
                    orden_compra_id: (typeof orden_compra_id !== 'undefined') ? _.toString(orden_compra_id) : '',
                    factura_compra_id: (typeof factura_compra_id !== 'undefined') ? _.toString(factura_compra_id) : '',
                    pedidos_id: pedidosid,
                    pedido_id: (typeof window.sp_pedido_id !== 'undefined') ? window.sp_pedido_id : '', //from subpanels ver pedido
                    caja_id: cajaId
                },
                height: "auto",
          			autowidth: true,
          			rowList: [10, 20,50, 100],
          			rowNum: 10,
          			page: 1,
                  pager: gridId + "Pager",
           			loadtext: '<p>Cargando...</p>',
          			hoverrows: false,
          		    viewrecords: true,
          		    refresh: true,
          		    gridview: true,
          		    multiselect: true,
          		    sortname: 'id',
          		    sortorder: "desc",

                beforeProcessing: function(data, status, xhr){
                if ($.isEmptyObject(data.session) === false){
                window.location = phost() + "login?expired";
                }
                },
                loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                        $(this).closest("div.ui-jqgrid-view").find("#tablaClientesGrid_cb, #jqgh_tablaClientesGrid_link").css("text-align", "center");
                },

                        loadComplete: function (data, status, xhr) {

                        if (gridObj.getGridParam('records') === 0) {
                        $('#gbox_' + gridId).hide();
                                $('#' + gridId + 'NoRecords').empty().append('No se encontraron Pagos.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                        } else {
                        $('#gbox_' + gridId).show();
                                $('#' + gridId + 'NoRecords').empty();
                        }


                        if (multiselect == true)
                        {
                        gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                                //floating headers
                                $('#gridHeader').sticky({
                        getWidthFrom: '.ui-jqgrid-view',
                                className: 'jqgridHeader'
                        });
                                $('#jqgh_' + gridId + "_cb").css("text-align", "center");
                        }

                        },
                        onSelectRow: function (id) {
                        $(this).find('tr#' + id).removeClass('ui-state-highlight');
                        }
                });
                };

                var eventos = function () {
                //Bnoton de Opciones
                gridObj.on("click", botones.opciones, function (e) {
                e.preventDefault();
                        e.returnValue = false;
                        e.stopPropagation();
                        var id = $(this).attr("data-id");
                        var rowINFO = $.extend({}, gridObj.getRowData(id));


                        var options = rowINFO.link;
                        //Init Modal
                        opcionesModal.find('.modal-title').empty().append('Opciones: ' + rowINFO.codigo + '');
                        opcionesModal.find('.modal-body').empty().append(options);
                        opcionesModal.find('.modal-footer').empty();
                        opcionesModal.modal('show');
                });
                };
        $(botones.limpiar).click(function (e) {
                  e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
                $('#buscarPagosForm').find('input[type="text"]').prop("value", "");
                $('#buscarPagosForm').find('select.chosen-select').prop("value", "");
                $('#buscarPagosForm').find('select').prop("value", "");
                $(".chosen-select").trigger("chosen:updated");
                recargar();
        });

         opcionesModal.on("click", botones.pagarColaborador, function(e){

       		e.preventDefault();
       		e.returnValue=false;
       		e.stopPropagation();

       		var tipo_formulario = $(this).attr('data-tipo');
       		var nombre = $(this).attr('data-nombre');
          var uuid_pago = $(this).attr('data-id');
       	    //Init boton de opciones
       		opcionesModal.find('.modal-title').empty().append('Confirme');
       		opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea confirmar este pago?');
       		opcionesModal.find('.modal-footer')
       			.empty()
       			.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancear</button>')
       			.append('<button id="confirmarPagoColaborador" data-tipo="'+ tipo_formulario +'" data-id="'+ uuid_pago +'" class="btn btn-w-m btn-primary" type="button">Confirmar</button>');
       	 });

         opcionesModal.on("click", botones.anularPago, function(e){
             e.preventDefault();
             e.returnValue=false;
             e.stopPropagation();

            var nombre = $(this).attr('data-nombre');
            var uuid_pago = $(this).attr('data-id');
               //Init boton de opciones
             opcionesModal.find('.modal-title').empty().append('Confirme');
             opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea anular este pago?');
             opcionesModal.find('.modal-footer')
               .empty()
               .append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
               .append('<button id="confirmarAnularPago" data-id="'+ uuid_pago +'" class="btn btn-w-m btn-primary" type="button">Anular</button>');
          });

          opcionesModal.on("click", "#confirmarAnularPago", function(e){
                 e.preventDefault();
                 e.returnValue=false;
                 e.stopPropagation();

                 var uuid_pago = $(this).attr('data-id');

                 $("div.modal-content").find('#confirmarAnularPago').attr('disabled', true);

                 $.ajax({
                         url: phost() + 'pagos/ajax-anular-pago',
                         data: {
                           uuid_pago: uuid_pago,
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
                                opcionesModal.modal('hide');
                                recargar();
                       }
                        else{
                                toastr.error(json.mensaje);
                                opcionesModal.modal('hide');
                         }
                      });

         });

         opcionesModal.on("click", botones.anularColaborador, function(e){

           e.preventDefault();
           e.returnValue=false;
           e.stopPropagation();

           var nombre = $(this).attr('data-nombre');
           var tipo_formulario = $(this).attr('data-tipo');
	          var uuid_pago = $(this).attr('data-id');
             //Init boton de opciones
           opcionesModal.find('.modal-title').empty().append('Confirme');
           opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea anular este pago?');
           opcionesModal.find('.modal-footer')
             .empty()
             .append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
             .append('<button id="confirmarAnularColaborador" data-id="'+ uuid_pago +'" data-tipo="'+ tipo_formulario +'" class="btn btn-w-m btn-primary" type="button">Confirmar</button>');
          });



          opcionesModal.on("click", botones.aplicarPago, function(e){

            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            var nombre = $(this).attr('data-nombre');
             var uuid_pago = $(this).attr('data-id');
              //Init boton de opciones
            opcionesModal.find('.modal-title').empty().append('Confirme');
            opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea aplicar este pago?');
            opcionesModal.find('.modal-footer')
              .empty()
              .append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
              .append('<button id="confirmarAplicarPago" data-id="'+ uuid_pago +'" class="btn btn-w-m btn-primary" type="button">Aplicar Pago</button>');
           });

          opcionesModal.on("click", botones.aprobarPago, function(e){

            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

             var nombre = $(this).attr('data-nombre');
             var uuid_pago = $(this).attr('data-id');
              //Init boton de opciones
             opcionesModal.find('.modal-title').empty().append('Confirme');
             opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea aprobar este pago?');
             opcionesModal.find('.modal-footer')
              .empty()
              .append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
              .append('<button id="confirmarAprobarPago" data-id="'+ uuid_pago +'" class="btn btn-w-m btn-primary" type="button">Aprobar Pago</button>');
           });

    opcionesModal.on("click", "#confirmarAplicarPago", function(e){
         e.preventDefault();
         e.returnValue=false;
         e.stopPropagation();

         var uuid_pago = $(this).attr('data-id');

         $("div.modal-content").find('#confirmarAplicarPago').attr('disabled', true);

         $.ajax({
                 url: phost() + 'pagos/ajax-aplicar-pago',
                 data: {
                   uuid_pago: uuid_pago,
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
                        opcionesModal.modal('hide');
                        recargar();
               }
                else{
                        toastr.error(json.mensaje);
                        opcionesModal.modal('hide');
                 }
              });

      });

opcionesModal.on("click", "#confirmarAprobarPago", function(e){
       e.preventDefault();
       e.returnValue=false;
       e.stopPropagation();

       var uuid_pago = $(this).attr('data-id');

       $("div.modal-content").find('#confirmarAprobarPago').attr('disabled', true);

       $.ajax({
               url: phost() + 'pagos/ajax-aprobar-pago',
               data: {
                 uuid_pago: uuid_pago,
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
                      opcionesModal.modal('hide');
                      recargar();
             }
              else{
                      toastr.error(json.mensaje);
                      opcionesModal.modal('hide');
               }
            });

});
          opcionesModal.on("click", "#confirmarAnularColaborador", function(e){
                 e.preventDefault();
                 e.returnValue=false;
                 e.stopPropagation();

                 var uuid_pago = $(this).attr('data-id');
                 var tipo_formulario = $(this).attr('data-tipo');

                 $("div.modal-content").find('#confirmarAnularColaborador').attr('disabled', true);

                 $.ajax({
                         url: phost() + 'pagos/ajax-anularpago-colaborador',
                         data: {
                           uuid_pago: uuid_pago,
                           tipo_formulario: tipo_formulario,
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
                                opcionesModal.modal('hide');
                                recargar();
                       }
                        else{
                                toastr.error(json.mensaje);
                                opcionesModal.modal('hide');
                         }
                      });

         });


        /*opcionesModal.on("click", "#confirmarPagoColaboradorExtraordinario", function(e){
       				 	e.preventDefault();
       			 		e.returnValue=false;
       			 		e.stopPropagation();

       	 				var uuid_pago = $(this).attr('data-id');

       					$("div.modal-content").find('#confirmarPagoColaboradorExtraordinario').attr('disabled', true);

       					$.ajax({
       				          url: phost() + 'pagos/ajax-pagar-colaborador-pagoextraordinario',
       				          data: {
       				          	uuid_pago: uuid_pago,
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
       												 opcionesModal.modal('hide');
       												 recargar();
       								}
       								 else{
       				                 toastr.error(json.mensaje);
       												 opcionesModal.modal('hide');
                        }
       							 });

       	});*/
       	opcionesModal.on("click", "#confirmarPagoColaborador", function(e){
       				 	e.preventDefault();
       			 		e.returnValue=false;
       			 		e.stopPropagation();

       	 				var uuid_pago = $(this).attr('data-id');
       	 				var tipo_formulario = $(this).attr('data-tipo');

       					$("div.modal-content").find('#confirmarPagoColaborador').attr('disabled', true);

       					$.ajax({
       				          url: phost() + 'pagos/ajax-pagar-colaborador',
       				          data: {
       				          	uuid_pago: uuid_pago,
                          tipo_formulario: tipo_formulario,
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
       												 opcionesModal.modal('hide');
       												 recargar();
       								}
       								 else{
       				                 toastr.error(json.mensaje);
       												 opcionesModal.modal('hide');
                        }
       							 });

       	});


        $(botones.buscar).click(function (e) {
                e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
                var desde = $('#fecha1').val();
                var hasta = $('#fecha2').val();
                var proveedor = $("#proveedor").val();
                var estado = $("#estado").val();
                var montoMin = $("#monto_min").val();
                var montoMax = $("#monto_max").val();
                var formaPago = $("#forma_pago").val();
                var tipo = $("#tipo").val();
                //var banco = $("#banco").val();
                var numeroDocumento = $("#numero_documento").val();
                if (desde !== "" || hasta !== "" || proveedor !== "" || estado !== "" || montoMin !== "" || montoMax !== "" || formaPago !== "" || tipo !== "" || numeroDocumento !== "") {
                  //Reload Grid
                  gridObj.setGridParam({
                  url: tablaUrl,
                          datatype: "json",
                          postData: {
                          desde: desde,
                                  hasta: hasta,
                                  proveedor: proveedor,
                                  estado: estado,
                                  montoMin: montoMin,
                                  montoMax: montoMax,
                                  formaPago: formaPago,
                                  tipo: tipo,
                                  //banco: banco,
                                  numeroDocumento:numeroDocumento,
                                  erptkn: tkn
                          }
                  }).trigger('reloadGrid');
                  }


        });
                var recargar = function () {

                //Reload Grid
                gridObj.setGridParam({
                url: tablaUrl,
                        datatype: "json",
                        postData: {
                        desde: '',
                                hasta: '',
                                proveedor: '',
                                estado: '',
                                montoMin: '',
                                montoMax: '',
                                formaPago: '',
                                tipo: '',
                                numeroDocumento: '',
                                erptkn: tkn
                        }
                }).trigger('reloadGrid');
                };
                var redimencionar_tabla = function () {
                $(window).resizeEnd(function () {
                $(".ui-jqgrid").each(function () {
                var w = parseInt($(this).parent().width()) - 6;
                        var tmpId = $(this).attr("id");
                        var gId = tmpId.replace("gbox_", "");
                        $("#" + gId).setGridWidth(w);
                });
                });
                };
                return{
                      init: function () {
                        tabla();
                        eventos();
                        redimencionar_tabla();
                      },
                      recargar: function(){
                  			recargar();
                  		},
                };
                })();
                $(function () {
                tablaPagos.init();
                        });
