var Subcripcion = function(){
  this.servicio = pusher = new Pusher('323c4c72368dae9707b9', {
        encrypted: false
  });
};

Subcripcion.prototype = function() {
  var escuchar = function(){

    var channel = this.servicio.subscribe('comentario_'+uuid_empresa);
    channel.bind('landing_comments', function(data) {
      var vista = $("#app_landing_page").length;
      if(vista === 0){
        //notificacion del browser
        toastr.info(data.comentario,data.nombre_usuario + ' Comento');
      }
    });

  };
  return {
    oir:escuchar
  };
}();
var mychannel = new Subcripcion();
mychannel.oir();
