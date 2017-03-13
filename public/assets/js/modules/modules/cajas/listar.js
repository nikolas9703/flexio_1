 $(function() {
 
	//Verificar si existe la funcion, para evitar errores de js
	if (typeof $.fn.inputmask !== 'undefined' && $.isFunction($.fn.inputmask)) {
		if($(':input[data-inputmask]').attr('class') != undefined){
			setTimeout(function(){
				$(':input[data-inputmask]').inputmask();
			}, 500);
		}
	}
	 
	 //Expotar a CSV
    $('#moduloOpciones ul').on("click", "#exportarBtn", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        if($('#tabla').is(':visible') == true){
            //Desde la Tabla
            exportarjQgrid();

        }
     });
    
    function exportarjQgrid() {
        //Exportar Seleccionados del jQgrid
        var registros_jqgrid = [];

        registros_jqgrid = $("#cajasGrid").jqGrid('getGridParam','selarrrow');

        var obj = new Object();
        obj.count = registros_jqgrid.length;

        if(obj.count) {

            obj.items = new Array();

            for(elem in registros_jqgrid) {
                //console.log(proyectos[elem]);
                var registro_jqgrid = $("#cajasGrid").getRowData(registros_jqgrid[elem]);

                //Remove objects from associative array
                delete registro_jqgrid['link'];
                delete registro_jqgrid['options'];

                //Push to array
                obj.items.push(registro_jqgrid);
            }
            
            
            var json = JSON.stringify(obj);
            var csvUrl = JSONToCSVConvertor(json);
            var filename = 'cajas_'+ Date.now() +'.csv';

            //Ejecutar funcion para descargar archivo
            downloadURL(csvUrl, filename);

            $('body').trigger('click');
        } 
    }      
});