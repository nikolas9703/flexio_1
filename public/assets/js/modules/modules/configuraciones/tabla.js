$(document).ready(function(){
  var tabla = moduloConfiguracion.getTipoActividades();
  $("#tablaGrid").on("click", ".viewOptions", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();

    var id_propiedad = $(this).attr("data-id");
    var rowINFO = $("#tablaGrid").getRowData(id_propiedad);
    var options = "";
    options = '<a href="javascript:" data-info="'+ id_propiedad +'" class="opcion-editar btn btn-block btn-outline btn-success">Editar</a>';
    options += '<a href="javascript:" data-info="'+ id_propiedad +'" class="opcion-borrar btn btn-block btn-outline btn-success">Borrar</a>';
      //Init boton de opciones
    $('#optionsModal').find('.modal-title').empty().append('Opciones - <span class="options-titulo">'+ rowINFO.nombre+ '</span>');
    $('#optionsModal').find('.modal-body').empty().append(options);
    $('#optionsModal').find('.modal-footer').empty();
    $('#optionsModal').modal('show');
  });

  $("a.openDialogGeneral").click(function(e){
    $('.actividades-icono').removeClass('active');
    e.preventDefault();
    e.stopPropagation();
    $('#generalModal').modal('show');
    $('#formularioTipoActividad').trigger("reset");
    $('#id').val(0);
  });

 $("#optionsModal").on('click','a.opcion-editar',function(e){
   e.preventDefault();
   e.stopPropagation();
    var id = $(this).data('info');
    var parametros = {id:id};
    var info_editar = moduloConfiguracion.getInfoTipoActividades(parametros);
    info_editar.done(function(data){
      var info =  $.parseJSON(data);
      $('#optionsModal').modal('hide');
      $('#generalModal').modal('show');
      $('.actividades-icono').removeClass('active');
      $('#nombre').val(info.nombre);
      $('#puntaje').val(info.puntaje);
      $('#id').val(id);
      $('#icono').val(info.icono);
      $("a[data-icono='" + info.icono +"']").addClass('active');
    });
  });


  $("#optionsModal").on('click','a.opcion-borrar',function(e){
    e.preventDefault();
    e.stopPropagation();
    $('#optionsModal').modal('hide');
    var id = $(this).data('info');
    sweetAlert({
      title: "Esta seguro que desea eliminarlo",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      cancelButtonText: "Cancelar",
      confirmButtonText: "Si, Eliminarlo",
      closeOnConfirm: false },
      function(isConfirm){
       if(isConfirm) {
         var parametros = {id:id};
         var eliminar = moduloConfiguracion.eliminarTipoActividad(parametros);
         eliminar.done(function(data){
           var resultado = $.parseJSON(data);
           if(resultado.exito){
           swal({title:"Eliminado!", text:"Los datos fueron eliminados.", type:"success", timer: 2000, showConfirmButton: false});
           $("#tablaGrid").trigger('reloadGrid');
           }
         });
       }
      });
  });

});
