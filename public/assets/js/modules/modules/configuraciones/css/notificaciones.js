var notificaciones;

var formularioNotificacion = {
  settings:{
    guardar: $('#guardarFormBtn'),
    roleChangeIn: $('table.tabla-dinamica'),
    updateCheckbox: $('table.tabla-dinamica')
  },
  init:function() {
    notificaciones = this.settings;
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
    notificaciones.guardar.on('click',function(){
      moduloConfiguracion.guardarNotificaciones($('form'));
    });
  }

};

(function(){
  formularioNotificacion.init();
})();
