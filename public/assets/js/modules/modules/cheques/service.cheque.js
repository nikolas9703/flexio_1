bluapp.service('serviceCheque',function($http){
  var model = this,
  datos = {erptkn: tkn},
    ruta = {
        proveedores: phost() + 'cheques/ajax-proveedores-cheques',//retorna los proveedores con facturas que se pueden pagar
        facturas: phost() + 'cheques/ajax-facturas-cheques',//retorna las facturas que se pueden pagar
        pagoInfo: phost() +  'cheques/ajax-pago-info',//
        proveedorPago:  phost() +  'cheques/ajax-pago-proveedor',
        pagos:  phost() +  'cheques/ajax-pagos-cheques',
        chequeInfo: phost() + 'cheques/ajax-info-cheque',
        chequeraInfo: phost() + 'cheques/ajax-info-chequera',
        chequePago: phost() + 'cheques/ajax-cheque-pago'
    }, resultados;

    function extract(result){
        return result.data;
    }

    function resultado(result){
        resultados = extract(result);
	return resultados;
    }


    model.getProveedores = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.proveedores,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
  
    model.getPagos = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.pagos,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };

    model.getChequePago = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.chequePago,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(response) {
            return response.data;
        });
    };
  
    model.getCheque = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.chequeInfo,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };

    model.getChequera = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.chequeraInfo,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
  
    model.proveedorPago = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.proveedorPago,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };

    model.getInfoPago = function(info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.pagoInfo,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
});
