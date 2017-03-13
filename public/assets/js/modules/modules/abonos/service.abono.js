bluapp.service('serviceAbono',function($http){
  var model = this,
  datos = {erptkn: tkn},
    ruta = {
        proveedores: phost() + 'abonos/ajax-proveedores',//retorna los proveedores activos
        facturas: phost() + 'abonos/ajax-facturas-abonos',//retorna las facturas que se pueden pagar
        facturaInfo: phost() +  'abonos/ajax-factura-info',//
        proveedorInfo:  phost() +  'abonos/ajax-proveedor-info',
        abonoInfo: phost() + 'abonos/ajax-info-abono'
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
  
    model.getFacturas = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.facturas,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
  
    model.getAbono = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.abonoInfo,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
  
    model.proveedorInfo = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.proveedorInfo,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };

    model.getInfoFactura = function(info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.facturaInfo,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
});
