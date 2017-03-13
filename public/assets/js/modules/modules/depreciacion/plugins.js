$(function(){
"use strict";
	//Init Bootstrap Calendar Plugin
    $("#fecha_desde").datepicker({
      dateFormat: 'mm/dd/yy',
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
      }
    });
    $("#fecha_hasta").datepicker({
      dateFormat: 'mm/dd/yy',
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $("#fecha_desde").datepicker( "option", "maxDate", selectedDate );
        }
    });

    $(".select2").select2({
       theme: "bootstrap",
       width:"100%"
    });

    $(":input").inputmask();

});
