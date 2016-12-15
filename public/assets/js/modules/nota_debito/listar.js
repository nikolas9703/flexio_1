$(function(){
"use strict";
	//Init Bootstrap Calendar Plugin
    $("#fecha1").datepicker({
      dateFormat: 'dd/mm/yy',
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $("#fecha2").datepicker( "option", "minDate", selectedDate );
      }
    });
    $("#fecha2").datepicker({
      dateFormat: 'dd/mm/yy',
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $("#fecha1").datepicker( "option", "maxDate", selectedDate );
        }
    });

    $(".select2").select2({
       theme: "bootstrap",
       width:"100%"
    });

    $(":input").inputmask();

});
