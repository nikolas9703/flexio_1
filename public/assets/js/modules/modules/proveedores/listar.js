 $(function() {

    //ELEMENTOS DE TIPO CHOSEN
    $("#tipo, #categoria").chosen({
        width: '100%',
        allow_single_deselect: true
    });

    

    //Expotar a CSV
    $('#moduloOpciones ul').on("click", "#exportarBtn", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();


        if($('#proveedoresGrid').is(':visible') == true){
            //Desde la Tabla
            exportarGrid();

        }else{
            //Desde el Grid
            exportarGrid();
        }
     });

    //esta funcion no se usa
    function exportarjQgrid() {
        //Exportar Seleccionados del jQgrid
        var registros_jqgrid = [];
        registros_jqgrid = $("#proveedoresGrid").jqGrid('getGridParam','selarrrow');

        var obj = new Object();
        obj.count = registros_jqgrid.length;

        if(obj.count) {

            obj.items = new Array();

            for(elem in registros_jqgrid) {
                var registro_jqgrid = $("#proveedoresGrid").getRowData(registros_jqgrid[elem]);

                //Remove objects from associative array
                delete registro_jqgrid['link'];
                delete registro_jqgrid['options'];

                //aplicando decode
                registro_jqgrid["Categoria(s)"] = Utf8.decode(registro_jqgrid["Categoria(s)"]);
                //Push to array
                obj.items.push(registro_jqgrid);
            }


            var json = JSON.stringify(obj);
            var csvUrl = JSONToCSVConvertor(json);
            var filename = 'proveedores_'+ Date.now() +'.csv';
            //return;
            //Ejecutar funcion para descargar archivo
            downloadURL(csvUrl, filename);

            $('body').trigger('click');
        }
    }



    function exportarGrid(){

        //contiene un arreglo con los identificadores de los proveedores (uuid_proveedor)
        var registros_jqgrid = $("#proveedoresGrid").jqGrid('getGridParam','selarrrow');

        if(registros_jqgrid.length)
        {
            var url = phost() + "proveedores/ajax-exportar";
            var vars = "";
            $.each(registros_jqgrid, function(i, val){
                vars += '<input type="hidden" name="uuid_proveedor[]" value="'+ val +'">';
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


});
