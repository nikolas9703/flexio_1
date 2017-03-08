bluapp.controller("crearAbonoController", function($scope, $timeout,serviceAbono, $document, $http){
  var model = this;
  var opciones;
  $scope.tipo = ''; 
  $scope.acceso = true;
  $scope.nextItem = 0;
  $scope.vista = vista;
  $scope.disabled = false;
  $scope.disableCuenta = false;
  $scope.disabledEditar = false;
   
    //campos de la cabecera del formualrio
    $scope.abonosHeader = { 
        tipo:'cliente',
        uuid:'',
        colleccion:[]
    };
    
    //campos del cuerpo principal de formulario
    $scope.datosAbono={
        clienteActual:'',
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


    //Obtiene el catelogo de items...
    var getItems = function(){
        var itemsInfo = serviceAbono.getItems();
            
        itemsInfo.then(function(data){
            items = data;//Catalogo de items agrupados por categorias
            
            //SI ESTOY DESDE EL FORMULARIO DE EDICION CARGO
            //LOS DATOS QUE NO ME ESTOY TRAYENDO DESDE PHP
            if(dom.iFormularioEditar.length)
            {
                console.log("obtengo los datos de la factura -> formulario de edicion");
                $scope.gets.factura($("#abono_id").val());
            }
        });
    };
    
    var getItem = function(item_id, index){
        return _.find($scope.articulos[index].items,function(query){
            return query.id == item_id;
        });
    };
    
    var calcularFactura = function(){
       
        var opcionAbonos = $scope.opcionAbonos;
        var totalFila = 0;
        
        var total_abonado = 0;
        
        $.each(opcionAbonos, function(i, abono){
            
            totalFila = abono.total_abonado;
            
            total_abonado += parseFloat(totalFila) || 0;
           
           // $scope.set.sumaTotales(total_abonado);
           
           $('#total_abono').val(total_abonado.toFixed(2));
            
        });
    };
    
    
    $scope.ngClick = {
        
        addRow: function(index){
            
            //Cuando se esta facturando desde un contrato
            //o una orden de compra no se pueden agregar
            //mas filas a la factura
          //  if(_.isEmpty($scope.uuid_tipo) || $scope.uuid_tipo == '0'){
                
                   
                $scope.opcionAbonos.push({
                    cantidad:1,
                    //totalizadores de la fila
                });
              
                var row = "tr#items" + ($scope.opcionAbonos.length - 1);
                $timeout(function(){
                    //activo la mascara
                    $(row).find(':input[data-inputmask]').inputmask();
                    
                    if($(row).find('select.chosen-select').attr('class') !== undefined){

                        $(row).find('.chosen-container').remove();
                        $(row).find('.chosen-container-single').remove();
                        $(row).closest('table').find('select.chosen-select').chosen({
                            width: '100%'
                        }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
                            $(row).closest('div.table-responsive').css("overflow", "visible");
                        }).on('chosen:hiding_dropdown', function(evt, params) {
                            $(row).closest('div.table-responsive').css({'overflow-x':'auto !important'});
                        });
                    }
                },800);
          //  }
        },
        
        deleteRow: function(abono){
            var index = $scope.opcionAbonos.indexOf(abono);
            if(!angular.isUndefined($scope.opcionAbonos[index]))
            {
                
            $scope.nextItem = $scope.nextItem - 1;
            $scope.opcionAbonos.splice(index, 1);
            calcularFactura();
            
           var monto = parseInt($('#monto').val());
           var total_abono = parseInt($('#total_abono').val());
         
           if(monto ==  total_abono){
           // alert("funciona");   
            $("#totals-error").empty();
            $("#guardarBtn").prop("disabled",false);
        }else{
             
            $("#totals-error").empty().html('El total debe ser igual al monto');
            $("#totals-error").css('display','block');
            $("#guardarBtn").prop("disabled",true);
        }
            
            }
        }
        
    };
    
    $scope.ngBlur = {
        itemCantidad: function(valor, index){
            calcularFactura();
        },
        itemPrecioUnidad: function(valor, index){
            calcularFactura();
        },
        itemDescuento: function(valor, index){
            calcularFactura();
        }
    };
    
    
    $scope.empezarDesde = function(tipo){
        if(tipo !==''){
            if(tipo === 'cliente'){
               
                //buscar solo los proveedores q tienen facturas
                var clientes = serviceAbono.getClientes({vista:$scope.vista});
                clientes.then(function(data){
                 
                    $scope.abonosHeader.collection = data;
                    $scope.abonosHeader.tipo = tipo;
                    
                    //indicar proveedor y popular sus datos
                    $scope.llenarFormulario({uuid:uuid_cliente,nombre:''});
                });
            }
        }   
    };

    $scope.llenarFormulario = function(uuid){
       
        if(uuid !=='' && uuid !==null){
            $scope.abonosHeader.uuid = uuid;
            if($scope.abonosHeader.tipo === 'cliente'){
                var clienteInfo =  serviceAbono.clienteInfo({uuid:uuid.uuid,vista:$scope.vista});
                clienteInfo.then(function(data){
                    $scope.datosFormularioCliente(data);
                });
            }
        }
        
   
    };

    $scope.datosFormulario = function(data){
        $scope.datosAbono.clienteActual = data.cliente_id.toString();
        $scope.datosAbono.saldo = data.cliente.saldo_pendiente;//data.cliente.saldo_pendiente;
        $scope.datosAbono.credito_favor = accounting.toFixed(data.cliente.credito_favor,2);//devoluciones, etc. por desarrollar
        if($scope.vista =='crear'){
            $scope.datosAbono.monto = 0;
            $scope.datosAbono.total_abonado = 0;
            $scope.opcionAbonos = [{icon:'fa fa-plus', index:0,total_abonado:'',tipo_abono:''}];
            //indico el banco del proveedor en caso de que indique "ACH" data.proveedor.id_banco
            $("#nombre_banco_ach0").val(data.cliente.id_banco);
            $("#cuenta_proveedor0").val(data.cliente.numero_cuenta);
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

    $scope.datosFormularioCliente = function(data){//desarrollador solo para la vista "Crear"
        $scope.datosAbono.clienteActual = data.id.toString();
        $scope.datosAbono.credito_favor = accounting.toFixed(data.credito_favor,2);//devoluciones, etc. por desarrollar
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
        
        var abono = serviceAbono.getAbono({uuid_abono: uuid_abono});
        
        console.log(abono);
        
        $scope.abonosHeader.tipo = "cliente";
        $scope.abonosHeader.uuid = uuid_abono;
        $scope.disableTipo = true;
        $scope.disabledEditar = true;
        $('#guardarBtn').attr('disabled', 'disabled');
        
       
        
        abono.then(function(data){
            
            console.log(data);
            var fecha = data.fecha_abono;
            var newDate = fecha.split(' ')[0];
                        
            $scope.datosAbono.monto = data.monto_abonado.toString();
            $scope.datosAbono.fecha_abono = newDate;
            $scope.datosAbono.ClienteActual = data.cliente_id.toString();
            $scope.datosAbono.cuenta_id = data.cuenta_id.toString();
            $scope.datosAbono.total_abono = data.monto_abonado;
          //  $scope.datosAbono.total_abonado = data.total_abonado;
            //habilito la opcion de guardar sino esta anulado el abono -> vista:ver
            //solo se puede editar "el estado" del abono. Los otros valores no son
            //editables
           
            $scope.opcionAbonos = [];
            angular.forEach(data.items,function(value, i){
            
                $scope.opcionAbonos.push({icon: i === 0? 'fa fa-plus':'fa fa-trash', index:i,total_abonado:value.total_abonado, tipo_abono:value.tipo_abono});
            });
            $scope.metodollenar(data.items);
        });
    }

    $scope.metodollenar = function(items){
        $timeout(function(){
            angular.forEach(items,function(value, i){
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
        if($scope.abonosHeader.tipo !=='cliente'){
            $scope.nextItem = $scope.nextItem + 1;
            $scope.opcionAbonos.push({icon:'fa fa-trash', index:$scope.nextItem,total_abonado:'',tipo_abono:''});
        }
    };
    
    
    $('body').on('click','a#editarabono',function(e){
			e.preventDefault();

			var uuid = $(this).data('abonouuid');
			var datos = {erptkn: tkn, uuid_abono: uuid};
			$('#optionsModal').modal('hide');
			$scope.abonoInfo(datos);
		});

	 $scope.abonoInfo = function(datos){
		 $http({
					 url: phost() + 'clientes_abonos/ajax-info-abono',
					 method: 'POST',
					 data : $.param(datos),
					 cache: false,
					 xsrfCookieName: 'erptknckie_secure',
					 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
				}).then(function (response) {
					if(response){
						var dataAbono = response.data;
						$scope.abono = dataAbono;
						clienteProvider.config(true);
						angular.element("#vistaCliente").addClass('hide');
						angular.element("#vistaFormularioAbono").removeClass('hide');
					}
				});
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
        $("#cliente").removeAttr("disabled");
        $("#tipo").removeAttr("disabled");
        $("#total_abono").removeAttr("disabled");
        $("#cuenta").removeAttr("disabled");
        form.submit();
      }
    });
    
    


  };
  
$scope.init();

});
