var aplicar_nota_credito = function(){

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
            data.mdModal.find('.modal-title').empty().append('Aplicar nota de cr&eacute;dito a favor: '+ $(rowINFO['No. Factura']).text() +' - '+ $(rowINFO['Proveedor']).text());
            data.mdModal.find('.modal-body').empty().append('<p>Por favor, espere un momento...</p>');
            data.mdModal.find('.modal-footer').empty();
            data.mdModal.modal('show');

            //get info
            methods.ajaxGetNotaCreditoAplicable(id);
        },
        ajaxGetNotaCreditoAplicable: function(factura_compra_uuid){
            var params = $.extend({erptkn:window.tkn},{factura_compra_uuid:factura_compra_uuid});
            $.ajax({
                url: phost() + "facturas_compras/ajax_get_nota_credito_aplicable_factura",
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
            data.mdModal.find('#aplicarNotaCreditoDiv').css('display', 'none');
            data.mdModal.find('#aplicarNotaCreditoForm').append(contents.summit());
        },
        updateTotal:function(ele){
            var total = methods.roundWrite(ele.val());
            var saldo = methods.roundWrite(ele.closest('tr').find('.saldo').val());
            if(total > saldo){
                ele.val(methods.roundRead(saldo));
            }else{
                ele.val(methods.roundRead(total));
            }
            var suma = _.sumBy(ele.closest('table').find('.total'), function(total){
                return methods.roundWrite($(total).val());
            });
            ele.closest('table').find('.suma_totales').val(methods.roundRead(suma));
        },
        summit:function(form){

            var params = form.serializeArray();
    		params.push({name:'erptkn',value:window.tkn});

    		data.mdModal.modal('hide');
    		$.ajax({
    			url: phost() + "facturas_compras/ajax_aplicar_nota_credito",
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
            var html = '<form id="aplicarNotaCreditoForm"><div id="aplicarNotaCreditoDiv" class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">';
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


            html += '<table class="table">';
            html += '<thead>';
            html += '<tr>';
            html += '<th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">No. Documento</th>';
            html += '<th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">Monto</th>';
            html += '<th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">Saldo</th>';
            html += '<th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">Pago</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            var i = 0;
            _.forEach(response.notas_debito, function(nota_debito){
                html += '<tr class="item-listing">';
                html += '<td style="font-weight: bold;vertical-align: middle;">'+ nota_debito.codigo +'</td>';
                html += '<td style="text-align: right;font-weight: bold;vertical-align: middle;">'+ methods.roundRead(nota_debito.total) +'</td>';
                html += '<td style="text-align: right;font-weight: bold;vertical-align: middle;">'+ methods.roundRead(nota_debito.saldo) +'</td>';
                html += '<td>';
                html += '<div class="input-group">';
                html += '<span class="input-group-addon">$</span>';
                html += '<input type="text" class="form-control total" style="text-align: right;" placeholder="0.00" name="campo[pagos]['+ i +'][total]">';
                html += '<input type="hidden" class="saldo" value="'+ methods.roundRead(nota_debito.saldo) +'">';
                html += '<input type="hidden" name="campo[pagos]['+ i +'][aplicable_id]" value="'+ nota_debito.id +'">';
                html += '<input type="hidden" name="campo[pagos]['+ i +'][aplicable_type]" value="'+ nota_debito.aplicable_type +'">';
                html += '</div>';
                html += '</td>';
                html += '</tr>';
                i += 1;
            });
            html += '<tr class="item-listing">';
            html += '<td colspan="3" style="font-weight: bold;vertical-align: middle;"></td>';
            html += '<td>';
            html += '<div class="input-group">';
            html += '<span class="input-group-addon">$</span>';
            html += '<input type="text" class="form-control suma_totales" style="text-align: right;" placeholder="0.00" disabled="">';
            html += '<input type="hidden" name="campo[id]" value="'+ response.factura_id +'">';
            html += '</div>';
            html += '</td>';
            html += '</tr>';
            html += '</tbody>';
            html += '</table>';


            html += '  </div>';
        	html += '  <div class="row">';
        	html += '<div class="row">';
            html += '   <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>';
            html += '   <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">';
            html += '       <a href="#" class="btn btn-default btn-block btn-facebook" data-dismiss="modal">';
            html += '       <i class="fa fa-ban"> </i> Cancelar</a>';
            html += '   </div>';
            html += '   <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">';
            html += '       <a href="#" class="btn btn-success btn-block btn-facebook confirmar-nota-credito-favor">';
            html += '       <i class="fa fa-save"> </i> Guardar</a>';
            html += '   </div>';
            html += '</div>';
            html += '</div></form>';
            return html;
        },
        summit: function () {
            var html = '<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">';
            html += '<h3>¿Seguro desea aplicar la nota de cr&eacute;dito a esta factura?</h3>';
        	html += '<div class="row">';
            html += '   <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>';
            html += '   <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">';
            html += '       <a href="#" class="btn btn-default btn-block btn-facebook" data-dismiss="modal">';
            html += '       <i class="fa fa-ban"> </i> Cancelar</a>';
            html += '   </div>';
            html += '   <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">';
            html += '       <button type="summit" class="btn btn-success btn-block btn-facebook guardar-nota-credito-favor">';
            html += '       <i class="fa fa-save"> </i> Guardar</button>';
            html += '   </div>';
            html += '</div>';
            html += '</div>';
            return html;
        }
    };

    return {m:methods};

};
