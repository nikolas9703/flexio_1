$(document).ready(function(e){
  "use strict";
  	//Init Bootstrap Calendar Plugin
      $(':input[data-inputmask]').inputmask();
      $("#fecha_inicio").datepicker({
        dateFormat: 'mm/dd/yy',
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
          $("#fecha_final").datepicker( "option", "minDate", selectedDate );
        }
      });
      $("#fecha_final").datepicker({
        dateFormat: 'mm/dd/yy',
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
          $("#fecha_inicio").datepicker( "option", "maxDate", selectedDate );
          }
      });

      $(".select2").select2({
         theme: "bootstrap",
         width:"100%"
      });

});
