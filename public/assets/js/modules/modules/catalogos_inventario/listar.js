$(function(){

    $(".porcentaje").inputmask('percentage',{
      suffix: "",
      clearMaskOnLostFocus: false
    });

    $(".entero").inputmask('integer');
    $("#fcuenta").chosen({width: '100%', allow_single_deselect: true});

$("#fcuenta").chosen().change(function(evt, params){
    console.log(params.selected);
    ConfigInventarioCatItems.campo.cuenta_id = params.selected;
});
    /*$(".select2").select2({
        theme: "bootstrap",
        width: "100%"
    });*/
});
