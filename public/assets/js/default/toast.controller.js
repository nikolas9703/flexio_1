bluapp.controller("toastController",function($scope,$document){
  $document.ready(function(){
    if(typeof toast_mensaje !== 'undefined'){
        if(angular.isDefined(toast_mensaje)){
          if(toast_mensaje !==''){
            var mensaje = JSON.parse(toast_mensaje);
            if(mensaje.estado === 200){
              toastr.success(mensaje.mensaje);
            }else if(mensaje.estado === 500){
              toastr.error(mensaje.mensaje);
            }
          }
        }
    }
  });
});
