var formula = {
  settings:{
    aplicar: $('#aplicarBtn'),
    ventana: $('#presupuestoFillTableModal')
  },
  init:function(){
    this.eventos();
  },
  eventos:function(){
    $("#ajuste1, #ajuste2,#ajuste3").change(function(){
      if($("#ajuste1").is(":checked")) {
        $("#monto_mensual").val("");
        $("#monto_porcentaje").val("");
      }else if($("#ajuste2").is(":checked")){
        $("#monto_fijo").val("");
        $("#monto_porcentaje").val("");
      }else if($("#ajuste3").is(":checked")){
        $("#monto_fijo").val("");
        $("#monto_mensual").val("");
      }
    });

    this.settings.aplicar.click(function(e){
        var anchorAtributo = $('a.cog.active').data('my');
        var monto = parseFloat($('input#monto_fijo').val()) || 0;
        var tipo = "";
        if($("#ajuste1").is(":checked")) {
          var montofijo = monto;
          tipo = "fijo";
          formula.llenarTabla(montofijo,tipo,anchorAtributo);
        }else if($("#ajuste2").is(":checked")){
          var monto_mensual = parseFloat($('input#monto_mensual').val());
          tipo = "mensual";
          monto = parseFloat($('input#aux_monto').val());
          formula.llenarTabla(monto, tipo, anchorAtributo, monto_mensual);
        }else if($("#ajuste3").is(":checked")){
          var monto_porcentaje = parseFloat($('input#monto_porcentaje').val()) / 100;
          tipo = 'porcentaje';
            monto = parseFloat($('input#aux_monto').val());
          formula.llenarTabla(monto,tipo,anchorAtributo,monto_porcentaje);
        }
        formula.settings.ventana.modal('hide');
    });
  },
  llenarTabla:function(monto, tipo, anchorAtributo, porcentaje){

    var totales = 0;
    var mensual = parseInt(porcentaje);
    $('input[id$="_'+anchorAtributo+'"]').each(function(a,b){
      if(tipo == 'fijo'){
        $(b).val(accounting.toFixed(monto,2));
      }else if(tipo == 'mensual'){
        if(a > 0)
        monto += mensual;
        $(b).val(accounting.toFixed(monto,2));
      }else if(tipo == 'porcentaje'){
        if(a > 0)
        monto += monto * porcentaje;
        $(b).val(accounting.toFixed(monto,2));
      }

      totales +=monto;

    });
      $('#totales'+anchorAtributo).val(accounting.toFixed(totales,2));
  }
};
$(document).ready(function(){
 formula.init();

});
