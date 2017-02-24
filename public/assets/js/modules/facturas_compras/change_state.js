var change_state_factura_compra = function(){

    var data = {
        opcionesModal: $('#optionsModal, #opcionesModal'),
        mdModal: $('#mdModal'),
        gridObj: $("#tablaFacturasComprasGrid")
    };

    var methods = {
        run: function(row){
            //render
            var id = row.attr("data-id");
            var uuid = row.attr("data-uuid");
            var rowINFO = $.extend({}, data.gridObj.getRowData(uuid));

            //Init Modal
            if(id){
                data.opcionesModal.find('.modal-title').empty().append('Cambio de estado: '+ $(rowINFO['No. Factura']).text() +' - '+ $(rowINFO['Proveedor']).text());
            }else{
                data.opcionesModal.find('.modal-title').empty().append('Cambio de estado: Multiple');
            }
            data.opcionesModal.find('.modal-body').empty().append('<p>Por favor, espere un momento...</p>');
            data.opcionesModal.find('.modal-footer').empty();

            //get info
            methods.ajaxGetStatesSegment(id ? id : -1);
        },
        ajaxGetStatesSegment: function(factura_id){
            var aux = data.gridObj.jqGrid('getGridParam','selarrrow');
            var params = $.extend({erptkn:window.tkn},{factura_id: factura_id == -1 ? data.gridObj.jqGrid('getGridParam','selarrrow') :factura_id});
            if(!aux.length && factura_id == -1){
                toastr.error('No se ha seleccionado ning&uacute;n elemento');
                data.opcionesModal.modal('hide');
                return;
            }
            $.ajax({
                url: phost() + "facturas_compras/ajax_get_states_segment",
                type: "POST",
                data: params,
                dataType: "json",
                success: function (response) {
                    if (!_.isEmpty(response)) {
                        data.opcionesModal.find('.modal-body').empty().append(response.html);
                    }
                }
            });
        },

        summit:function(btn){

            var params = {estado_id: btn.data('estado-id'), id:btn.data('id')};
            data.opcionesModal.modal('hide');
    		$.ajax({
    			url: phost() + "facturas_compras/ajax_update_state",
    			type: "POST",
    			data: $.extend(params, {erptkn:window.tkn}),
    			dataType: "json",
    			success: function (response) {
    				if (!_.isEmpty(response)) {
    					toastr[response.response ? 'success' : 'error'](response.mensaje);
                        if(response.aplicar_credito.length){
                            $aux = $('<span data-id="'+ response.aplicar_credito +'"></span>');
                            aplicar_credito().m.run($aux);
                        }
                        data.gridObj.trigger('reloadGrid');
    				}
    			}
    		});
        }
    };

    return {m:methods};

};
