//modulo compra
var tablasConfig = (function () {
    var botones = {
        exportar: "#exportarBtn"
    };
    $(botones.exportar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        //Exportar Seleccionados del jQgrid
        var ids = [];
        var name='';
         if ($('#categoriasGrid').is(':visible') == true) {
              console.log('Exportar categoriasGrid');
             ids = $('#categoriasGrid').jqGrid('getGridParam', 'selarrrow');
             name = 'categoria';
         }else if ($('#chequerasGrid').is(':visible') == true){
             console.log('Exportar chequerasGrid');
             ids = $('#chequerasGrid').jqGrid('getGridParam', 'selarrrow');
             name = 'chequera';
         }else {
             ids = $('#tiposGrid').jqGrid('getGridParam', 'selarrrow');
             name = 'tipos';
         }


    //Verificar si hay seleccionados
        if (ids.length > 0) {

            $('#ids').val(ids);
            $('#tabla').val(name);
            $('form#exportarTablasConfig').submit();
            $('body').trigger('click');
        }
    });
    return{
        init: function () {

        }
    };
})();
$(function () {
    tablasConfig.init();
});