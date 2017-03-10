$(document).ready(function() {
    $('#exportarReporteLnk').css('display', 'none');

$('#Submit').on( "click", function() {

    listar_reporte();
});

$('#limpiar').on( "click", function() {

    $('#exportarReporteLnk').css('display', 'none');
    $('#tabla2').css('display', 'none');
    $('#fecha_desde').val('');
    $('#fecha_hasta').val('');
});


});

function listar_reporte(){
$('#exportarReporteLnk').css('display', 'block');
var fecha_desde = $('#fecha_desde').val();
$('#fecha_desde2').empty();
$('#fecha_desde2').append(fecha_desde);
var fecha_hasta = $('#fecha_hasta').val();
$('#fecha_hasta2').empty();
$('#fecha_hasta2').append(fecha_hasta);
$('#tabla2').css('display', '');

$.ajax({
    url: phost() + "acreedores/ajax-listar-reporte",
    type:"POST",
    data:{
    erptkn:tkn,
    acreedor_id :acreedor_id,
    fecha_desde:fecha_desde,
    fecha_hasta:fecha_hasta
    },
    dataType:"json",
    success: function(json){
    var a = 0;
    var b = 0;
    $("#registros").empty();
   $.each(json, function(i, e){
        var colaborador_nombre = json[i].colaborador.nombre;
        var colaborador_apellido = json[i].colaborador.apellido;
        var fecha_pago = json[i].fecha_inicio;
        var categoria = json[i].tipo_descuento.etiqueta;
        var cantidades = json[i].pagadas_descuentos;
        var monto_adeudado = parseFloat(json[i].monto_inicial);
        var monto_ciclo = 0;
        var saldo_restante = 0;
        $.each(cantidades, function(j, e){
        monto_ciclo = parseFloat(cantidades[j].monto_ciclo);
        saldo_restante = parseFloat(cantidades[j].saldo_restante);

        });

     a += parseFloat(monto_ciclo.toFixed(2));
     b += parseFloat(saldo_restante.toFixed(2));

    $("#registros").append("<tr><td>"+colaborador_nombre+" "+ colaborador_apellido +"</td><td>"+fecha_pago+"</td><td>"+categoria+"</td><td>$"+monto_adeudado.toFixed(2)+"</td><td>$"+monto_ciclo.toFixed(2)+"</td><td>$"+saldo_restante.toFixed(2)+"</td></tr>");
    });

    $("#registros").append("<tr><td></td><td></td><td></td><td><strong>Total:</strong></td><td><strong>$"+a.toFixed(2)+"</strong></td><td><strong>$"+ b.toFixed(2) +"</strong></td></tr>");

    }
    });



}

//Plugin Datepicker
    $("#fecha_desde").datepicker({
      dateFormat: 'dd/mm/yy',
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
      }
    });
    $("#fecha_hasta").datepicker({
      dateFormat: 'dd/mm/yy',
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $("#fecha_desde").datepicker( "option", "maxDate", selectedDate );
        }
    });

$('#exportarReporteLnk').click(function () {
    $('#Submit').css('display', 'none');
    $('#limpiar').css('display', 'none');
    var data_html = $('.ibox-content'),
            cache_width = data_html.width(),
            a4 = [595.28, 750.89];

    //Generamos una imagen usando Canvas

    getCanvas().then(function (canvas) {
        var img = canvas.toDataURL("image/png");
        var imgWidth = 265;
        var pageHeight = 245;
        var imgHeight = canvas.height * imgWidth / canvas.width;
        var heightLeft = imgHeight;

        var doc = new jsPDF('l', 'mm');
        var position = 10;

        doc.addImage(img, 'PNG', 3, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            doc.addPage();
            doc.addImage(img, 'PNG', 3, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }
        var currentDate = new Date();
        var day = currentDate.getDate();
        var month = currentDate.getMonth() + 1;
        var year = currentDate.getFullYear();
        var n = currentDate.getTime();
        doc.save('reporte_pagos-' + day + month + year + n + '.pdf');
        data_html.width(cache_width);
    });

    function getCanvas() {
        data_html.width((a4[0] * 2.88888) - 80).css('max-width', '100%');
        return html2canvas(data_html, {
            imageTimeout: 2000,
            removeContainer: true
        });
    }

    $('#Submit').css('display', '');
    $('#limpiar').css('display', '');

});
