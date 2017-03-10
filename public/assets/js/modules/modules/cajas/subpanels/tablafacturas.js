function subPanelsFacturasVentas (){
  this.jqgrid = $("#facturasVentasGrid");
  this.url = 'cajas/ajax-listar-facturas-ventas';
  this.gridId = "facturasVentasGrid";
  this.botones = {
		opciones: ".viewOptions"
	};
  this.caja_id = caja_id;
  this.opcionesModal = $('#opcionesModal');
}


subPanelsFacturasVentas.prototype = function(){

  var tablaFacturasVentas = function(){
    var self = this;
    this.jqgrid.jqGrid({
      url: phost() + this.url,
      datatype: "json",
      colNames:[
      '',
      'No. Factura',
      'Cliente',
      'Fecha de emision',
      'Fecha de vencimiento',
      'Estado',
      'Monto',
      'Saldo',
      'Vendedor',
      '',
      ''
    ],
    colModel: [
              {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
              {name: 'codigo', index: 'codigo', width: 30, sortable: true},
              {name: 'cliente', index: 'cliente', width: 70, sortable: true},
              {name: 'fecha_desde', index: 'fecha_desde', width: 50, sortable: false, },
              {name: 'fecha_hasta', index: 'fecha_hasta', width: 70, sortable: false, },
              {name: 'estado', index: 'estado', width: 30, sortable: false},
              {name: 'monto', index: 'monto', width: 30, sortable: false},
              {name: 'saldo', index: 'saldo', width: 30, sortable: false},
              {name: 'vendedor', index: 'vendedor', width: 30, sortable: false},
              {name: 'options', index: 'options', width: 40},
              {name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
          ],
          mtype: "POST",
          postData: {
              erptkn: tkn,
              caja_id: this.caja_id,
          },
          height: "auto",
                autowidth: true,
                rowList: [10, 20, 50, 100],
                rowNum: 10,
                page: 1,
                pager: this.gridId + "Pager",
                loadtext: '<p>Cargando...',
                hoverrows: false,
                viewrecords: true,
                refresh: true,
                gridview: true,
                multiselect: false,
                sortname: 'codigo',
                sortorder: "DESC",
                beforeProcessing: function (data, status, xhr) {
                    if ($.isEmptyObject(data.session) === false) {
                        window.location = phost() + "login?expired";
                    }
                },
                loadBeforeSend: function () {//propiedadesGrid_cb
                    $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                    $(this).closest("div.ui-jqgrid-view").find("#facturasVentasGrid_cb, #jqgh_facturasVentasGrid_link").css("text-align", "center");
                },
                loadComplete: function (data, status, xhr) {
                  console.log(this);
                    if ($("#facturasVentasGrid").getGridParam('records') === 0) {
                        $('#gbox_' + self.gridId).hide();
                        $('#' + self.gridId + 'NoRecords').empty().append('No se encontraron Facturas.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                    } else {
                        $('#gbox_' + self.gridId).show();
                        $('#' + self.gridId + 'NoRecords').empty();
                    }
                },
                onSelectRow: function (id) {
                    $(this).find('tr#' + id).removeClass('ui-state-highlight');
                }

    });
    self.jqgrid.on("click", self.botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");

            var rowINFO = $.extend({}, self.jqgrid.getRowData(id));
            var options = rowINFO.link;
            //Init Modal
            self.opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.codigo).text() + '');
            self.opcionesModal.find('.modal-body').empty().append(options);
            self.opcionesModal.find('.modal-footer').empty();
            self.opcionesModal.modal('show');
        });
  };
  return {
    setJqgrid:tablaFacturasVentas
  };
}();

var cajasFacturas = new subPanelsFacturasVentas();
cajasFacturas.setJqgrid();
