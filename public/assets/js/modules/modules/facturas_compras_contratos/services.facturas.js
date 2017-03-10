bluapp.service('serviceFactura',function($http){
    var model = this;
    
    var datos = {
        erptkn: tkn
    };
    
    var ruta = {
        getItems: phost() + 'facturas_compras_contratos/ajax-get-items',
        getEmpezarDesde: phost() + 'facturas_compras_contratos/ajax-get-empezar-desde',
        getFactura: phost() + 'facturas_compras_contratos/ajax-get-factura',
        getProveedor: phost() + 'proveedores/ajax-get-proveedor'
    };
    
    var resultados;

    var extract = function (result){
        return result.data;
    };

    var resultado = function (result){
        resultados = extract(result);
	return resultados;
    }


    //Obtiene el catalogo de items y su estructura completa
    //(unidades) -> catalogo de unidades
    //(unidad_id) -> unidad base del item
    //(impuesto_id) -> impuesto para compra del item por defecto 
    //(cuenta_id) -> cuenta de gasto de item por defecto
    model.getItems = function (){
        return $http({
            url:ruta.getItems,
            method: 'POST',
            data : $.param(datos),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
    
    model.getEmpezarDesde = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.getEmpezarDesde,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
    
    model.getFactura = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.getFactura,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
    
    model.getProveedor = function (info){
        var parametros = $.extend(datos,info);
        return $http({
            url:ruta.getProveedor,
            method: 'POST',
            data : $.param(parametros),
            cache: true,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(resultado);
    };
});
