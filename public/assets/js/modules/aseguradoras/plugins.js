$(function(){
         "use strict";
         //Init Bootstrap Calendar Plugin
         $('#vigencia_desde, #vigencia_hasta').daterangepicker({
             format: 'DD/MM/YYYY',
             showDropdowns: true,
             defaultDate: '',
             singleDatePicker: true
         }).val('');
         
$("#suma_asegurada, #prima_anual, #impuesto, #otros, #descuentos, #total").inputmask('currency',{
      prefix: "",
      autoUnmask : true,
      removeMaskOnSubmit: true
    });         
     });

$(document).ready(function() {
var stickyNavTop = $('.nav-tabs').offset().top;
 
var stickyNav = function(){
var scrollTop = $(window).scrollTop();
      
if (scrollTop > stickyNavTop) { 
    $('.nav-tabs').addClass('sticky');
} else {
    $('.nav-tabs').removeClass('sticky'); 
}
};
 
stickyNav();
 
$(window).scroll(function() {
  stickyNav();
});
});