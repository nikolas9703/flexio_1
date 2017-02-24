$(document).ready(function(){
    $("#centro_contable").select2({
        width:'100%'
    });
    $("#fecha_min").datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#fecha_max").datepicker( "option", "minDate", selectedDate );
		}
	});
	$("#fecha_max").datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#fecha_min").datepicker( "option", "maxDate", selectedDate );
	    }
	});
});
