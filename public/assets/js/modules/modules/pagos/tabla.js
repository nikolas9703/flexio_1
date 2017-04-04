var multiselect = window.location.pathname.match(/pagos/g) ? true : false;

var tablaPagos = (function(){
    var campos = {};
if (typeof uuid_cotizacion === 'undefined'){
    uuid_cotizacion = "";
}
    if(!_.isUndefined(window.campo)){
        campos = window.campo;
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
                aprobarPagoPE: ".aprobarPagoPE", //No planilla
                anularPago: ".anularPago",
                aplicarPago: ".aplicarPago",
                generarAplicadoMultiple:"#generarAplicadoMultiple",
                changeStateBtn: ".change-state-btn",
		            changeStateMultipleBtn: "#change-state-multiple-btn"

        };

var getParametrosFiltroInicial = function(){
console.log("iniciando filtro inicial");
      //Parametros default
      var data = {
        erptkn: tkn,
        proveedor: (typeof uuid_proveedor !== 'undefined') ? _.toString(uuid_proveedor) : '',
        orden_compra_id: (typeof orden_compra_id !== 'undefined') ? _.toString(orden_compra_id) : '',
        factura_compra_id: (typeof factura_compra_id !== 'undefined') ? _.toString(factura_compra_id) : '',
        pedidos_id: (typeof pedidosid !== 'undefined') ? _.toString(pedidosid) : '',
        pedido_id: (typeof window.sp_pedido_id !== 'undefined') ? window.sp_pedido_id : '', //from subpanels ver pedido
        caja_id: (typeof cajaId !== 'undefined') ? _.toString(cajaId) : '',
        campo:(typeof campos !== 'undefined') ? campos : '',
      };

      //Parametros guardados en localStorage
      if (multiselect && typeof(Storage) !== "undefined") {
        if(typeof localStorage.desde != "undefined" && localStorage.desde != "null" && localStorage.desde !=""){ 
          data.desde = localStorage.desde;
        }
        if(typeof localStorage.hasta != "undefined" && localStorage.hasta != "null" && localStorage.hasta !=""){
          data.hasta = localStorage.hasta;
        }
        if(typeof localStorage.proveedor != "undefined" && localStorage.proveedor != "null" && localStorage.proveedor != ""){
          data.proveedor = localStorage.proveedor;
        }
        if(typeof localStorage.estado != "undefined" && localStorage.estado != '' && localStorage.estado != "null"){

          if(localStorage.estado.match(/,/gi)){
            data.estado = [];
            $.each(localStorage.estado.split(","), function(i, estado){
              data.estado[i] = estado;
            });

          }else{
            data.estado = localStorage.estado;
          }
        }
        if(typeof localStorage.montoMin != "undefined" && localStorage.montoMin != "null" && localStorage.montoMin != ""){
          data.montoMin = localStorage.montoMin;
        }
        if(typeof localStorage.montoMax != "undefined" && localStorage.montoMax != "null" && localStorage.montoMax != ""){
          data.montoMax = localStorage.montoMax;
        }
        if(typeof localStorage.formaPago != "undefined" && localStorage.formaPago != "null" && localStorage.formaPago != ""){
          data.formaPago = localStorage.formaPago;
        }
        if(typeof localStorage.tipo != "undefined" && localStorage.tipo != "null" && localStorage.tipo != ""){
          data.tipo = localStorage.tipo;
        }
        if(typeof localStorage.categoria_proveedor != "undefined" && localStorage.categoria_proveedor != '' && localStorage.categoria_proveedor != "null"){

          if(localStorage.categoria_proveedor.match(/,/gi)){
            data.categoria_proveedor = [];
            $.each(localStorage.categoria_proveedor.split(","), function(i, categoria_proveedor){
              data.categoria_proveedor[i] = categoria_proveedor;
            });

          }else{
            data.categoria_proveedor = localStorage.categoria_proveedor;
          }
        }
        if(typeof localStorage.numeroDocumento != "undefined" && localStorage.numeroDocumento != "null" && localStorage.numeroDocumento != ""){
          data.numeroDocumento = localStorage.numeroDocumento;
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

      if(typeof localStorage.desde != "undefined" && localStorage.desde != ''){
        setTimeout(function(){
        $('#fecha1').val(localStorage.desde);
        haybusqueda += 1;
        }, 400);

      }
      if(typeof localStorage.hasta != "undefined" && localStorage.hasta != ''){
        setTimeout(function(){
        $('#fecha2').val(localStorage.hasta);
        haybusqueda += 1;
        }, 400);
      }
      if(typeof localStorage.proveedor != "undefined" && localStorage.proveedor != ''){
        $("#proveedor3").append('<option value="'+ localStorage.proveedor +'" selected="selected">'+ localStorage.proveedor_nombre +'</option>');
        haybusqueda += 1;
      }
      if(typeof localStorage.estado != "undefined" && localStorage.estado != ''){
        //verificar si hay varios estados seleccionados
        if(localStorage.estado.match(/,/gi)){
          $.each(localStorage.estado.split(","), function(i, estado){
            $('#estado').find('option[value="'+ estado +'"]').attr("selected", "selected");
          });

        }else{
          $('#estado').find('option[value="'+ localStorage.estado +'"]').attr("selected", "selected");
        }

        haybusqueda += 1;
      }
      if(typeof localStorage.montoMin != "undefined" && localStorage.montoMin != ''){
        $('#monto_min').val(localStorage.montoMin);
        haybusqueda += 1;
      }
      if(typeof localStorage.montoMax != "undefined" && localStorage.montoMax != ''){
         $('#monto_max').val(localStorage.montoMax);
         haybusqueda += 1;
      }
      if(typeof localStorage.formaPago != "undefined" && localStorage.formaPago != ''){
         $('#forma_pago').find('option[value="'+ localStorage.formaPago +'"]').attr("selected", "selected");
         $('#forma_pago').trigger('chosen:updated');
         haybusqueda += 1;
      }
      if(typeof localStorage.tipo != "undefined" && localStorage.tipo != ''){
         $('#tipo').find('option[value="'+ localStorage.tipo +'"]').attr("selected", "selected");
         $('#tipo').trigger('chosen:updated');
         haybusqueda += 1;
      }
      if(typeof localStorage.categoria_proveedor != "undefined" && localStorage.categoria_proveedor != ''){
          if(localStorage.categoria_proveedor.match(/,/gi)){
          $.each(localStorage.categoria_proveedor.split(","), function(i, categoria_proveedor){
            $('#categoria').find('option[value="'+ categoria_proveedor +'"]').attr("selected", "selected");
          });

        }else{
          $('#categoria').find('option[value="'+ localStorage.categoria_proveedor +'"]').attr("selected", "selected");
        }
      }
      if(typeof localStorage.numeroDocumento != "undefined" && localStorage.numeroDocumento != ''){
         $('#numero_documento').val(localStorage.numeroDocumento);
         haybusqueda += 1;
      }
      //si existe parametros en localStorage
      //mostrar el panel de busqueda abierto.
      if(haybusqueda > 0){
        $('#proveedor3').closest('.ibox-content').removeAttr("style");
      }

      $("#estado, #proveedor3").trigger("chosen:updated");
    };

    var guardarBusquedaLocalStorage = function(dom) {
      localStorage.setItem("desde", $('#fecha1').val());
      localStorage.setItem("hasta", $('#fecha2').val());
      localStorage.setItem("proveedor", $('#proveedor3').val());
      localStorage.setItem("proveedor_nombre", $("#proveedor3").find("option:selected").text());
      localStorage.setItem("estado", $('#estado').val());
      localStorage.setItem("montoMin", $('#monto_min').val());
      localStorage.setItem("montoMax", $('#monto_max').val());
      localStorage.setItem("formaPago", $('#forma_pago').val());
      localStorage.setItem("tipo", $('#tipo').val());
      localStorage.setItem("categoria_proveedor", $('#categoria').val());
      localStorage.setItem("numeroDocumento", $('#numero_documento').val());
    };

    var limpiarBusquedaLocalStorage = function() {
      if (typeof(Storage) == "undefined") {
          return false;
      }
      localStorage.removeItem("desde");
      localStorage.removeItem("hasta");
      localStorage.removeItem("proveedor");
      localStorage.removeItem("proveedor_nombre");
      localStorage.removeItem("estado");
      localStorage.removeItem("montoMin");
      localStorage.removeItem("montoMax");
      localStorage.removeItem("formaPago");
      localStorage.removeItem("tipo");
      localStorage.removeItem("categoria_proveedor");
      localStorage.removeItem("numeroDocumento");
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
        var nombreProveedorPagos = (localStorage['ms-selected'] == "seguros") ? 'Pago a' : 'Proveedor' ; 
        gridObj.jqGrid({
        url: tablaUrl,
                mtype: "POST",
                datatype: "json",
                colNames:['', 'No. Pago', nombreProveedorPagos, 'Fecha de Pago', 'Monto Pagado', 'No. Documento',  'M&eacute;todo de Pago', 'Estado', 'estado_etiqueta','', ''],
                colModel:[
                {name:'uuid', index:'uuid', width:30, hidedlg:true, hidden: true},
                {name:'codigo', index:'codigo', width:55, sortable:true},
                {name:'Proveedor', index:'proveedor_id', width:70, sortable:true, },
                {name:'fecha', index:'fecha_pago', width:50, sortable:true},
                {name:'monto', index:'monto_pagado', width: 60, sortable:true},
                {name:'no_documento', index:'no_documento', width: 50, sortable:true},
                {name:'forma_pago', index:'pag_pagos_metodo_pago.tipo_pago', width: 40, sortable:false},
                //{name:'banco', index:'banco', width: 60, sortable:false},
                {name:'estado', index:'estado', width: 45, sortable:true, align:'center'},
                {name:'estado_etiqueta', index:'estado_etiqueta', width: 45, hidden: true},
                {name:'options', index:'options', width: 40},
                {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidden: true, hidedlg:true},
                ],
                postData: getParametrosFiltroInicial(),
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


                if (multiselect === true)
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
                //limpiar localStorage
                limpiarBusquedaLocalStorage();
                $('#buscarPagosForm').find('input[type="text"]').prop("value", "");
                $('#buscarPagosForm').find('select.chosen-select').prop("value", "");
                $('#buscarPagosForm').find('select').prop("value", "");
                $('#buscarPagosForm').find('#categoria').prop("value", "");
                $(".chosen-select").trigger("chosen:updated");
                $("#categoria").trigger("chosen:updated");
                $("#proveedor3").val(null).trigger("change");
                recargar();
        });
  //en localStorage si existen
  setBusquedaDeLocalStorage();
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

         $('body').on('click', '.state-btn', function(){
             var btn = $(this);
             var id = btn.data('id');
             var aux = gridObj.jqGrid('getGridParam','selarrrow');
             var params = $.extend({erptkn:window.tkn}, {id: !id ? aux : id, estado: btn.data('estado')});
             opcionesModal.modal('hide');
             $.ajax({
                 url: phost() + "pagos/ajax_update_state",
                 type: "POST",
                 data: params,
                 dataType: "json",
                 success: function (response) {
                     if (!_.isEmpty(response)) {
                         toastr[response.response ? 'success' : 'error'](response.mensaje);
                         recargar();
                     }
                 }
             });
         });

         opcionesModal.on("click", botones.anularPago, function(e){
             e.preventDefault();
             e.returnValue=false;
             e.stopPropagation();

            var nombre = $(this).attr('data-nombre');
            var uuid_pago = $(this).attr('data-pagoid');
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
                         url: phost() + 'pagos/ajax-cambiar-estado',
                         data: {
                           campo:{
                               id:uuid_pago,
                               estado:'anulado'
                           },
                           uuid_pago: uuid_pago,
                           erptkn: tkn,
                         },
                         type: "POST",
                         dataType: "json",
                         cache: false,
                 }).done(function(json) {

                       if( $.isEmptyObject(json.session) === false){
                                    window.location = phost() + "login?expired";
                       }

                       if(json.response === true){
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
             var uuid_pago = $(this).attr('data-pagoid');
              //Init boton de opciones
            opcionesModal.find('.modal-title').empty().append('Confirme');
            opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea aplicar este pago?');
            opcionesModal.find('.modal-footer')
              .empty()
              .append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
              .append('<button id="confirmarAplicarPago" data-id="'+ uuid_pago +'" class="btn btn-w-m btn-primary" type="button">Aplicar Pago</button>');
           });
           opcionesModal.on("click", botones.aprobarPagoPE, function(e){

             e.preventDefault();
             e.returnValue=false;
             e.stopPropagation();

              var nombre = $(this).attr('data-nombre');
              var uuid_pago = $(this).attr('data-pagoid');
               //Init boton de opciones
              opcionesModal.find('.modal-title').empty().append('Confirme');
              opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea aprobar este pago?');
              opcionesModal.find('.modal-footer')
               .empty()
               .append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
               .append('<button id="confirmarAprobarPagoPE" data-id="'+ uuid_pago +'" class="btn btn-w-m btn-primary" type="button">Aprobar Pago</button>');
            });

          opcionesModal.on("click", botones.aprobarPago, function(e){

            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

             var nombre = $(this).attr('data-nombre');
             var uuid_pago = $(this).attr('data-pagoid');
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
                 url: phost() + 'pagos/ajax-cambiar-estado',
                 data: {
                     campo:{
                         id:uuid_pago,
                         estado:'aplicado'
                     },
                   erptkn: tkn,
                 },
                 type: "POST",
                 dataType: "json",
                 cache: false,
         }).done(function(json) {

               if( $.isEmptyObject(json.session) === false){
                            window.location = phost() + "login?expired";
               }

               if(json.response === true){
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
      opcionesModal.on("click", "#confirmarAprobarPagoPE", function(e){
             e.preventDefault();
             e.returnValue=false;
             e.stopPropagation();

             var id = $(this).attr('data-id');

             $("div.modal-content").find('#confirmarAprobarPago').attr('disabled', true);

             $.ajax({
                     url: phost() + 'pagos/ajax_aprobar_pagoPE',
                     data: {
                       id: id,
                       estado:'por_aplicar',
                       erptkn: tkn,
                     },
                     type: "POST",
                     dataType: "json",
                     cache: false,
             }).done(function(json) {

                   if( $.isEmptyObject(json.session) === false){
                                window.location = phost() + "login?expired";
                   }

                   if(json.response === true){
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
               url: phost() + 'pagos/ajax-cambiar-estado',
               data: {
                 campo:{
                      id: uuid_pago,
                      estado:'por_aplicar'
                 },

                 erptkn: tkn,
               },
               type: "POST",
               dataType: "json",
               cache: false,
       }).done(function(json) {

             if( $.isEmptyObject(json.session) === false){
                          window.location = phost() + "login?expired";
             }

             if(json.response === true){
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

                       if( $.isEmptyObject(json.session) === false){
                                    window.location = phost() + "login?expired";
                       }

                       if(json.response === true){
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

//change state
	opcionesModal.on("click", botones.changeStateBtn, function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		//aplicar_credito.js
		change_state_pagos().m.run($(this));
	});

	gridObj.on("click", botones.changeStateBtn, function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		//aplicar_credito.js
		opcionesModal.modal('show');
		change_state_pagos().m.run($(this));
	});

	$("#moduloOpciones").on("click", botones.changeStateMultipleBtn, function (e) {
		//aplicar_credito.js
		opcionesModal.modal('show');
		change_state_pagos().m.run($(this));
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

       				        if( $.isEmptyObject(json.session) === false){
       				                     window.location = phost() + "login?expired";
       				        }

       				        if(json.response === true){
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

        var tipoPagos = function () {
          if (localStorage['ms-selected'] == "seguros")
          {
            var tiposDeConsultas = $("#proveedor3").val().split("|");
            var tipoPago = 0;
            switch(tiposDeConsultas[0]){
              case 'Aseguradoras':
                tipoPago = 1;
              break;
              case 'Proveedores':
                tipoPago = 2;
              break;
              case 'Agentes':
                tipoPago = 3;
              break;
            }
            return tipoPago;
          }
          return 0;
        }

        $(botones.buscar).click(function (e) {
                e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
                var desde = $('#fecha1').val();
                var hasta = $('#fecha2').val();
                var proveedor = proveedor3.value;
                var estado = $("#estado").val();
                var montoMin = $("#monto_min").val();
                var montoMax = $("#monto_max").val();
                var formaPago = $("#forma_pago").val();
                var tipo = $("#tipo").val();
                var categoria_proveedor = $("#categoria").val();
                //var banco = $("#banco").val();
                var numeroDocumento = $("#numero_documento").val();
                /*if (localStorage['ms-selected'] == "seguros")
                {
                  var tiposDeConsultas = $("#proveedor3").val().split("|");
                  switch(tiposDeConsultas[0]){
                    case 'Aseguradoras':
                      //tablaUrl = phost()+"aseguradoras/ajax_listar";
                      tablaUrl = phost()+"pagos/ajax-listar-aseguradora";
                    break;
                    case 'Proveedores':
                        tablaUrl = phost()+"pagos/ajax-listar";
                    break;
                    case 'Agentes':
                        tablaUrl = phost()+"pagos/ajax-listar-agentes";
                    break;
                  }
                  proveedor = parseInt(tiposDeConsultas[1]);
                }*/

                if (desde !== "" || hasta !== "" || proveedor !== "" || estado !== "" || montoMin !== "" || montoMax !== "" || formaPago !== "" || tipo !== "" || numeroDocumento !== "" || categoria_proveedor !== "") {
                if (typeof(Storage) !== "undefined") {
                guardarBusquedaLocalStorage();
                }
                gridObj.setGridParam({postData:null});
                  //Reload Grid
                setTimeout(function(){
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
                                  categoria_proveedor: categoria_proveedor,
                                  erptkn: tkn,

                          }
                  }).trigger('reloadGrid');
                }, 1000);
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
                                categoria_proveedor: '',
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
  var rutaEjecuccionAjax = localStorage['ms-selected'] == "seguros" ? "pagos/ajax_agentes_proovedores" : "proveedores/ajax_catalogo_proveedores" ;
  $("#proveedor3").select2({
    width:"100%",
    theme: "bootstrap",
    language: "es",
    maximumInputLength: 10,
    ajax: {
                url: phost() + rutaEjecuccionAjax,
                dataType: 'json',
                cache: true,
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        erptkn: tkn
                    };
                },
                processResults: function (data, params) {

                   var resultados = data.map(function(resp){
                       return [{'id': resp.proveedor_id,'text': resp.nombre}];
                   }).reduce(function(a,b){
                       return a.concat(b);
                   },[]);
                     return {
                          results:resultados
                     };
                },
                escapeMarkup: function (markup) { return markup; },
            }
});
 });
