var listarFacturasCompras = (function(){
    var st = {
        cFechas: "#fecha1, #fecha2",
        cChosens: ".chosen-select",
        iExportar: "#exportarListaFacturasCompras",
        iGrid: "#tablaFacturasComprasGrid"
    };
    
    var config = {
        chosen:{
            width:"100%"
        },
        dateRangePicker:{
            locale:{
                format: 'DD-MM-YYYY'
            },
            showDropdowns: true,
            defaultDate: '',
            singleDatePicker: true
        }
    };
    
    var dom = {};
    
    var catchDom = function(){
        dom.cFechas = $(st.cFechas);
        dom.cChosens = $(st.cChosens);
        dom.iExportar = $(st.iExportar);
        dom.iGrid = $(st.iGrid);
    };
    
    var suscribeEvents = function(){
        dom.cFechas.daterangepicker(config.dateRangePicker).val("");
        dom.cChosens.chosen(config.chosen);
        $("#moduloOpciones").on("click", st.iExportar, events.eExportar);
    };
    
    var events = {
        eExportar: function(){
            //contiene un arreglo con los identificadores de los proveedores (uuid_proveedor)
            var registros_jqgrid = dom.iGrid.jqGrid('getGridParam','selarrrow');

            if(registros_jqgrid.length)
            {
                var url = phost() + "facturas_compras/ajax-exportar";
                var vars = "";
                $.each(registros_jqgrid, function(i, val){
                    vars += '<input type="hidden" name="uuid_facturas_compra[]" value="'+ val +'">';
                });
                var form = $(
                    '<form action="' + url + '" method="post" style="display:none;">' +
                    vars +
                    '<input type="hidden" name="erptkn" value="' + tkn + '">' +
                    '<input type="submit">' +
                    '</form>'
                );
                $('body').append(form);
                form.submit();
            }
        }
    };
    
    var initialize = function(){
        catchDom();
        suscribeEvents();
    };

    return{
        init:initialize
    };
})();

listarFacturasCompras.init();
