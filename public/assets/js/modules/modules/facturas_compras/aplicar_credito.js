var aplicar_credito = function(){

    var data = {
        opcionesModal: $('#optionsModal, #opcionesModal'),
        mdModal: $('#mdModal'),
        gridObj: $("#tablaFacturasComprasGrid")
    };

    var methods = {
        run: function(row){
            //render
            var id = row.attr("data-id");
            var rowINFO = $.extend({}, data.gridObj.getRowData(id));

            //Init Modal
            data.opcionesModal.modal('hide');
            data.mdModal.find('.modal-title').empty().append('Aplicar cr&eacute;dito a favor: '+ $(rowINFO['No. Factura']).text() +' - '+ $(rowINFO['Proveedor']).text());
            data.mdModal.find('.modal-body').empty().append('<p>Por favor, espere un momento...</p>');
            data.mdModal.find('.modal-footer').empty();
            data.mdModal.modal('show');

            //get info
            methods.ajaxGetCreditoAplicable(id);
        },
        ajaxGetCreditoAplicable: function(factura_compra_uuid){
            var params = $.extend({erptkn:window.tkn},{factura_compra_uuid:factura_compra_uuid});
            $.ajax({
                url: phost() + "facturas_compras/ajax_get_credito_aplicable_factura",
                type: "POST",
                data: params,
                dataType: "json",
                success: function (response) {
                    if (!_.isEmpty(response)) {
                        data.mdModal.find('.modal-body').empty().append(contents.form(response));
                    }
                }
            });
        },
        showSummit:function(){
            data.mdModal.find('#aplicarCreditoDiv').css('display', 'none');
            data.mdModal.find('#aplicarCreditoForm').append(contents.summit());
        },
        updateTotal:function(ele){
            var total = methods.roundWrite(ele.val());
            var credito_favor = methods.roundWrite(data.mdModal.find('.credito_favor').val());
            if(total > credito_favor){
                ele.val(methods.roundRead(credito_favor));
            }else{
                ele.val(methods.roundRead(total));
            }
        },
        summit:function(form){

            var params = form.serializeArray();
    		params.push({name:'erptkn',value:window.tkn});

    		data.mdModal.modal('hide');
    		$.ajax({
    			url: phost() + "facturas_compras/ajax_aplicar_credito",
    			type: "POST",
    			data: params,
    			dataType: "json",
    			success: function (response) {
    				if (!_.isEmpty(response)) {
    					toastr[response.response ? 'success' : 'error'](response.mensaje);
                        data.gridObj.trigger('reloadGrid');
    				}
    			}
    		});
        },
        roundRead: function(value) {
            return accounting.formatNumber(methods.roundWrite(value), 2, ",");
        },

        roundWrite: function(value) {
            return accounting.unformat(value);
        }
    };

    var contents = {
        form: function (response) {
            var html = '<form id="aplicarCreditoForm"><div id="aplicarCreditoDiv" class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">';
        	html += '  <div class="row">';
        	html += '      <div class="input-group">';
        	html += '          <span class="input-group-addon"><i class="fa fa-dollar"></i></span>';
        	html += '          <input type="text" disabled="" value="'+ methods.roundRead(response.credito_favor) +'" class="form-control credito_favor">';
        	html += '      </div>';
        	html += '      <br>';
        	html += '      <span class="btn btn-primary btn-rounded btn-block">Cr&eacute;dito a favor</span>';
        	html += '  </div>';
        	html += '  <br>';
        	html += '  <div class="row">';
        	html += '      <label>Confirme el monto del cr&eacute;dito por aplicar</label>';
        	html += '      <div class="input-group">';
        	html += '          <span class="input-group-addon"><i class="fa fa-dollar"></i></span>';
        	html += '          <input type="text" name="campo[total]" value="" class="form-control total">';
            html += '          <input type="hidden" name="campo[id]" value="'+ response.id +'">';
        	html += '      </div>';
        	html += '  </div>';
            html += '<div class="row">';
            html += '   <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>';
            html += '   <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">';
            html += '       <a href="#" class="btn btn-default btn-block btn-facebook" data-dismiss="modal">';
            html += '       <i class="fa fa-ban"> </i> Cancelar</a>';
            html += '   </div>';
            html += '   <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">';
            html += '       <a href="#" class="btn btn-success btn-block btn-facebook confirmar-credito-favor">';
            html += '       <i class="fa fa-save"> </i> Guardar</a>';
            html += '   </div>';
            html += '</div>';
            html += '</div></form>';
            return html;
        },
        summit: function () {
            var html = '<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">';
            html += '<h3>¿Seguro desea aplicar el cr&eacute;dito a esta factura?</h3>';
        	html += '<div class="row">';
            html += '   <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>';
            html += '   <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">';
            html += '       <a href="#" class="btn btn-default btn-block btn-facebook" data-dismiss="modal">';
            html += '       <i class="fa fa-ban"> </i> Cancelar</a>';
            html += '   </div>';
            html += '   <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">';
            html += '       <button type="summit" class="btn btn-success btn-block btn-facebook guardar-credito-favor">';
            html += '       <i class="fa fa-save"> </i> Guardar</button>';
            html += '   </div>';
            html += '</div>';
            html += '</div>';
            return html;
        }
    };

    return {m:methods};

};
