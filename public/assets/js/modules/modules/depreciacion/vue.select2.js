$(function(){
  $("#categoria_id").on("change", function(e) {
     depreciacionFormulario.$set('datos.categoria_id',$(this).val());
     depreciacionFormulario.$set('articulos',[]);
     var option = $('option:selected', this).attr('cuenta_id');
     depreciacionFormulario.$set('datos.cuenta_id_debito',option);
     if(option!=0){
        $("#cuenta_id_debito").val(option).change();
     } else {
         $("#cuenta_id_debito").val('').change();
     }
 });

 $("#centro_contable_id").on("change", function(e) {
    depreciacionFormulario.$set('datos.centro_contable_id',$(this).val());
});
$("#cuenta_id_credito").on("change", function(e) {
    depreciacionFormulario.$set('datos.cuenta_id_credito', $(this).val());
});

$("#tipo_item").on("change", function(e) {
    depreciacionFormulario.$set('datos.tipo_item', $(this).val());
});

$("#cuenta_id_debito").on("change", function(e) {
    depreciacionFormulario.$set('datos.cuenta_id_debito', $(this).val());
})

});
