 $(function() {
	
	 $('#optionsModal').on("click", "#subirArchivosBtn", function(e){
			
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var uuid_cliente = $(this).attr("data-cliente");
			
			$('#optionsModal').modal('hide');
			$('#crearDocumentoModal').modal('show');
				 
			moduloSubirDocumentos.subirArchivo('clientes', uuid_cliente);	 
		});
    //Expotar Cliente a CSV
    $('#moduloOpciones ul').on("click", "#exportarClientesBtn", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

           // var clientes = [];

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
		var clientes = [];
		
		clientes = $("#clientesGrid").jqGrid('getGridParam','selarrrow');
		
		var obj = new Object();
		obj.count = clientes.length;
	
		if(obj.count) {
			
			obj.items = new Array();
			
			for(elem in clientes) {
				//console.log(proyectos[elem]);
				var cliente = $("#clientesGrid").getRowData(clientes[elem]);
				
				//Remove objects from associative array
				  delete cliente['linkcliente'];
                  delete cliente['options'];
                  delete cliente['link'];
				
				//Push to array
				obj.items.push(cliente);
			}
			
			var json = JSON.stringify(obj);
			var csvUrl = JSONToCSVConvertor(json);
			var filename = 'clientes_'+ Date.now() +'.csv';
			
			//Ejecutar funcion para descargar archivo
			downloadURL(csvUrl, filename);
			
			$('body').trigger('click');
		} 
	}
    
    function exportarGrid(){
 		 var clientes = [];
		 
		 $("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
			 clientes.push(this.value);
			});
 			//Verificar si ha seleccionado algun proyecto
			if(clientes.length==0){
				return false;
			}
 			//Convertir array a srting separado por guion
			var clientes_string = clientes.join('-');
  			//Armar url
			var url = phost() + 'clientes/ajax-exportar/'+ clientes_string;
 			downloadURL(url);
	}
    
     function preparar_titulo(str) {
        //quitamos los piso
        str = str.replace(/\_/g, ' ');

        //colocamos la primera letra en mayuscula
        str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
            return letter.toUpperCase();
        });

        return str;    
    }
        
        
});
 