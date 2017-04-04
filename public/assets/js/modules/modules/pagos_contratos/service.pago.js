bluapp.service('servicePago',function($http){
  var model = this,
  datos = {erptkn: tkn},
    ruta = {
        proveedores: phost() + 'pagos/ajax-proveedores-pagos',//retorna los proveedores con facturas que se pueden pagar
        subcontratos: phost() + 'pagos/ajax-subcontratos-pagos',//retorna los proveedores con facturas que se pueden pagar
        facturas: phost() + 'pagos/ajax-facturas-pagos',//retorna las facturas que se pueden pagar
        facturaInfo: phost() +  'pagos/ajax-factura-info',//
        proveedorFacturas:  phost() +  'pagos/ajax-facturas-proveedor',
        subcontratoFacturas:  phost() +  'pagos/ajax-facturas-subcontrato',
        pagoInfo: phost() + 'pagos/ajax-info-pago'
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
    
    model.getSubcontratos = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.subcontratos,
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
  
    model.getPago = function (info){
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
  
    model.proveedorFacturas = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.proveedorFacturas,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
    
    model.subcontratoFacturas = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.subcontratoFacturas,
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
