$(document).ready(function() {
  //importacion de plan contable
  $('.opcion-crear-plan').click(function(e) {

    var importacion = moduloContabilidad.crearPlan();
    $('#crearPlanModal').find('.modal-title').empty().html('Crear Plan Contable');
    var opciones = '<div class="loading-progress"></div>';
    $('#crearPlanModal').find('.modal-body').empty().html(opciones);
    $('#crearPlanModal').find('.modal-footer').empty();
    $("#crearPlanModal").modal('show');
    var progress = $(".loading-progress").progressTimer({
      timeLimit: 300,
      completeStyle: 'progress-bar-success',
      onFinish: function() {
        $("#crearPlanModal").modal('hide');
        tablaPlanContable.grid_obj.trigger('reloadGrid');
        $("#moduloOpciones").empty().html('<a href="javascript:" class="btn btn-primary opcion-agregar-cuenta">Crear</a>');
        console.log('completed!');
      }
    });
    importacion.fail(function() {
      progress.progressTimer('error', {
        errorText: 'esta empresa ya cuenta con plan contable!',
        onFinish: function() {
          console.log('hubo un error en la importacion');
        }

      });
    });
    importacion.done(function() {
      progress.progressTimer('complete');
    });
  });

  //open modal agregar cuenta
  $("#moduloOpciones").on('click', '.opcion-agregar-cuenta', function(e) {

    $("#idEdicion").remove();
    $('#addCuentaModal').find('.modal-title').empty().html('Agregar Cuenta');
    var cuentas = moduloContabilidad.listarCuenta();
    cuentas.success(function() {
      $("#cuentas_tabs li:first-child").addClass('active');
      $("#nombre1").val('');
      $("#codigo").val('');
      $("#descripcion").val('');
      $("#padre_id").val('');
      $('#codigo').prop('readonly', false);
      $("#plan_cuentas").jstree("destroy");
    });
    cuentas.done(function(data) {

      var arbol = jQuery.parseJSON(data);
      $('#plan_cuentas').jstree(arbol)
        .bind("select_node.jstree", function(e, data) {
          var nodo = data.node;
          var nodo_id = nodo.id;
          $('#padre_id').val(nodo_id);
          if (_.isEmpty(nodo.children)) {
            var original = nodo.original;
            var codigo = original.codigo;
            $('#codigo').val(codigo + '01.');
          } else {
            var parametros = {
              node: nodo_id
            };
            var nodoCodigo = moduloContabilidad.getCodigo(parametros);
            nodoCodigo.done(function(result) {
              var codigo = $.parseJSON(result);
              $('#codigo').val(codigo.codigo);
            });
          }

        });
      $('#plan_cuentas').jstree(true).redraw(true);
      $("#addCuentaModal").modal('show');
    }); // fin del done

  }); //fin open modal

    $(function(){
        "use strict";
        //Init Bootstrap Calendar Plugin
        /*$('#start, #end').daterangepicker({
            locale:{
                format: 'DD-MM-YYYY',
            },
            showDropdowns: true,
            defaultDate: '',
            singleDatePicker: true
        }).val('');*/

        $("#moduloOpciones").on("click",function(){

            $('#crearModal').modal();

        });
    });


}); //fin dom ready