$(function(){
	//jQuery Daterange
	$("#fecha_contratacion_desde").datepicker({
		defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#fecha_contratacion_hasta").datepicker( "option", "minDate", selectedDate );
		}
	});
	$("#fecha_contratacion_hasta").datepicker({
		defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#fecha_contratacion_desde").datepicker( "option", "maxDate", selectedDate );
	    }
	});
});
