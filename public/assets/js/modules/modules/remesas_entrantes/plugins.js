$(function(){

	if(vista == "crear"){           
	  $('#fecha_desde,#fecha_hasta').daterangepicker({ 
	     format: 'YYYY-MM-DD',
	     showDropdowns: true,
	     defaultDate: '',
	     singleDatePicker: true
	  }).val('');
	}else if(vista == "editar"){
		$('#fecha_desde').daterangepicker({ 
		    format: 'YYYY-MM-DD',
		    showDropdowns: true,
		    defaultDate: '',
		    singleDatePicker: true
	  	}).val(fecha_desde);
		$('#fecha_hasta').daterangepicker({ 
		    format: 'YYYY-MM-DD',
		    showDropdowns: true,
		    defaultDate: '',
		    singleDatePicker: true
	  	}).val(fecha_hasta);
	}

});
       

         