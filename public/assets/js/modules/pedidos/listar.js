 $(function() {
     
    //ELEMENTOS DE TIPO CHOSEN
    $("#estado, #centro").chosen({
        width: '100%',
        allow_single_deselect: true 
    });
    
    
	
	 
    //Expotar a CSV
    $('#moduloOpciones ul').on("click", "#exportarBtn", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        if($('#tabla').is(':visible') == true){
            //Desde la Tabla
            exportarjQgrid();

        }else{
            //Desde el Grid
            exportarGrid();
        }
     });
    
    function exportarjQgrid() {
        //Exportar Seleccionados del jQgrid
        var registros_jqgrid = [];

        registros_jqgrid = $("#pedidosGrid").jqGrid('getGridParam','selarrrow');

        var obj = new Object();
        obj.count = registros_jqgrid.length;

        if(obj.count) {

            obj.items = new Array();

            for(elem in registros_jqgrid) {
                //console.log(proyectos[elem]);
                var registro_jqgrid = $("#pedidosGrid").getRowData(registros_jqgrid[elem]);

                //Remove objects from associative array
                delete registro_jqgrid['link'];
                delete registro_jqgrid['options'];

                //Push to array
                obj.items.push(registro_jqgrid);
            }
            
            
            var json = JSON.stringify(obj);
            var csvUrl = JSONToCSVConvertor(json);
            var filename = 'pedidos_'+ Date.now() +'.csv';

            //Ejecutar funcion para descargar archivo
            downloadURL(csvUrl, filename);

            $('body').trigger('click');
        } 
    }
    
    function exportarGrid(){
        var registros_grid = [];

        $("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
            registros_grid.push(this.value);
        });
        
        //Verificar si ha seleccionado algun proyecto
        if(registros_grid.length==0){
            return false;
        }
        //Convertir array a srting separado por guion
        var registros_grid_string = registros_grid.join('-');
        var obj;
        
        $.ajax({
            url: phost() + "pedidos/ajax-exportar",
            type:"POST",
            data:{
                erptkn:tkn,
                id_registros: registros_grid_string
            },
            dataType:"json",
            success: function(data){
                if(!data)
                {
                    return;
                }

                var json = JSON.stringify(data);
                var csvUrl = JSONToCSVConvertor(json);
                var wfilename = 'pedidos_'+ Date.now() +'.csv';

                //Ejecutar funcion para descargar archivo
                downloadURL(csvUrl, filename);

                $('body').trigger('click');
            }
            
        });
        
    }

     $(function(){
         "use strict";
         //Init Bootstrap Calendar Plugin
         $('#fecha1, #fecha2').daterangepicker({
             format: 'YYYY-MM-DD',
             showDropdowns: true,
             defaultDate: '',
             singleDatePicker: true
         }).val('');

         $(".chosen-select").chosen({width: "100%"});

     });

 });
 