$(document).ready(function(){
  CKEDITOR.replace('comentario',
  {
    toolbar :
		[
			{ name: 'basicstyles', items : [ 'Bold','Italic' ] },
			{ name: 'paragraph', items : [ 'NumberedList','BulletedList' ] }
		],
    uiColor : '#F5F5F5'
  });
  $('#cancelarFormBoton').remove();
  $('#guardarFormBoton').remove();
  $('button.agregarTransaccionesBtn').attr('disabled', true);
});
bluapp.controller("entradaManualComentarioController", function($scope, $http, $sce){
  var objFrom = {
    comentarioForm: $('#entradaComentarioForm'),
  };

  var rutas = {
    getComentario:  phost() + 'entrada_manual/ajax-getComentario',
    postComentario:  phost() + 'entrada_manual/ajax-postComentario'
  };
  $scope.comentarios = {
    comentario:''
  };
  $scope.historial ={};
  $scope.guardarComentario = function(comentario, event){
    $scope.comentarios = angular.copy(comentario);
  //
  
    CKEDITOR.instances.comentario.updateElement();
    var formularioComentario = objFrom.comentarioForm;
    if(formularioComentario.valid() === true){
      var comentario_usuario = {};
      console.info('pass');
      $http({
        url: rutas.postComentario,
        method: 'POST',
        data : $.param({
          erptkn: tkn,
          entrada_id: entrada_id,
          comentario:CKEDITOR.instances.comentario.getData()
        }),
        cache: false,
        xsrfCookieName: 'erptknckie_secure',
        headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
     }).then(function (data) {
       if(data){
         CKEDITOR.instances.comentario.setData('');
            $scope.historial.push(data.data.comentario);
       }
       });

  }

  };
  $scope.limpiarCampo = function(e) {
    //console.log(e);
    $scope.comentarios = "";//cambiar
    CKEDITOR.instances.comentario.setData('');
  };
  $scope.init = function(){
    objFrom.comentarioForm.validate({
      ignore: '',
      wrapper: '', 
    });
  };
  $http({
    url: rutas.getComentario,
    method: 'POST',
    data : $.param({
      erptkn: tkn,
      entrada_id: entrada_id
    }),
    cache: false,
    xsrfCookieName: 'erptknckie_secure',
    headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
 }).then(function (data) {
   if(data){
      $scope.historial = data.data;
      
   }
   });
   $scope.renderHtml = function (htmlCode) {
            return $sce.trustAsHtml(htmlCode);
        }; $scope.renderHtml = function (htmlCode) {
            return $sce.trustAsHtml(htmlCode);
        };
$scope.init();
$scope.nextPage=function(){
  var last = $scope.historial[$scope.historial.length - 1];
    for(var i = 1; i <= $scope.historial.length; i++) {
      $scope.historial.push(last + i);
    }
};
});
