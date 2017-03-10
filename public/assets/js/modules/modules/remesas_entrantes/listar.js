var listarSolicitudes = (function() {

//Init Bootstrap Calendar Plugin
$('#inicio_fecha').daterangepicker({
 format: 'DD/MM/YYYY',
 showDropdowns: true,
 defaultDate: '',
 singleDatePicker: true
}).val('');
$('#fin_fecha').daterangepicker({
 format: 'DD/MM/YYYY',
 showDropdowns: true,
 defaultDate: '',
 singleDatePicker: true
}).val('');
      
     
return{
init: function() {
}
      };
})();
listarSolicitudes.init();

