$(function(){
         "use strict";
         //Init Bootstrap Calendar Plugin
         //*********************************
         if(vigencia != '' && vigencia != 'undefined'  && prima != 'undefined' && prima != ''){
            console.log(vigencia);
            $('#vigencia_desde').daterangepicker({
               locale: {
                 format: 'DD-MM-YYYY',
               },
               showDropdowns: true,
               defaultDate: '',
               startDate: (vigencia.vigencia_desde != "") ? vigencia.vigencia_desde : '' ,
               singleDatePicker: true
            }).val(vigencia.vigencia_desde);

            $('#vigencia_hasta').daterangepicker({
               locale: {
                 format: 'DD-MM-YYYY',
               },
               showDropdowns: true,
               defaultDate: '',
               startDate: (vigencia.vigencia_hasta != "") ? vigencia.vigencia_hasta : '' ,
               singleDatePicker: true
            }).val(vigencia.vigencia_hasta);

            $('#fecha_primer_pago').daterangepicker({
               locale: {
                 format: 'DD-MM-YYYY',
               },
               showDropdowns: true,
               defaultDate: '',
               startDate: (prima.fecha_primer_pago != "") ? prima.fecha_primer_pago : '' ,
               singleDatePicker: true
            }).val(prima.fecha_primer_pago);
            $("#fecha_primerPago").val(prima.fecha_primer_pago);
         }else{
         
            $('#vigencia_desde,#vigencia_hasta,#fecha_primer_pago').daterangepicker({ //
               locale: {
                 format: 'DD-MM-YYYY',
               },
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