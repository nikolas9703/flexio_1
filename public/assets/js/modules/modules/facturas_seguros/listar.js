$(function(){
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
      $("#fechavencimiento1").datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
          $("#fecha2").datepicker( "option", "minDate", selectedDate );
        }
      }); 
    $("#fechavencimiento2").datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
          $("#fecha1").datepicker( "option", "maxDate", selectedDate );
          }
      });    

     $(".chosen-select").chosen({width: "100%"});

});
