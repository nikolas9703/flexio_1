$(function(){
         "use strict";
         //Init Bootstrap Calendar Plugin
         //*********************************
         if(vigencia != '' && vigencia != 'undefined'  && prima != 'undefined' && prima != ''){
            console.log('ingreso');
            console.log(vigencia.vigencia_desde);
            $('#vigencia_desde').daterangepicker({
               format: 'DD/MM/YYYY',
               showDropdowns: true,
               defaultDate: '',
               singleDatePicker: true
            }).val(vigencia.vigencia_desde);

            $('#vigencia_hasta').daterangepicker({
               format: 'DD/MM/YYYY',
               showDropdowns: true,
               defaultDate: '',
               singleDatePicker: true
            }).val(vigencia.vigencia_hasta);

            $('#fecha_primer_pago').daterangepicker({
               format: 'DD/MM/YYYY',
               showDropdowns: true,
               defaultDate: '',
               singleDatePicker: true
            }).val(prima.fecha_primer_pago);
            $("#fecha_primerPago").val(prima.fecha_primer_pago);
         }else{
          console.log('no ingreso');
            $('#vigencia_desde,#vigencia_hasta,#fecha_primer_pago').daterangepicker({ //
               format: 'YYYY-MM-DD',
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