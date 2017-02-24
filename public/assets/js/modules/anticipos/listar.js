$(function(){
	//jQuery Daterange
	$(".fecha-menor").datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$(".fecha-mayor").datepicker( "option", "minDate", selectedDate );
		}
	});
	$(".fecha-mayor").datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$(".fecha-menor").datepicker( "option", "maxDate", selectedDate );
	    }
	});

	$(".moneda").inputmask('currency',{
		prefix: "",
		autoUnmask : true,
		removeMaskOnSubmit: true 
	});
	$(".chosen-select").chosen({
		width:"100%",
		no_results_text: "No se encontraron resultados!"
	});
});
