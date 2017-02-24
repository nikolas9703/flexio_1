bluapp.service('serviceCobro',function($http){
  var model = this,
  datos = {erptkn: tkn},
  ruta = {
       clientes: phost() + 'cobros_seguros/ajax-clientes-cobros',//cuando se hace change del cliente
       facturas: phost() + 'cobros_seguros/ajax-facturas-cobros',//cuando se hace change del factura
       facturaInfo: phost() +  'cobros_seguros/ajax-factura-info',//
       clienteFacturas:  phost() +  'cobros_seguros/ajax-facturas-cliente',
       cobroInfo: phost() + 'cobros_seguros/ajax-info-cobro',
       contratos: phost() + 'cobros_seguros/ajax-contratos',
       contratoFacturas: phost() + 'cobros_seguros/ajax-contrato-facturas'
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
  model.getContratos = function(info){
      var parametros = $.extend(datos,info);
      return $http({
        url: ruta.contratos,
        method: 'POST',
        data : $.param(parametros),
        cache: true,
        xsrfCookieName: 'erptknckie_secure',
        headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(resultado);
  };
  model.getCobro = function (info){
    var parametros = $.extend(datos,info);
    return $http({
      url:ruta.cobroInfo,
      method: 'POST',
      data : $.param(parametros),
      cache: true,
      xsrfCookieName: 'erptknckie_secure',
      headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(resultado);
  };
  model.clienteFacturas = function (info){
    var parametros = $.extend(datos,info);
   	return $http({
      url:ruta.clienteFacturas,
      method: 'POST',
      data : $.param(parametros),
      cache: true,
      xsrfCookieName: 'erptknckie_secure',
      headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(resultado);
  };

  model.contratoFacturas = function (info){
    var parametros = $.extend(datos,info);
   	return $http({
      url:ruta.contratoFacturas,
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
