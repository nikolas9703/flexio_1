$(function(){
    $("#fecha_desde").datepicker({
      dateFormat: 'YY-MM-DD',
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
      }
    });
    $("#fecha_hasta").datepicker({
      dateFormat: 'YY-MM-DD',
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $("#fecha_desde").datepicker( "option", "maxDate", selectedDate );
        }
    });
});
