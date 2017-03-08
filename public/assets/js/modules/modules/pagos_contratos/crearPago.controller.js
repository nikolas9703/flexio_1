bluapp.controller("crearPagoController", function($scope, $timeout,servicePago, $document){
  
	var model = this;
        $scope.tipo = '';
        $scope.acceso = acceso === 0? false : true;
        $scope.nextItem = 0;
        $scope.vista = vista;
        $scope.disabled = false;
        $scope.disableCuenta = false;
        $scope.disabledEditar = false;
        $scope.cajasList = $.parseJSON(cajasList);
  
    //campos de la cabecera del formualrio
    $scope.pagosHeader = {
        tipo:'',
        uuid:'',
        colleccion:[]
    };
    
    
    
    //campos del cuerpo principal de formulario
    $scope.datosPago={
        proveedorActual:'',
        termino_pago:'',
        credito:'',
        saldo:'',
        fecha_pago: moment().format('DD-MM-YYYY'),
        comentario:'',
        estado:'',
        total_pago: 0,
        monto: 0,
        id:''
    };

    //valor desconocido
    $scope.opcionPagos = [
        {icon:'fa fa-plus', index:0,total_pagado:'',tipo_pago:''}]
    ;

    //formulario para la creacion y edicion de pagos
    var objFrom = {
        pagoForm: $('#form_crear_pago')
    };

    
    $scope.empezarDesde = function(tipo){
        if(tipo !==''){
            if(tipo === 'factura'){
                //buscar las facturas
                var facturas = servicePago.getFacturas({vista:$scope.vista});
                facturas.then(function(data){
                    $scope.pagosHeader.collection = data;
                    $scope.pagosHeader.tipo = tipo;
                    if(factura_compra_uuid.length > 0)
                    {
                        $scope.llenarFormulario({uuid:factura_compra_uuid});
                    }
                });
            }else if(tipo === 'proveedor'){
                //buscar solo los proveedores q tienen facturas
                var proveedores = servicePago.getProveedores({vista:$scope.vista});
                proveedores.then(function(data){
                    $scope.pagosHeader.collection = data;
                    $scope.pagosHeader.tipo = tipo;
                    if(proveedor_id.length)
                    {
                        $scope.llenarFormulario({uuid:proveedor_id});
                    }
                });
            }else if(tipo === 'subcontrato'){
                //buscar solo subcontratos con facturas por pagar o pagadas parcial
                var subcontratos = servicePago.getSubcontratos({vista:$scope.vista});
                subcontratos.then(function(data){
                    $scope.pagosHeader.collection = data;
                    $scope.pagosHeader.tipo = tipo;
                });
            }else if(tipo === 'planilla'){
                alert("Esta funcionalidad esta en desarrollo");
                return;
                //buscar solo los clientes q tienen facturas
                var planilla = serviceCobro.getPlanilla({vista:$scope.vista});
                cliente.then(function(data){
                    $scope.pagosHeader.collection = data;
                    $scope.pagosHeader.tipo = tipo;
                });
            }
        }   
    };
    
    
    

    $scope.llenarFormulario = function(uuid){
    
        if(uuid !=='' && uuid !==null){
            $scope.pagosHeader.uuid = uuid;
            if($scope.pagosHeader.tipo === 'factura'){
                var facturaInfo =  servicePago.getInfoFactura({uuid:uuid.uuid});
                facturaInfo.then(function(data){
                    $scope.datosFormulario(data);
                });
            }else if($scope.pagosHeader.tipo === 'proveedor'){//por desarrollar
                var proveedorInfo =  servicePago.proveedorFacturas({uuid:uuid.uuid,vista:$scope.vista});
                proveedorInfo.then(function(data){
                    $scope.datosFormularioProveedor(data);
                });
            }else if($scope.pagosHeader.tipo === 'subcontrato'){
                var subcontratoInfo =  servicePago.subcontratoFacturas({uuid:uuid.uuid,vista:$scope.vista});
                subcontratoInfo.then(function(data){
                    $scope.datosFormularioProveedor(data);
                });
            }else if($scope.pagosHeader.tipo === 'planilla'){
                alert("por desarrollar");
                return;
                var proveedorInfo =  servicePago.proveedorFacturas({uuid:uuid.uuid,vista:$scope.vista});
                proveedorInfo.then(function(data){
                    $scope.datosFormularioProveedor(data);
                });
            }
        }
    };

    $scope.datosFormulario = function(data){
        $scope.datosPago.proveedorActual = data.proveedor_id.toString();
        $scope.datosPago.saldo = data.proveedor.saldo_pendiente;//data.cliente.saldo_pendiente;
        $scope.datosPago.credito = data.proveedor.credito;//devoluciones, etc. por desarrollar
        if($scope.vista =='crear'){
            $scope.datosPago.monto = 0;
            $scope.datosPago.total_pagado = 0;
            $scope.opcionPagos = [{icon:'fa fa-plus', index:0,total_pagado:'',tipo_pago:''}];
            //indico el banco del proveedor en caso de que indique "ACH" data.proveedor.id_banco
            $("#nombre_banco_ach0").val(data.proveedor.id_banco);
            $("#cuenta_proveedor0").val(data.proveedor.numero_cuenta);
        }
        var pagado =  _.sumBy(data.pagos, function(o) { return parseFloat(o.pivot.monto_pagado); });
        $scope.facturas = [{
            factura_id: data.id,
            codigo: data.codigo,
            fecha_emision: data.fecha_desde,
            fecha_finalizacion: data.fecha_hasta,
            monto: data.total,
            pagado: accounting.toFixed(pagado,2),
            saldo_pendiente: accounting.toFixed(parseFloat(data.total) - pagado,2),
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

    $scope.datosFormularioProveedor = function(data){//desarrollador solo para la vista "Crear"
        $scope.datosPago.proveedorActual = data.id.toString();
        $scope.datosPago.credito = data.credito;//devoluciones, etc. por desarrollar
        $scope.datosPago.saldo = data.saldo_pendiente;
        if($scope.vista =='crear'){
            $scope.datosPago.monto = 0;
            $scope.datosPago.total_pagado = 0;
            $scope.opcionPagos = [{icon:'fa fa-plus', index:0,total_pagado:'',tipo_pago:''}];
            
            $("#nombre_banco_ach0").val(data.id_banco);
            $("#cuenta_proveedor0").val(data.numero_cuenta);
        }
        $scope.facturas =[];
        var facturas = [];
        if($scope.vista =='crear'){
            facturas = data.facturas_crear;
        }else if($scope.vista =='registrar_pago_cobro'){
            facturas = data.facturas_habilitadas;
        }else{
            facturas = data.facturas_no_anuladas;
        }
        
        angular.forEach(facturas,function(value, key){
            var pagado =  _.sumBy(value.pagos, function(o) { return parseFloat(o.pivot.monto_pagado); });
            $scope.facturas.push({
                factura_id: value.id,
                codigo: value.codigo,
                fecha_emision: value.fecha_desde,
                fecha_finalizacion: value.fecha_hasta,
                monto: value.total,
                pagado: accounting.toFixed(pagado,2),
                saldo_pendiente: accounting.toFixed(parseFloat(value.total) - pagado,2),
                precio_total:''
            });
        });
        
        $timeout(function(){
            $(':input[data-inputmask]').inputmask();
        },500);
    };
    
    //EN CASO DE QUE TENGA EL PROVEEDOR LO BUSCO Y LO SELECCIONO.
    if(proveedor_id.length)
    {
        $scope.empezarDesde("proveedor");
    }

  if($scope.vista ==='registrar_pago_cobro'){
    var cobro = serviceCobro.getCobro({uuid: uuid_pago});
    $scope.empezarDesde(tipo);
    $scope.pagosHeader.tipo = tipo;
    $scope.disableTipo = true;
    if(tipo ==='cliente'){
      $scope.llenarFormulario({uuid: uuid_proveedor});
    }else if(tipo ==='factura'){
      $scope.llenarFormulario({uuid: uuid_factura});
    }
    cobro.then(function(data){
     $scope.datosPago.id = data.id;
     $scope.datosPago.cuenta_id = data.cuenta_id.toString();
     $scope.disableCuenta = true;
    });
    }
    else if($scope.vista ==='ver'){
        
        var pago = servicePago.getPago({uuid: uuid_pago});
        $scope.empezarDesde(tipo);
        $scope.pagosHeader.tipo = tipo;
        $scope.disableTipo = true;
        $scope.disabledEditar = true;
    
        if(tipo ==='proveedor'){
            $scope.pagosHeader.uuid = {uuid:uuid_proveedor};
        }else if(tipo ==='factura'){
            $scope.pagosHeader.uuid = {uuid: uuid_factura};
        }else if(tipo ==='planilla'){
            $scope.pagosHeader.uuid = {uuid: uuid_planilla};
        }else if(tipo ==='subcontrato'){
            $scope.pagosHeader.uuid = {uuid: uuid_subcontrato};
        }
        
        pago.then(function(data){
            $scope.datosPago.proveedorActual = data.proveedor_id.toString();
            $scope.datosPago.id = data.id;
            $scope.datosPago.estado = data.estado;
            $scope.datosPago.monto = data.monto_pagado;
            $scope.datosPago.total_pago = data.monto_pagado;
            $scope.datosPago.cuenta_id = data.cuenta_id.toString();
            $scope.disableCuenta = true;
            
            //habilito la opcion de guardar sino esta anulado el pago -> vista:ver
            //solo se puede editar "el estado" del pago. Los otros valores no son
            //editables
            if(data.estado !== "anulado")
            {
                $scope.disabledEditar = false;
                if(data.metodo_pago[0].tipo_pago === "cheque")
                {
                    $scope.disabledEditar = true;
                }
            }
            

            $scope.facturas = [];
            var aux = (tipo === 'planilla') ? data.planillas : data.facturas;
            var facturas = _.uniqBy( aux, 'id');
            angular.forEach(facturas,function(value, key){

                var pagado =  _.sumBy(value.pagos, function(o) {
                    return parseFloat(o.pivot.monto_pagado);//solo los pagos aplicados
                });
                $scope.facturas.push({
                    factura_id: value.id,
                    codigo: value.codigo,
                    fecha_emision: value.fecha_desde,
                    fecha_finalizacion: value.fecha_hasta,
                    monto: value.total,
                    pagado: accounting.toFixed(pagado,2),
                    saldo_pendiente: accounting.toFixed(parseFloat(value.total) - pagado,2),
                    precio_total:''
                });
            });

            $scope.opcionPagos = [];
            angular.forEach(data.metodo_pago,function(value, i){
                $scope.opcionPagos.push({icon: i === 0? 'fa fa-plus':'fa fa-trash', index:i,total_pagado:value.total_pagado, tipo_pago:value.tipo_pago});
            });
            $scope.metodollenar(data.metodo_pago);
        });
    }

    $scope.metodollenar = function(metodo){
        $timeout(function(){
            angular.forEach(metodo,function(value, i){
                if(!_.isEmpty(value.referencia)){
                    var referencia = $.parseJSON(value.referencia);
                    if(value.tipo_pago === 'cheque'){
                        angular.element('#numero_cheque'+i).val(referencia.numero_cheque);
                        angular.element('#nombre_banco_cheque'+i).val(referencia.nombre_banco_cheque);
                    }else if(value.tipo_pago === 'ach'){
                        angular.element('#nombre_banco_ach'+i).val(referencia.nombre_banco_ach);
                        angular.element('#cuenta_proveedor'+i).val(referencia.cuenta_proveedor);
                    }else if(value.tipo_pago === 'tarjeta_de_credito'){
                        $('#numero_tarjeta'+i).val(referencia.numero_tarjeta);
                        angular.element('#numero_recibo'+i).val(referencia.numero_recibo);
                    } else if(value.tipo_pago === 'caja_chica'){
                        $('#caja_id'+i).val(referencia.caja_id);
                        angular.element('#caja_id'+i).val(referencia.caja_id);
                    }
                }
            });
        },2000);
    };

    $scope.focusPago = function(index){
        if($scope.facturas[index].precio_total == 0){
            var saldo_pendiente = parseFloat($scope.facturas[index].saldo_pendiente);
            $scope.facturas[index].precio_total = accounting.toFixed(saldo_pendiente,2);
            $scope.datosPago.monto =  accounting.toFixed((parseFloat($scope.datosPago.monto) + saldo_pendiente),2);
        }
    };
    $scope.addRow = function(index){
        if($scope.pagosHeader.tipo !=='proveedor'){
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
                    return parseFloat(o.precio_total) || 0;
                });
                $scope.datosPago.monto = accounting.toFixed(montoCredito,2);
            }else if(accounting.toFixed(cantidad,2) != "0.00" && cantidad < parseFloat($scope.facturas[index].saldo_pendiente)){
                $scope.facturas[index].precio_total = cantidad;
                montoCredito =  _.sumBy($scope.facturas, function(o) {
                    return parseFloat(o.precio_total) || 0;
                });
                $scope.datosPago.monto = accounting.toFixed(montoCredito,2);
            }else if(accounting.toFixed(cantidad,2) == "0.00"){
                $scope.facturas[index].precio_total =  $scope.facturas[index].saldo_pendiente;
                montoCredito =  _.sumBy($scope.facturas, function(o) {
                    return parseFloat(o.precio_total) || 0;
                });
                $scope.datosPago.monto = accounting.toFixed(montoCredito,2);
            }
        }
    };
  
    $scope.sumaTotales = function(index){//no toma en cuenta si se paga desde el credito
        var total = 0;
        angular.forEach($scope.opcionPagos,function(value, key){
            total += parseFloat(value.total_pagado);
        });
        $scope.datosPago.total_pago= accounting.toFixed(total,2);
        if($scope.datosPago.total_pago !=  $scope.datosPago.monto){
            angular.element("#totals-error").empty().html('El total debe ser igual al monto');
            angular.element("#totals-error").css('display','block');
            angular.element("#guardarBtn").prop("disabled",true);
        }else if($scope.datosPago.total_pago ==  $scope.datosPago.monto){
            angular.element("#totals-error").empty();
            angular.element("#guardarBtn").prop("disabled",false);
        }

        //suma total de credito
        var montoCredito =  _.sumBy($scope.opcionPagos, function(o) {
            if(o.tipo_pago ==='aplicar_credito')  return parseFloat(o.total_pagado);
        });
        if($scope.opcionPagos[index].tipo_pago ==='aplicar_credito' && parseFloat(montoCredito) > parseFloat($scope.datosPago.credito)){
            angular.element("#totals-error").empty().html('El total no puede ser mayor al credito');
            angular.element("#totals-error").css('display','block');
            angular.element("#guardarBtn").prop("disabled",true);
        }else if($scope.opcionPagos[index].tipo_pago ==='aplicar_credito' && parseFloat(montoCredito) < parseFloat($scope.datosPago.credito)){
            angular.element("#totals-error").empty();
            angular.element("#guardarBtn").prop("disabled",false);
        }
    };
    
  $scope.$watch("datosPago.monto", function(newValue, oldValue) {
    if ($scope.datosPago.total_pago ===  $scope.datosPago.monto) {
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
  
    $scope.init = function(){
        angular.element("#fecha_pago").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            numberOfMonths: 1,
            defaultDate: moment().format('DD-MM-YYYY')
        });

        objFrom.pagoForm.validate({
            ignore: '',
            wrapper: '',
            submitHandler: function(form) {
                // do other things for a valid form desabilitar boton guardar
                $("#proveedor").removeAttr("disabled");
                $("#total_pago").removeAttr("disabled");
                $("#cuenta").removeAttr("disabled");
                $("input, select").removeAttr("disabled");
                form.submit();
            }
        });

        if(factura_compra_uuid.length > 0)
        {
            $scope.pagosHeader.tipo = 'factura';
            $scope.empezarDesde($scope.pagosHeader.tipo);
        }

    };

$scope.init();

});
