$(function(){
    "use strict";
    //Init Bootstrap Calendar Plugin
    $('#fecha1, #fecha2').daterangepicker({
        locale:{
            format: 'DD-MM-YYYY',
        },
        showDropdowns: true,
        defaultDate: '',
        singleDatePicker: true
    }).val('');

    $(".chosen-select").chosen({width: "100%"});
    
    //funcionalidad de exportacion
    var gridObj = $("#tablaPagosGrid");
    
    $("#moduloOpciones").on("click", "#exportarListaPagos", function(){
        //contiene un arreglo con los identificadores de los proveedores (uuid_proveedor)
        var registros_jqgrid = gridObj.jqGrid('getGridParam','selarrrow');

        if(registros_jqgrid.length)
        {
            var url = phost() + "pagos/ajax-exportar";
            var vars = "";
            $.each(registros_jqgrid, function(i, val){
                vars += '<input type="hidden" name="uuid_pagos[]" value="'+ val +'">';
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
    });
    
    //fin de funcionalida de exportacion

});
