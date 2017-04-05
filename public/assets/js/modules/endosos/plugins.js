$(function(){
   
    if(vista == "listar"){
   		$('#fecha_desde,#fecha_hasta').daterangepicker({ 
		    format: 'YYYY-MM-DD',
		    showDropdowns: true,
		    defaultDate: '',
		    singleDatePicker: true
		}).val('');
    }else if(vista == "crear"){
   		$('#fecha_afectacion').daterangepicker({ 
		    locale:{ format: 'DD-MM-YYYY'},
		    showDropdowns: true,
		    defaultDate: '',
		    singleDatePicker: true
		}).val('');
    }else if(vista == "editar"){
    	var fecha = '';
    	if(fecha_efectividad != "0000-00-00"){
    		fecha = fecha_efectividad;
    	}

   		$('#fecha_afectacion').daterangepicker({ 
		    locale:{ format: 'DD-MM-YYYY'},
		    showDropdowns: true,
		    defaultDate: '',
			startDate: fecha,
		    singleDatePicker: true
		}).val(fecha);
    }
	
   
});
       

         