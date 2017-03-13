$(document).ready(function() {
    $('#exportarDescuentoLnk').css('display', 'none');
    var centro_contable = $('#centro_contable').text();
    var cargo = $('#cargo').text();
   
    $('#centro_contable2').append(centro_contable);
    $('#cargo2').append(cargo);
  
    var colaborador_id = $('#colaborador_id').val();
    var monto = $('#monto').val();
        
    $.ajax({
    url: phost() + "descuentos/ajax-calcular-capacidad-endeudamiento",
    type:"POST",
    data:{
    erptkn:tkn,
    colaborador_id:colaborador_id,
    monto:monto
    },
    dataType:"json",
    success: function(json){
        
    $.each(json, function(i, value) {
            $('#disponible').val(value.capacidad);
            $('#disponible2').append(value.capacidad);
        });
             
    }
    });
    
    
    
			
			
                        
  

$('#Submit').on( "click", function() {
    
    listar_estado();
});

$('#limpiar').on( "click", function() {
    
    $('#exportarDescuentoLnk').css('display', 'none'); 
    $('#tabla2').css('display', 'none');
    $('#fecha_desde').val('');
    $('#fecha_hasta').val('');
});
    
     
});

function listar_estado(){
$('#exportarDescuentoLnk').css('display', 'block'); 
var fecha_desde = $('#fecha_desde').val();
$('#fecha_desde2').empty();
$('#fecha_desde2').append(fecha_desde);
var fecha_hasta = $('#fecha_hasta').val();       
$('#fecha_hasta2').empty();
$('#fecha_hasta2').append(fecha_hasta);
$('#tabla2').css('display', '');

$.ajax({
    url: phost() + "descuentos/ajax-listar-estado",
    type:"POST",
    data:{
    erptkn:tkn,
    id_descuento :id_descuento,
    fecha_desde:fecha_desde, 
    fecha_hasta:fecha_hasta
    },
    dataType:"json",
    success: function(json){
    var a = 0;
    var b = 0; 
    $("#registros").empty();
   $.each(json, function(i, e){
        var acreedor = json[i].acreedor;
        var fecha_pago = json[i].fecha_creacion;
        //var etiqueta = json[i].tipo_descuento.etiqueta;
        var monto_adeudado = parseFloat(json[i].descuentos.monto_adeudado);
        var monto_ciclo = parseFloat(json[i].monto_ciclo);
        var saldo_restante = parseFloat(json[i].saldo_restante); 
     a += parseFloat(monto_ciclo.toFixed(2));
     b += parseFloat(saldo_restante.toFixed(2)); 
    
    $("#registros").append("<tr><td>"+fecha_pago+"</td><td>"+monto_adeudado.toFixed(2)+"</td><td>"+monto_ciclo.toFixed(2)+"</td><td>"+saldo_restante.toFixed(2)+"</td></tr>");

    });
   
    $("#registros").append("<tr><td></td><td><strong>Total:</strong></td><td><strong>"+a.toFixed(2)+"</strong></td><td><strong>"+ b.toFixed(2) +"</strong></td></tr>");    
         
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

$('#exportarDescuentoLnk').click(function () {
    $('#tabla_info').css('display', '');
   
    var pdf = new jsPDF('p', 'pt', 'letter');
    
    
       
    
    source = $('.ibox-content')[0];
    
    
    // we support special element handlers. Register them with jQuery-style 
    // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
    // There is no support for any other type of selectors 
    // (class, of compound) at this time.
    specialElementHandlers = {
        // element with id of "bypass" - jQuery style selector
        '#buscarEstadoForm': function (element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
        
    };
    margins = {
        top: 80,
        bottom: 60,
        left: 75,
        width: 622
    };
    // all coords and widths are in jsPDF instance's declared units
    // 'inches' in this case
    //doc.text(35, 25, $('#nombre').val());
    pdf.fromHTML(
    source, // HTML string or DOM elem ref.
    margins.left, // x coord
    margins.top, { // y coord
        'width': margins.width, // max width of content on PDF
        'elementHandlers': specialElementHandlers
    },
    function (dispose) {
        // dispose: object with X, Y of the last line add to the PDF 
        //          this allow the insertion of new lines after html
    var currentDate = new Date();
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    var n = currentDate.getTime();
        pdf.save('estadocuenta'+ day + month + year + n +'.pdf');
    }, margins);
    $('#tabla_info').css('display', 'none');
   
});