var cuentaPlanilla = {
    rutas: {
        cuentaActivo: phost() + 'contabilidad/ajax-cuenta-activo',
        guardarcuentaCobro: phost() + 'configuracion_contabilidad/ajax-cuenta-planilla'
    },
    vista: {
        guardarCuenta: $('#planilla').find('button#btnGuardar'),
        botonCancelar: $('table#cuentas_planilla').find('button#btnCancelar'),
        divCuentas: $('#cuentas_activo_planilla'),
        eliminarCuenta: 'a#close_cuenta'
    },
    init: function () {
        this.cargar_cuenta();
        /*$('#configuracionTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
         e.target // newly activated tab
         e.relatedTarget // previous active tab
         var planilla = e.target;
         //  var palntilla2 = planilla.split('#');
         this.cargar_cuenta();
         //$('#plantilla').tab('show');
         console.log("entre"+planilla);
         });*/
    },
    mensaje: function (data) {
        if (data.tipo === 'success') {
            toastr.success(data.mensaje);
        } else if (data.tipo === 'error') {
            toastr.error(data.mensaje);
        } else if (data.tipo === 'info') {
            toastr.info(data.mensaje);
        }
    },
    cargar_cuenta: function () {
        var self = this;
        var cuentas = moduloConfiguracionContabilidad.getCuentaActivos();
        cuentas.done(function (data) {
            self.treeRender(data);
        });
        /* $("#planilla").on( "click", function( event, ui ) {
         //control comes here when you clicked on tab 1
         self.treeRender(cuentas);
         });*/
        self.vista.guardarCuenta.click(function () {
            console.log("guardar");
            var cuenta = $('#id_seleccion_planilla').val();
            if (cuenta.length === 0) {
                swal("Cuenta Planilla", "Debe seleccionar una cuenta", "info");
                return false;
            }

            var parametros = {id: cuenta};
            var guardar = moduloConfiguracionContabilidad.guardarCuentaPlanilla(parametros);
            guardar.done(function (data) {
                $('#id_seleccion_planilla').val("");
                self.mensaje(data);
                if (data.tipo === 'success') {
                    var arbol_cuenta = $.jstree.reference($('#cuentas_activo_planilla'))._model.data;//.disable=true;
                    $.each(arbol_cuenta, function (i, elem) {
                        if (elem.id != cuenta) {
                            $('#cuentas_activo_planilla').jstree('disable_node', elem.id);
                        }
                    });
                }
            });
        });
        $('#cuenta_seleccionada_planilla').on('click', self.vista.eliminarCuenta, function (e) {
            //el id
            var cuenta_id = $(this).data('item');
            // console.log(cuenta_id);
            var parametros = {cuenta_id: cuenta_id};
            var cuentaEliminada = moduloConfiguracionContabilidad.eliminarCuentaPlanilla(parametros);
            cuentaEliminada.done(function (data) {
                if (!data.puede_eliminar) {
                    swal("Cuenta Planilla", "La cuenta no se puede eliminar porque tiene transacciones", "warning");
                } else {
                    self.mensaje(data);
                    $('table#cuentas_planilla').find('div.item-cuenta').fadeIn('slow');
                    $('table#cuentas_planilla').find('div.item-cuenta').remove();
                    var arbol_cuenta = $.jstree.reference($('#cuentas_activo_planilla'))._model.data;
                    $.each(arbol_cuenta, function (i, elem) {
                        if (elem.es_padre) {
                            $('#cuentas_activo_planilla').jstree('disable_node', elem.id);
                        } else {
                            $('#cuentas_activo_planilla').jstree('enable_node', elem.id);
                        }
                    });
                    $('table#cuentas_planilla').find('li#' + cuenta_id + ' a').removeClass('jstree-clicked');
                    $('table#cuentas_planilla').find('li#' + cuenta_id + ' div').removeClass('jstree-wholerow-clicked');
                    $('#id_seleccion_planilla').val("");
                }
            });
        });
        self.vista.botonCancelar.click(function () {
            console.log('log()');
        });
    },
    treeRender: function (data) {
        var arbol = data;


        var div = $('#cuentas_activo_planilla');
        div.jstree(arbol);
        div.jstree(true).redraw(true);
        div.bind("loaded.jstree",
            function (event, data) {
                $('table#cuentas_planilla').find("a:contains('1. Activos')").css("visibility", "hidden");
                $('table#cuentas_planilla').find(".jstree-last .jstree-icon").first().hide();
                //$('#cuentas_activo_planilla').jstree('select_node', 11);
                ///busca el selecionado
                var selecionado = moduloConfiguracionContabilidad.getCuentaPlanilla();
                // console.log(selecionado);
                selecionado.done(function (data) {
                    console.log("Seleccionado:", data);
                    var obj = data;
                    if (!_.isEmpty(obj)) {
                        // console.log(obj[0].cuenta_id);
                        $('#cuentas_activo_planilla').jstree('select_node', obj[0].cuenta_id);
                        //$.jstree.reference($('#cuentas_activo_planilla'))._model.default_state.disabled=true;
                        var arbol_cuenta = $.jstree.reference($('#cuentas_activo_planilla'))._model.data;//.disable=true;
                        $.each(arbol_cuenta, function (i, elem) {
                            if (elem.id != obj[0].cuenta_id) {
                                $('#cuentas_activo_planilla').jstree('disable_node', elem.id);
                            }
                        });
                    }
                });
                //*/
            });
        $('#cuentas_activo_planilla').bind("select_node.jstree", function (e, data) {

            var cuenta = data.node;
            var contenido = '';

            console.log("cuenta seleccionada:",cuenta);

            $('#id_seleccion_planilla').val(cuenta.id);
            contenido = '<div class="item-cuenta"><div class="pull-left icono-cerrar"><a id="close_cuenta" data-item="' + cuenta.id + '"><i class="fa fa-close fa-lg fa-border "></i></a></div><div class="pull-left text-right cuenta-texto">' + cuenta.text + '</div></div>';
            $('#cuenta_seleccionada_planilla').html(contenido);

            //console.log($('ul.jstree-children').find('li'));
        });
        /*$('#cuentas_activo_planilla').bind('hover_node.jstree', function (e, data) {
         console.log("changed",data);
         });*/
    }
};

(function(){
    cuentaPlanilla.init();
})();

