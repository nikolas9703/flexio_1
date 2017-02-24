
Vue.component('datos_factura', {
    template: '#datos_factura',
    data: function () {
        return {
            active: '',
            factura: {
                id: typeof factura_venta_id != 'undefined' ? factura_venta_id : '',
                cliente_id: '',
                saldo_pendiente: '0.00',
                credito_favor: '0.00',
                termino_pago_id: '',
                vendedor_id: '',
                lista_precio_id: '',
                centro_contable_id: '',
                centro_facturacion_id: infofactura.centro_facturacion_id,
                bodega_id: '',
                fecha_desde: moment().format('YYYY-MM-DD'),
                fecha_hasta: moment().add(30, 'days').format('YYYY-MM-DD'),
                estado_id: 'por_aprobar',
                comentario: '',
                cuenta_tipo: 'Cuentas por cobrar de clientes',
                fac_facturable_id: '',
                factura_id: '',
                uuid_venta: '',
                metodoPago: '1',
                pagador: infopoliza.pagador,
                subtotal: infofactura.subtotal,
                descuento: infofactura.descuento,
                impuesto: infofactura.impuestos,
                otros: infofactura.otros,
                total: infofactura.total,
                disabledCliente: false,
                impuestoplan: impuesto_plan,
                agente: $.parseJSON(agentemayor),
                formulario: infofactura.formulario == 'facturas_seguro' ? 'Póliza' : infofactura.formulario,
                vendedorId: typeof usuario_id != 'undefined' ? usuario_id : '',
                precioId: typeof precio_id != 'undefined' ? precio_id : '',
                clienteOptions: typeof clientesArray != 'undefined' ? clientesArray : [],
                terminosPagoOptions: typeof terminosPagoArray != 'undefined' ? $.parseJSON(terminosPagoArray) : [],
                frecuenciaPagoOptions: typeof frecuenciaArray != 'undefined' ? $.parseJSON(frecuenciaArray) : [],
                metodoPagoOptions: typeof metodo_pago != 'undefined' ? $.parseJSON(metodo_pago) : [],
                sitioPagoOptions: typeof sitio_pago != 'undefined' ? $.parseJSON(sitio_pago) : [],
                vendedoresOptions: typeof vendedoresArray != 'undefined' ? $.parseJSON(vendedoresArray) : [],
                agentesOptions: typeof agentesArray != 'undefined' ? $.parseJSON(agentesArray) : [],
                listaPreciosOptions: typeof preciosArray != 'undefined' ? $.parseJSON(preciosArray) : [],
                centrosContablesOptions: typeof centrosContablesArray != 'undefined' ? $.parseJSON(centrosContablesArray) : [],
                centrosFacturacionOptions: typeof centrosFacturacionArray != 'undefined' ? $.parseJSON(centrosFacturacionArray) : [],
                bodegasOptions: typeof bodegasArray != 'undefined' ? $.parseJSON(bodegasArray) : [],
                estadosOptions: typeof estadosArray != 'undefined' ? $.parseJSON(estadosArray) : []
            },
            cliente: [{
                id: infofactura.cliente.id,
                nombre: infofactura.cliente.nombre 
            }],
            poliza: $.parseJSON(infopoliza),
            fechas: $.parseJSON(fechas)
        };
    },
    ready: function () {
        
        //console.log($.parseJSON(infopoliza));
        //console.log($.parseJSON(fechas));
        
        var scope = this;
        // Si existe variable infofactura (vista: editar)
        if (typeof infofactura != 'undefined') {

            scope.$nextTick(function () {
                scope.factura.id = infofactura.id != '' ? infofactura.id : '';
                scope.factura.factura_id = infofactura.id != '' ? infofactura.id : '';
                scope.factura.cliente_id = infofactura.cliente_id != '' ? infofactura.cliente_id : '';
                scope.factura.saldo_pendiente = typeof infofactura.cliente != 'undefined' ? infofactura.cliente.saldo_pendiente : '0.00';
                scope.factura.credito_favor = typeof infofactura.cliente != 'undefined' ? infofactura.cliente.credito_favor : '0.00';
                scope.factura.termino_pago_id = infofactura.termino_pago != '' ? infofactura.termino_pago : '';
                scope.factura.vendedor_id = infofactura.created_by != '' ? infofactura.created_by : '';
                scope.factura.lista_precio_id = infofactura.item_precio_id != '' ? infofactura.item_precio_id : '';
                scope.factura.centro_contable_id = infofactura.centro_contable_id != '' ? infofactura.centro_contable_id : '';
                scope.factura.centro_facturacion_id = infofactura.centro_facturacion_id != '' ? infofactura.centro_facturacion_id : '';
                scope.factura.bodega_id = infofactura.bodega_id != '' ? infofactura.bodega_id : '';
                scope.factura.fecha_desde = infofactura.fecha_desde != '' ? infofactura.fecha_desde : '';
                scope.factura.fecha_hasta = infofactura.fecha_hasta != '' ? infofactura.fecha_hasta : '';
                scope.factura.estado_id = infofactura.estado != '' ? infofactura.estado.toString() : '';
                scope.factura.uuid_venta = typeof infofactura[scope.$root.$refs.filtro.ordendesde_id] != 'undefined' ? infofactura[scope.$root.$refs.filtro.ordendesde_id] : '';
                scope.factura.comentario = infofactura.comentario != '' ? infofactura.comentario : '';
                window.inpuesto_total = typeof infofactura.impuestos != 'undefined' ? infofactura.impuestos : '';
             //   console.log(scope.factura.fecha_desde);
                //
                // verificar estado de la factura
                // para desabilitar boton de guaradr
                
            });
            if (scope.factura.estado_id.match(/(cobrado_parcial||cobrado_completo||anulada)/)) {
                    scope.$root.guardarBtnDisabled = true;   
                    
                }
        }
        var select_vendedor = $('#vendedor option[value="' + usuario_id + '"]').val();
        if (typeof select_vendedor != "undefined") {
            $('#vendedor').attr('disabled', true);
        }
        $("#cuenta_id0").val("Cuentas por cobrar de clientes");
        $("#cuenta_id0").trigger("change");

        if (parseFloat(infofactura.otros)>0) {
            $("#muestraotros").show();
        }

    },
    events: {
        popularDatosFactura: function (info) {

            if (typeof info === 'undefined') {
                return false;
            }
            this.factura.disabledCliente = true;
            this.factura.cliente_id = typeof info.cliente_id != 'undefined' ? info.cliente_id : '';
            //this.factura.saldo_pendiente = typeof info.cliente.saldo_pendiente != 'undefined' ? info.cliente.saldo_pendiente : '';
            //this.factura.credito_favor = typeof info.cliente.credito_favor != 'undefined' ? info.cliente.credito_favor : '';
            if (typeof vista == 'undefined'){
            if (typeof info.fecha_desde != 'undefined') {
                this.factura.fecha_desde = moment().format('YYYY-MM-DD');
            } else if (typeof info.fecha_inicio != 'undefined') {
              var date = info.fecha_inicio.date;
              var fecha = moment(date).format("YYYY-MM-DD");
                this.factura.fecha_desde = fecha;
            } else {
                this.factura.fecha_desde = moment().format('YYYY-MM-DD');
            }
            if (typeof info.fecha_hasta != 'undefined') {
                this.factura.fecha_hasta = moment().add(30, 'days').format('YYYY-MM-DD');
            } else if (typeof info.fecha_final != 'undefined') {
                this.factura.fecha_hasta = moment().add(30, 'days').format('YYYY-MM-DD');
            } else {
                this.factura.fecha_hasta = moment().add(30, 'days').format('YYYY-MM-DD');
            }
            }
            if(typeof tipo_chosen  != 'undefined' && typeof contrato_alquiler_uuid != 'undefined'){
             console.log("vamo a entra");
             this.factura.vendedor_id = typeof created_by != 'undefined' ? created_by : '';
             this.factura.centro_contable_id = typeof info.centro_contable_id != 'undefined' ? info.centro_contable_id : '';
             this.factura.bodega_id = typeof bodega_id != 'undefined' ? bodega_id : '';
             this.factura.termino_pago_id = typeof termino_pago != 'undefined' ? termino_pago : '';
             this.factura.lista_precio_id = typeof info.item_precio_id != 'undefined' ? info.item_precio_id : '';
             if (typeof info.contrato_id != 'undefined') {

            this.factura.fac_facturable_id = typeof info.contrato_id != 'undefined' ? info.contrato_id : '';
          }else{
            this.factura.fac_facturable_id = typeof info.id != 'undefined' ? info.id : '';
          }
            }else{
            console.log("vamo a sali");
            //console.log(info.item_precio_id);
            this.factura.bodega_id = typeof info.bodega_id != 'undefined' ? info.bodega_id : '';
           // this.factura.lista_precio_id = typeof info.item_precio_id != 'undefined' ? info.item_precio_id : infofactura.itemn_precio_id;
            this.factura.vendedor_id = typeof info.created_by != 'undefined' ? info.created_by : usuario_id;
            //this.factura.termino_pago_id = typeof info.termino_pago != 'undefined' ? info.termino_pago : '';
            //this.factura.estado_id                    = typeof info.estado != 'undefined' ? info.estado.toString() : '';
            if (typeof info.contrato_id != 'undefined') {

            this.factura.fac_facturable_id = typeof info.contrato_id != 'undefined' ? info.contrato_id : '';
          }else{
            this.factura.fac_facturable_id = typeof info.id != 'undefined' ? info.id : '';
          }
            this.factura.uuid_venta = typeof info.uuid_venta != 'undefined' ? info.uuid_venta : '';
            this.factura.centro_contable_id = typeof info.centro_contable_id != 'undefined' ? info.centro_contable_id : '';
            this.factura.lista_precio_id = typeof info.item_precio_id != 'undefined' ? info.item_precio_id : '';
            //this.factura.centrosFacturacionOptions = typeof info.cliente != 'undefined' && typeof info.cliente.centro_facturable != 'undefined' ? info.cliente.centro_facturable : '';
            //this.factura.centro_facturacion_id = typeof info.centro_facturacion_id != 'undefined' ? info.centro_facturacion_id : infofactura;

            }


            window.impuesto_total = typeof info.impuestos != 'undefined' ? info.impuestos : '';

        }
    },
    computed: { 
              getEstados: function() {
                  var context = this;

                  console.log(context.factura.estado_id);
                  
                  if(context.factura.estado_id == 'por_aprobar')
                  {
                      return _.filter(context.factura.estadosOptions, function(estado){
                            return estado.id == 'por_aprobar' || estado.id == 'por_cobrar' || estado.id == 'anulada';});
                  }
                  else if(context.factura.estado_id == 'por_cobrar')
                  {
                      return _.filter(context.factura.estadosOptions, function(estado){return estado.id == 'por_cobrar';});
                  } else if (context.factura.estado_id == 'anulada') {
                      
                      return _.filter(context.factura.estadosOptions, function(estado){return estado.id == 'anulada';});
                  } 
                      
                      return context.factura.estadosOptions;
                
                 
              }
            },
    methods: {

        cambiarListaPrecio:function(){

            var context = this;
            context.$broadcast('update-precio_unidad');

        },

        reset: function () {
            //limpiar datos
            this.factura = {
                id: typeof factura_venta_id != 'undefined' ? factura_venta_id : '',
                cliente_id: '',
                saldo_pendiente: '0.00',
                credito_favor: '0.00',
                termino_pago_id: '',
                vendedor_id: '',
                lista_precio_id: '',
                centro_contable_id: '',
                centro_facturacion_id: '',
                bodega_id: '',
                //fecha_desde: moment().format('DD/MM/YYYY'),
                //fecha_hasta: moment().add(30, 'days').format('DD/MM/YYYY'),
                fecha_desde: moment().format('YYYY-MM-DD'),
                fecha_hasta: moment().add(30, 'days').format('YYYY-MM-DD'),
                estado_id: 'por_aprobar',
                comentario: '',
                fac_facturable_id: '',
                factura_id: '',
                uuid_venta: '',
                disabledCliente: false,
                vendedorId: typeof usuario_id != 'undefined' ? usuario_id : '',
                precioId: typeof precio_id != 'undefined' ? precio_id : '',
                clienteOptions: typeof clientesArray != 'undefined' ? clientesArray : [],
                terminosPagoOptions: typeof terminosPagoArray != 'undefined' ? $.parseJSON(terminosPagoArray) : [],
                vendedoresOptions: typeof vendedoresArray != 'undefined' ? $.parseJSON(vendedoresArray) : [],
                listaPreciosOptions: typeof preciosArray != 'undefined' ? $.parseJSON(preciosArray) : [],
                centrosContablesOptions: typeof centrosContablesArray != 'undefined' ? $.parseJSON(centrosContablesArray) : [],
                centrosFacturacionOptions: typeof centrosFacturacionArray != 'undefined' ? $.parseJSON(centrosFacturacionArray) : [],
                bodegasOptions: typeof bodegasArray != 'undefined' ? $.parseJSON(bodegasArray) : [],
                estadosOptions: typeof estadosArray != 'undefined' ? $.parseJSON(estadosArray) : []
            }

            //limpiar items
            this.$refs.tabla.$refs.items.resetItems();
        }
    },
    watch: {
        'factura.cliente_id': function (nv, ov) {
            var scope = this;
            if (nv == "") {
                return;
            }

            var cliente = _.find(this.factura.clienteOptions, function (query) {
                return query.id == nv;
            });

            if (typeof cliente != 'undefined' && _.isEmpty(cliente.centro_facturable)) {
                toastr.info("no tiene centro de facturaci&oacute;n", cliente.nombre);
            }

            //Popular datos de la factura
            this.$nextTick(function () {

                //popular saldo pendiente
                scope.factura.saldo_pendiente = roundNumber(parseFloat(infofactura.cliente.saldo_pendiente.toString().replace(/,/g, '')), 2);

                //popular credito a favor
                scope.factura.credito_favor = roundNumber(parseFloat(infofactura.cliente.credito_favor.toString().replace(/,/g, '')), 2);

                //popular dropdown centros de facturacion
                scope.factura.centrosFacturacionOptions = infofactura.cliente.centro_facturable;
                setTimeout(function () {
                    //popular centro de facturacion
                    var length = $('#centro_facturacion_id option').length;
                    if (length <= 2) {
                        //$('#centro_facturacion_id option:eq(1)').attr("selected", "selected");
                    }
                }, 500);
            });
        }
    }
});
