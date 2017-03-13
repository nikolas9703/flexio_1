$(function() {

  var gridObj = $("#pedidosGrid");

  //ELEMENTOS DE TIPO CHOSEN
  $("#estado, #centro").chosen({
      width: '100%',
      allow_single_deselect: true
  });

  var verificarConversion = function(){

    var pedidos = gridObj.jqGrid('getGridParam','selarrrow');

    if(pedidos.length<=0) {
      return false;
    }
    //Validar seleccion
    var valido = true;
    var check_centros = [];
    var check_bodegas = [];
    var i=0;

    for(pedido_uuid in pedidos) {
        var pedido = gridObj.getRowData(pedidos[pedido_uuid]);
        var estado = normalize($(pedido['Estado']).text().toLowerCase());
        var centro_id = pedido['centro_id'];
        var bodega_id = pedido['bodega_id'];

        //si el objeto es vacio continuar la iteracion
        if($.isEmptyObject(pedido)){
          continue;
        }

        //Verificar Estados
        if(!estado.match(/cotizacion|parcial/gi)){
          toastr.warning('Por favor! seleccione s&oacute;lo pedidos en estado <strong>En cotizaci&oacute;n</strong> o <strong>Parcial</strong>.');
          valido = false;
          break;
        }

        //Armar array de centros y bodegas
        check_centros[i] = centro_id;
        check_bodegas[i] = bodega_id;
        i++;
    }

    //Verificar si estado valido
    if(valido==false) {
      $('body').trigger('click');
      return false;
    }

    //Verificar si los pedidos son del mismo Centro
    if(check_centros.allValuesSame()==false) {
      $('body').trigger('click');
      toastr.warning('Por favor! seleccione s&oacute;lo pedidos con el mismo centro contable.');
      return false;
    }

    //Verificar si los pedidos son de la misma bodega
    if(check_bodegas.allValuesSame()==false) {
      $('body').trigger('click');
      toastr.warning('Por favor! seleccione s&oacute;lo pedidos con la misma bodega.');
      return false;
    }

    //Convertir pedidos seleccionados
    //a orden de compras.
    convertirOrdenCompra();
  };

  var convertirOrdenCompra = function(){

    var pedidos = gridObj.jqGrid('getGridParam','selarrrow');
    if(pedidos.length<=0) {
      return false;
    }

    var url = phost() + "ordenes/crear/pedidos";
    var fields = "";
    $.each(pedidos, function(i, pedido_uuid){
        var pedido = gridObj.getRowData(pedido_uuid);
        fields += '<input type="hidden" name="pedidos_id[]" value="'+ pedido['pedido_id'] +'">';
        fields += '<input type="hidden" name="centro_uuid" value="'+ pedido['centro_uuid'] +'">';
        fields += '<input type="hidden" name="bodega_uuid" value="'+ pedido['bodega_uuid'] +'">';
    });

    var form = $(
        '<form action="' + url + '" method="POST" style="display:none;">' +
        fields +
        '<input type="hidden" name="erptkn" value="' + tkn + '">' +
        '<input type="submit">' +
        '</form>'
    );

    $('body').trigger('click').append(form);
    form.submit();
  };

  //Convertir orden de compra
  $('#moduloOpciones ul').on("click", "#convertirOrdenBtn", function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();

      //Verificar si selecciono solo
      //pedidos en estado cotizacion.
      verificarConversion();
   });

  //Expotar a CSV
  $('#moduloOpciones ul').on("click", "#exportarBtn", function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();

      if($('#tabla').is(':visible') == true){
          //Desde la Tabla
          exportarjQgrid();

      }else{
          //Desde el Grid
          exportarGrid();
      }
   });

  function exportarjQgrid() {
      //Exportar Seleccionados del jQgrid
      var registros_jqgrid = [];

      registros_jqgrid = gridObj.jqGrid('getGridParam','selarrrow');

      var obj = new Object();
      obj.count = registros_jqgrid.length;

      if(obj.count) {

          obj.items = new Array();

          for(elem in registros_jqgrid) {
              //console.log(proyectos[elem]);
              var registro_jqgrid = gridObj.getRowData(registros_jqgrid[elem]);

              //Remove objects from associative array
              delete registro_jqgrid['link'];
              delete registro_jqgrid['options'];

              //Push to array
              obj.items.push(registro_jqgrid);
          }


          var json = JSON.stringify(obj);
          var csvUrl = JSONToCSVConvertor(json);
          var filename = 'pedidos_'+ Date.now() +'.csv';

          //Ejecutar funcion para descargar archivo
          downloadURL(csvUrl, filename);

          $('body').trigger('click');
      }
  }

  function exportarGrid(){
      var registros_grid = [];

      $("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
          registros_grid.push(this.value);
      });

      //Verificar si ha seleccionado algun proyecto
      if(registros_grid.length==0){
          return false;
      }
      //Convertir array a srting separado por guion
      var registros_grid_string = registros_grid.join('-');
      var obj;

      $.ajax({
          url: phost() + "pedidos/ajax-exportar",
          type:"POST",
          data:{
              erptkn:tkn,
              id_registros: registros_grid_string
          },
          dataType:"json",
          success: function(data){
              if(!data)
              {
                  return;
              }

              var json = JSON.stringify(data);
              var csvUrl = JSONToCSVConvertor(json);
              var wfilename = 'pedidos_'+ Date.now() +'.csv';

              //Ejecutar funcion para descargar archivo
              downloadURL(csvUrl, filename);

              $('body').trigger('click');
          }

      });

  }

  $(function(){
     "use strict";
     //Init Bootstrap Calendar Plugin
     $('#fecha1, #fecha2').daterangepicker({
         format: 'YYYY-MM-DD',
         showDropdowns: true,
         defaultDate: '',
         singleDatePicker: true
     }).val('');

     $(".chosen-select").chosen({width: "100%"});
  });

});
