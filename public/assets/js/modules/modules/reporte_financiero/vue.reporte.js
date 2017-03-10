Vue.http.options.emulateJSON = true;

var reporteFinanciero = new Vue({
    el: '#reportes_financieros',
    data: {
        catalogo: catalogo,
        reporteActual: _.kebabCase(reporte_actual),
        cabecera: {
            reporte_actual: reporte_actual
        },
        tituloReporte: '',
        dataReporte: {},
        reporte: '',
        isModulo: window.modulo === 1? true:false,
        disabledCabecera:false
    },
    ready: function() {
        var context = this;
        var reporte_catalogo = _.find(this.catalogo, function(query) {
            return query.etiqueta == context.cabecera.reporte_actual;
        });
        this.tituloReporte = reporte_catalogo.valor;
        if(this.isModulo)this.disabledCabecera = true;
        $('#imprimirReporte').css('display', 'none'); //Inhabilita la impresion que solo sirve para reporte ITBMS
    },
    components: {
        'balance-situacion': reporteBalanceSituacion,
        'ganancias-perdidas': reporteGananciasPerdidas,
        'estado-cuenta-proveedor': formularioEstadoCuentaProveedor,
        'costo-por-centro-compras': formularioCostoPorCentroCompras,
        'transacciones-por-centro-contable': formularioTransaccionesPorCentroContable,
        'cuenta-por-pagar-por-antiguedad': formCuentaPorPagarAntiguedad,
        'cuenta-por-cobrar-por-antiguedad': formCuentaPorCobrarAntiguedad,
        'estado-de-cuenta-de-cliente': formularioEstadoCuentaCliente,
        'impuestos-sobre-ventas':formularioImpuestoSobreVenta,
        'flujo-efectivo':formularioFlujoEjectivo,
        'formulario-43':formulario43,
        'formulario-433':formulario433,
        'reporte-caja': formularioReporteCaja,
        'impuestos-sobre-itbms':formularioImpuestoSobreItbms
    },
    methods: {
        seleccionarAplicar: function(componente) {
            var context = this;
            context.$set('reporteActual', _.kebabCase(componente));
            context.$set('dataReporte', {});
            context.$set('reporte', '');
            //seleccione de nombre de reporte
            var reporte_catalogo = _.find(context.catalogo, function(query) {
                return query.etiqueta == _.snakeCase(context.reporteActual);
            });
            context.tituloReporte = reporte_catalogo.valor;
            $('#imprimirReporte').css('display', 'none'); //Inhabilita la impresion que solo sirve para reporte ITBMS
        }

    }
});
