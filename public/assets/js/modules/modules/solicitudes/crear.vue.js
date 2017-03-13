Vue.http.options.emulateJSON = true;
var conta = 0;
var planesCoberturasDeducibles = [];
var arrayauxiliar=[];
for (var i =0; i< documentacionesgbd.length; i++) {
    arrayauxiliar[documentacionesgbd[i].valor]=documentacionesgbd[i].valor
}
var validateFields = [
{field: {input: "input[name='coberturasNombre[]']", valiation: "", type: "cn"}},
{field: {input: "input[name='coberturasValor[]']", valiation: "numeric", type: "cv"}},
{field: {input: "input[name='deduciblesNombre[]']", valiation: "", type: "dn"}},
{field: {input: "input[name='deduciblesValor[]']", valiation: "numeric", type: "dv"}}];
var opcionesModal = $('#verCoberturas');
var formularioCrear = new Vue({
    el: ".wrapper-content",
    data: {
        acceso: acceso === 1 ? true : false,
        disabledOpcionPlanes: true,
        ramo: ramo,
        tipoPoliza: id_tipo_poliza,
        codigoRamo: codigo_ramo,
        catalogoClientes: catalogo_clientes,
        //catalogoPagador: pagador,
        catalogoPagador: pagador != "undefined" ? pagador : [],
        catalogoCantidadPagos: cantidad_pagos,
        catalogoFrecuenciaPagos: frecuencia_pagos,
        catalogoMetodoPago: metodo_pago,
        catalogoSitioPago: sitio_pago,
        catalogoCentroFacturacion: [],
        clienteCentro: '',
        provinciasList: provincias,
        documentacionesList: documentaciones,
        letrasList: letras,
        clientes: [],
        documentacion: [],
        modulo: [],
        //***************************************************
        clienteInfo: {},
        asegurada: asegurada != "undefined" ? asegurada : false,
        plan: plan != "undefined" ? plan : false,
        vigencia: vigencia != "undefined" ? vigencia : false,
        prima: prima != "undefined" ? prima : false,
        estado: estado != "undefined" ? estado : false,
        participacion: participacion != "undefined" ? participacion : false,
        acreedores: acreedores != "undefined" ? acreedores : false,
        centros_contables: centros_contables,
        id_centro: id_centro_contable,
        //***************************************************
        planesInfo: [],
        comisionPlanInfo: '',
        exoneradoImpuestos: '',
        primaAnual: 0,
        impuestoPlan: 0,
        impuestoMonto: '0',
        otrosPrima: 0,
        descuentosPrima: 0,
        totalPrima: 0,
        participacionTotal: agtPrincipalporcentaje,
        aseguradorasListar: aseguradoras,
        planList: planes,
        agentesArray: [1],
        agentesList: agentes,
        porcentajeParticipacion: [],
        disabledOpcionClientes: true,
        disabledCoberturas: true,
        disabledAseguradora: true,
        disabledSubmit: true,
        disabledCentro: true,
        nombrepadre: nombre_padre,
        catalogoEstado: estado_solicitud,
        ramoscadena: ramoscadena,
        InteresesAsociados: '',
        isEditable: true,
        grupogbd: grupogbd,
        direcciongbd: direcciongbd,
        documentacionesgbd: documentacionesgbd,
        arrayauxiliar: arrayauxiliar,
        //********************************
        usuario_id: usuario_id,
        listadoUsuarios: usersList,
        sIntereses: {}
    },
    methods: {
        getClienteSeleccionado: function () {
            //polula el segundo select del header

            var self = this;
            var cliente_tipo = $('#formulario_tipo').val();
            self.$set('clientes', '');
            this.$http.post({
                url: phost() + 'solicitudes/ajax-get-clientes',
                method: 'POST',
                data: {tipo_cliente: cliente_tipo, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {

                    self.tablaError = "";

                    self.$set('clientes', response.data);
                    self.$set('tablaError', '');
                    self.$set('disabledOpcionClientes', false);

                    if (vista == "crear") {
                        self.$set('disabledEstados', true);
                    } else {
                        self.$set('disabledEstados', false);
                    }

                }
            });
            this.getPagadorCampo();
        },
        seleccionarCliente: function (e) {
            this.getClienteSeleccionado();
        },
        clienteInfoSelect: function () {
            this.getClienteSeleccionadoInfo();
            this.getClienteCentroFacturable();
            this.grupoInfo();
            this.direccionInfo();
        },
        nombrePlan: function () {
            this.getPlanesInfo();
        },
        grupoInfo: function () {
            var grupo_val = $('#grupo_nombre').val();
            $('#grupoInfo').val(grupo_val);
        },
        direccionInfo: function () {
            var direccion_val = $('#direccion_nombre').val();
            $('#direccionInfo').val(direccion_val);
        },
        coberturasPlan: function () {
            this.getCoberturasPlanInfo();
            this.getComisionesInfo();
            //this.getPrimaNeta();
        },
        valorporcentajeAgentes: function (index) {
            this.sumatotal();
        },
        porcentajeAgentes: function (index) {
            this.getPorcentajeParticipacion(index);
        },
        clienteDireccion: function () {
            this.getClienteDireccion();
        },
        opcionPagador: function () {
            this.getOpcionPagador();
        },
        getClienteSeleccionadoInfo: function () {
            //polula el segundo select del header
            var self = this;
            var cliente_id = $('#cliente_seleccionado').val();
            localStorage.setItem("id_cliente", cliente_id);
            this.$http.post({
                url: phost() + 'solicitudes/ajax-get-cliente',
                method: 'POST',
                data: {cliente_id: cliente_id, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {

                    if (response.data.tipo_identificacion == 'pasaporte' && response.data.identificacion == '') {
                        response.data.identificacion = response.data.detalle_identificacion.pasaporte;
                    }
                    self.$set('clienteInfo', response.data);


                    //self.$set('exoneradoImpuestos', response.data.exonerado_impuesto);
                    if (response.data.tipo_identificacion == 'natural') {
                        response.data.tipo_identificacion = 'Cédula';
                    } else if (response.data.tipo_identificacion == 'juridico') {
                        response.data.tipo_identificacion = 'RUC';
                    }
                    if (vista != 'editar') {
                        if (!_.isEmpty(response.data.direccion)) {
                            $('#direccionInfo').val(response.data.direccion2.direccion);
                        }
                        if (!_.isEmpty(response.data.group2)) {
                            $('#grupoInfo').val(response.data.group2.nombre);
                        }
                        self.$set('disabledAseguradora', false);
                    } else if (vista === 'editar') {
                        $('#grupoInfo').val(grupogbd);
                        $('#grupoInfo').val(grupogbd);
                        $('#direccionInfo').val(direcciongbd);
                        $("#grupo_nombre option[text='" + grupogbd + "']").attr("selected", "selected");
                        $("#direccion_nombre option[text='" + direcciongbd + "']").attr("selected", "selected");

                    }

                }
            });

        },
        getClienteCentroFacturable: function () {
            //polula el segundo select del header
            var self = this;
            var cliente_id = $('#cliente_seleccionado').val();
            this.$http.post({
                url: phost() + 'solicitudes/ajax-get-centro-facturable',
                method: 'POST',
                data: {cliente_id: cliente_id, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.$set('catalogoCentroFacturacion', response.data);
                    self.$set('disabledCentro', false);
                } else {
                    self.$set('catalogoCentroFacturacion', '');
                }
            });

        },
        getClienteDireccion: function () {
            //polula el segundo select del header
            var self = this;
            var centro_id = $('#centro_facturacion').val();
            this.$http.post({
                url: phost() + 'solicitudes/ajax-get-direccion',
                method: 'POST',
                data: {centro_id: centro_id, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.$set('clienteCentro', response.data[0].direccion);
                }
            });

        },
        getPlanesInfo: function () {
            //polula el segundo select del header
            var self = this;
            self.$set('planesInfo', "");
            this.$http.post({
                url: phost() + 'solicitudes/ajax-get-planes',
                method: 'POST',
                data: {codigoRamo: codigo_ramo, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    $("#aseguradoras2").val($("#aseguradoras").val());
                    if (response.data.primaNeta > 0) {
                        $("input[name='campodetalle[prima_anual]']").val(response.data.primaNeta);
                    }
                    self.$set('planesInfo', response.data);
                    self.$set('disabledOpcionPlanes', false);
                }
            });

        },
        getCoberturasPlanInfo: function () {
            //polula el segundo select del header
            var self = this;
            var plan_id = $('#planes').val();
            
            console.log(plan_id);
            if(plan_id != ''){
                $('.guardarInteresSolicitud').attr('disabled',false);
            }else{
                $('.guardarInteresSolicitud').attr('disabled',true);
            }
            $("#planes2").val(plan_id);
            this.$http.post({
                url: phost() + 'solicitudes/ajax-get-coberturas',
                method: 'POST',
                data: {plan_id: plan_id, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    coberturasForm.$set('coberturasInfo', response.data);
                    //indCoverageArray = response.data;
                    self.$set('disabledCoberturas', false);
                    self.$set('disabledSubmit', false);
                }
            });

        },
        getCoberturasPlanEditar: function () {
            //polula el segundo select del header
            var self = this;
            var id_solicitud = $('#idSolicitud').val();
            this.$http.post({
                url: phost() + 'solicitudes/ajax_get_coberturas_editar',
                method: 'POST',
                data: {id_solicitud: id_solicitud, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    coberturasForm.$set('coberturasInfo', response.data);
                    self.$set('disabledCoberturas', false);
                    editar == 1 ? self.$set('disabledSubmit', false) : '';

                }
            });

        },
        selectIntereses: function () {
            this.getIntereses();
        },
        getIntereses: function () {
            //polula el segundo select del header
            var self = this;
            var interes = $('#formulario').val();

            if (id_tipo_int_asegurado != "") {
                interes = id_tipo_int_asegurado;
                if (interes == 1) {
                    interes = "articuloTab";
                    $("#tabladetalle_articulo").show();
                } else if (interes == 2) {
                    interes = "cargaTab";
                    $("#tabladetalle_carga").show();
                } else if (interes == 3) {
                    interes = "casco_aereoTab";
                    $("#tabladetalle_aereo").show();
                } else if (interes == 4) {
                    interes = "casco_maritimoTab";
                    $("#tabladetalle_maritimo").show();
                } else if (interes == 5) {
                    interes = "personaTab";
                    $("#tabladetalle_persona").show();
                } else if (interes == 6) {
                    interes = "proyecto_actividadTab";
                    $("#tabladetalle_proyecto").show();
                } else if (interes == 7) {
                    interes = "ubicacionTab";
                    $("#tabladetalle_ubicacion").show();
                } else if (interes == 8) {
                    interes = "vehiculoTab";
                    $("#tabladetalle_vehiculo").show();
                }
                $("#formulario option[value='" + interes + "']").attr("selected", "selected");
                $("#" + interes + "").addClass("active");
                var unico = $('#detalleunico').val();
                //if (interes != "") {
                    this.$http.post({
                        url: phost() + 'solicitudes/ajax_get_tipointereses',
                        method: 'POST',
                        data: {interes: interes, unico: unico, tablaTipo: tablaTipo, erptkn: tkn}
                    }).then(function (response) {
                        if (_.has(response.data, 'session')) {
                            window.location.assign(phost());
                        }
                        if (!_.isEmpty(response.data)) {

                        if (interes == 1) {
                        } else if (interes == 2) {
                        } else if (interes == 3) {
                        } else if (interes == 4) {
                        } else if (interes == 5) {
                        } else if (interes == 6) {
                        } else if (interes == 7) {
                        } else if (interes == 8) {
                        }
                        self.$set('sIntereses', response.data.inter);
                    }
                });
                }

            },
            verInteres: function () {
                this.getInteres();
            },
            getInteres: function () {
            //polula el segundo select del header
            var self = this;
            var interes = $('#selInteres').val();
            if (interes == null) {
                interes = $("#selInteres2").val();
            }

            if (validavida == 1 && id_tipo_poliza == 2) {
                self.$set('acreedores', []);
                $("#suma_asegurada_persona").attr("disabled", false);
                //Acreedores
                counter_acre2 = 2;
                inicializaCamposAcreedor();
                $("#vigencia_vida_colectivo").hide();
            }

            var tipointeres = $('#formulario').val();
            if (interes != "") {
                hasInteres = true;
                this.$http.post({
                    url: phost() + 'solicitudes/ajax_get_intereses',
                    async: false,
                    method: 'POST',
                    data: {interes: interes, tipointeres: tipointeres, erptkn: tkn}
                }).then(function (response) {
                    if (_.has(response.data, 'session')) {
                        window.location.assign(phost());
                    }
                    if (!_.isEmpty(response.data)) {
                        var tipoint = response.data.inter.tipointeres;
                        localStorage.setItem("id_intereses", response.data.inter.id);                        

                        if (tipoint == 1) {
                            $(".uuid_carga, #nombre, #clase_equipo, #marca, #modelo, #anio_articulo, #numero_serie, .valor_articulo, #observaciones_articulo").val("");
                            $("#uuid_articulo").val(response.data.inter.uuid_intereses);
                            $("#nombre").val(response.data.inter.nombre);
                            $("#clase_equipo").val(response.data.inter.clase_equipo);
                            $("#marca").val(response.data.inter.marca);
                            $("#modelo").val(response.data.inter.modelo);
                            $("#anio_articulo").val(response.data.inter.anio);
                            $("#numero_serie").val(response.data.inter.numero_serie);
                            $(".valor_articulo").val(response.data.inter.valor);
                            $("#observaciones_articulo").val(response.data.inter.observaciones);

                            $("#id_condicion").val(response.data.inter.id_condicion);
                            $("#campo2[estado]").val(response.data.inter.estado);

                            /*$("#id_condicion option[value='" + response.data.inter.id_condicion + "']").attr("selected", "selected");
                            $("#campo2[estado] option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/


                        } else if (tipoint == 2) {
                            $(".uuid_carga, #no_liquidacion, #fecha_despacho, #fecha_arribo, #fecha_arribo, #detalle, #origen, #destino, .valor_mercancia, #acreedor_carga_opcional, #tipo_obligacion_opcional, #observaciones_carga").val("");
                            $(".uuid_carga").val(response.data.inter.uuid_intereses);
                            $("#no_liquidacion").val(response.data.inter.no_liquidacion);
                            $("#fecha_despacho").val(response.data.inter.fecha_despacho);
                            $("#fecha_arribo").val(response.data.inter.fecha_arribo);
                            $("#detalle").val(response.data.inter.detalle);
                            $("#origen").val(response.data.inter.origen);
                            $("#destino").val(response.data.inter.destino);
                            $(".valor_mercancia").val(response.data.inter.valor);

                            $("#observaciones_carga").val(response.data.inter.observaciones);
                            $(".tipo_empaque").val(response.data.inter.tipo_empaque);
                            //$(".tipo_empaque option[value='" + response.data.inter.tipo_empaque + "']").attr("selected", "selected");

                            var y = $(".acreedor_carga option[value='" + response.data.inter.acreedor + "']");
                            if (y.length == 1) {

                                $(".acreedor_carga").val(response.data.inter.acreedor);
                                //$(".acreedor_carga option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");

                                if (response.data.inter.acreedor == "otro") {
                                    $("#acreedor_carga_opcional").val(response.data.inter.acreedor_opcional);
                                }
                            }
                            var w = $(".tipo_obligacion option[value='" + response.data.inter.tipo_obligacion + "']");
                            if (w.length == 1) {
                                $(".tipo_obligacion").val(response.data.inter.tipo_obligacion);
                                //$(".tipo_obligacion option[value='" + response.data.inter.tipo_obligacion + "']").attr("selected", "selected");

                                if (response.data.inter.tipo_obligacion == "otro") {
                                    $("#tipo_obligacion_opcional").val(response.data.inter.tipo_obligacion_opcional);
                                }
                            }

                            $(".condicion_envio").val(response.data.inter.condicion_envio);
                            $(".medio_transporte").val(response.data.inter.medio_transporte);
                            $(".estado_carga").val(response.data.inter.estado);
                            /*
                             $(".condicion_envio option[value='" + response.data.inter.condicion_envio + "']").attr("selected", "selected");
                             $(".medio_transporte option[value='" + response.data.inter.medio_transporte + "']").attr("selected", "selected");
                             $(".estado_carga option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/

                         } else if (tipoint == 3) {
                            $(".uuid_aereo, #serie_aereo, #marca_aereo, #modelo_aereo, #matricula_aereo, #valor_aereo, #pasajeros_aereo, #tripulacion_a, #observaciones_aereo").val("");
                            $(".uuid_aereo").val(response.data.inter.uuid_intereses);
                            $("#serie_aereo").val(response.data.inter.serie);
                            $("#marca_aereo").val(response.data.inter.marca);
                            $("#modelo_aereo").val(response.data.inter.modelo);
                            $("#matricula_aereo").val(response.data.inter.matricula);
                            $("#valor_aereo").val(response.data.inter.valor);
                            $("#pasajeros_aereo").val(response.data.inter.pasajeros);
                            $("#tripulacion_a").val(response.data.inter.tripulacion);
                            $("#observaciones_aereo").val(response.data.inter.observaciones);

                            $(".estado_aereo").val(response.data.inter.estado);
                            //$(".estado_aereo option[value='" + response.data.inter.estado + "']").attr("selected", "selected");

                            conta = 1;
                        } else if (tipoint == 4) {
                            $(".uuid_casco_maritimo, #serie_maritimo, .serier, #marca_maritimo, #nombre_embarcacion, .porcentaje_acreedor_maritimo, #valor_maritimo, #pasajeros_maritimo, #observaciones_maritimo").val("");
                            $(".uuid_casco_maritimo").val(response.data.inter.uuid_intereses);
                            $("#serie_maritimo").val(response.data.inter.serie);
                            $(".serier").val(response.data.inter.serie);
                            $("#nombre_embarcacion").val(response.data.inter.nombre_embarcacion);
                            $("#marca_maritimo").val(response.data.inter.marca);
                            $(".porcentaje_acreedor_maritimo").val(response.data.inter.porcentaje_acreedor);
                            $("#valor_maritimo").val(response.data.inter.valor);
                            $("#pasajeros_maritimo").val(response.data.inter.pasajeros);
                            $("#observaciones_maritimo").val(response.data.inter.observaciones);

                            $(".tipo_maritimo").val(response.data.inter.tipo);
                            $(".acreedor_maritimo").val(response.data.inter.acreedor);
                            $(".estado_casco").val(response.data.inter.estado);
                            /*
                             $(".tipo_maritimo option[value='" + response.data.inter.tipo + "']").attr("selected", "selected");
                             $(".acreedor_maritimo option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");
                             $(".estado_casco option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/

                         } else if (tipoint == 5) {
                            $("#estadoPersona").val("Activo");
                            $("#asociadodetalle_persona,#tipo_relacion_persona,#certificadoPersona, #validar_editar, #montodetalle_persona,#participacion_persona,#suma_asegurada_persona,#relaciondetalle_persona,#beneficiodetalle_persona").val('');
                            $("#asociadodetalle_persona").trigger('change');
                            $(".uuid").val(response.data.inter.uuid_intereses);
                            $("#nombrePersona").val(response.data.inter.nombrePersona);
                            $("input:checkbox").prop('checked', false);
                            var splitIden = response.data.inter.identificacion;
                            if (splitIden.indexOf('-') > -1) {
                                $("#identificacion").val("cedula");
                                $('.noPAS').show();
                                $(".PAS").hide();
                                splitIden = splitIden.split("-");
                                if (splitIden.length == 4) {
                                    $("#provincia").val(splitIden[0]);
                                    $("#letra").val(splitIden[1]);
                                    $("#tomo").val(splitIden[2]);
                                    $("#asiento").val(splitIden[3]);
                                    $("#provincia").prop("disabled", false);
                                    $('#id_letras').val(splitIden[1]);
                                   
                                } else if(splitIden.length == 3) {
                                    if(isNaN(splitIden[0])){

                                        $("#provincia").val("");
                                        $("#provincia").prop("disabled", true);
                                        $("#letra").val(splitIden[0]);
                                        $("#tomo").val(splitIden[1]);
                                        $("#asiento").val(splitIden[2]);
                                    }else{
                                        
                                        $("#provincia").prop("disabled", false);
                                        $("#provincia").val(splitIden[0]);   
                                        $("#letra").val("0");
                                        $("#tomo").val(splitIden[1]);
                                        $("#asiento").val(splitIden[2]);
                                    }
                                }
                                 $('#id_letras').val(splitIden[0]);

                            } else {
                                $("#identificacion").val("pasaporte");
                                $("#pasaporte").val(splitIden);
                                $('.noPAS').hide();
                                $(".PAS").show();
                                $('#id_letras').val(0);
                                $('#id_provincia').val(0);
                            }
                            $("#fecha_nacimiento").val(response.data.inter.fecha_nacimiento);
                            $('#estado_civil').val(response.data.inter.estado_civil);
                            $('#nacionalidad').val(response.data.inter.nacionalidad);
                            $('#sexo').val(response.data.inter.sexo);
                            $('#estatura').val(response.data.inter.estatura);
                            $('#peso').val(response.data.inter.peso);
                            $('#telefono_residencial').val(response.data.inter.telefono_residencial);
                            $('#telefono_oficina').val(response.data.inter.telefono_oficina);
                            $('#direccion').val(response.data.inter.direccion_residencial);
                            $('#direccion_laboral').val(response.data.inter.direccion_laboral);
                            $('#observacionesPersona').val(response.data.inter.observaciones);
                            $('#estadoPersona').val(response.data.inter.estado);
                            $('#idPersona').val(response.data.inter.interesestable_id);
                            $('#correoPersona').val(response.data.inter.correo);


                            if (response.data.inter.telefono_principal == 'Residencial') {
                                $('#telefono_residencial_check').prop('checked', true);
                            } else if (response.data.inter.telefono_principal == 'Laboral') {
                                $('#telefono_oficina_check').prop('checked', true);
                            }

                            if (response.data.inter.direccion_principal == 'Residencial') {
                                $('#direccion_residencial_check').prop('checked', true);
                            } else if (response.data.inter.direccion_principal == 'Laboral') {
                                $('#direccion_laboral_check').prop('checked', true);
                            }

                            var today = new Date();
                            var format = response.data.inter.fecha_nacimiento.split("-");
                            var dob = new Date(format[0], format[1], format[2]);
                            var diff = (today - dob);
                            var age = Math.floor(diff / 31536000000);
                            $("[id*=edad]").val(age);
                            $("[id*=edad]").attr('readonly', true);


                        } else if (tipoint == 6) {
                            //$('.acreedor_proyecto').removeAttr("selected");
                            //$('.acreedor_proyecto').prop('selected', true);
                            $(".uuid_proyecto, #nombre_proyecto, #contratista_proyecto, #representante_legal_proyecto, #fecha_concurso, #no_orden_proyecto, .no_ordenr, #duracion_proyecto, .fecha_proyecto, .monto_proyecto, #monto_afianzado, #asignado_acreedor, #ubicacion_proyecto, #acreedor_opcional, #validez_fianza_opcional, #observaciones_proyecto").val("");
                            $(".uuid_proyecto").val(response.data.inter.uuid_intereses);
                            $("#nombre_proyecto").val(response.data.inter.nombre_proyecto);
                            $("#contratista_proyecto").val(response.data.inter.contratista);
                            $("#representante_legal_proyecto").val(response.data.inter.representante_legal);
                            $("#fecha_concurso").val(response.data.inter.fecha_concurso);
                            $("#no_orden_proyecto").val(response.data.inter.no_orden);
                            $(".no_ordenr").val(response.data.inter.no_orden);
                            $("#duracion_proyecto").val(response.data.inter.duracion);
                            $(".fecha_proyecto").val(response.data.inter.fecha);
                            $(".monto_proyecto").val(response.data.inter.monto);
                            $("#monto_afianzado").val(response.data.inter.monto_afianzado);
                            //var acreedor_proyecto2 = $(".acreedor_proyecto option[value='" + response.data.inter.acreedor + "']").attr("selected", true);
                            $(".acreedor_proyecto").val(response.data.inter.acreedor);
                            $(".acreedor_proyecto").trigger("change");
                            $("#ubicacion_proyecto").val(response.data.inter.ubicacion);
                            $("#acreedor_opcional").val(response.data.inter.acreedor_opcional);
                            $("#validez_fianza_opcional").val(response.data.inter.validez_fianza_opcional);
                            $("#observaciones_proyecto").val(response.data.inter.observaciones);
                            $(".tipo_fianza").val(response.data.inter.tipo_fianza);
                            $(".tipo_fianza").trigger("change");
                            //$(".tipo_fianza option[value='" + response.data.inter.tipo_fianza + "']").attr("selected", "selected");
                            $(".tipo_propuesta").val(response.data.inter.tipo_propuesta);
                            $(".tipo_propuesta").trigger("change");
                            //$(".tipo_propuesta option[value='" + response.data.inter.tipo_propuesta + "']").attr("selected", "selected");
                            //$(".acreedor_proyecto option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");
                            $(".validez_fianza_pr").val(response.data.inter.validez_fianza_pr);
                            $(".validez_fianza_pr").trigger("change");

                            //$(".validez_fianza_pr option[value='" + response.data.inter.validez_fianza_pr + "']").attr("selected", "selected");

                            //$(".estado_proyecto").val(response.data.inter.estado);
                            //$(".estado_proyecto").trigger("change");

                            $(".estado_proyecto").val(response.data.inter.estado);

                            //$(".estado_proyecto option[value='" + response.data.inter.estado + "']").attr("selected", "selected");

                            var acreedor_proyecto = $('.acreedor_proyecto').val();
                            if (acreedor_proyecto === 'otro') {
                                $('.acreedor_opcional_proyecto').removeAttr('disabled');

                            } else {
                                $('.acreedor_opcional_proyecto').attr('disabled', true);
                                $(".acreedor_opcional_proyecto").val('');

                            }
                            var validar_tipo_fianza = $('.tipo_fianza').val();
                            if (validar_tipo_fianza === 'propuesta') {
                                $('.tipo_propuesta').removeAttr('disabled');
                            } else {
                                $('.tipo_propuesta').attr('disabled', true);
                                $('.tipo_propuesta_opcional').attr('disabled', true);
                                $(".tipo_propuesta").val('');
                                $(".tipo_propuesta_opcional").val('');

                            }
                            var tipo_propuesta = $('.tipo_propuesta').val();
                            if (tipo_propuesta === 'otro') {
                                $('.tipo_propuesta_opcional').removeAttr('disabled');

                            } else {
                                $('.tipo_propuesta_opcional').attr('disabled', true);
                                $(".tipo_propuesta_opcional").val('');

                            }
                            var validez_fianza_pr = $('.validez_fianza_pr').val();
                            if (validez_fianza_pr === 'otro') {
                                $('.validez_fianza_opcional').removeAttr('disabled');

                            } else {
                                $('.validez_fianza_opcional').attr('disabled', true);
                                $(".validez_fianza_opcional").val('');
                            }
                        } else if (tipoint == 7) {

                            $(".uuid_ubicacion, #nombre_ubicacion, #direccion_ubicacion, .serier, #edif_mejoras, #contenido, #maquinaria, #inventario, #acreedor_ubicacion_opcional, #porcentaje_acreedor_ubicacion, #observaciones_ubicacion").val("");
                            $(".uuid_ubicacion").val(response.data.inter.uuid_intereses);
                            $("#nombre_ubicacion").val(response.data.inter.nombre);
                            $("#direccion_ubicacion").val(response.data.inter.direccion);
                            $(".serier").val(response.data.inter.direccion);
                            $("#edif_mejoras").val(response.data.inter.edif_mejoras);
                            $("#contenido").val(response.data.inter.contenido);
                            $("#maquinaria").val(response.data.inter.maquinaria);
                            $("#inventario").val(response.data.inter.inventario);
                            $("#acreedor_ubicacion_opcional").val(response.data.inter.acreedor_ubicacion_opcional);

                            $("#porcentaje_acreedor_ubicacion").val(response.data.inter.porcentaje_acreedor);
                            $("#observaciones_ubicacion").val(response.data.inter.observaciones);
                            $("#acreedor_ubicacion").val(response.data.inter.acreedor);
                            $("#acreedor_ubicacion").trigger("change");

                            $("#acreedor_ubicacion").val(response.data.inter.acreedor);
                            $(".estado_ubicacion").val(response.data.inter.estado);
                            /*$("#acreedor_ubicacion option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");
                            $(".estado_ubicacion option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/

                            var acreedor_ubicacion = $('.acreedor_ubicacion').val();
                            if (acreedor_ubicacion === 'otro') {
                                $('#acreedor_ubicacion_opcional').removeAttr('disabled');

                            } else {
                                $('#acreedor_ubicacion_opcional').attr('disabled', true);
                            }

                        } else if (tipoint == 8) {

                            $("#uuid_vehiculo, #chasis, #unidad, #marca, #modelo, #placa, #ano, #motor, #color, #capacidad, #operador, #extras, #valor_extras, #porcentaje_acreedor, #observaciones_vehiculo ").val("");
                            $("#uuid_vehiculo").val(response.data.inter.uuid_intereses);
                            $("#chasis").val(response.data.inter.chasis);
                            $("#unidad").val(response.data.inter.unidad);
                            $("#placa").val(response.data.inter.placa);
                            $(".marca_vehiculo").val(response.data.inter.marca);
                            $(".modelo_vehiculo").val(response.data.inter.modelo);
                            $("#ano").val(response.data.inter.ano);
                            $("#motor").val(response.data.inter.motor);
                            $("#color").val(response.data.inter.color);
                            $("#capacidad").val(response.data.inter.capacidad);
                            $("#operador").val(response.data.inter.operador);
                            $("#extras").val(response.data.inter.extras);
                            $("#valor_extras").val(response.data.inter.valor_extras);
                            $(".porcentaje_vehiculo").val(response.data.inter.porcentaje_acreedor);
                            $("#observaciones_vehiculo").val(response.data.inter.observaciones);

                            $("#uso").val(response.data.inter.uso);
                            $(".condicion_vehiculo").val(response.data.inter.condicion);
                            $(".acreedor").val(response.data.inter.acreedor);
                            $(".estado").val(response.data.inter.estado);

                            /*
                             $("#uso option[value='" + response.data.inter.uso + "']").attr("selected", "selected");
                             $(".condicion_vehiculo option[value='" + response.data.inter.condicion + "']").attr("selected", "selected");
                             $(".acreedor option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");
                             $(".estado option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/

                         } else {

                         }
                     }

                 });

} else {

    $("#uuid_articulo, #nombre, #clase_equipo, #marca_articulo, #modelo_articulo, #anio_articulo, #numero_serie, .valor_articulo, #observaciones_articulo, certificadodetalle_articulo, #sumaaseguradadetalle_articulo, #primadetalle_articulo, #deducibledetalle_articulo").val("");
    $(".uuid_carga, #no_liquidacion, #fecha_despacho, #fecha_arribo, #fecha_arribo, #detalle, #origen, #destino, .valor_mercancia, #acreedor_carga_opcional, #tipo_obligacion_opcional, #observaciones_carga, #certificadodetalle_carga, #sumaaseguradadetalle_carga, #primadetalle_carga, #deducibledetalle_carga").val("");
    $(".uuid_aereo, #serie_aereo, #marca_aereo, #modelo_aereo, #matricula_aereo, #valor_aereo, #pasajeros_aereo, #tripulacion_a, #observaciones_aereo, #certificadodetalle_aereo, #sumaaseguradadetalle_aereo, #primadetalle_aereo, #deducibledetalle_aereo").val("");
    $(".uuid_casco_maritimo, #serie_maritimo, .serier, #nombre_embarcacion, #marca_maritimo, .porcentaje_acreedor_maritimo, #valor_maritimo, #pasajeros_maritimo, #observaciones_maritimo, #certificadodetalle_maritimo, #sumaaseguradadetalle_maritimo, #primadetalle_maritimo, #deducibledetalle_maritimo").val("");
    $(".uuid,#correoPersona,#nombrePersona,#provincia,#idPersona,#fecha_nacimiento,#estado_civil,#nacionalidad,#sexo,#estatura,#peso,#telefono_residencial,#telefono_oficina,#direccion,#direccion_laboral,#observacionesPersona,#identificacion,#pasaporte,#provinicia,#letra,#tomo,#asiento,#certificadoPersona, #primadetalle_persona, #montodetalle_persona,#participacion_persona,#suma_asegurada_persona").val("");
    $(".uuid_proyecto, #nombre_proyecto, .no_ordenr, #contratista_proyecto, #representante_legal_proyecto, #fecha_concurso, #no_orden_proyecto, #duracion_proyecto, .fecha_proyecto, .monto_proyecto, #monto_afianzado, #asignado_acreedor, #ubicacion_proyecto, #acreedor_opcional, #validez_fianza_opcional, #observaciones_proyecto, #certificadodetalle_proyecto, #sumaaseguradadetalle_proyecto, #primadetalle_proyecto, #deducibledetalle_proyecto").val("");
    $(".uuid_ubicacion, #nombre_ubicacion, #direccion_ubicacion, #edif_mejoras, #contenido, #maquinaria, #inventario, #acreedor_ubicacion_opcional, #porcentaje_acreedor_ubicacion, #observaciones_ubicacion, #certificadodetalle_ubicacion, #sumaaseguradadetalle_ubicacion, #primadetalle_ubicacion, #deducibledetalle_ubicacion").val("");
    $("#uuid_vehiculo, #chasis, #unidad, #marca, #modelo, #placa, #ano, #motor, #color, #capacidad, #operador, #extras, #valor_extras, #porcentaje_acreedor, #observaciones_vehiculo, #certificadodetalle_vehiculo, #sumaaseguradadetalle_vehiculo, #primadetalle_vehiculo, #deducibledetalle_vehiculo").val("");
    $("#selInteres,#asociadodetalle_persona,#relaciondetalle_persona,#beneficiodetalle_persona").val('');
    $("#asociadodetalle_persona").trigger('change');
    $("input:checkbox").prop('checked', false);
    $("#id_condicion option, .tipo_empaque option, .condicion_envio option, .medio_transporte option, .acreedor_carga option, .tipo_obligacion option, .tipo_maritimo option, .acreedor_maritimo option, .tipo_fianza option, .tipo_propuesta option, .acreedor_proyecto option, .validez_fianza_pr option, #acreedor_ubicacion option, #uso option, .condicion_vehiculo option, .acreedor option , #relaciondetalle_persona, #asociadodetalle_persona, ·beneficiodetalle_persona").removeAttr("selected");
    if (id_tipo_poliza == 2) {
        if (id_tipo_int_asegurado == 1) {
            formularioCrear.getPrimaNeta("primadetalle_articulo");
        } else if (id_tipo_int_asegurado == 2) {
            formularioCrear.getPrimaNeta("primadetalle_carga");
        } else if (id_tipo_int_asegurado == 3) {
            formularioCrear.getPrimaNeta("primadetalle_aereo");
        } else if (id_tipo_int_asegurado == 4) {
            formularioCrear.getPrimaNeta("primadetalle_maritimo");
        } else if (id_tipo_int_asegurado == 5) {
            formularioCrear.getPrimaNeta("primadetalle_persona");
        } else if (id_tipo_int_asegurado == 6) {
            formularioCrear.getPrimaNeta("primadetalle_proyecto");
        } else if (id_tipo_int_asegurado == 7) {
            formularioCrear.getPrimaNeta("primadetalle_ubicacion");
        } else if (id_tipo_int_asegurado == 8) {
            formularioCrear.getPrimaNeta("primadetalle_vehiculo");

        }
    }
}

},
getAsociado: function () {
    var self = this;
    var unico = $('#detalleunico').val();
    if(tablaTipo == undefined){
        tablaTipo = 0;
    }
    this.$http.post({
        url: phost() + 'solicitudes/ajax_get_asociados',
        method: 'POST',
        data: {unico: unico, tablaTipo: tablaTipo, erptkn: tkn}
    }).then(function (response) {

        if (_.has(response.data, 'session')) {
            window.location.assign(phost());
        }
        if (!_.isEmpty(response.data)) {
            self.$set('InteresesAsociados', response.data.inter);

                    /* for (var i =response.data.inter.length - 1; i >= 0; i--) {
                     value =response.data.inter[i];
                     options+="<option value='"+value.nombrePersona+"'>"+value.nombrePersona+"</option>";    
                     
                     }
                     $('#selpagadornombre').empty().append(options);*/
                     $("#relaciondetalle_persona option").each(function () {

                        if ($(this).val() == "Dependiente" || $(this).val() == "Beneficiario") {
                            $(this).prop("disabled", false);
                        }
                    });
                 }
             });
},
coberturasModal: function (e) {
            //Inicializar opciones del Modal
            $('#verCoberturas').modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
            });
            //Cerrar modal de opciones        
            var pantalla = $('.div_coberturas');
            var botones_coberturas = $('.botones_coberturas');

            pantalla.css('display', 'block');
            botones_coberturas.css('display', 'block');
            opcionesModal.find('.modal-tile').empty();
            opcionesModal.find('.modal-body').empty().append(pantalla);
            opcionesModal.find('.modal-footer').empty().append(botones_coberturas);
            opcionesModal.modal('show');
        },
        getComisionesInfo: function (e) {

            var self = this;
            var id_planes = $('#planes').val();
            this.$http.post({
                url: phost() + 'solicitudes/ajax-get-comision',
                method: 'POST',
                data: {id_planes: id_planes, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    if(vista == "editar"){
                        self.$set('impuestoPlan', response.data.planes.impuesto.impuesto);
                    }else{
                        $("#comision").val(response.data.planes.comisiones.comision);
                        self.$set('comisionPlanInfo', response.data.planes.comisiones.comision);
                        self.$set('impuestoPlan', response.data.planes.impuesto.impuesto);
                        self.$set('impuestoMonto', response.data.planes.impuesto.impuesto);
                        self.$set('sobreComision', response.data.planes.sobre_comisiones.sobre_comision);
                        permisos_editar == true ? self.$set('isEditable', false) : '';
                    }
                }
            });
        },
        getPrimaNeta: function (e) {

            var self = this;
            var id_planes = $('#planes').val();
            this.$http.post({
                url: phost() + 'solicitudes/ajax_get_prima',
                method: 'POST',
                data: {id_planes: id_planes, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    //self.$set('primaAnual', response.data.planes.prima);
                    /* if(!hasInteres){
                     $("input[name='campodetalle[prima_anual]']").val(response.data.planes.prima);
                 }*/
                 $("#" + e + "").val(response.data.planes.prima);

             } else {
                $("#" + e + "").val("");
                //this.getPlanesInfo();
            }
        });
        },
        getPrimaAnual: function (e) {
            this.primaAnual = e;
        },
        getPorcentajeParticipacion: function (index, e) {
            var self = this;
            var agente_id = $('#agentes_' + index).val();
            var id_ramo = $('#ramo_id').val();
            this.$http.post({
                url: phost() + 'solicitudes/ajax-get-porcentaje',
                method: 'POST',
                data: {agente_id: agente_id, ident_ramo: id_ramo, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    $('#agentes_participacion_' + index).val(response.data[0].participacion);
                    var divList = $("div > #total_agentes_participantes");
                    var total = divList.length;
                    $('#cantidad').val(total);
                    this.sumatotal();
                } else {
                    $('#agentes_participacion_' + index).val("");
                    var divList = $("div > #total_agentes_participantes");
                    var total = divList.length;
                    $('#cantidad').val(total);
                    this.sumatotal();
                }
            });
        },
        getPagadorCampo: function () {
            //polula el segundo select del header
            var self = this;
            var cliente_tipo = id_tipo_int_asegurado;
            $("#divpagadornombre").hide();
            $("#campopagador").removeAttr("data-rule-required");
            $("#selpagadornombre").removeAttr("data-rule-required");
            this.$http.post({
                url: phost() + 'solicitudes/ajax_get_pagador',
                method: 'POST',
                data: {tipo_cliente: cliente_tipo, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.tablaError = "";
                    self.$set('catalogoPagador', response.data);
                    self.$set('tablaError', '');
                }
            });
        },
        getOpcionPagador: function () {
            var self = this;
            var pagador_tipo = $('#pagador').val();
            if (pagador_tipo == "cliente" || pagador_tipo == "otro") {
                $("#divpagadornombre").show();
                $("#divpgnombre").show();
                $("#campopagador").attr("data-rule-required", true);
                $("#divselpagador").hide();
                $("#selpagadornombre").removeAttr("data-rule-required");
                var paga = $(".ncli").val();
                if (pagador_tipo == "cliente") {
                    $("#campopagador").val(paga);
                    $("#campopagador").attr("readonly", "readonly");
                } else {
                    $("#campopagador").val("");
                    $("#campopagador").removeAttr("readonly");
                }
            } else if (pagador_tipo == "asegurado") {
                $("#divpagadornombre").show();
                $("#divpgnombre").hide();
                $("#campopagador").removeAttr("data-rule-required");
                $("#divselpagador").show();
                $("#selpagadornombre").attr("data-rule-required", true);
            } else {
                $("#divpagadornombre").hide();
                $("#campopagador").removeAttr("data-rule-required");
                $("#selpagadornombre").removeAttr("data-rule-required");
            }
        },
        addAgente: function () {
            var divList = $("div > #total_agentes_participantes");
            var total = divList.length;
            $('#cantidad').val(total);
            this.sumatotal();
            this.agentesArray.push({value: ''});

        },
        removeAgente: function (agt) {
            var divList = $("div > #total_agentes_participantes");
            var total = parseInt(divList.length) - parseInt(1);
            $('#cantidad').val(total);
            if (total == 1) {
                $('#removerAgente').hide();
            } else {
                $('#removerAgente').show();
            }
            this.sumatotal();
            this.agentesArray.$remove(agt);
        },
        addAgenteEditar: function () {
            var divList = $("div > #total_agentes_participantes");
            var total = divList.length;
            $('#cantidad').val(total);
            this.sumatotal();
            this.participacion.push({value: ''});

        },
        removeAgenteEditar: function (agt) {
            var divList = $("div > #total_agentes_participantes");
            var total = parseInt(divList.length) - parseInt(1);
            $('#cantidad').val(total);
            if (total == 1) {
                $('#removerAgente').hide();
            } else {
                $('#removerAgente').show();
            }
            this.sumatotal();
            this.participacion.$remove(agt);
        },
        documenteshion: function (n_check, nombre, obligatorio, modulo) {
            var id_intereses = localStorage.getItem("id_intereses");
            var id_cliente = localStorage.getItem("id_cliente");
            var mensaje = "";
            var cont = 1;
            var cont2 = 0;

            for (var x = 0; x <= $('#cantidad_check').val(); x++) {
                if ($('#documentacion_' + x).prop('checked') === true) {
                    cont = cont + 1;
                }

            }

            if ($('#documentacion_' + n_check).prop('checked') === false && obligatorio === "Si") {
                mensaje = "Campo requerido";
                $('#error_check').val("<label class='error'>" + mensaje + "</label>");
            }
            if ($('#documentacion_' + n_check).prop('checked') === true) {

                $('#requerido_chek_' + n_check).val(nombre);
                //$('#file_tools_solicitudes_' + n_check).before('<div class="file_upload_solicitudes" id="f' + n_check + '"><input readonly="readonly" value="' + nombre + '" name="nombre_documento[]" id="nombre_documento" type="text" style="width: 300px!important; float: left;" class="form-control"><input data-rule-required="true" name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><input type="hidden" value="' + nombre + '"  v-model="modulo" id="nombre'+ n_check +'" name="campodocumentacion[nombre_'+ n_check +']" class="modulo"><input type="hidden" value="' + modulo + '"  v-model="modulo" id="opcion'+ n_check +'" name="campodocumentacion[modulo_'+ n_check +']" class="modulo"><br><br><br></div>');
                $('#file_tools_solicitudes_' + n_check).before('<div class="file_upload_solicitudes" id="f' + n_check + '"><input readonly="readonly" value="' + nombre + '" name="nombre_documento[]" id="nombre_documento" type="text" style="width: 300px!important; float: left;" class="form-control"><input data-rule-required="true" name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><input type="hidden" value="' + modulo + '" id="opcion' + n_check + '" name="campomodulo[]" class="modulo"><input type="hidden" value="' + id_tipo_int_asegurado + '" name="campoidinteres[]"><input type="hidden" value="' + id_cliente + '" name="campoidcliente[]"><input type="hidden" value="' + id_intereses + '" name="campoidinteresunico[]"><br><br><br></div>');

            } else if ($('#documentacion_' + n_check).prop('checked') === false) {
                $('#requerido_chek_' + n_check).val("");
//                $('#del_file_solicitudes').hide();

$('#f' + n_check).remove();
} else {
    $('#f' + n_check).remove();
}
for (var x = 0; x <= $('#cantidad_check').val(); x++) {
    if ($('#f' + x).length) {
        cont2 = cont2 + 1;
    }

}

if (vista === 'crear') {
    if (cont > 1) {
        $("#nombre_doc_titulo").show();
    } else {
        $("#nombre_doc_titulo").hide();
    }
} else if (vista === 'editar') {
    if (cont2 > 0) {
        $("#nombre_doc_titulo_editar").show();
    } else {
        $("#nombre_doc_titulo_editar").hide();
    }
}

},
sumatotal: function () {
    var divList = $("div > #total_agentes_participantes");
    var total = divList.length;

            var suma = 0;
            var agente = "";
            var porcentaje = "";
            var cantidad = $('#cantidad').val();


            for (var i = 0; i < cantidad; i++) {
                suma = parseFloat(suma) + parseFloat($('#agentes_participacion_' + i).val());
                if (i === 0) {
                    agente = $('#agentes_' + i).val();
                    porcentaje = $('#agentes_participacion_' + i).val();
                } else {
                    agente = agente + "," + $('#agentes_' + i).val();
                    porcentaje = porcentaje + "," + $('#agentes_participacion_' + i).val();
                }
            }
            
            if(agtPrincipal!="")
            {
                $('#participacionTotal').val(parseFloat(100));
            }
            else
            {
                $('#participacionTotal').val(parseFloat(suma));
            }
            
            $('#agente').val(agente);
            $('#porcentaje').val(porcentaje);
            
            
            if(isNaN(suma))
                    $('#porcAgentePrincipal').val(parseFloat(100).toFixed(2));
            else
                $('#porcAgentePrincipal').val(parseFloat(parseFloat(100).toFixed(2)-parseFloat(suma)).toFixed(2));  
            
        },
        limpiarDetalle: function () {
            $(" #sumaaseguradadetalle_articulo, #primadetalle_articulo, #certificadodetalle_articulo, #deducibledetalle_articulo, #sumaaseguradadetalle_carga, #primadetalle_carga, #certificadodetalle_carga, #deducibledetalle_carga, #sumaaseguradadetalle_aereo, #primadetalle_aereo, #certificadodetalle_aereo, #deducibledetalle_aereo, #sumaaseguradadetalle_maritimo, #primadetalle_maritimo, #certificadodetalle_maritimo, #deducibledetalle_maritimo, #sumaaseguradadetalle_proyecto, #primadetalle_proyecto, #certificadodetalle_proyecto, #deducibledetalle_proyecto, #sumaaseguradadetalle_ubicacion, #primadetalle_ubicacion, #certificadodetalle_ubicacion, #deducibledetalle_ubicacion, #sumaaseguradadetalle_vehiculo, #primadetalle_vehiculo, #certificadodetalle_vehiculo, #deducibledetalle_vehiculo").val("");
        },
        setPrimaNeta: function () {
            if (id_tipo_int_asegurado == 1) {
                this.getPrimaNeta("primadetalle_articulo");
            } else if (id_tipo_int_asegurado == 2) {
                this.getPrimaNeta("primadetalle_carga");
            } else if (id_tipo_int_asegurado == 3) {
                this.getPrimaNeta("primadetalle_aereo");
            } else if (id_tipo_int_asegurado == 4) {
                this.getPrimaNeta("primadetalle_maritimo");
            } else if (id_tipo_int_asegurado == 5) {
                this.getPrimaNeta("primadetalle_persona");
            } else if (id_tipo_int_asegurado == 6) {
                this.getPrimaNeta("primadetalle_proyecto");
            } else if (id_tipo_int_asegurado == 7) {
                this.getPrimaNeta("primadetalle_ubicacion");
            } else if (id_tipo_int_asegurado == 8) {
                this.getPrimaNeta("primadetalle_vehiculo");
            }
        },
        cargaAcreedores: function (id) {
            //polula el segundo select del header
            var self = this;
            var idinteres_detalle = id;
            this.$http.post({
                url: phost() + 'solicitudes/ajax_carga_acreedores_vida_colectivo',
                method: 'POST',
                asyn: false,
                data: {idinteres_detalle: idinteres_detalle, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    console.log("acreedor colectivo");
                    console.log(response.data);
                    self.tablaError = "";
                    self.$set('acreedores', response.data);
                    counter_acre2 = response.data.length + 2;
                    
                    setTimeout(function() {
                        $("#vigencia_vida_colectivo").show();
                        inicializaCamposAcreedor();
                    }, 1000);                    
                }else{
                    console.log("Erroooooor");
                    self.$set('acreedores', []);
                    inicializaCamposAcreedor();
                    $("#vigencia_vida_colectivo").show();
                }
            });
        }

    },
    computed: {

        impuestoMonto: function () {
            //var imp = this.exoneradoImpuestos == false ? this.impuestoPlan : 0; 
            /*var cadena = $("#ramoscadena").val();
             var imp = 7;
             if ((cadena.indexOf("Auto Individual") > -1) || (cadena.indexOf("Auto Colectivo/Flota") > -1)) {
             imp = 6;
             }*/
             if (this.exoneradoImpuestos == null || this.exoneradoImpuestos == "" || this.exoneradoImpuestos == false) {
                this.exoneradoImpuestos = false;
            }
            var impuesto = this.exoneradoImpuestos == false ? this.impuestoPlan : 0;
            var impuesto_monto = (parseFloat(this.primaAnual) + parseFloat(this.otrosPrima) - parseFloat(this.descuentosPrima)) * (impuesto / 100);

            return parseFloat(impuesto_monto);
        },
        totalPrima: function () {
            this.primaAnual = this.primaAnual == '' ? '0.00' : this.primaAnual;
            this.otrosPrima = this.otrosPrima == '' ? '0.00' : this.otrosPrima;
            this.descuentosPrima = this.descuentosPrima == '' ? '0.00' : this.descuentosPrima;
            var impuesto_monto = this.impuestoMonto;
            var total = parseFloat(this.primaAnual) + parseFloat(impuesto_monto) + parseFloat(this.otrosPrima) - parseFloat(this.descuentosPrima);
            return parseFloat(total);
        },

}

});

formularioCrear.selectIntereses();
formularioCrear.getPagadorCampo();


function verIntereses() {
    formularioCrear.getInteres();
    formularioCrear.limpiarDetalle();
    formularioCrear.setPrimaNeta();
}

$("#cliente_seleccionado").change(function () {
    formularioCrear.getClienteSeleccionadoInfo();
    formularioCrear.getClienteCentroFacturable();
    formularioCrear.grupoInfo();
    formularioCrear.direccionInfo();
});

var coberturasForm = new Vue({
    el: ".div_coberturas",
    data: {
        coberturasInfo: [],
    },
    methods: {

        addCampos: function () {

            this.coberturasInfo.coberturas.push({value: 'hola'});
        },
        removeCampos: function (find) {
            this.coberturasInfo.coberturas.$remove(find);
        },
        addCamposDeduc: function () {
            this.coberturasInfo.deducion.push({value: ''});
        },
        removeCamposDeduc: function (find) {

            this.coberturasInfo.deducion.$remove(find);
        },
        setPlanValues: function () {

            if (customModalValidation(validateFields) === 0 && vista != 'editar') {
                planesCoberturasDeducibles.push({
                    'coberturas': {nombre: $("input[name='coberturasNombre[]']").map(function () {
                        return $(this).val();
                    }).get(),
                    valor: $("input[name='coberturasValor[]']").map(function () {
                        return $(this).val();
                    }).get()
                },
                'deducibles': {nombre: $("input[name='deduciblesNombre[]']").map(function () {
                    return $(this).val();
                }).get(),
                valor: $("input[name='deduciblesValor[]']").map(function () {
                    return $(this).val();
                }).get()
            }

        });
                $("#planesCoberturasDeducibles").val(JSON.stringify(planesCoberturasDeducibles[0]));
                $("#verCoberturas").modal("hide");

            } else if (customModalValidation(validateFields) === 0 && vista === 'editar') {

                planesCoberturasDeducibles = [];
                planesCoberturasDeducibles.push({
                    'coberturas': {nombre: $("input[name='coberturasNombre[]']").map(function () {
                        return $(this).val();
                    }).get(),
                    valor: $("input[name='coberturasValor[]']").map(function () {
                        return $(this).val();
                    }).get()
                },
                'deducibles': {nombre: $("input[name='deduciblesNombre[]']").map(function () {
                    return $(this).val();
                }).get(),
                valor: $("input[name='deduciblesValor[]']").map(function () {
                    return $(this).val();
                }).get()
            }
        });

                $("#planesCoberturasDeducibles").val(JSON.stringify(planesCoberturasDeducibles[0]));
                $("#verCoberturas").modal("hide");

            } else {

                $("#planesCoberturasDeducibles").val("");
            }
        },
        clearFields: function () {

            clearFields(validateFields);
            $(".error").remove();
            $("#verCoberturas").modal("hide");

        }
    }


});

function eliminaacreedor(x){
    $('#a' + x +'').remove();  
}

if (vista == "crear") {
    var counter_acre = 2;
    var counter_acre2 = 2;
    $('.del_file_acreedores_adicionales').hide();
}else if (vista == "editar") {
    var counter_acre = contacre+2;
    var counter_acre2 = contacre+2;
    $('#del_acre').hide();
}


$(document).ready(function () {
    
    if (vista === 'editar') {
        $('#documentos_editar').show();
        $('#docentregados_crear').hide();
        $("#nombre_doc_titulo").hide();
        $("#nombre_doc_titulo_editar").hide();
        $('#cantidad_check').val(documentaciones.length);

        if (validavida == 1 && id_tipo_poliza == 1) {
            $("#campos_vida_acreedor_crear").remove();
        }        
        
        if(agtPrincipal!="")
        {
            $('.agentePrincipal').show();
            //$('#nombreAgentePrincipal').val(agtPrincipal);
            $('#nombreAgentePrincipal').append('<option value="" selected="selected">'+agtPrincipal+'</option>');
            $('#porcAgentePrincipal').val(parseFloat(agtPrincipalporcentaje).toFixed(2));
        }
        else
        {
            $('.agentePrincipal').hide();
        }

        //Inicializa El Check de Poliza Declrativa con valor
        $("span.switchery-default").remove();
        var elem = document.querySelector('#polizaDeclarativa');
        var init = new Switchery(elem); 
        
    } else {
        $("#documentos_editar").hide();
        $('#docentregados_crear').show();
        $("#nombre_doc_titulo").hide();
        $("#nombre_doc_titulo_editar").hide();

        $("#campos_vida_acreedor_editar").remove();
        
        
        if(agtPrincipal!="")
        {
            $('.agentePrincipal').show();
            $('#nombreAgentePrincipal').append('<option value="" selected="selected">'+agtPrincipal+'</option>');
            $('#porcAgentePrincipal').val(parseFloat(agtPrincipalporcentaje).toFixed(2));
        }
        else
        {
            $('.agentePrincipal').hide();
        }
       
        if (localStorage.getItem('ml-selected') == "Solicitudes" && id_tipo_poliza == 1) 
        {
            $(".docentregado,.documentos_entregados,#espac").css('display', 'none');
        }
    }
    var counter = 2;
    $('#del_file_solicitudes_adicionales').hide();
    $('#add_file_solicitudes_adicionales').click(function () {

        $('#file_tools_solicitudes_adicionales').before('<div class="file_upload_solicitudes_adicionales  col-xs-8 col-sm-8 col-md-8 col-lg-7" id="h' + counter + '"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" '+
            ' class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><button type="button" onclick="javascript:$(\'#h'+counter+'\').remove();" style="float: left; width: 40px; margin-top: 0px !important; display: block;" class="btn btn-default btn-block" id="del_file_solicitudes_adicionales"><i class="fa fa-trash"></i> </button><br><br> </div>');
        $('#del_file_solicitudes_adicionales').fadeIn(0);
        counter++;
    });
    $('#del_file_solicitudes_adicionales').click(function () {
        if (counter == 3) {
            $('#del_file_solicitudes_adicionales').hide();
        }
        counter--;
        $('#h' + counter).remove();
    });

    if (editar_asignado != 1) {
        $("#usuario_id").attr("disabled", "disabled");
    }
    //----------------------------------------------------------------------
    //Inicializa Parametros para acreedores
    //----------------------------------------------------------------------
    $(".monto_cesion_acreedor").inputmask('currency',{ 
        prefix: "", 
        autoUnmask : true, 
        removeMaskOnSubmit: true 
    });

    $(".porcentaje_cesion_acreedor").inputmask('Regex', { regex: "^[1-9][0-9][.][0-9][0-9]?$|^100[.]00?$|^[0-9][.][0-9][0-9]$" });
    //$(".porcentaje_cesion_acreedor").inputmask('decimal',{min:0, max:100});

    $('.fechas_acreedores_inicio').each(function () {
        var f = $(this).val();
        if ($(this).val() == "0000-00-00") {
            var now = new Date();
            now.setDate(now.getDate());
            var dat = now.getDate();
            var mon = now.getMonth() + 1;
            var year = now.getFullYear();
            if (mon < 10) {
                mon = "0" + mon;
            }
            if (dat < 10) {
                dat = "0" + dat;
            }
            var fe = mon + "/" + dat + "/" + year;
            $(this).val(fe);
            f = "";
        }
        $(this).daterangepicker({ //
           locale: { format: 'MM/DD/YYYY' },
           showDropdowns: true,
           defaultDate: '',
           singleDatePicker: true
        }).val(f);
    });
    $('.fechas_acreedores_fin').each(function () {
        var f = $(this).val();
        if ($(this).val() == "0000-00-00") {
            var now = new Date();
            now.setDate(now.getDate());
            var dat = now.getDate();
            var mon = now.getMonth() + 1;
            var year = now.getFullYear();
            if (mon < 10) {
                mon = "0" + mon;
            }
            if (dat < 10) {
                dat = "0" + dat;
            }
            var fe = mon + "/" + dat + "/" + year;
            $(this).val(fe);
            f = "";
        }
        $(this).daterangepicker({ //
           locale: { format: 'MM/DD/YYYY' },
           showDropdowns: true,
           defaultDate: '',
           singleDatePicker: true
        }).val(f);
    });

    
    /*$('.fechas_acreedores_fin').daterangepicker({ //
         locale: { format: 'YYYY-MM-DD' },
         showDropdowns: true,
         defaultDate: '',
         singleDatePicker: true
     });

    $('#fechainicio_1').daterangepicker({ //
         locale: { format: 'YYYY-MM-DD' },
         showDropdowns: true,
         defaultDate: '',
         singleDatePicker: true
     }).val('');*/
    $('#fechafin_1').daterangepicker({ //
         locale: { format: 'MM/DD/YYYY' },
         showDropdowns: true,
         defaultDate: '',
         singleDatePicker: true
     }).val('');

     $(".fechas_acreedores_inicio").change(function () {
        var vigini = $("#vigencia_desde").val();
        var vigfin = $("#vigencia_hasta").val();
        var actual = $(this).val();

        var id = $(this).attr("id");
        var x = id.split('_');
        var final = $("#fechafin_"+x[1]).val();

        /*if (vigini.indexOf('/') > -1) {
            var dat = vigini.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigini = anio + '-' + mes + '-' + dia ;
        }
        if (vigfin.indexOf('/') > -1) {
            var dat = vigfin.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigfin = anio + '-' + mes + '-' + dia ;
        }*/

        var ini = new Date(vigini);
        var fin = new Date(vigfin);
        var act = new Date(actual);
        var fefin = new Date(final);

        if (act < ini) {
            $(this).val(vigini);
        }else if(act > fin){
            $(this).val(vigfin);
        }else if(final != "" && act > fefin){
            $(this).val(final);
        }
    });
    
    $(".fechas_acreedores_fin").change(function () {
        var vigini = $("#vigencia_desde").val();
        var vigfin = $("#vigencia_hasta").val();
        var actual = $(this).val();

        var id = $(this).attr("id");
        var x = id.split('_');
        var inicial = $("#fechainicio_"+x[1]).val();

        /*if (vigini.indexOf('/') > -1) {
            var dat = vigini.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigini = anio + '-' + mes + '-' + dia ;
        }
        if (vigfin.indexOf('/') > -1) {
            var dat = vigfin.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigfin = anio + '-' + mes + '-' + dia ;
        }*/

        var ini = new Date(vigini);
        var fin = new Date(vigfin);
        var act = new Date(actual);
        var feini = new Date(inicial);

        if (act < ini) {
            $(this).val(vigini);
        }else if(act > fin){
            $(this).val(vigfin);
        }else if(inicial != "" && act < feini){
            $(this).val(inicial);
        }
    });

    $(".monto_cesion_acreedor").keyup(function(){
        var id = $(this).attr("id");
        var x = id.split('_');
        var monto = $("#montocesion_"+x[1]).val();

        if (id_tipo_poliza == 1) {
            var sumaasegurada = $("#suma_asegurada").val();
        }else if(id_tipo_poliza == 2){
            var sumaasegurada = $("#suma_asegurada_persona").val();
        }        
        if (sumaasegurada == "") { sumaasegurada = 0;}
        var porcentaje = (monto * 100 )/(sumaasegurada);
        if (porcentaje > 100) { porcentaje = 100;}
        $("#porcentajecesion_"+x[1]).val(porcentaje);
    });

    $(".porcentaje_cesion_acreedor").keyup(function(){
        var id = $(this).attr("id");
        var x = id.split('_');
        var porcentaje = $("#porcentajecesion_"+x[1]).val();
        if (porcentaje == "") { porcentaje = 0;}
        if (id_tipo_poliza == 1) {
            var sumaasegurada = $("#suma_asegurada").val();
        }else if(id_tipo_poliza == 2){
            var sumaasegurada = $("#suma_asegurada_persona").val();
        } 
        var monto = (porcentaje * sumaasegurada )/(100);
        if (porcentaje>100) {
            $("#montocesion_"+x[1]).val(sumaasegurada);
        }else{
            $("#montocesion_"+x[1]).val(monto);
        }            
    });
    //----------------------------------------------------------------------
    //Fin Inicializacion Parametros para acreedores
    //----------------------------------------------------------------------

    $('input[name="campovigencia[suma_asegurada]"]').keyup(function(){
        var total = $(this).val();        
        $('input[name="campoacreedores_por[]"]').each(function () {
            var id = $(this).attr("id");
            var y = id.split('_');
            var x = $(this).val();
            if (x != "") {
               var monto = (parseFloat(total) * parseFloat(x))/100 ;
               $("#montocesion_"+y[1]).val(monto);
            }                
        });
    });

    $('#suma_asegurada_persona').keyup(function(){
        var total = $(this).val();        
        $('input[name="campoacreedores_por[]"]').each(function () {
            var id = $(this).attr("id");
            var y = id.split('_');
            var x = $(this).val();
            if (x != "") {
               var monto = (parseFloat(total) * parseFloat(x))/100 ;
               $("#montocesion_"+y[1]).val(monto);
            }                
        });
    });

    //Agregar Acreedores a la Vigencia    
    /*$('.add_file_acreedores_adicionales1').click(function () {
        $(".add_file_acreedores_adicionales").hide();
        $(".del_file_acreedores_adicionales").show();
        $('#agrega_acre').before('<div class="row" id="a' + counter_acre2 + '"><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px"><input type="text" name="campoacreedores[]" id="acreedor_'+counter_acre2+'" class="form-control"></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">%</span> <input type="text" name="campoacreedores_por[]" id="porcentajecesion_'+counter_acre2+'" class="form-control porcentaje_cesion_acreedor" value="0"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">$</span> <input type="text" name="campoacreedores_mon[]" id="montocesion_'+counter_acre2+'" value="0" class="form-control monto_cesion_acreedor"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input type="text" name="campoacreedores_ini[]" id="fechainicio_'+counter_acre2+'" class="form-control fechas_acreedores_inicio"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input type="text" name="campoacreedores_fin[]" id="fechafin_'+counter_acre2+'" class="form-control fechas_acreedores_fin"></div></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><button type="button" data="'+counter_acre2+'" class="btn btn-default btn-block add_file_acreedores_adicionales" style="float: left; width: 40px; margin-right:5px;" ><i class="fa fa-plus"></i></button><button type="button" data="'+counter_acre2+'" style="float: left; width: 40px; margin-top:0px!important; display: none" class="btn btn-default btn-block del_file_acreedores_adicionales"><i class="fa fa-trash"></i></button></div></div>');
        //$('#del_file_acreedores_adicionales').fadeIn(0);
        //-----------------------------------------------------
        $(".monto_cesion_acreedor").inputmask('currency',{ 
            prefix: "", 
            autoUnmask : true, 
            removeMaskOnSubmit: true 
        });
        $(".porcentaje_cesion_acreedor").inputmask('decimal',{min:0, max:100});
        $('#fechainicio_'+counter_acre2+'').daterangepicker({ //
           locale: { format: 'YYYY-MM-DD' },
           showDropdowns: true,
           defaultDate: '',
           singleDatePicker: true
        }).val('');
        $('#fechafin_'+counter_acre2+'').daterangepicker({ //
           locale: { format: 'YYYY-MM-DD' },
           showDropdowns: true,
           defaultDate: '',
           singleDatePicker: true
        }).val('');

        $(".fechas_acreedores_inicio").change(function () {
            var vigini = $("#vigencia_desde").val();
            var vigfin = $("#vigencia_hasta").val();
            var actual = $(this).val();

            var id = $(this).attr("id");
            var x = id.split('_');
            var final = $("#fechafin_"+x[1]).val();

            if (vigini.indexOf('/') > -1) {
                var dat = vigini.split('/');
                var dia = dat[1];
                var mes = dat[0];
                var anio = dat[2];
                vigini = anio + '-' + mes + '-' + dia ;
            }
            if (vigfin.indexOf('/') > -1) {
                var dat = vigfin.split('/');
                var dia = dat[1];
                var mes = dat[0];
                var anio = dat[2];
                vigfin = anio + '-' + mes + '-' + dia ;
            }

            var ini = new Date(vigini);
            var fin = new Date(vigfin);
            var act = new Date(actual);
            var fefin = new Date(final);

            if (act < ini) {
                $(this).val(vigini);
            }else if(act > fin){
                $(this).val(vigfin);
            }else if(final != "" && act > fefin){
                $(this).val(final);
            }
        });
        $(".fechas_acreedores_fin").change(function () {
            var vigini = $("#vigencia_desde").val();
            var vigfin = $("#vigencia_hasta").val();
            var actual = $(this).val();

            var id = $(this).attr("id");
            var x = id.split('_');
            var inicial = $("#fechainicio_"+x[1]).val();

            if (vigini.indexOf('/') > -1) {
                var dat = vigini.split('/');
                var dia = dat[1];
                var mes = dat[0];
                var anio = dat[2];
                vigini = anio + '-' + mes + '-' + dia ;
            }
            if (vigfin.indexOf('/') > -1) {
                var dat = vigfin.split('/');
                var dia = dat[1];
                var mes = dat[0];
                var anio = dat[2];
                vigfin = anio + '-' + mes + '-' + dia ;
            }

            var ini = new Date(vigini);
            var fin = new Date(vigfin);
            var act = new Date(actual);
            var feini = new Date(inicial);

            if (act < ini) {
                $(this).val(vigini);
            }else if(act > fin){
                $(this).val(vigfin);
            }else if(inicial != "" && act < feini){
                $(this).val(inicial);
            }
        });

        $(".monto_cesion_acreedor").keyup(function(){
            var id = $(this).attr("id");
            var x = id.split('_');
            var monto = $("#montocesion_"+x[1]).val();

            var sumaasegurada = $("#suma_asegurada").val();
            var porcentaje = (monto * 100 )/(sumaasegurada);
            console.log(porcentaje);
            $("#porcentajecesion_"+x[1]).val(porcentaje);
        });

        $(".porcentaje_cesion_acreedor").keyup(function(){
            var id = $(this).attr("id");
            var x = id.split('_');
            var porcentaje = $("#porcentajecesion_"+x[1]).val();

            var sumaasegurada = $("#suma_asegurada").val();
            var monto = (porcentaje * sumaasegurada )/(100);
            console.log(monto);
            if (porcentaje>100) {
                $("#montocesion_"+x[1]).val(sumaasegurada);
            }else{
                $("#montocesion_"+x[1]).val(monto);
            }            
        });
        //-------------------------------------------------------
        counter_acre++;
        $('.del_file_acreedores_adicionales').unbind().click(function () {
            var d = $(this).attr('data');
           $('#a' + d +'').remove();                        
        });

        counter_acre2++;

    });*/
    // Fin Acreedores
    
   

    if (validavida == 1 && id_tipo_int_asegurado == 5) {
        if (id_tipo_poliza == 1) {
            $("#vigencia_vida_individual").show();
            $("#vigencia_vida_colectivo").remove();
        }else{
            //$("#vigencia_vida_colectivo").show();
            $("#vigencia_vida_individual").remove();
        }
    }else{
        $("#vigencia_vida_individual").remove();
        $("#vigencia_vida_colectivo").remove();
    }
        //-------------------------------------------------------------------------------


});
 

function agregaracre(){
    $(".add_file_acreedores_adicionales").hide();
    $(".del_file_acreedores_adicionales").show();
    $('#agrega_acre').before('<div class="row" id="a' + counter_acre2 + '"><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px"><input type="text" name="campoacreedores[]" id="acreedor_'+counter_acre2+'" class="form-control"></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">%</span> <input type="text" name="campoacreedores_por[]" id="porcentajecesion_'+counter_acre2+'" class="form-control porcentaje_cesion_acreedor" value="0"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">$</span> <input type="text" name="campoacreedores_mon[]" id="montocesion_'+counter_acre2+'" value="0" class="form-control monto_cesion_acreedor"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input type="text" name="campoacreedores_ini[]" id="fechainicio_'+counter_acre2+'" class="form-control fechas_acreedores_inicio"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input type="text" name="campoacreedores_fin[]" id="fechafin_'+counter_acre2+'" class="form-control fechas_acreedores_fin"></div></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><button type="button" data="'+counter_acre2+'" class="btn btn-default btn-block add_file_acreedores_adicionales" onclick="agregaracre()" style="float: left; width: 40px; margin-right:5px;" ><i class="fa fa-plus"></i></button><button type="button" data="'+counter_acre2+'" onclick="eliminaracre('+counter_acre2+')" style="float: left; width: 40px; margin-top:0px!important; display: none" class="btn btn-default btn-block del_file_acreedores_adicionales"><i class="fa fa-trash"></i></button></div></div>');
        //$('#del_file_acreedores_adicionales').fadeIn(0);
        //-----------------------------------------------------
        $(".monto_cesion_acreedor").inputmask('currency',{ 
            prefix: "", 
            autoUnmask : true, 
            removeMaskOnSubmit: true 
        });
        $(".porcentaje_cesion_acreedor").inputmask('Regex', { regex: "^[1-9][0-9][.][0-9][0-9]?$|^100[.]00?$|^[0-9][.][0-9][0-9]$" });
        //$(".porcentaje_cesion_acreedor").inputmask('decimal',{min:0, max:100});
        $('#fechainicio_'+counter_acre2+'').daterangepicker({ //
         locale: { format: 'MM/DD/YYYY' },
         showDropdowns: true,
         defaultDate: '',
         singleDatePicker: true
     }).val('');
        $('#fechafin_'+counter_acre2+'').daterangepicker({ //
         locale: { format: 'MM/DD/YYYY' },
         showDropdowns: true,
         defaultDate: '',
         singleDatePicker: true
     }).val('');

        $(".fechas_acreedores_inicio").change(function () {
            var vigini = $("#vigencia_desde").val();
            var vigfin = $("#vigencia_hasta").val();
            var actual = $(this).val();

            var id = $(this).attr("id");
            var x = id.split('_');
            var final = $("#fechafin_"+x[1]).val();

            /*if (vigini.indexOf('/') > -1) {
                var dat = vigini.split('/');
                var dia = dat[1];
                var mes = dat[0];
                var anio = dat[2];
                vigini = anio + '-' + mes + '-' + dia ;
            }
            if (vigfin.indexOf('/') > -1) {
                var dat = vigfin.split('/');
                var dia = dat[1];
                var mes = dat[0];
                var anio = dat[2];
                vigfin = anio + '-' + mes + '-' + dia ;
            }*/

            var ini = new Date(vigini);
            var fin = new Date(vigfin);
            var act = new Date(actual);
            var fefin = new Date(final);

            if (act < ini) {
                $(this).val(vigini);
            }else if(act > fin){
                $(this).val(vigfin);
            }else if(final != "" && act > fefin){
                $(this).val(final);
            }
        });
        $(".fechas_acreedores_fin").change(function () {
            var vigini = $("#vigencia_desde").val();
            var vigfin = $("#vigencia_hasta").val();
            var actual = $(this).val();

            var id = $(this).attr("id");
            var x = id.split('_');
            var inicial = $("#fechainicio_"+x[1]).val();

            /*if (vigini.indexOf('/') > -1) {
                var dat = vigini.split('/');
                var dia = dat[1];
                var mes = dat[0];
                var anio = dat[2];
                vigini = anio + '-' + mes + '-' + dia ;
            }
            if (vigfin.indexOf('/') > -1) {
                var dat = vigfin.split('/');
                var dia = dat[1];
                var mes = dat[0];
                var anio = dat[2];
                vigfin = anio + '-' + mes + '-' + dia ;
            }*/

            var ini = new Date(vigini);
            var fin = new Date(vigfin);
            var act = new Date(actual);
            var feini = new Date(inicial);

            if (act < ini) {
                $(this).val(vigini);
            }else if(act > fin){
                $(this).val(vigfin);
            }else if(inicial != "" && act < feini){
                $(this).val(inicial);
            }
        });

        $(".monto_cesion_acreedor").keyup(function(){
            var id = $(this).attr("id");
            var x = id.split('_');
            var monto = $("#montocesion_"+x[1]).val();
            if (id_tipo_poliza == 1) {
                var sumaasegurada = $("#suma_asegurada").val();
            }else if(id_tipo_poliza == 2){
                var sumaasegurada = $("#suma_asegurada_persona").val();
            }            
            if (sumaasegurada == "") { sumaasegurada = 0;}
            var porcentaje = (monto * 100 )/(sumaasegurada);
            if (porcentaje > 100) { porcentaje = 100;}
            $("#porcentajecesion_"+x[1]).val(porcentaje);
        });

        $(".porcentaje_cesion_acreedor").keyup(function(){
            var id = $(this).attr("id");
            var x = id.split('_');
            var porcentaje = $("#porcentajecesion_"+x[1]).val();
            if (porcentaje == "") { porcentaje = 0;}
            if (id_tipo_poliza == 1) {
                var sumaasegurada = $("#suma_asegurada").val();
            }else if(id_tipo_poliza == 2){
                var sumaasegurada = $("#suma_asegurada_persona").val();
            } 
            var monto = (porcentaje * sumaasegurada )/(100);
            if (porcentaje>100) {
                $("#montocesion_"+x[1]).val(sumaasegurada);
            }else{
                $("#montocesion_"+x[1]).val(monto);
            }            
        });
        counter_acre++;

    counter_acre2++;
}

function eliminaracre(d){
    $('#a' + d +'').remove(); 
}

function customModalValidation(type) {


    var mensaje = "",
    msgErrors = [],
    errType = 0,
    isValid = true,
    error = "";

    for (var i = 0; i < type.length; i++) {

        $(type[i].field.input).each(function () {
            var element = $(this).val(),
            error = "";
            elementType = type[i].field.type,
            id = elementType + "-" + errType++;

            $(this).attr('id', "");
            if ($.trim(element).length <= 0) {
                mensaje = "Campo obligatorio";
                error = "<label class='error'>" + mensaje + "</label>";

            }
            if (type[i].field.valiation == "numeric" && ($.trim(element).length !== 0)) {
                if (!validateNumbers(element)) {

                    mensaje = "Campo númerico";
                    error = "<label class='error'>" + mensaje + "</label>";

                }
            }

            if (type[i].field.valiation == "alfanúmerico") {

                if (!validateAlphaNumeric(element) && ($.trim(element).length !== 0)) {

                    mensaje = "Campo alfanúmerico";
                    error = "<label class='error'>" + mensaje + "</label>";

                }
            }
            $(".error").remove();

            $(this).attr('id', id);
            msgErrors.push({pair: {id: id, error: error}});
            isValid = addMessages(msgErrors);


        }).get();


    }
    return isValid;
}

function validateAlphaNumeric(text) {

    var exp = new RegExp(/^[A-Za-z\d\s]+$/);

    return  exp.test(text);

}

function validateNumbers(text) {

    //var exp = new RegExp(/^[0-9]$/);
    var exp = new RegExp(/^[0-9]+([.][0-9]+)?$/);
    return  exp.test(text);

}

function addMessages(msg) {

    var counter = 0;
    for (var i = 0; i < msg.length; i++) {

        if (msg[i].pair.error !== "") {

            $("#" + msg[i].pair.id).parent().append(msg[i].pair.error);
            counter++;
        }

    }
    return counter;
}

function clearFields(fieldName) {
    for (var i = 0; i < fieldName.length; i++) {

        $(fieldName[i].field.input).each(function () {
            $(this).val("");
            $("#planesCoberturasDeducibles").val("");

        });

    }

}

$(document).ajaxStop(function () {

    if (selInteres != '') {
        if (selInteres != '' && id_tipo_int_asegurado == 1) {

            $("#selInteres").val(selInteres).trigger('change');

            $("#selInteres").attr('disabled', true);
            $("#nombre").attr('disabled', true);
            $("#clase_equipo").attr('disabled', true);
            $("#marca").attr('disabled', true);
            $("#modelo").attr('disabled', true);
            $("#anio_articulo").attr('disabled', true);
            $("#numero_serie").attr('disabled', true);
            $(".valor_articulo").attr('disabled', true);
            $("#observaciones_articulo").attr('disabled', true);
            $("#id_condicion").attr('disabled', true);
            $(".estado").attr('disabled', true);

        } else if (selInteres != '' && id_tipo_int_asegurado == 2) {

            $("#selInteres").val(selInteres).trigger('change');

            $("#selInteres").attr('disabled', true);
            $("#no_liquidacion").attr('disabled', true);
            $("#fecha_despacho").attr('disabled', true);
            $("#fecha_arribo").attr('disabled', true);
            $("#detalle").attr('disabled', true);
            $("#origen").attr('disabled', true);
            $("#destino").attr('disabled', true);
            $(".valor_mercancia").attr('disabled', true);
            $("#observaciones_carga").attr('disabled', true);
            $(".tipo_empaque").attr('disabled', true);
            $(".acreedor_carga").attr('disabled', true);
            $("#acreedor_carga_opcional").attr('disabled', true);
            $(".tipo_obligacion").attr('disabled', true);
            $("#tipo_obligacion_opcional").attr('disabled', true);
            $(".condicion_envio").attr('disabled', true);
            $(".medio_transporte").attr('disabled', true);
            $(".estado_carga").attr('disabled', true);
        } else if (selInteres != '' && id_tipo_int_asegurado == 3) {


            $("#selInteres").val(selInteres).trigger('change');

            $("#selInteres").attr('disabled', true);
            $(".uuid_aereo").attr('disabled', true);
            $("#serie_aereo").attr('disabled', true);
            $("#marca_aereo").attr('disabled', true);
            $("#modelo_aereo").attr('disabled', true);
            $("#matricula_aereo").attr('disabled', true);
            $("#valor_aereo").attr('disabled', true);
            $("#pasajeros_aereo").attr('disabled', true);
            $("#tripulacion_a").attr('disabled', true);
            $("#observaciones_aereo").attr('disabled', true);
            $(".estado_aereo").attr('disabled', true);
        } else if (selInteres != '' && id_tipo_int_asegurado == 4) {

            $("#selInteres").val(selInteres).trigger('change');

            $("#selInteres").attr('disabled', true);
            $("#serie_maritimo").attr('disabled', true);
            $(".serier").attr('disabled', true);
            $("#nombre_embarcacion").attr('disabled', true);
            $("#marca_maritimo").attr('disabled', true);
            $(".porcentaje_acreedor_maritimo").attr('disabled', true);
            $("#valor_maritimo").attr('disabled', true);
            $("#pasajeros_maritimo").attr('disabled', true);
            $("#observaciones_maritimo").attr('disabled', true);
            $(".tipo_maritimo").attr('disabled', true);
            $(".acreedor_maritimo").attr('disabled', true);
            $(".estado_casco").attr('disabled', true);
        } else if (selInteres != '' && id_tipo_int_asegurado == 5) {

            $("#selInteres").val(selInteres).trigger('change');

            $("#selInteres").attr('disabled', true);
            $("#nombrePersona").attr('disabled', true);
            $("input:checkbox").attr('disabled', true);
            $("#identificacion").attr('disabled', true);
            $('.noPAS').attr('disabled', true);
            $(".PAS").attr('disabled', true);
            $("#provincia").attr('disabled', true);
            $("#letra").attr('disabled', true);
            $("#tomo").attr('disabled', true);
            $("#asiento").attr('disabled', true);
            $("#identificacion").attr('disabled', true);
            $("#pasaporte").attr('disabled', true);
            $('.noPAS').attr('disabled', true);
            $(".PAS").attr('disabled', true);
            $("#fecha_nacimiento").attr('disabled', true);
            $("#edad").attr('disabled', true);
            $('#estado_civil').attr('disabled', true);
            $('#nacionalidad').attr('disabled', true);
            $('#sexo').attr('disabled', true);
            $('#estatura').attr('disabled', true);
            $('#peso').attr('disabled', true);
            $('#telefono_residencial').attr('disabled', true);
            $('#telefono_oficina').attr('disabled', true);
            $('#direccion').attr('disabled', true);
            $('#direccion_laboral').attr('disabled', true);
            $('#observacionesPersona').attr('disabled', true);
            $('#estadoPersona').attr('disabled', true);
            $('#idPersona').attr('disabled', true);
            $('#telefono_residencial_check').attr('disabled', true);
            $('#telefono_oficina_check').attr('disabled', true);
            $('#direccion_residencial_check').attr('disabled', true);
            $('#direccion_laboral_check').attr('disabled', true);

        } else if (selInteres != '' && id_tipo_int_asegurado == 6) {

            $("#selInteres").val(selInteres).trigger('change');

            $("#selInteres").attr('disabled', true);
            $("#nombre_proyecto").attr('disabled', true);
            $("#contratista_proyecto").attr('disabled', true);
            $("#representante_legal_proyecto").attr('disabled', true);
            $("#fecha_concurso").attr('disabled', true);
            $("#no_orden_proyecto").attr('disabled', true);
            $(".no_ordenr").attr('disabled', true);
            $("#duracion_proyecto").attr('disabled', true);
            $(".fecha_proyecto").attr('disabled', true);
            $(".monto_proyecto").attr('disabled', true);
            $("#monto_afianzado").attr('disabled', true);
            $("#asignado_acreedor").attr('disabled', true);
            $("#ubicacion_proyecto").attr('disabled', true);
            $(".acreedor_opcional_proyecto").attr('disabled', true);
            $("#observaciones_proyecto").attr('disabled', true);
            $(".tipo_fianza").attr('disabled', true);
            $(".tipo_propuesta").attr('disabled', true);
            $(".acreedor_proyecto").attr('disabled', true);
            $(".validez_fianza_pr").attr('disabled', true);
            $(".estado_proyecto").attr('disabled', true);
            $('.tipo_propuesta_opcional').attr('disabled', true);
            $('.validez_fianza_opcional').attr('disabled', true);

        } else if (selInteres != '' && id_tipo_int_asegurado == 7) {

            $("#selInteres").val(selInteres).trigger('change');

            $("#selInteres").attr('disabled', true);
            $("#nombre_ubicacion").attr('disabled', true);
            $("#direccion_ubicacion").attr('disabled', true);
            $(".serier").attr('disabled', true);
            $("#edif_mejoras").attr('disabled', true);
            $("#contenido").attr('disabled', true);
            $("#maquinaria").attr('disabled', true);
            $("#inventario").attr('disabled', true);
            $("#acreedor_ubicacion_opcional").attr('disabled', true);
            $("#porcentaje_acreedor_ubicacion").attr('disabled', true);
            $("#observaciones_ubicacion").attr('disabled', true);
            $('.acreedor_ubicacion').attr('disabled', true);
            $('.estado_ubicacion').attr('disabled', true);
        } else if (selInteres != '' && id_tipo_int_asegurado == 8) {

            $("#selInteres").val(selInteres).trigger('change');

            $("#selInteres").attr('disabled', true);
            $("#chasis").attr('disabled', true);
            $("#unidad").attr('disabled', true);
            $(".marca_vehiculo").attr('disabled', true);
            $(".modelo_vehiculo").attr('disabled', true);
            $("#placa").attr('disabled', true);
            $("#ano").attr('disabled', true);
            $("#motor").attr('disabled', true);
            $("#color").attr('disabled', true);
            $("#capacidad").attr('disabled', true);
            $("#operador").attr('disabled', true);
            $("#extras").attr('disabled', true);
            $("#valor_extras").attr('disabled', true);
            $("#porcentaje_acreedor").attr('disabled', true);
            $("#observaciones_vehiculo").attr('disabled', true);
            $("#uso").attr('disabled', true);
            $(".condicion_vehiculo").attr('disabled', true);
            $(".porcentaje_vehiculo").attr('disabled', true);
            $(".acreedor").attr('disabled', true);
            $(".estado").attr('disabled', true);
        }
    }

});


$("input[name='campo[comision]']").on('change blur', function () {
    $("#comision").val($(this).val());
});


var search = $("#selInteres");


$("body").on("click", search, function () {
    $('ul.select2-results__options li').each(function () {
        $(this).attr("aria-selected", false);

    });
});

function saveInvidualCoverage(interesId,interesNumero){
    var coverageArray =[],
    deductiblesArray =[];
    var solicitud = vista==="crear"?vista:solicitud_id;
    var unico = $('#detalleunico').val();
    var validateCoverageFields = [
    {field: {input: "input[name='coverageName[]']", valiation: ""}},
    {field: {input: "input[name='coverageValue[]']", valiation: "numeric"}},
    {field: {input: "input[name='deductibleName[]']", valiation: "", }},
    {field: {input: "input[name='deductibleValue[]']", valiation: "numeric",}}];
    if (customModalValidation(validateCoverageFields) === 0) {
        coverageArray.push({
            'coberturas': {nombre: $("input[name='coverageName[]']").map(function () {
                return $(this).val();
            }).get(),
            valor: $("input[name='coverageValue[]']").map(function () {
                return $(this).val();
            }).get()
        }});
        deductiblesArray.push({'deducibles': {nombre: $("input[name='deductibleName[]']").map(function () {
            return $(this).val();
        }).get(),
        valor: $("input[name='deductibleValue[]']").map(function () {
            return $(this).val();
        }).get()
    } });


        $.ajax({
            type: "POST",
            data: {
              deductibles: deductiblesArray[0],
              coverage:  coverageArray[0],
              unicDetail:unico,
              interesId : interesId,
              solicitud : solicitud,
              erptkn: tkn
          },
          url: phost() + 'solicitudes/saveIndividualCoverage',
          success: function(data)
          {    

            if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
                else if(data==="success"){
                  $("#IndCoberturas").modal("hide");  
                  toastr.success('Se han Guardado los cambios correctamente para Interés <b>'+interesNumero);
              }else{
                toastr.error('Ocurrio un error al Guardar las coberturas para Interés <b>'+interesNumero);
              }                 
               
          }
      });   
    }
}

function getValuesFromArrayInput(inputsName){

    return $("input[name='"+inputsName+"[]']").map(function () {
                return $(this).val();
            }).get();

}


function constructJSONArray(jsonName1,jsonName2,namesArray,valuesArray){
    var json =[];
    for (var i = namesArray.length - 1; i >= 0; i--) {
        nombre= namesArray[i];
        valor = valuesArray[i];
        json.push( {[jsonName1] : nombre,
         [jsonName2] :valor});
    }
    return json;
}

function drawInputsInCoverageInModal(id,btnAdd,stringId,del_row){
 var wrapper = $("#"+id); 
 var parameters = "'"+id+"','"+del_row+"','"+stringId+"'";
 $("#"+btnAdd).unbind().click(function(e){
    e.preventDefault();
    appendHtmlTag(wrapper,parameters,stringId,del_row,undefined);
    $(".moneda").inputmask('currency',{
      prefix: "",
      autoUnmask : true,
      removeMaskOnSubmit: true
  }); 
});
}

function deleteFieldsInCoverageModal(id,del_row,idToRemove){
    var wrapper = $("#"+id);
    var removeStringClass = '.'+del_row+'';
    $(wrapper).unbind().on("click",removeStringClass, function(e){ //user click on remove text
        e.preventDefault();
        var unicNumber=$(this).data("id"); 
        var stringId = "#"+ idToRemove+'_'+ unicNumber;    
        $(stringId).remove();
    }); 
}

function appendHtmlTag(wrapper,parameters,stringId,del_row,inputValue){
  var counterCoverage= new Date().valueOf(),value={},enabled;
  var URL =window.location.href.split("/");
  var urlLastSegment= URL.pop();
  value.nombre = inputValue === undefined ? "" : inputValue.nombre;
  value.monetario = inputValue === undefined ? "" : inputValue.monetario;
  enabled = urlLastSegment == "renovar" ? "" : "";
  var text = '<div class="'+stringId+'" id="'+stringId+'_'+ counterCoverage+'"><div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"> <input type="text" '+enabled+' name="'+stringId+'Name[]" value="'+value.nombre+'" class="form-control"></div>'+'<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"><div class="input-group"><span class="input-group-addon">$</span><input '+enabled+' type="text" name="'+stringId+'Value[]" value="'+value.monetario+'" class="form-control moneda"  value=""></div></div>'+'<div class="col-xs-12 col-sm-3 col-md-3 col-lg-1 renewal '+del_row+'" data-id="'+counterCoverage+'" onclick="deleteFieldsInCoverageModal('+parameters+')"><button class="btn btn-default btn-block "><i class="fa fa-trash"></i></button></div></div>';
  $(wrapper).append(text);   
}
function  populateStoredCovergeData(id,stringId,del_row,coverage,nombre,monetario){
   wrapper = $("#"+id); 
   parameters = "'"+id+"','"+del_row+"','"+stringId+"'";
   for (var i = coverage.length - 1; i >= 0; i--) {
    var value = coverage[i];
    var attribute={
        nombre: value[nombre],
        monetario:value[monetario]
    };
    appendHtmlTag(wrapper,parameters,stringId,del_row,attribute);
}
}

function showIndividualCoverageModal(serialName){

    var btnDismiss ='<button type="button" class="close" data-dismiss="modal">&times;</button>';      
    var pantalla = $('.individual');
    var modalContainer = $("#IndCoberturas");
    var botones_coberturas = $('.btnIndidualCoverage');
    

    pantalla.css('display', 'block');
    botones_coberturas.css('display', 'block');
    modalContainer.find('.modal-header').empty().append(btnDismiss+"<h4 style='text-align:center'>Coberturas Interés: "+serialName+"</h4>");
    modalContainer.find('.modal-body').empty().append(pantalla);
    modalContainer.find('.modal-footer').empty().append(botones_coberturas);
    $(modalContainer).modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
            });
    modalContainer.modal("show");
}