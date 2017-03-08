$(function(){
  $("#categoria_id").on("change", function(e) {
     depreciacionFormulario.$set('datos.categoria_id',$(this).val());
     depreciacionFormulario.$set('articulos',[]);
 });

 $("#centro_contable_id").on("change", function(e) {
    depreciacionFormulario.$set('datos.centro_contable_id',$(this).val());
});

});
