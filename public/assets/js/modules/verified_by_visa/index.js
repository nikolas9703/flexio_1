//Obtener el host actual
function phost()
{
	//if(window.location.hostname == "localhost"){
//		host = "/Optinet/";
//	}else if(window.location.hostname == "162.209.57.159"){
//		host = "/desarrollos/Optinet/";
//	}else{
//		host = "/Optinet/";
//	}
        
        host = "/desarrollo/Optinet/";
        
	return host;
}


$(document).ready(function() {
	
	
});





$("#tipo_documento").change(function(){
	
    
        
       // var tipo_identificacion = $('#tipo_identificacion option:selected').val();
                
          var tipo_documento = $('#tipo_documento option:selected').val();
      // alert(tipo_documento);
         
         if (tipo_documento == "OV") {
              // document.getElementById('etiqueta_numero_documento').innerHTML = 'C&eacute;dula/RUC';
                 document.getElementById('etiqueta_numero_documento').innerHTML = 'No. &Oacute;rden de Venta';
         }
      
         if (tipo_documento == "OC") {
                 document.getElementById('etiqueta_numero_documento').innerHTML = 'No. &Oacute;rden de Compra';
         }
     
   
  });

