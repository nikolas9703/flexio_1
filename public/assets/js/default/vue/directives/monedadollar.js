Vue.directive('moneda', function(){
        $(this.el).inputmask('currency',{prefix: "$",autoUnmask : true,removeMaskOnSubmit: true});
});
$(document).ready(function(){

$(".moneda").inputmask('currency',{
     prefix: "",
     autoUnmask : true,
     removeMaskOnSubmit: true
   });
   $(".porcentaje").inputmask('percentage',{
     suffix: "",
     clearMaskOnLostFocus: false
   });
});
