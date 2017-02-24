bluapp.controller("crearCobroController", function($scope, $timeout,serviceCobro, $document){
  var model = this;
  $scope.tipo = '';
  $scope.acceso = acceso === 0? false : true;
  $scope.nextItem = 0;
  $scope.vista = vista;
  $scope.disabled = false;
  $scope.disableCuenta = false;
  $scope.disabledEditar = false;
  //filtro de aplicar cobro
  $scope.cobrosHeader = {
    tipo:'',
    uuid:'',
    colleccion:[]
  };

  $scope.depositable = {};

  $scope.datosCobro={
    clienteActual:'',
    termino_pago:'',
    credito:'',
    saldo:'',
    fecha_pago: '',
    comentario:'',
    estado:'',
    total_pago: 0,
    monto: 0,
    id:'',
    tipo_deposito:'banco'
  };

  $scope.opcionPagos = [{icon:'fa fa-plus', index:0,total_pagado:'',tipo_pago:''}];

  var objFrom = {
    cobroForm: $('#form_crear_cobro'),
  };

  $scope.empezarDesde = function(tipo){
    if(tipo !==''){
      if(tipo === 'factura'){
          //buscar las facturas
          var facturas = serviceCobro.getFacturas({vista:$scope.vista,tipo:tipo});
          facturas.then(function(data){
            $scope.cobrosHeader.collection = data;
              $scope.cobrosHeader.tipo = tipo;
          });
      }else if(tipo === 'cliente'){
          //buscar solo los clientes q tienen facturas
          var cliente = serviceCobro.getClientes({vista:$scope.vista});
          cliente.then(function(data){
            $scope.cobrosHeader.collection = data;
              $scope.cobrosHeader.tipo = tipo;
          });
      }else if(tipo == 'contrato_venta'){
          //buscar los contratos con facturas
          var contrato = serviceCobro.getContratos({vista:$scope.vista,tipo:tipo});
          contrato.then(function(data){
              $scope.cobrosHeader.collection = data;
              $scope.cobrosHeader.tipo = tipo;
          });
      }
    }
  };

  $scope.llenarFormulario = function(uuid){
	  alert("du hast");
    if(uuid !=='' && uuid !==null){
      $scope.cobrosHeader.uuid = uuid;
      if($scope.cobrosHeader.tipo === 'factura'){
        var facturaInfo =  serviceCobro.getInfoFactura({uuid:uuid.uuid});
          facturaInfo.then(function(data){
            $scope.datosFormulario(data);
          });
      }else if($scope.cobrosHeader.tipo === 'cliente'){
        var clienteInfo =  serviceCobro.clienteFacturas({uuid:uuid.uuid,vista:$scope.vista});
        clienteInfo.then(function(data){
          $scope.datosFormularioCliente(data);
        });
      }else if($scope.cobrosHeader.tipo === 'contrato_venta'){
        var contratoInfo =  serviceCobro.contratoFacturas({uuid:uuid.uuid,vista:$scope.vista});
        contratoInfo.then(function(data){
          $scope.datosFormularioContrato(data);
        });
      }
    }
  };

  $scope.datosFormulario = function(data){
      console.log(data);
    $scope.datosCobro.clienteActual = data.cliente_id.toString();
    $scope.datosCobro.saldo = data.cliente.saldo_pendiente;
    $scope.datosCobro.fecha_pago = data.fecha_pago;
    $scope.datosCobro.credito = data.cliente.credito_favor.toFixed(2);
    if($scope.vista =='crear'){
      $scope.datosCobro.fecha_pago = moment().format('DD/MM/YYYY');
     $scope.datosCobro.monto = 0;
     $scope.datosCobro.total_pagado = 0;
     $scope.opcionPagos = [{icon:'fa fa-plus', index:0,total_pagado:'',tipo_pago:''}];
    }
   var pagado =  _.sumBy(data.cobros, function(o) { return parseFloat(o.pivot.monto_pagado); });
    $scope.facturas = [{
      factura_id: data.id,
      codigo: data.codigo,
      fecha_emision: data.fecha_desde,
      fecha_finalizacion: data.fecha_hasta,
      monto:data.total,
      pagado: pagado,
      saldo_pendiente: parseFloat(data.total) - pagado,
      precio_total:0
    }];
    $scope.inputPago = data.total;
    if(data.total == pagado){
      angular.element("#guardarBtn").prop('disabled',true);
    }
    $timeout(function(){
       $(':input[data-inputmask]').inputmask();
     },500);
  };

  $scope.datosFormularioCliente = function(data){
      //console.log(data);
    $scope.datosCobro.clienteActual = data.id.toString();
    $scope.datosCobro.credito = parseFloat(data.credito_favor).toFixed(2) || 0;
    $scope.datosCobro.saldo = data.saldo_pendiente;
    $scope.datosCobro.fecha_pago = data.fecha_pago;
    if($scope.vista =='crear'){
    $scope.datosCobro.fecha_pago = moment().format('DD/MM/YYYY');
    $scope.datosCobro.monto = 0;
    $scope.datosCobro.total_pagado = 0;
    $scope.opcionPagos = [{icon:'fa fa-plus', index:0,total_pagado:'',tipo_pago:''}];
    }
    $scope.facturas =[];
    var facturas = [];
    if($scope.vista =='crear'){
        facturas = data.con_factura_para_cobrar;
    }else if($scope.vista =='registrar_pago_cobro'){
        facturas = data.facturas_validas;
    }else{
        facturas = data.facturas_no_anuladas;
    }
    angular.forEach(facturas,function(value, key){

    var pagado =  _.sumBy(value.cobros, function(o) { return parseFloat(o.pivot.monto_pagado); });
     $scope.facturas.push({
       factura_id: value.id,
       codigo: value.codigo,
       fecha_emision: value.fecha_desde,
       fecha_finalizacion: value.fecha_hasta,
       monto:  value.total,
       pagado:pagado,
       saldo_pendiente: parseFloat(value.total) - pagado,
       precio_total:0
     });
  });

    /* $scope.inputPago = data.total;
     if(data.total == pagado){
       angular.element("#guardarBtn").prop('disabled',true);
     }*/
     $timeout(function(){
        $(':input[data-inputmask]').inputmask();
      },500);
  };

  $scope.datosFormularioContrato = function(data){
    console.log(data);
    $scope.datosCobro.clienteActual = data.cliente_id.toString();
    $scope.datosCobro.credito = "0.00";
    $scope.datosCobro.saldo = data.cliente.saldo_pendiente;
    if($scope.vista =='crear'){
    $scope.datosCobro.monto = 0;
    $scope.datosCobro.total_pagado = 0;
    $scope.opcionPagos = [{icon:'fa fa-plus', index:0,total_pagado:'',tipo_pago:''}];
    }
    $scope.facturas =[];
    var facturas = [];
    if($scope.vista =='crear'){
        facturas = data.facturas_por_cobrar;
    }else if($scope.vista =='registrar_pago_cobro'){
        facturas = data.facturas_cobro_parcial;
    }else{
        facturas = data.facturas_no_anuladas;
    }
    angular.forEach(facturas,function(value, key){

    var pagado =  _.sumBy(value.cobros, function(o) { return parseFloat(o.pivot.monto_pagado); });
     $scope.facturas.push({
       factura_id: value.id,
       codigo: value.codigo,
       fecha_emision: value.fecha_desde,
       fecha_finalizacion: value.fecha_hasta,
       monto: value.total,
       pagado: pagado,
       saldo_pendiente: parseFloat(value.total) - pagado,
       precio_total:0
     });
  });

    /* $scope.inputPago = data.total;
     if(data.total == pagado){
       angular.element("#guardarBtn").prop('disabled',true);
     }*/
     $timeout(function(){
        $(':input[data-inputmask]').inputmask();
      },500);
  };

  if($scope.vista ==='registrar_pago_cobro'){
    var cobro = serviceCobro.getCobro({uuid: uuid_cobro});
    $scope.empezarDesde(tipo);
    $scope.cobrosHeader.tipo = tipo;
    $scope.disableTipo = true;
    if(tipo ==='cliente'){
      $scope.llenarFormulario({uuid: uuid_cliente});
    }else if(tipo ==='factura'){
      $scope.llenarFormulario({uuid: uuid_factura});
    }else if(tipo ==='contrato_venta'){
      $scope.llenarFormulario({uuid: uuid_contrato});
    }
    cobro.then(function(data){
     //$scope.datosCobro.id = data.id;
     $scope.datosCobro.depositable_id = data.depositable_id.toString();
     $scope.disableCuenta = true;
     $scope.datosCobro.tipo_deposito = data.tipo_deposito;
    });
  }else if($scope.vista ==='ver'){
    var cobro = serviceCobro.getCobro({uuid: uuid_cobro});
    $scope.empezarDesde(tipo);
    $scope.cobrosHeader.tipo = tipo;
    $scope.disableTipo = true;
    $scope.disabled = true;
    if(tipo ==='cliente'){
      $scope.cobrosHeader.uuid = {uuid:uuid_cliente};
    }else if(tipo ==='factura'){
       $scope.cobrosHeader.uuid = {uuid:uuid_factura};
    } else if(tipo ==='contrato_venta'){
      $scope.cobrosHeader.uuid = {uuid:uuid_contrato};
    }
    //$scope.disabledEditar = true;
    cobro.then(function(data){

     $scope.datosCobro.clienteActual  = data.cliente_id.toString();
     $scope.datosCobro.id = data.id;
     $scope.datosCobro.estado = data.estado;
     $scope.datosCobro.fecha_pago = data.fecha_pago;
     $scope.datosCobro.monto = accounting.formatMoney(data.monto_pagado,'');
     $scope.datosCobro.total_pago =  data.monto_pagado;
     $scope.datosCobro.depositable_id = data.depositable_id.toString();
     $scope.datosCobro.tipo_deposito = data.tipo_deposito;
     $scope.disableCuenta = true;
     if($scope.datosCobro.tipo_deposito === 'caja'){
         $scope.depositable = window.caja;
     }

     if(data.estado ==='anulada'){
       $scope.disabledEditar = true;
     }

     $scope.facturas =[];
     var facturas = _.uniqBy( data.factura_cobros, 'id');
     angular.forEach(facturas,function(value, key){

     var pagado =  _.sumBy(data.cobros_facturas, function(o) {
       if(o.factura_id == value.id)
        return parseFloat(o.monto_pagado);
       });
      $scope.facturas.push({
        factura_id: value.id,
        codigo: value.codigo,
        fecha_emision: value.fecha_desde,
        fecha_finalizacion: value.fecha_hasta,
        monto: value.total,
        pagado: accounting.formatMoney(pagado,''),
        saldo_pendiente: parseFloat(value.total) - pagado,
        precio_total:''
      });
   });



     $scope.opcionPagos = [];
     angular.forEach(data.metodo_cobro,function(value, i){
       $scope.opcionPagos.push({icon: i === 0? 'fa fa-plus':'fa fa-trash', index:i,total_pagado:accounting.formatMoney(value.total_pagado,''), tipo_pago:value.tipo_pago});
     });
     $scope.metodollenar(data.metodo_cobro);
    });
  }

  $scope.metodollenar = function(metodo){
    $timeout(function(){
    angular.forEach(metodo,function(value, i){
      if(!_.isEmpty(value.referencia)){var referencia = $.parseJSON(value.referencia);
        if(value.tipo_pago === 'cheque'){
          angular.element('#numero_cheque'+i).val(referencia.numero_cheque);
          angular.element('#nombre_banco_cheque'+i).val(referencia.nombre_banco_cheque);
        }else if(value.tipo_pago === 'ach'){
          angular.element('#nombre_banco_ach'+i).val(referencia.nombre_banco_ach);
          angular.element('#cuenta_cliente'+i).val(referencia.cuenta_cliente);
        }else if(value.tipo_pago === 'tarjeta_de_credito'){
          //$('#numero_tarjeta'+i).val(referencia.numero_tarjeta);

          //angular.element('#numero_recibo'+i).val(referencia.numero_recibo);
        }
   }
  });

},2000);
  };

  $scope.focusPago = function(index){
    //console.log($scope.facturas[index].precio_total);
    if($scope.facturas[index].precio_total === 0){
      var saldo_pendiente = parseFloat($scope.facturas[index].saldo_pendiente);
      $scope.facturas[index].precio_total = accounting.toFixed(saldo_pendiente,2);
      $scope.datosCobro.monto = accounting.formatMoney((parseFloat($scope.datosCobro.monto) + saldo_pendiente),'');
      $scope.datosCobro.total_pago = accounting.formatMoney($scope.datosCobro.monto,'');
      $scope.opcionPagos[0].total_pagado = accounting.formatMoney($scope.datosCobro.monto,'');
    }
  };
  $scope.addRow = function(index){
    if($scope.cobrosHeader.tipo !=='cliente'){
    $scope.nextItem = $scope.nextItem + 1;
    $scope.opcionPagos.push({icon:'fa fa-trash', index:$scope.nextItem,total_pagado:'',tipo_pago:''});
    }
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
      var montoCredito = 0;
      if(cantidad >= parseFloat($scope.facturas[index].saldo_pendiente)){
        $scope.facturas[index].precio_total =  $scope.facturas[index].saldo_pendiente;
        montoCredito =  _.sumBy($scope.facturas, function(o) {
            return parseFloat(o.precio_total);
        });
        $scope.datosCobro.monto = accounting.formatMoney(montoCredito,'');
        $scope.datosCobro.total_pago = accounting.formatMoney(montoCredito,'');
        $scope.opcionPagos[0].total_pagado = accounting.formatMoney(montoCredito,'');
      }else if(accounting.toFixed(cantidad,2) != "0.00" && cantidad < parseFloat($scope.facturas[index].saldo_pendiente)){
        $scope.facturas[index].precio_total = cantidad;
        montoCredito =  _.sumBy($scope.facturas, function(o) {
            return parseFloat(o.precio_total);
        });
          $scope.datosCobro.monto = accounting.formatMoney(montoCredito,'');
          $scope.datosCobro.total_pago = accounting.formatMoney(montoCredito,'');
          $scope.opcionPagos[0].total_pagado = accounting.formatMoney(montoCredito,'');
      }else if(accounting.toFixed(cantidad,2) == "0.00"){
        $scope.facturas[index].precio_total =  $scope.facturas[index].saldo_pendiente;
        montoCredito =  _.sumBy($scope.facturas, function(o) {
            return parseFloat(o.precio_total);
        });
          $scope.datosCobro.monto = accounting.formatMoney(montoCredito,'');
          $scope.datosCobro.total_pago = accounting.formatMoney(montoCredito,'');
          $scope.opcionPagos[0].total_pagado = accounting.formatMoney(montoCredito,'');
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

    if($scope.datosCobro.total_pago !=  $scope.datosCobro.monto){
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
        if($scope.vista !='ver'){
         angular.element("#guardarBtn").prop("disabled",false);
        }
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
      dateFormat: 'dd/mm/yy',
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
        $("#cuenta").removeAttr("disabled");
        $("#guardarBtn").attr("disabled", true);
        form.submit();
      }
    });
    
    $(".moneda").inputmask('currency',{
      prefix: "",
      autoUnmask : true,
      removeMaskOnSubmit: true
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
