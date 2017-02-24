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
