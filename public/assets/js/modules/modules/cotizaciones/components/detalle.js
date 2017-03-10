Vue.component('detalle', {

    template: '#detalle_template',

    props: {

        config: Object,
        detalle: Object,
        catalogos: Object,
        empezable: Object,
        isClientePotencial: '',
        disabledCliente: false

    },

    ready: function () {

        var context = this;
        if (context.config.vista == 'crear') {

            Vue.nextTick(function () {

                context.setVendedor();
                context.setPrecio();

            });

        }

        if (window.acceso == 0) {
            context.config.disableDetalle = true;
        }

    },

    watch: {
        'detalle.cliente_id': function (val, oldVal) {

            var context = this;

            console.log(context.empezable.type);
            if (context.config.vista == 'editar' && val != '') {
                console.log('entre');
                //...no hace nada
            }
            else if (val == '' || context.empezable.type == 'cliente') {
                console.log('entre cliente type');
                context.cliente_id = context.empezable.id;
            }
            else if (val == '' || context.empezable.type == 'cliente_potencial' || !context.enableWatch) {
                this.detalle.saldo_cliente = 0;
                this.detalle.credito_cliente = 0;
                return '';
            }

            context.enableWatch = true;
            var datos = $.extend({erptkn: tkn}, {cliente_id: val});
            this.$http.post({
                url: window.phost() + "clientes/ajax-get-montos",
                method: 'POST',
                data: datos
            }).then(function (response) {

                if (_.has(response.data, 'session')) {
                    window.location.assign(window.phost());
                    return;
                }
                if (!_.isEmpty(response.data)) {

                    context.detalle.saldo_cliente = response.data.saldo;
                    context.detalle.credito_cliente = response.data.credito;

                    if (response.data.exonerado_impuesto != null) {
                        $('#cliente_ID').val(response.data.cliente_id);
                    } else {
                        console.log("es null");
                        setTimeout(function () {
                            $('#cliente_ID').val('');
                        }, 500);
                    }
                    if (context.empezable.type == '') {

                        context.detalle.centros_facturacion = response.data.centros_facturacion;
                        context.detalle.centro_facturacion_id = response.data.centro_facturacion_id;
                        console.log('asignar catalogo de centros de facturacion y el perteneciente al cliente');
                    }
                    context.enableWatch = true;

                }
            }).catch(function (err) {
                window.toastr['error'](err.statusText + ' (' + err.status + ') ');
            });

        }

    },

    methods: {

        setVendedor: function () {

            var context = this;
            var vendedor = _.find(context.catalogos.vendedores, function (vendedor) {
                return vendedor.id == context.catalogos.usuario_id;
            });

            if (!_.isEmpty(vendedor)) {

                context.detalle.creado_por = vendedor.id;

            }

        },

        setPrecio: function () {

            var context = this;
            var precio = _.find(context.catalogos.precios, function (precio) {
                return precio.principal == 1;
            });

            if (!_.isEmpty(precio)) {

                context.detalle.item_precio_id = precio.id;

            }

        }

    },

    computed: {

        getClientes: function () {

            var context = this;
            if (context.empezable.type != '') {
                this.$set('disabledCliente', true);
                if (context.empezable.type == 'cliente_potencial') {
                    this.$set('isClientePotencial', 'potencial');
                } else {
                    this.$set('isClientePotencial', '');
                }

                return context.empezable[context.empezable.type + 's'];
            }
            this.$set('disabledCliente', false);
            this.$set('isClientePotencial', '');
            return context.empezable['cliente' + 's'];


        }

    },

    data: function () {

        return {

            enableWatch: true

        };

    }


});
