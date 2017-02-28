$(function(){
    "use strict";
    //Init Bootstrap Calendar Plugin
    $('#fecha1, #fecha2').daterangepicker({
        locale:{
            format: 'DD-MM-YYYY',
        },
        showDropdowns: true,
        defaultDate: '',
        singleDatePicker: true
    }).val('');

    $(".chosen-select").chosen({
        width: "100%",
        allow_single_deselect: true
    });
    //ELEMENTOS DE TIPO CHOSEN
    $("#tipo, #categoria").chosen({
        width: '100%',
        allow_single_deselect: true 
    });
    //funcionalidad de exportacion
    var gridObj = $("#tablaPagosGrid");
	  var aplicarPagosModal = $('#aplicarPagosModal');
    var  aplicarPagosForm = $("#aplicarPagosForm");
    $("#moduloOpciones").on("click", "#exportarListaPagos", function(){
        //contiene un arreglo con los identificadores de los proveedores (uuid_proveedor)
        var registros_jqgrid = gridObj.jqGrid('getGridParam','selarrrow');

        if(registros_jqgrid.length)
        {
            var url = phost() + "pagos/ajax-exportar";
            var vars = "";
            $.each(registros_jqgrid, function(i, val){
                vars += '<input type="hidden" name="uuid_pagos[]" value="'+ val +'">';
            });
            var form = $(
                '<form action="' + url + '" method="post" style="display:none;">' +
                vars +
                '<input type="hidden" name="erptkn" value="' + tkn + '">' +
                '<input type="submit">' +
                '</form>'
            );
            $('body').append(form);
            form.submit();
        }
    });

    $('#moduloOpciones').on("click", 'a[id*="generarAplicadoMultiple"]', function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();

      var seleccionados = gridObj.jqGrid('getGridParam','selarrrow');
	    var rows = '';

       if(seleccionados.length == 0){
        toastr.warning('Debe seleccionar uno o varias pagos de tipo por aplicar');
        return false;
      }else{

        var check = true;
        var aprobados = true;

        $.each(seleccionados, function(indice, pago_uuid){
           var rowinfo = gridObj.getRowData(pago_uuid);
           var estado = $.trim(rowinfo.estado_etiqueta);
           if(!estado.match(/(por_aplicar)/gi)){
             aprobados = false;
           }
           rows += '<tr><td>'+ rowinfo["codigo"] +'</td><td>'+rowinfo["forma_pago"]  +'</td></tr>';

           //Agregar campos a formulario
           aplicarPagosForm.append('<input type="hidden" name="pago['+ indice +']" value="'+ pago_uuid +'" />');
         });



         if(aprobados == false){
          toastr.warning('Debe seleccionar pagos con estado por aplicar.');
          return false;
        }
      }
      //HTML Tabla con listado de Colaboradores
      var html = [
      '<div class="m-b-sm"><h3 class="m-xs">'+ seleccionados.length + (seleccionados.length > 1 ? ' Pagos' : ' Pago') +'</h3></div>',
        '<table class="table table-bordered">',
          '<thead>',
            '<tr>',
              '<th>Numero de Pago</th>',
              '<th>Forma de pago</th>',
            '</tr>',
          '</thead>',
          '<tbody>',
						rows,
					'</tbody>',
        '</table>',
      ].join('\n');

      //HTML Botones del Modal
    	var botones_modal = ['<div class="row">',
    	     		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
     		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
     		   '</div>',
     		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
     		   		'<button id="cambiarEstadoModalBtn" class="btn btn-w-m btn-primary btn-block" type="button">Aplicar</button>',
     		   '</div>',
     		   '</div>'
    	].join('\n');

      //Inicializar opciones del Modal
      aplicarPagosModal.modal({
        backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
        show: false
      });
      aplicarPagosModal.find('.modal-title').empty().append('Pagos a cambiar de estado: Por aplicar a aplicado.');
      aplicarPagosModal.find('.modal-body').empty().append(html);
      aplicarPagosModal.find('.modal-footer').empty().append(botones_modal);
      aplicarPagosModal.modal('show');

     });

     $(aplicarPagosModal).on("click", '#cambiarEstadoModalBtn', function(e){
         e.preventDefault();
         e.returnValue=false;
         e.stopPropagation();

         $("div.modal-content").find('#cambiarEstadoModalBtn').attr('disabled', true);

          $.ajax({
              url: phost() + 'pagos/ajax_aplicar_pagos',
              data: $('#aplicarPagosForm').serialize(),
              type: "POST",
              dataType: "json",
              cache: false,
          }).done(function (json) {

                if( $.isEmptyObject(json.session) == false){
                             window.location = phost() + "login?expired";
                }

                if(json.response == 1){
                         toastr.success(json.mensaje);

                }
                else if(json.response == 0){
                     toastr.error(json.mensaje);

                }
                else if(json.response == 2){
                        toastr.warning(json.mensaje);

                }
                aplicarPagosModal.modal('hide');
                tablaPagos.recargar();
        });
  });
     /*valicación para cuando se accede desde seguros */
     console.log("holis");
     if(localStorage['ms-selected'] == "seguros") {
      $("a.btn.btn-primary").each(function(index, el) {
        if($(this).html() == "Crear") {

          $(this).replaceWith('<label class="btn btn-primary" data-toggle="dropdown" >Acción</label>');
        }
      });
      $(".breadcrumb").html($(".breadcrumb").html().replace("Compras","Seguros"));
      $("label").each(function(index, el) {
        if($(this).html() == "Proveedor"){
          $(this).html("Pago a");
        }
        else if($(this).html() == "Categoría(s) de Proveedor"){
          $(this).parent().remove();//obtenemos nodo padre eliminamos todo el nodo donde esta categoria
        }
      });
      $('#tipo').html('<option value="">Seleccione</option>'+
        ' <option value="compras">Compras</option> '+
        ' <option value="planilla">Planilla</option> '+
        ' <option value="participacion">Participación</option> '+
        ' <option value="remesa">Remesa</option>').trigger('chosen:updated');
     }


    $("#moduloOpciones").on("click", "#generarMultiplesACH", function(){
        //contiene un arreglo con los identificadores de los proveedores (uuid_proveedor)
        var registros_jqgrid = gridObj.jqGrid('getGridParam','selarrrow');

        if(registros_jqgrid.length)
        {
            var url = phost() + "pagos/ajax-generar-ach";
            var vars = "";
            $.each(registros_jqgrid, function(i, val){
                vars += '<input type="hidden" name="uuid_pagos[]" value="'+ val +'">';
            });
            var form = $(
                '<form action="' + url + '" method="post" style="display:none;">' +
                vars +
                '<input type="hidden" name="erptkn" value="' + tkn + '">' +
                '<input type="submit">' +
                '</form>'
            );
            $('body').append(form);
            form.submit();
        }
    });



    //fin de funcionalida de exportacion

});