var change_state_pagos = function(){

    var data = {
        opcionesModal: $('#optionsModal, #opcionesModal'),
        mdModal: $('#mdModal'),
        gridObj: $("#tablaPagosGrid")
    };

    var methods = {
        run: function(row){
            //render
            var id = row.attr("data-id");
            var uuid = row.attr("data-uuid");
            //console.log(row.attr());
            var rowINFO = $.extend({}, data.gridObj.getRowData(uuid));
            //Init Modal
            if(id){
                data.opcionesModal.find('.modal-title').empty().append('Cambio de estado: '+ $(rowINFO['codigo']).text() +' - '+ $(rowINFO['Proveedor']).text());
            }else{
                data.opcionesModal.find('.modal-title').empty().append('Cambio de estado: Multiple');
            }
            data.opcionesModal.find('.modal-body').empty().append('<p>Por favor, espere un momento...</p>');
            data.opcionesModal.find('.modal-footer').empty();

            //get info
            methods.ajaxGetStatesSegment(id ? id : -1);
        },
        ajaxGetStatesSegment: function(pago_id){
            var aux = data.gridObj.jqGrid('getGridParam','selarrrow');
            var params = $.extend({erptkn:window.tkn}, {id: pago_id == -1 ? aux : pago_id});

            if(!aux.length && pago_id == -1){
                toastr.error('No se ha seleccionado ning&uacute;n elemento');
                data.opcionesModal.modal('hide');
                return;
            }
            $.ajax({
                url: phost() + "pagos/ajax_get_states_segment",
                type: "POST",
                data: params,
                dataType: "json",
                success: function (response) {
                    if (!_.isEmpty(response)) {
                        data.opcionesModal.find('.modal-body').empty().append(response.data);
                    }
                }
            });
        },

        summit:function(btn){

            var params = {estado_id: btn.data('estado-id'), id:btn.data('id')};
            data.opcionesModal.modal('hide');
    		$.ajax({
    			url: phost() + "pagos/ajax_update_state",
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
