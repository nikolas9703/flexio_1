bluapp.service('serviceAbono',function($http){
  var model = this, 
  datos = {erptkn: tkn},
    ruta = {
        clientes: phost() + 'clientes_abonos/ajax-clientes',//retorna los proveedores activos
        facturas: phost() + 'clientes_abonos/ajax-facturas-abonos',//retorna las facturas que se pueden pagar
        facturaInfo: phost() +  'clientes_abonos/ajax-factura-info',//
        clienteInfo:  phost() +  'clientes_abonos/ajax-cliente-info',
        abonoInfo: phost() + 'clientes_abonos/ajax-info-abono'
    }, resultados;

    function extract(result){ 
        return result.data;
    }

    function resultado(result){
        resultados = extract(result);
	return resultados;
    }


    model.getClientes = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.clientes,
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
  
    model.clienteInfo = function (info){
        var parametros = $.extend(datos,info);
        
        console.log(parametros);
        
        return $http({
            url:ruta.clienteInfo,
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
