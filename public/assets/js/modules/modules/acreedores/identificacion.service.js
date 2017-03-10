bluapp.service('identificacionService',function($http){
  var model = this,
  datos = {erptkn: tkn},
    ruta = {
        getAcreedor: phost() + 'acreedores/ajax-get-acreedor'
    }, resultados;

    function extract(result){
        return result.data;
    }

    function resultado(result){
        resultados = extract(result);
	return resultados;
    }


    model.getAcreedor = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.getAcreedor,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
});
