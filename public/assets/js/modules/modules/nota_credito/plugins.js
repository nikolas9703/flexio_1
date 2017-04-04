$(function(){
"use strict";
	//Init Bootstrap Calendar Plugin
    $("#fecha_desde").datepicker({
      dateFormat: 'dd/mm/yy',
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
      }
    });
    $("#fecha_hasta").datepicker({
      dateFormat: 'dd/mm/yy',
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

	var modulo = localStorage.getItem("ms-selected");
	if(modulo=="seguros"){
		$("ol.breadcrumb > li").each(function(i){
			if($(this).text() == "Ventas"){
				$(this).text("Seguros");
			}
		});
	}
	
});
