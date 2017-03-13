bluapp.controller("registrarCobroController", function($scope, $timeout,serviceCobro, $document){
  var model = this;
  $scope.tipo = "";
  $scope.acceso = acceso === 0? false : true;
  $scope.uuid_factura = uuid_factura;
  $scope.nextItem = 0;
  $scope.vista = vista;
  $scope.disabled = false;
  $scope.disableCuenta = false;
  $scope.datosCobro={
    clienteActual:'',
    termino_pago:'',
    credito:'',
    saldo:'',
    fecha_pago: moment().format('DD/MM/YYYY'),
    comentario:'',
    estado:'',
    total_pago: 0,
    monto: 0,
    tipo_deposito:'banco'
  };

  $scope.opcionPagos = [{icon:'fa fa-plus', index:0,total_pagado:'',tipo_pago:''}];
  $scope.depositable = {};
  var objFrom = {
    cobroForm: $('#form_crear_cobro'),
  };

  $scope.cobrosHeader = {
    tipo:'',
    uuid:'',
    colleccion:[]
  };

  if( $scope.vista === 'registrar_pago'){
    $scope.tipo = 'factura';
    $scope.cobrosHeader.tipo = 'factura';
    $scope.disableTipo = true;
    $scope.disableSelected = true;

    //buscar datos de factura
    var facturaInfo =  serviceCobro.getInfoFactura({uuid:uuid_factura});
    facturaInfo.then(function(data){
      $scope.datosCobro.clienteActual = data.cliente_id.toString();
      $scope.datosCobro.credito = "0.00";
      $scope.datosCobro.saldo = data.cliente.saldo_pendiente;

     var pagado =  _.sumBy(data.cobros, function(o) { return parseFloat(o.monto_pagado); });
      $scope.facturas = [{
        factura_id: data.id,
        codigo: data.codigo,
        fecha_emision: data.fecha_desde,
        fecha_finalizacion: data.fecha_hasta,
        monto: data.total,
        pagado: accounting.toFixed(pagado,2),
        saldo_pendiente: accounting.toFixed(parseFloat(data.total) - pagado,2),
        precio_total:''
      }];
      $scope.inputPago = data.total;
      if(data.total == pagado){
        angular.element("#guardarBtn").prop('disabled',true);
      }

    });
    $timeout(function(){
       $(':input[data-inputmask]').inputmask();
     },3000);

  }
  $scope.focusPago = function(index){
    if($scope.datosCobro.monto === 0){
        var saldo_pendiente = $scope.facturas[index].saldo_pendiente;
    $scope.facturas[index].precio_total = saldo_pendiente;
    $scope.datosCobro.monto = saldo_pendiente;
    }
  };
  $scope.addRow = function(index){
    $scope.nextItem = $scope.nextItem + 1;
    $scope.opcionPagos.push({icon:'fa fa-trash', index:$scope.nextItem,total_pagado:'',tipo_pago:''});
  };
  $scope.deleteRow = function(index){
    if(!angular.isUndefined($scope.opcionPagos[index])){
      $scope.nextItem = $scope.nextItem - 1;
      $scope.opcionPagos.splice(index, 1);
    }
  };
  $scope.selecionePago = function(index,pago){
    if(pago !==''){
      $scope.opcionPagos[index].tipo_pago = pago;
    }
  };
  $scope.pagoClass = function(index){
    return $scope.opcionPagos[index].tipo_pago === ''? 'hide':'show';
  };

  $scope.cambiarCantidad = function(index, cantidad){

    if(cantidad !==''){
      if(cantidad > parseFloat($scope.facturas[index].saldo_pendiente)){
        $scope.facturas[index].precio_total =  $scope.facturas[index].saldo_pendiente;
        $scope.datosCobro.monto = $scope.facturas[index].saldo_pendiente;
      }else if(accounting.toFixed(cantidad,2) != "0.00" && cantidad < parseFloat($scope.facturas[index].saldo_pendiente)){
        $scope.facturas[index].precio_total = cantidad;
        $scope.datosCobro.monto = cantidad;
      }else if(accounting.toFixed(cantidad,2) == "0.00"){
        $scope.facturas[index].precio_total =  $scope.facturas[index].saldo_pendiente;
        $scope.datosCobro.monto = $scope.facturas[index].saldo_pendiente;
      }
    }
  };
  $scope.sumaTotales = function(index){
    //$scope.datosCobro.total_pago = $scope.datosCobro.total_pago + parseFloat($scope.opcionPagos[index].total_pagado);
    //sumar totales del tipo credito
    var total = 0;
    angular.forEach($scope.opcionPagos,function(value, key){
      total += parseFloat(value.total_pagado);
    });
    $scope.datosCobro.total_pago= accounting.toFixed(total,2);
    if($scope.datosCobro.total_pago !==  $scope.datosCobro.monto){
        angular.element("#totals-error").empty().html('El total debe ser igual al monto');
        angular.element("#totals-error").css('display','block');
        angular.element("#guardarBtn").prop("disabled",true);
    }else if($scope.datosCobro.total_pago ===  $scope.datosCobro.monto){
        angular.element("#totals-error").empty();
        angular.element("#guardarBtn").prop("disabled",false);
    }

    //suma total de credito

    var montoCredito =  _.sumBy($scope.opcionPagos, function(o) {
      if(o.tipo_pago ==='aplicar_credito')  return parseFloat(o.total_pagado);
    });
    if($scope.opcionPagos[index].tipo_pago ==='aplicar_credito' && parseFloat(montoCredito) > parseFloat($scope.datosCobro.credito)){
      angular.element("#totals-error").empty().html('El total no puede ser mayor al credito');
      angular.element("#totals-error").css('display','block');
      angular.element("#guardarBtn").prop("disabled",true);
    }else if($scope.opcionPagos[index].tipo_pago ==='aplicar_credito' && parseFloat(montoCredito) < parseFloat($scope.datosCobro.credito)){
      angular.element("#totals-error").empty();
      angular.element("#guardarBtn").prop("disabled",false);
    }

  };
  $scope.$watch("datosCobro.monto", function(newValue, oldValue) {
    if ($scope.datosCobro.total_pago ===  $scope.datosCobro.monto) {
        angular.element("#totals-error").empty();
        angular.element("#guardarBtn").prop("disabled",false);
    }
  });
  $scope.setCredito = function(){

  };
  $scope.disabledButton = function(){
      return $scope.facturas[0].monto === $scope.facturas[0].pagado? true : false;
  };
  if($scope.datosCobro.tipo_deposito === 'banco'){
    $scope.depositable = window.cuenta_bancos;
  }

  $scope.depositoEn = function(deposito){
    if(deposito ==="banco"){
      $scope.depositable = window.cuenta_bancos;
    }else if(deposito ==="caja"){
      $scope.depositable = window.caja;
    }
  };
  $scope.init = function(){
    angular.element("#fecha_pago").datepicker({
      format: 'dd/mm/yy',
      changeMonth: true,
      numberOfMonths: 1,
      defaultDate: moment().format('DD/MM/YYYY')
    });

    objFrom.cobroForm.validate({
      ignore: '',
      wrapper: '',
      submitHandler: function(form) {
        // do other things for a valid form desabilitar boton guardar
        $("#cliente_id").removeAttr("disabled");
        $("#total_pago").removeAttr("disabled");
        form.submit();
      }
    });


  };

$scope.init();

});
$(function(){
  $(".moneda").inputmask('currency',{
    prefix: "",
    autoUnmask : true,
    removeMaskOnSubmit: true
  });
});
