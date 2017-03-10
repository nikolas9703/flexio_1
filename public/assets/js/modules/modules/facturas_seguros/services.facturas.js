bluapp.service('serviceFactura',function($http, $q){
  var model = this,
  datos = {erptkn: tkn},
  ruta = {
       test: phost() + 'cotizaciones/ajax-data-formulario',
       //clientes: phost() + 'cotizaciones/ajax-cliente-info',//cuando se hace change del cliente
       //items: phost() + 'cotizaciones/ajax-items-cotizacion',//buscarlos item de inventario
       ordenVentaInfo:  phost() + 'ordenes_ventas/ajax-ordenVenta-info',//busca las ordenes de venta
       facturaInfo: phost() +  'facturas/ajax-factura-info',
       impuestoExonerado: phost() + 'contabilidad/ajax-get-impuesto-exonerado',
       empezarFactura: phost() + 'facturas/ajax-empezar-factura-desde',
       contratoInfo: phost() + 'contratos/ajax-contrato-info'
    }, resultados;

  function extract(result){
    return result.data;
  }

  function resultado(result){
	  resultados = extract(result);
	  return resultados;
  }


  model.datosFormulario = function(){
    return (resultados) ? $q.when(resultados) : $http({
      url:ruta.test,
      method: 'POST',
      data : $.param(datos),
      cache: true,
      xsrfCookieName: 'erptknckie_secure',
      headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(resultado);
  };

  model.getImpuestoExonerado = function(){
    return (resultados) ? $q.when(resultados) : $http({
      url:ruta.impuestoExonerado,
      method: 'POST',
      data : $.param(datos),
      cache: true,
      xsrfCookieName: 'erptknckie_secure',
      headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(resultado);
  };


  model.getInfoOrdenVenta = function(info){
    var parametros = $.extend(datos,info);
    return $http({
      url:ruta.ordenVentaInfo,
      method: 'POST',
      data : $.param(parametros),
      cache: true,
      xsrfCookieName: 'erptknckie_secure',
      headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(resultado);
  };

  model.getInfoContrato = function(info){
    var parametros = $.extend(datos,info);
    return $http({
      url:ruta.contratoInfo,
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

  model.llenarSelect = function(info){
    var parametros = $.extend(datos,info);
    return $http({
      url:ruta.empezarFactura,
      method: 'POST',
      data : $.param(parametros),
      cache: true,
      xsrfCookieName: 'erptknckie_secure',
      headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(resultado);

  };
});
