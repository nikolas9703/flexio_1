$(function(){
         "use strict";
         //Init Bootstrap Calendar Plugin
         //*********************************
         if(vigencia != '' && vigencia != 'undefined'  && prima != 'undefined' && prima != ''){
            
            $('#vigencia_desde').daterangepicker({
               locale: { format: 'DD-MM-YYYY'},
               showDropdowns: true,
               defaultDate: '',
               singleDatePicker: true,
               startDate: (vigencia.vigencia_desde != "") ? vigencia.vigencia_desde : ''
            }).val(vigencia.vigencia_desde);

            $('#vigencia_hasta').daterangepicker({
               locale: { format: 'DD-MM-YYYY'},
               showDropdowns: true,
               defaultDate: '',
               singleDatePicker: true,
               startDate: (vigencia.vigencia_hasta != "") ? vigencia.vigencia_hasta : ''
            }).val(vigencia.vigencia_hasta);

            $('#fecha_primer_pago').daterangepicker({
               locale: { format: 'DD-MM-YYYY'},
               showDropdowns: true,
               defaultDate: '',
               singleDatePicker: true,
               startDate: (prima.fecha_primer_pago != "") ? prima.fecha_primer_pago : '' 
            }).val(prima.fecha_primer_pago);
            $("#fecha_primerPago").val(prima.fecha_primer_pago);
         }else{
         
            $('#vigencia_desde,#vigencia_hasta,#fecha_primer_pago').daterangepicker({ //
               locale: { format: 'DD-MM-YYYY'},
               showDropdowns: true,
               defaultDate: '',
               singleDatePicker: true
            }).val('');
         }
         //*********************************
         
$("#suma_asegurada, #prima_anual, #impuesto, #otros, #descuentos, #total").inputmask('currency',{
      prefix: "",
      autoUnmask : true,
      removeMaskOnSubmit: true
    });         
     });

$(document).ready(function() {
  var stickyNavTop = $('.tab-principal').offset().top;

  var stickyNav = function(){
    var scrollTop = $(window).scrollTop();
    
    if (scrollTop > stickyNavTop) { 
      $('.tab-principal').addClass('sticky');
    } else {
      $('.tab-principal').removeClass('sticky'); 
    }
  };

  stickyNav();

  $(window).scroll(function() {
    stickyNav();
  });

   
});