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

  	$("#fecha_desde_formulario").val(fecha_desde);
    $("#fecha_desde_formulario1").val(fecha_desde);
  	$("#fecha_hasta_formulario").val(fecha_hasta);
    $("#fecha_hasta_formulario1").val(fecha_hasta);
  }
           

});
       

         