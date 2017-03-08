bluapp.controller("crearAbonoController", function($scope, $timeout,serviceAbono, $document){
  var model = this;
  $scope.tipo = '';
  $scope.acceso = acceso === 0? false : true;
  $scope.nextItem = 0;
  $scope.vista = vista;
  $scope.disabled = false;
  $scope.disableCuenta = false;
  $scope.disabledEditar = false;
  
    //campos de la cabecera del formualrio
    $scope.abonosHeader = {
        tipo:'proveedor',
        uuid:'',
        colleccion:[]
    };
    
    //campos del cuerpo principal de formulario
    $scope.datosAbono={
        proveedorActual:'',
        termino_abono:'',
        credito:'',
        saldo:'',
        fecha_abono: moment().format('DD-MM-YYYY'),
        comentario:'',
        estado:'',
        total_abono: 0,
        monto: 0,
        id:''
    };

    //valor desconocido
    $scope.opcionAbonos = [
        {icon:'fa fa-plus', index:0,total_abonado:'',tipo_abono:''}]
    ;

    //formulario para la creacion y edicion de abonos
    var objFrom = {
        abonoForm: $('#form_crear_abono')
    };

    
    $scope.empezarDesde = function(tipo){
        if(tipo !==''){
            if(tipo === 'proveedor'){
                //buscar solo los proveedores q tienen facturas
                var proveedores = serviceAbono.getProveedores({vista:$scope.vista});
                proveedores.then(function(data){
                    $scope.abonosHeader.collection = data;
                    $scope.abonosHeader.tipo = tipo;
                    
                    //indicar proveedor y popular sus datos
                    $scope.llenarFormulario({uuid:uuid_proveedor,nombre:''});
                });
            }
        }   
    };

    $scope.llenarFormulario = function(uuid){
        if(uuid !=='' && uuid !==null){
            $scope.abonosHeader.uuid = uuid;
            if($scope.abonosHeader.tipo === 'proveedor'){
                var proveedorInfo =  serviceAbono.proveedorInfo({uuid:uuid.uuid,vista:$scope.vista});
                proveedorInfo.then(function(data){
                    $scope.datosFormularioProveedor(data);
                });
            }
        }
    };

    $scope.datosFormulario = function(data){
        $scope.datosAbono.proveedorActual = data.proveedor_id.toString();
        $scope.datosAbono.saldo = data.proveedor.saldo_pendiente;//data.cliente.saldo_pendiente;
        $scope.datosAbono.credito = accounting.toFixed(data.proveedor.credito,2);//devoluciones, etc. por desarrollar
        if($scope.vista =='crear'){
            $scope.datosAbono.monto = 0;
            $scope.datosAbono.total_abonado = 0;
            $scope.opcionAbonos = [{icon:'fa fa-plus', index:0,total_abonado:'',tipo_abono:''}];
            //indico el banco del proveedor en caso de que indique "ACH" data.proveedor.id_banco
            $("#nombre_banco_ach0").val(data.proveedor.id_banco);
            $("#cuenta_proveedor0").val(data.proveedor.numero_cuenta);
        }
        var abonado =  _.sumBy(data.abonos, function(o) { return parseFloat(o.pivot.monto_abonado); });
        $scope.facturas = [{
            factura_id: data.id,
            codigo: data.codigo,
            fecha_emision: data.fecha_desde,
            fecha_finalizacion: data.fecha_hasta,
            monto: data.total,
            abonado: accounting.toFixed(abonado,2),
            saldo_pendiente: accounting.toFixed(parseFloat(data.total) - abonado,2),
            precio_total:0
        }];
        $scope.inputAbono = data.total;
        if(data.total == abonado){
            angular.element("#guardarBtn").prop('disabled',true);
        }
        $timeout(function(){
            $(':input[data-inputmask]').inputmask();
        },500);
    };

    $scope.datosFormularioProveedor = function(data){//desarrollador solo para la vista "Crear"
        $scope.datosAbono.proveedorActual = data.id.toString();
        $scope.datosAbono.credito = accounting.toFixed(data.credito,2);//devoluciones, etc. por desarrollar
        $scope.datosAbono.saldo = data.saldo_pendiente;
        if($scope.vista =='crear'){
            $scope.datosAbono.monto = 0;
            $scope.datosAbono.total_abonado = 0;
            $scope.opcionAbonos = [{icon:'fa fa-plus', index:0,total_abonado:'',tipo_abono:''}];
            
            $("#nombre_banco_ach0").val(data.id_banco);
            $("#cuenta_proveedor0").val(data.numero_cuenta);
        }
        
        $timeout(function(){
            $(':input[data-inputmask]').inputmask();
        },500);
    };

    if($scope.vista ==='ver'){
        
        var abono = serviceAbono.getAbono({uuid: uuid_abono});
        $scope.empezarDesde(tipo);
        $scope.abonosHeader.tipo = tipo;
        $scope.disableTipo = true;
        $scope.disabledEditar = true;
    
        if(tipo ==='proveedor'){
            $scope.abonosHeader.uuid = {uuid:uuid_proveedor};
        }else if(tipo ==='factura'){
            $scope.abonosHeader.uuid = {uuid: uuid_factura};
        }else if(tipo ==='planilla'){
            $scope.abonosHeader.uuid = {uuid: uuid_planilla};
        }
        
        abono.then(function(data){
            $scope.datosAbono.proveedorActual = data.proveedor_id.toString();
            $scope.datosAbono.id = data.id;
            $scope.datosAbono.estado = data.estado;
            $scope.datosAbono.monto = data.monto_abonado;
            $scope.datosAbono.total_abono = data.monto_abonado;
            $scope.datosAbono.cuenta_id = data.cuenta_id.toString();
            $scope.disableCuenta = true;
            
            //habilito la opcion de guardar sino esta anulado el abono -> vista:ver
            //solo se puede editar "el estado" del abono. Los otros valores no son
            //editables
            if(data.estado !== "anulado")
            {
                $scope.disabledEditar = false;
                if(data.metodo_abono[0].tipo_abono === "cheque")
                {
                    $scope.disabledEditar = true;
                }
            }
            

            $scope.opcionAbonos = [];
            angular.forEach(data.metodo_abono,function(value, i){
                $scope.opcionAbonos.push({icon: i === 0? 'fa fa-plus':'fa fa-trash', index:i,total_abonado:value.total_abonado, tipo_abono:value.tipo_abono});
            });
            $scope.metodollenar(data.metodo_abono);
        });
    }

    $scope.metodollenar = function(metodo){
        $timeout(function(){
            angular.forEach(metodo,function(value, i){
                if(!_.isEmpty(value.referencia)){
                    var referencia = $.parseJSON(value.referencia);
                    if(value.tipo_abono === 'cheque'){
                        angular.element('#numero_cheque'+i).val(referencia.numero_cheque);
                        angular.element('#nombre_banco_cheque'+i).val(referencia.nombre_banco_cheque);
                    }else if(value.tipo_abono === 'ach'){
                        angular.element('#nombre_banco_ach'+i).val(referencia.nombre_banco_ach);
                        angular.element('#cuenta_proveedor'+i).val(referencia.cuenta_proveedor);
                    }else if(value.tipo_abono === 'tarjeta_de_credito'){
                        $('#numero_tarjeta'+i).val(referencia.numero_tarjeta);
                        angular.element('#numero_recibo'+i).val(referencia.numero_recibo);
                    }
                }
            });
        },2000);
    };

    $scope.focusAbono = function(index){
        if($scope.facturas[index].precio_total == 0){
            var saldo_pendiente = parseFloat($scope.facturas[index].saldo_pendiente);
            $scope.facturas[index].precio_total = accounting.toFixed(saldo_pendiente,2);
            $scope.datosAbono.monto =  accounting.toFixed((parseFloat($scope.datosAbono.monto) + saldo_pendiente),2);
        }
    };
    $scope.addRow = function(index){
        if($scope.abonosHeader.tipo !=='proveedor'){
            $scope.nextItem = $scope.nextItem + 1;
            $scope.opcionAbonos.push({icon:'fa fa-trash', index:$scope.nextItem,total_abonado:'',tipo_abono:''});
        }
    };
    $scope.deleteRow = function(index){
        if(!angular.isUndefined($scope.opcionAbonos[index])){
            $scope.nextItem = $scope.nextItem - 1;
            $scope.opcionAbonos.splice(index, 1);
        }
    };
    $scope.selecioneAbono = function(index,abono){
        if(abono !==''){
            $scope.opcionAbonos[index].tipo_abono = abono;
        }
    };
    $scope.abonoClass = function(index){
        return $scope.opcionAbonos[index].tipo_abono === ''? 'hide':'show';
    };

    $scope.sumaTotales = function(index){//no toma en cuenta si se paga desde el credito
        var total = 0;
        angular.forEach($scope.opcionAbonos,function(value, key){
            total += parseFloat(value.total_abonado);
        });
        $scope.datosAbono.total_abono= accounting.toFixed(total,2);
        if(parseFloat($scope.datosAbono.total_abono) !=  accounting.toFixed($scope.datosAbono.monto,2)){
            angular.element("#totals-error").empty().html('El total debe ser igual al monto');
            angular.element("#totals-error").css('display','block');
            angular.element("#guardarBtn").prop("disabled",true);
        }else if($scope.datosAbono.total_abono ==  accounting.toFixed($scope.datosAbono.monto,2)){
            angular.element("#totals-error").empty();
            angular.element("#guardarBtn").prop("disabled",false);
        }

        //suma total de credito
        var montoCredito =  _.sumBy($scope.opcionAbonos, function(o) {
            if(o.tipo_abono ==='aplicar_credito')  return parseFloat(o.total_abonado);
        });
        if($scope.opcionAbonos[index].tipo_abono ==='aplicar_credito' && parseFloat(montoCredito) > parseFloat($scope.datosAbono.credito)){
            angular.element("#totals-error").empty().html('El total no puede ser mayor al credito');
            angular.element("#totals-error").css('display','block');
            angular.element("#guardarBtn").prop("disabled",true);
        }else if($scope.opcionAbonos[index].tipo_abono ==='aplicar_credito' && parseFloat(montoCredito) < parseFloat($scope.datosAbono.credito)){
            angular.element("#totals-error").empty();
            angular.element("#guardarBtn").prop("disabled",false);
        }
    };
    
  $scope.$watch("datosAbono.monto", function(newValue, oldValue) {
    if ($scope.datosAbono.total_abono ===  $scope.datosAbono.monto) {
        angular.element("#totals-error").empty();
        if($scope.vista !='ver'){
         angular.element("#guardarBtn").prop("disabled",false);
        }
    }
  });
  $scope.setCredito = function(){

  };
  $scope.disabledButton = function(){
      return $scope.facturas[0].monto === $scope.facturas[0].abonado? true : false;
  };
  
    $scope.init = function(){
        $scope.empezarDesde($scope.abonosHeader.tipo);
      
    angular.element("#fecha_abono").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        numberOfMonths: 1,
        defaultDate: moment().format('DD-MM-YYYY')
    });

    objFrom.abonoForm.validate({
      ignore: '',
      wrapper: '',
      submitHandler: function(form) {
        // do other things for a valid form desabilitar boton guardar
        $("#proveedor").removeAttr("disabled");
        $("#total_abono").removeAttr("disabled");
        $("#cuenta").removeAttr("disabled");
        form.submit();
      }
    });


  };

$scope.init();

});
