var listarFacturasCompras = (function(){
    var st = {
        cFechas: "#fecha1, #fecha2, #creacion_min, #creacion_max",
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
        //no se debe formatear campo fecha de buscador si existe valor en localStorage
        //author: @josecoder
        //date: 14/02/2017
        var prefix = "";
        if (typeof prex != 'undefined') {
            var prefix = prex;
        }
        dom.cFechas.daterangepicker(config.dateRangePicker)
        if (typeof localStorage[prefix+ '_fecha1'] == "undefined" || _.isEmpty(localStorage[prefix+ '_fecha1'])) {
          $('#fecha1').val("");
        }
        if (typeof localStorage[prefix+ '_fecha2'] == "undefined" || _.isEmpty(localStorage[prefix+ '_fecha2'])) {
          $('#fecha2').val("");
        }
        if (typeof localStorage[prefix+ '_creacion_min'] == "undefined" || _.isEmpty(localStorage[prefix+ '_creacion_min'])) {
          $('#creacion_min').val("");
        }
        if (typeof localStorage[prefix+ '_creacion_max'] == "undefined" || _.isEmpty(localStorage[prefix+ '_creacion_max'])) {
          $('#creacion_max').val("");
        }
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
$(document).ready(function() {
    listarFacturasCompras.init();
    $("#proveedor3").select2({
    width:"100%",
    theme: "bootstrap",
    language: "es",
    maximumInputLength: 10,
    ajax: {
                url: phost() + 'proveedores/ajax_catalogo_proveedores',
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
