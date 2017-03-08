$(document).ready(function(){
  $(document).on("click", "a.cog", function(e) {
    $("#presupuestoFillTableModal").find("h4.modal-title").html('Aplicar Formula');
    $('a.cog').each(function(i, elem){
      $(elem).removeClass('active');
    });

    var tdAdjunto = $(this).parent().next('td');
    var valorPrimerInput = $(tdAdjunto).find('input').val();
    if(_.isEmpty(valorPrimerInput)){
      swal("Debe de llenar el monto del primer mes");
      return false;
    }

    $(this).addClass('active');
    $("#presupuestoFillTableModal").modal('show');
    $("#monto_mensual").val("");
    $("#monto_porcentaje").val("");
    $("#ajuste1").prop("checked", true);
    $("#presupuestoFillTableModal").find('input#monto_fijo').val(valorPrimerInput);
    $("#presupuestoFillTableModal").find('input#aux_monto').val(valorPrimerInput);

  });
  $("#cancelarBtn").click(function(){
    $("#presupuestoFillTableModal").modal('hide');
  });
});
