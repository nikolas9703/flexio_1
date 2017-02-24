$(document).ready(function() {
  $("a#exportarReporte").click(function(){
  var tipo = $("#tipo").val();
   if(tipo==="balance_situacion" || tipo ==="ganancias_perdidas"){
    var mes = $("#mes").val();
    var year = $("#year").val();
    var periodo = $("#periodo").val();
    var rango = $("#rango").val();


    if(mes.length !==0 || year.length !==0 || periodo.length !==0 || rango.length !==0){
      $("#form_reporte_exportar").submit();
      //submit formulario
    }
   }
   if(tipo==="estado_cuenta_proveedor"){
     var proveedor =  $("#provedor").val();
     var fecha_desde = $("#fecha_desde").val();
     var fecha_hasta = $("#fecha_hasta").val();
     if(proveedor.length !==0 || fecha_desde.length !==0 || fecha_hasta.length !==0){
       $("#form_reporte_exportar").submit();
     }
   }

   if(tipo==="costo_por_centro_compras") {
     var centro_contable_id =  $("#centro_contable_id").val();
     var categoria_id = $("#categoria_id").val();
     var fecha_desde = $("#fecha_desde").val();
     var fecha_hasta = $("#fecha_hasta").val();
     if(centro_contable_id.length !==0 || categoria_id.length !==0 || fecha_desde.length !==0 || fecha_hasta.length !==0){
       $("#form_reporte_exportar").submit();
     }
   }

   if(tipo==="transacciones_por_centro_contable") {
      var centro_contable_id =  $("#centro_contable_id").val();
      var fecha_desde = $("#fecha_desde").val();
      var fecha_hasta = $("#fecha_hasta").val();
      if(centro_contable_id.length !==0 || fecha_desde.length !==0 || fecha_hasta.length !==0){
        $("#form_reporte_exportar").submit();
      }
    }

   if(tipo==="estado_de_cuenta_de_cliente"){
     var cliente =  $("#cliente").val();
     var fecha_desde = $("#fecha_desde").val();
     var fecha_hasta = $("#fecha_hasta").val();
     if(cliente.length !==0 || fecha_desde.length !==0 || fecha_hasta.length !==0){
       $("#form_reporte_exportar").submit();
     }
   }
   if(tipo ==="cuenta_por_pagar_por_antiguedad" || tipo ==="cuenta_por_cobrar_por_antiguedad" ){
     /*var mes = $("#mes").val();
     var year = $("#year").val();
     if(mes.length !==0 || year.length !==0){*/
       $("#form_reporte_exportar").attr('target', '_blank');
       $("#form_reporte_exportar").submit();
       //submit formulario
     //}
   }
   if(tipo ==="impuestos_sobre_ventas"){
     var mes = $("#mes").val();
     var year = $("#year").val();
     if(mes.length !==0 || year.length !==0){
       $("#form_reporte_exportar").attr('target', '_blank');
       $("#form_reporte_exportar").submit();
       //submit formulario
     }
   }
   if(tipo ==="impuestos_sobre_itbms"){
     var proveedor =  $("#proveedores").val();
     var fecha_desde = $("#fecha_desde").val();
     var fecha_hasta = $("#fecha_hasta").val();  
     if(proveedor != null && fecha_desde.length != 0 && fecha_hasta.length != 0){
       $("#form_reporte_exportar").submit();
     }
   }
   if(tipo ==="formulario_43" || tipo ==="formulario_433"){
     var mes = $("#mes").val();
     var year = $("#year").val();
     if(mes.length !==0 || year.length !==0){
       //$("#form_reporte_exportar").attr('target', '_blank');
       $("#form_reporte_exportar").submit();
       //submit formulario
     }
   }
  });

$("a#imprimirReporte").click(function(){
  var tipo = $("#tipo").val();
   if(tipo==="impuestos_sobre_itbms"){
     var proveedor =  $("#proveedores").val();
     var fecha_desde = $("#fecha_desde").val();
     var fecha_hasta = $("#fecha_hasta").val();    
     if(proveedor !== '' && fecha_desde.length !== 0 && fecha_hasta.length !== 0){
       $("#form_reporte_exportar").submit();
     }
  }
});

});
