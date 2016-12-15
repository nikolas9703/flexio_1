$(function(){
	//jQuery Daterange
	$("#fecha_min").datepicker({
		//defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#fecha_max").datepicker( "option", "minDate", selectedDate );
		}
	});
	$("#fecha_max").datepicker({
		//defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#fecha_min").datepicker( "option", "maxDate", selectedDate );
	    }
	});


});
