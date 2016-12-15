bluapp.controller("crearChequeController", function($scope, $timeout,serviceCheque, $document){

	var model = this;
  $scope.tipo = '';
  $scope.acceso = acceso === 0? false : true;
  $scope.nextItem = 0;
  $scope.vista = vista;
  $scope.disabled = false;
  $scope.disableCuenta = false;
  $scope.disabledEditar = false;
  $scope.habilitar_formulario = habilitar_formulario;

    //campos de la cabecera del formualrio
    $scope.pagosHeader = {
        tipo:'',
        uuid:''
    };

    //campos de la cabecera del formualrio
    $scope.chequesHeader = {
        tipo:'',
        uuid:'',
        colleccion:[]
    };

    //campos del cuerpo principal de formulario
    $scope.datosCheque={
        proveedorActual:'',
        termino_cheque:'',
        chequera_id:'',
        credito:'',
        saldo:'',
        fecha_cheque: moment().format('DD-MM-YYYY'),
        comentario:'',
        estado:'',
        total_cheque: 0,
        monto: 0,
        tipo_pago: "cheque",
        id:''
    };

    //valor desconocido
    $scope.opcionCheques = [
        {icon:'fa fa-plus', index:0,total_pagado:'',tipo_cheque:'cheque'}]
    ;

    //formulario para la creacion y edicion de cheques
    var objFrom = {
        chequeForm: $('#form_crear_cheque')
    };

    $scope.empezarDesde = function(tipo){
        if(tipo !==''){
            if(tipo === 'pago'){
                //buscar las facturas
                var pagos = serviceCheque.getPagos({vista:$scope.vista});
                pagos.then(function(data){
                    $scope.chequesHeader.collection = data;
                    $scope.chequesHeader.tipo = tipo;

                    if(pago_uuid.length)
                    {
                        $scope.chequesHeader.uuid = pago_uuid;
                        $scope.llenarFormulario({uuid:pago_uuid});
                    }
                });
            }
        }
    };

    $scope.proximoCheque = function(chequera_uuid){
        if(chequera_uuid !==''){
            var chequera = serviceCheque.getChequera({chequera_uuid:chequera_uuid});
            chequera.then(function(data){
                $scope.datosCheque.numero_cheque = data.proximo_cheque;
            });
        }
    };

    $scope.llenarFormulario = function(uuid){
        if(uuid !=='' && uuid !==null){
            $scope.chequesHeader.uuid = uuid;
            if($scope.chequesHeader.tipo === 'pago'){
                var proveedorInfo = typeof uuid != 'undefined' ? serviceCheque.proveedorPago({uuid:uuid.uuid,vista:$scope.vista}) : '';
                proveedorInfo.then(function(data){
                    $scope.datosFormularioProveedor(data);
                });
                var pagoInfo =  serviceCheque.getInfoPago({uuid:uuid.uuid});
                pagoInfo.then(function(data){
                    $scope.datosFormularioCheque(data);
                });
            }
        }
    };




    $scope.datosFormulario = function(data){
        $scope.datosCheque.proveedorActual = data.proveedor_id.toString();
        $scope.datosCheque.saldo = data.proveedor_cheque.saldo_pendiente;//data.cliente.saldo_pendiente;
        $scope.datosCheque.credito = data.proveedor_cheque.credito;//devoluciones, etc. por desarrollar
        $scope.datosCheque.monto_pagado = data.monto_pagado;

        if($scope.vista =='crear'){
            $scope.datosCheque.monto = 0;
            $scope.datosCheque.fecha_pago = data.fecha_pago;
            $scope.datosCheque.proveedorActual = data.proveedor_id.toString();
            $scope.datosCheque.total_pagado = 0;
            $scope.opcionCheques = [{icon:'fa fa-plus', index:0,total_pagado:'',tipo_cheque:'cheque'}];
            //indico el banco del proveedor en caso de que indique "ACH" data.proveedor.id_banco
            //$("#nombre_banco_ach0").val(data.proveedor.id_banco);
            //$("#cuenta_proveedor0").val(data.proveedor.numero_cuenta);
        }
        //var pagado =  _.sumBy(data, function(o) { return parseFloat(o.monto_pagado); });
        var pagado =  parseFloat(data.monto_pagado);
        $scope.facturas = [{
            factura_id: data.id,
            codigo: data.codigo,
            fecha_emision: data.fecha_pago,
            monto: data.total.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"),
            pagado: accounting.toFixed(pagado,2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"),
            saldo_pendiente: accounting.toFixed(parseFloat(data.total) - pagado,2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"),
            precio_total:0
        }];
        $scope.inputCheque = data.total;
        if(data.total == pagado){
            angular.element("#guardarBtn").prop('disabled',true);
        }

    };

    $scope.datosFormularioCheque = function(data){
        $scope.datosCheque.monto_pagado = data.monto_pagado;
        if($scope.vista =='crear'){
            $scope.datosCheque.monto = 0;
            $scope.datosCheque.fecha_pago = data.fecha_pago;
            $scope.datosCheque.total_pagado = 0;
            $scope.opcionCheques = [{icon:'fa fa-plus', index:0,total_pagado:'',tipo_cheque:'cheque'}];
        }
        //var pagado =  _.sumBy(data, function(o) { return parseFloat(o.monto_pagado); });
        $scope.datosCheque.tipo_pago="cheque";

        var pagado =  parseFloat(data.monto_pagado);
        $scope.facturas = [{
            factura_id: data.id,
            codigo: data.codigo,
            fecha_emision: data.fecha_pago,
            monto: typeof data.total != 'undefined' ? data.total.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") : 0,
            pagado: typeof data.monto_pagado != 'undefined' ?  accounting.toFixed(data.monto_pagado,2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") : 0,
            saldo_pendiente: typeof data.total != 'undefined' ?  accounting.toFixed(parseFloat(data.total) - pagado,2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") : 0,
            precio_total:0
        }];
        $scope.inputCheque = data.total;
        if(data.total == pagado){
            angular.element("#guardarBtn").prop('disabled',true);
        }

    };

    $scope.datosFormularioProveedor = function(data){//desarrollador solo para la vista "Crear"

        $scope.datosCheque.proveedorActual = data.id.toString();
        $scope.datosCheque.credito = data.credito;//devoluciones, etc. por desarrollar
        $scope.datosCheque.saldo = data.saldo_pendiente;
        if($scope.vista =='crear'){
            $scope.datosCheque.monto = 0;
            $scope.datosCheque.total_pagado = 0;
            $scope.opcionCheques = [{icon:'fa fa-plus', index:0,total_pagado:'',tipo_cheque:'cheque'}];

            $("#nombre_banco_ach0").val(data.id_banco);
            $("#cuenta_proveedor0").val(data.numero_cuenta);
        }
        $scope.facturas =[];
        var facturas = [];
        if($scope.vista =='crear'){
            facturas = data.facturas_crear;
        }else if($scope.vista =='registrar_cheque_cobro'){
            facturas = data.facturas_habilitadas;
        }else{
            facturas = data.facturas_no_anuladas;
        }

        angular.forEach(facturas,function(value, key){
            var pagado =  _.sumBy(value.cheques, function(o) { return parseFloat(o.pivot.monto_pagado); });
            $scope.facturas.push({
                factura_id: value.id,
                codigo: value.codigo,
                fecha_emision: value.fecha_desde,
                monto: typeof value.total != 'undefined' ?  parseFloat(value.total) : 0,
                pagado: typeof pagado != 'undefined' ?  accounting.toFixed(pagado,2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") : 0,
                saldo_pendiente: typeof value.total != 'undefined' ?  accounting.toFixed(parseFloat(value.total) - pagado,2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") : 0,
                precio_total:''
            });
        });


    };

  if($scope.vista ==='registrar_cheque_cobro'){
    var cobro = serviceCobro.getCobro({uuid: uuid_cheque});
    $scope.empezarDesde(tipo);
    $scope.chequesHeader.tipo = tipo;
    $scope.disableTipo = true;
    if(tipo ==='cliente'){
      $scope.llenarFormulario({uuid: uuid_proveedor});
    }else if(tipo ==='factura'){
      $scope.llenarFormulario({uuid: uuid_factura});
    }
    cobro.then(function(data){
     $scope.datosCheque.id = data.id;
     $scope.datosCheque.cuenta_id = data.cuenta_id.toString();
     $scope.disableCuenta = true;
    });
    }

    else if($scope.vista ==='ver'){
        $scope.chequesHeader.tipo = 'pago';
        $scope.empezarDesde($scope.chequesHeader.tipo);
        $scope.chequesHeader.uuid = {uuid:cheque.pago.uuid_pago};

        $scope.disableTipo = true;
				$scope.disabledEditar = true;
 				if($scope.habilitar_formulario == 'si'){
					$scope.disabledEditar = false;
				}

        $scope.disableCuenta = true;

 				var var_uuid_chequera = ''; //Se ha creado esta condicion por que ahora se puede generar cheque sin chequera.
 				if (cheque.chequera !== null) {
						  var_uuid_chequera = cheque.chequera.uuid_chequera.toLowerCase()
				}

        $scope.datosCheque = {
            id:cheque.id,
            fecha_pago:cheque.fecha_cheque,
            proveedorActual:cheque.pago.proveedor_id.toString(),
            saldo:accounting.toFixed(parseFloat(cheque.pago.proveedor.saldo_pendiente),2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"),
            credito:accounting.toFixed(parseFloat(cheque.pago.proveedor.credito),2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"),
            monto_pagado:cheque.monto.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"),
            chequera_id:var_uuid_chequera,
            numero_cheque:cheque.numero
        };

        $scope.facturas = [
            {
                factura_id: cheque.pago.id,
                codigo: cheque.pago.codigo,
                fecha_emision: cheque.pago.created_at,
                pagado: cheque.pago.monto_pagado.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
            }
        ];

        $scope.opcionCheques = [
            {
                icon:'fa fa-plus',
                index:0,
                total_pagado:$scope.datosCheque.monto_pagado,
                tipo_cheque:'cheque'
            }
        ];
    }

    $scope.metodollenar = function(metodo){
        $timeout(function(){
            angular.forEach(metodo,function(value, i){
                if(!_.isEmpty(value.referencia)){
                    var referencia = $.parseJSON(value.referencia);
                    if(value.tipo_cheque === 'cheque'){
                        angular.element('#numero_cheque'+i).val(referencia.numero_cheque);
                        angular.element('#nombre_banco_cheque'+i).val(referencia.nombre_banco_cheque);
                    }else if(value.tipo_cheque === 'ach'){
                        angular.element('#nombre_banco_ach'+i).val(referencia.nombre_banco_ach);
                        angular.element('#cuenta_proveedor'+i).val(referencia.cuenta_proveedor);
                    }else if(value.tipo_cheque === 'tarjeta_de_credito'){
                        $('#numero_tarjeta'+i).val(referencia.numero_tarjeta);
                        angular.element('#numero_recibo'+i).val(referencia.numero_recibo);
                    } else if(value.tipo_cheque === 'caja_chica'){
                        $('#caja_id'+i).val(referencia.caja_id);
                        angular.element('#caja_id'+i).val(referencia.caja_id);
                    }
                }
            });
        },2000);
    };

    $scope.focusCheque = function(index){
        if($scope.facturas[index].precio_total == 0){
            var saldo_pendiente = parseFloat($scope.facturas[index].saldo_pendiente);
            $scope.facturas[index].precio_total = accounting.toFixed(saldo_pendiente,2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
            $scope.datosCheque.monto =  accounting.toFixed((parseFloat($scope.datosCheque.monto) + saldo_pendiente),2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        }
    };
    $scope.addRow = function(index){
        if($scope.chequesHeader.tipo !=='proveedor'){
            $scope.nextItem = $scope.nextItem + 1;
            $scope.opcionCheques.push({icon:'fa fa-trash', index:$scope.nextItem,total_pagado:'',tipo_cheque:'cheque'});
        }
    };
    $scope.deleteRow = function(index){
        if(!angular.isUndefined($scope.opcionCheques[index])){
            $scope.nextItem = $scope.nextItem - 1;
            $scope.opcionCheques.splice(index, 1);
        }
    };
    $scope.selecioneCheque = function(index,cheque){
        if(cheque !==''){
            $scope.opcionCheques[index].tipo_cheque = cheque;
        }
    };
    $scope.chequeClass = function(index){
    	return $scope.opcionCheques[index].tipo_cheque === ''? 'hide':'show';
    };

    $scope.cambiarCantidad = function(index, cantidad){
        if(cantidad !==''){
            var montoCredito = 0;
            if(cantidad >= parseFloat($scope.facturas[index].saldo_pendiente)){
                $scope.facturas[index].precio_total =  $scope.facturas[index].saldo_pendiente;
                montoCredito =  _.sumBy($scope.facturas, function(o) {
                    return parseFloat(o.precio_total) || 0;
                });
                $scope.datosCheque.monto = accounting.toFixed(montoCredito,2);
            }else if(accounting.toFixed(cantidad,2) != "0.00" && cantidad < parseFloat($scope.facturas[index].saldo_pendiente)){
                $scope.facturas[index].precio_total = cantidad;
                montoCredito =  _.sumBy($scope.facturas, function(o) {
                    return parseFloat(o.precio_total) || 0;
                });
                $scope.datosCheque.monto = accounting.toFixed(montoCredito,2);
            }else if(accounting.toFixed(cantidad,2) == "0.00"){
                $scope.facturas[index].precio_total =  $scope.facturas[index].saldo_pendiente;
                montoCredito =  _.sumBy($scope.facturas, function(o) {
                    return parseFloat(o.precio_total) || 0;
                });
                $scope.datosCheque.monto = accounting.toFixed(montoCredito,2);
            }
        }
    };

    $scope.sumaTotales = function(index){//no toma en cuenta si se paga desde el credito
        var total = 0;
        angular.forEach($scope.opcionCheques,function(value, key){
            total += parseFloat(value.total_pagado);
        });
        $scope.datosCheque.total_cheque= accounting.toFixed(total,2);
        if($scope.datosCheque.total_cheque !=  $scope.datosCheque.monto){
            angular.element("#totals-error").empty().html('El total debe ser igual al monto');
            angular.element("#totals-error").css('display','block');
            angular.element("#guardarBtn").prop("disabled",true);
        }else if($scope.datosCheque.total_cheque ==  $scope.datosCheque.monto){
            angular.element("#totals-error").empty();
            angular.element("#guardarBtn").prop("disabled",false);
        }

        //suma total de credito
        var montoCredito =  _.sumBy($scope.opcionCheques, function(o) {
            if(o.tipo_cheque ==='aplicar_credito')  return parseFloat(o.total_pagado);
        });
        if($scope.opcionCheques[index].tipo_cheque ==='aplicar_credito' && parseFloat(montoCredito) > parseFloat($scope.datosCheque.credito)){
            angular.element("#totals-error").empty().html('El total no puede ser mayor al credito');
            angular.element("#totals-error").css('display','block');
            angular.element("#guardarBtn").prop("disabled",true);
        }else if($scope.opcionCheques[index].tipo_cheque ==='aplicar_credito' && parseFloat(montoCredito) < parseFloat($scope.datosCheque.credito)){
            angular.element("#totals-error").empty();
            angular.element("#guardarBtn").prop("disabled",false);
        }
    };

  $scope.$watch("datosCheque.monto", function(newValue, oldValue) {
    if ($scope.datosCheque.total_cheque ===  $scope.datosCheque.monto) {
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
        angular.element("#fecha_cheque").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            numberOfMonths: 1,
            defaultDate: moment().format('DD-MM-YYYY')
        });


        if(pago_uuid.length)
        {
            $scope.chequesHeader.tipo = 'pago';
            $scope.empezarDesde($scope.chequesHeader.tipo);
        }

        $.validator.setDefaults({
            errorPlacement: function(error, element){
                if($(element).prop("id") == 'fecha_cheque')
                {
                    $(element).parent().parent().append(error);
                }
                else
                {
                    $(element).after(error);
                }
            }
        });

        objFrom.chequeForm.validate({
            ignore: '',
            wrapper: '',
            submitHandler: function(form) {
                // do other things for a valid form desabilitar boton guardar
                $("#proveedor").removeAttr("disabled");
                $("#total_cheque").removeAttr("disabled");
                $("#cuenta").removeAttr("disabled");

                form.submit();
            }
        });
    };


    $scope.init();


    if(vista != 'crear')
    {
        var data = {
            opcionesModal: '#opcionesModal',
            modalTitle: 'Anular cheque: '+ cheque.numero
        };

        var dom = {
            opcionesModal: $(data.opcionesModal)
        };

        var methods = {
            mGetNotification:function(notificacion, clase){
                var html = '';

                html += '<div class="alert '+ clase +'">';
                html += notificacion;
                html += '</div>';

                return html;
            },
            mGetLabel:function(titulo, subtitulo){
                var html = '';

                html += '<div>';
                html += '<h1>'+ titulo +'</h1>';
                html += '<h3 style="border-bottom: 3px solid silver;padding-bottom:4px;">'+ subtitulo +'</h3>';
                html += '</div>';

                return html;
            },
            mGetButtons:function(){
                var html = '';

                html += '<div style="text-align:center;">';
                html += '   <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">';
                html += '       <a href="#" class="btn btn-default btn-block" id="cancelarModal">Cancelar </a>';
                html += '   </div>';
                html += '   <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">';
                html += '       <input type="submit" id="confirmarModal" class="btn btn-primary btn-block" value="Confirmar">';
                html += '   </div>';
                html += '</div>';

                return html;
            },
            mGetModalBody: function(){
                var modalBody = '';

                modalBody += methods.mGetNotification('¡Atención! Esta acción no puede ser revertida', 'alert-danger');
                modalBody += methods.mGetLabel('$'+ cheque.monto, 'Monto');
                modalBody += methods.mGetLabel(cheque.pago.proveedor.nombre, 'Proveedor');
                modalBody += methods.mGetButtons();

                return modalBody;
            },
            mAnularCheque: function(e){
                e.preventDefault();
                e.returnValue=false;
                e.stopPropagation();

                dom.opcionesModal.on('click', '#cancelarModal, #confirmarModal', function(){
                    var self = $(this);
                    dom.opcionesModal.modal("hide");
                    self.unbind();

                    if(self.prop("id") == 'confirmarModal')
                    {
                        methods.mAnularChequePost();
                    }
                });

                //Init Modal
                dom.opcionesModal.find('.modal-title').empty().append(data.modalTitle);
                dom.opcionesModal.find('.modal-body').empty().append(methods.mGetModalBody);
                dom.opcionesModal.find('.modal-footer').empty();
                dom.opcionesModal.modal('show');
            },
            mAnularChequePost: function(){
                $.ajax({
                    url: phost() +"/cheques/ajax-anular-cheque",
                    type:"POST",
                    data:{
                        erptkn:tkn,
                        cheque_id:cheque.id
                    },
                    dataType:"json",
                    success: function(data){
                        if(data.success === true)
                        {
                            toastr.success("Éxito! Su solicitud se ha procesado correctamente");
                        }
                        else
                        {
                            toastr.error("Error! Su solicitud no se ha procesado");
                        }
                    }
                });
            }
        };



        $('#moduloOpciones').on('click', '#anular_cheque', methods.mAnularCheque);
    }





//    if(uuid_pago!=""){
//        $scope.chequesHeader.tipo="pago";
//        $scope.empezarDesde($scope.chequesHeader.tipo);
//        $scope.llenarFormulario({uuid: uuid_pago});
//     }
//    else if(uuid_cheque!=""){
//        var pago=serviceCheque.getChequePago({uuid: uuid_cheque});
//        var cheque=serviceCheque.getCheque({uuid: uuid_cheque});
//        $scope.chequesHeader.tipo="pago";
//        $scope.empezarDesde($scope.chequesHeader.tipo);
//        pago.then(function(data){
//            $scope.llenarFormulario({uuid: data[0].uuid_pago});
//        });
//
//        cheque.then(function(data){
//            $scope.datosCheque.chequera_id=data.chequera_uuid;
//            $scope.datosCheque.numero_cheque=data.numero;
//        });
//    }
});
