$(document).ready(function(){
  $(document).on("focusout", "input.mes", function(e) {
    var objTotal = $("#totales"+$(this).data('index'));
    var inicio = $(this).data('index');
    var valor = accounting.toFixed($(this).val(), 2);
    $(this).val(valor);
    var totales = 0;
    var monto = $("input[data-index='"+inicio+"']").filter(function(){
      return totales += parseFloat($(this).val() || 0);

    });
    objTotal.val(accounting.toFixed(totales, 2));
  });
});
