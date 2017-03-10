var notificaciones;

var formularioNotificacion = {
  settings:{
    guardar: $('#guardarFormBtn'),
    roleChangeIn: $('table.tabla-dinamica'),
    updateCheckbox: $('table.tabla-dinamica'),
    eliminarJob: $('table.tabla-dinamica'),
  },
  init:function() {
    notificaciones = this.settings;
    this.inicializar_plugin();
    this.viewNotificacions();
  },

  viewNotificacions:function(){
    notificaciones.roleChangeIn.on('change','select.role-change',function(){
      var rol_id = $("#"+$(this).attr('id')).chosen().val();
      var self = this;
      var parametros = {rol_id:rol_id};
      var role = moduloConfiguracion.getUsuariosByrol(parametros);
      role.done(function(data){
        var usuarios = $.parseJSON(data);
        moduloConfiguracion.polulateUsuariosSelect(self,usuarios);
      });

    });
    notificaciones.updateCheckbox.on('click','.notificaciones-checkbox',function(){
      var checkbox = $("#"+$(this).attr('id'));
          if (checkbox.is(":checked")) {
            checkbox.val('activo');
          } else {
            checkbox.val('desactivo');
          }
    });
   notificaciones.eliminarJob.on('click','.eliminarJobBtn',function(){
     var id = $(this).data('delete');
     var row = $(this).data('row');
     var parametros = {id:id};
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
          var eliminar = moduloConfiguracion.eliminarJob(parametros);
          eliminar.done(function(data){
            var respuesta = $.parseJSON(data);
            if(respuesta.estado == 200){
            swal({title:"Eliminado!", text:"Los datos fueron eliminados.", type:"success", timer: 2000, showConfirmButton: false});
            $('tr#'+row).remove();
            }
          });
        }
       });
   });
    /*notificaciones.guardar.on('click',function(){
      moduloConfiguracion.guardarNotificaciones($('form'));
    });*/
  },
  inicializar_plugin:function(){
    if ($().chosen) {
      if($(".chosen-select").attr("class") != undefined){
        $(".chosen-select").chosen({
          width: '100%'
        });
      }

      //Fix para campos chosen en tabla dinamica
      $('select.chosen-select').chosen({
            width: '100%',
        }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
            $(this).closest('div.table-responsive').css("overflow", "visible");
        }).on('chosen:hiding_dropdown', function(evt, params) {
          $(this).closest('div.table-responsive').css({'overflow-x':'auto !important'});
        });
    }

    //Init Bootstrap Calendar Plugin
      if ($().daterangepicker) {
      $('.daterange-picker').daterangepicker({
        singleDatePicker: true,
        timePicker: true,
        format: 'YYYY-MM-DD hh:mm:ss a',
          showDropdowns: true,
          opens: "left",
          locale: {
            applyLabel: 'Seleccionar',
              cancelLabel: 'Cancelar',
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
              monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
              firstDay: 1
          }
      }).on('apply.daterangepicker', function(ev, picker) {
      });
    }

  }  //fin de inicializar_plugin
};

(function(){
  formularioNotificacion.init();
})();
