$(document).ready(function(e){
  "use strict";
  	//Init Bootstrap Calendar Plugin
      $(':input[data-inputmask]').inputmask();
      $("#fecha_inicio").datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
          $("#fecha_final").datepicker( "option", "minDate", selectedDate );
        }
      });
      $("#fecha_final").datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
          $("#fecha_inicio").datepicker( "option", "maxDate", selectedDate );
          }
      });

});
