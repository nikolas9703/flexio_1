Vue.http.options.emulateJSON = true;
var conta = 0;
var conta1 = 0;
var contacoberturas = 0;
var contadorreclamo = 0 ;
var planesCoberturasDeducibles = [];
var arrayauxiliar=[];
for (var i =0; i< documentacionesgbd.length; i++) {
    arrayauxiliar[documentacionesgbd[i].valor]=documentacionesgbd[i].valor
}
var validateFields = [
    {field: {input: "input[name='coberturasNombre[]']", valiation: "alfanúmerico", type: "cn"}},
    {field: {input: "input[name='coberturasValor[]']", valiation: "numeric", type: "cv"}},
    {field: {input: "input[name='deduciblesNombre[]']", valiation: "alfanúmerico", type: "dn"}},
    {field: {input: "input[name='deduciblesValor[]']", valiation: "numeric", type: "dv"}}];
var coberturasModal = $('#verCoberturas');

var interesesModal = $();
var formularioCrear = new Vue({
    el: ".wrapper-content",
    data: {
        acceso: acceso === 1 ? true : false,
        disabledOpcionPlanes: true,
        ramo: ramo,
        tipoPoliza: id_tipo_poliza,
        codigoRamo: codigo_ramo,
        catalogoClientes: catalogo_clientes,
        catalogoCentroFacturacion: [],
        clienteCentro: '',
        documentacionesList: documentaciones,
        reclamoInfo: typeof reclamos != 'undefined' ? $.parseJSON(reclamos) : [],
        reclamoInfoAcc: typeof reclamosAccidentes != 'undefined' ? $.parseJSON(reclamosAccidentes) : [],
        reclamoInfoCob: typeof reclamosCoberturas != 'undefined' ? $.parseJSON(reclamosCoberturas) : [],
        reclamoInfoDed: typeof reclamosDeduccion != 'undefined' ? $.parseJSON(reclamosDeduccion) : [],
        polizas: [],
        reclamantes: [],
        documentacion: [],
        //***************************************************
        clienteInfo: {},
        //***************************************************
        planesInfo: [],
        listaContactos: [],
        listaCausa: causa,
        listaAccidente: accidente,
        listaSalud: tiposalud,
        comisionPlanInfo: '',
        exoneradoImpuestos: '',
        primaAnual: 0,
        impuestoPlan: 0,
        impuestoMonto: '0',
        otrosPrima: 0,
        descuentosPrima: 0,
        totalPrima: 0,
        participacionTotal: 0,
        planList: typeof planes != "undefined" ? planes : [],
        agentesArray: [1],
        porcentajeParticipacion: [],
        disabledOpcionClientes: true,
        disabledCoberturas: true,
        disabledAseguradora: true,
        disabledSubmit: true,
        disabledCentro: true,
        nombrepadre: nombre_padre,
        catalogoEstado: estado_reclamos,
        ramoscadena: ramoscadena,
        InteresesAsociados: '',
        isEditable: true,
        permiso_editar: permiso_editar,
        documentacionesgbd: documentacionesgbd,
        arrayauxiliar: arrayauxiliar,
        //********************************
        usuario_id: parseInt(usuario_id),
        listadoUsuarios: usersList,
        polizaid: typeof pol != "undefined" ? pol : '',
        reclamoid: typeof id_reclamo != "undefined" ? id_reclamo : '',
        listaAjustadores: ajustadoreslista,
        vista: vista
    },
    methods: {
        getPolizaSeleccionado: function () {
            //polula el segundo select del header
            var self = this;
            var id_ramo = ramo_id;
            console.log("ramoid="+id_ramo);
            this.$http.post({
                url: phost() + 'reclamos/ajax_get_polizas',
                method: 'POST',
                data: {ramo_id: id_ramo, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.tablaError = "";
                    self.$set('polizas', response.data);
                    //self.$set('tablaError', '');
                    //self.$set('disabledOpcionClientes', false);
                }else{

                }
            });
        },
        seleccionarPoliza: function (e) {
            this.getPolizaSeleccionado();
        },
        polizaInfoSelect: function () {
            this.getPolizaSeleccionadoInfo();
            //this.getClienteCentroFacturable();
            //this.grupoInfo();
            //this.direccionInfo();
        },
        getPolizaSeleccionadoInfo: function () {
            //polula el segundo select del header
            var self = this;
            if (conta1==0) {
                var c = typeof pol != "undefined" ? pol : '';
                if (c != "") { var poliza_id = c; }else{ var poliza_id = $('#poliza_seleccionado').val(); }
            }else{
                var poliza_id = $('#poliza_seleccionado').val();                
            }                        
            if (poliza_id=="" || poliza_id==null) {poliza_id=0;}
            //console.log(poliza_id);
            this.$http.post({
                url: phost() + 'reclamos/ajax_get_poliza',
                method: 'POST',
                data: {poliza_id: poliza_id, tipo_interes: id_tipo_int_asegurado, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.$set('polizaInfo', response.data);
                    localStorage.setItem("id_cliente", response.data.id_cliente);
                    if (conta1==0 && c != "") {
                        $('#poliza_seleccionado').val(response.data.idpoliza);
                        $('#poliza_seleccionado').trigger('change');
                        conta1=1;                        
                    } 
                    this.getReclamantes(response.data.idpoliza);
                    this.coberturasPoliza(response.data.idpoliza);
                    if (response.data.idpoliza=="") { 
                        $("#ver_coberturas").attr("disabled", "disabled");
                    }else{
                        $("#ver_coberturas").removeAttr("disabled");
                    }
                    if (id_tipo_poliza == 1 && vista=="crear") {
                        this.interesesPoliza( response.data.idpoliza, "selector", '');
                    }else if (vista=="editar" && contadorreclamo<2) {
                        this.interesesReclamo( this.reclamoid );
                        contadorreclamo++;
                    }                 
                }
            });


        },
        getReclamantes: function (pol) {
            //polula el segundo select del header
            //console.log(pol);
            var self = this;
            var poliza = pol;
            this.$http.post({
                url: phost() + 'reclamos/ajax_get_reclamantes',
                method: 'POST',
                data: {tipo_interes: id_tipo_int_asegurado, poliza: poliza, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.tablaError = "";
                    self.$set('reclamantes', response.data);
                    //self.$set('tablaError', '');
                    //self.$set('disabledOpcionClientes', false);
                }
            });
        },
        interesesPoliza: function (pol, tipo, interes) {
            //polula el segundo select del header
            var self = this;
            var poliza = pol;            
            if(pol==""){poliza = 0;}
            console.log("tipo_interes="+id_tipo_int_asegurado);
            this.$http.post({
                url: phost() + 'reclamos/ajax_get_intereses_poliza',
                method: 'POST',
                data: {tipo_interes: id_tipo_int_asegurado, poliza: poliza, interes: interes, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.tablaError = "";
                    if (!_.isEmpty(response.data)) {
                        localStorage.setItem("id_intereses", response.data.inter.id); 
                        //Se asigna el ID interes asegurado cuando es Individual
                        if ((id_tipo_poliza == 1  && tipo == "selector" && typeof response.data.inter != "undefined") || (id_tipo_poliza == 2  && tipo == "modal" && typeof response.data.inter != "undefined" )) {$("#reclamoidinteres").val(response.data.inter.id);}                        
                        var tipoint = id_tipo_int_asegurado;
                        if (tipoint == 1 && typeof response.data.inter != "undefined" && ( (id_tipo_poliza == 1  && tipo == "selector") || (id_tipo_poliza == 2  && tipo == "modal")  )) {
                            $("#reclamointeres").val(response.data.inter.nombre);
                            $(".uuid_carga, #nombre, #clase_equipo, #marca, #modelo, #anio_articulo, #numero_serie, .valor_articulo, #observaciones_articulo").val("");
                            $("#uuid_articulo").val(response.data.inter.uuid_intereses);
                            $("#nombre").val(response.data.inter.nombre);
                            $("#nombre").attr('disabled',true);
                            $("#clase_equipo").val(response.data.inter.clase_equipo);
                            $("#clase_equipo").attr('disabled',true);
                            $("#marca").val(response.data.inter.marca);
                            $("#marca").attr('disabled',true);
                            $("#modelo").val(response.data.inter.modelo);
                            $("#modelo").attr('disabled',true);
                            $("#anio_articulo").val(response.data.inter.anio);
                            $("#anio_articulo").attr('disabled',true);
                            $("#numero_serie").val(response.data.inter.numero_serie);
                            $("#numero_serie").attr('disabled',true);
                            $(".valor_articulo").val(response.data.inter.valor);
                            $(".valor_articulo").attr('disabled',true);
                            $("#observaciones_articulo").val(response.data.inter.observaciones);
                            $("#observaciones_articulo").attr('disabled',true);
                            $("#id_condicion").val(response.data.inter.id_condicion); //option[value='" + response.data.inter.id_condicion + "']
                            $("#id_condicion").attr('disabled',true);
                            $(".estado_articulo").empty();
                            $(".estado_articulo").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");
                            $(".estado_articulo").attr("disabled",true);

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                            /*$("#id_condicion option[value='" + response.data.inter.id_condicion + "']").attr("selected", "selected");
                             $("#campo2[estado] option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/


                        } else if (tipoint == 2 && typeof response.data.inter != "undefined" && ( (id_tipo_poliza == 1  && tipo == "selector") || (id_tipo_poliza == 2  && tipo == "modal")  )  ) {

                            $("#reclamointeres").val(response.data.inter.no_liquidacion);
                            $(".uuid_carga, #no_liquidacion, #fecha_despacho, #fecha_arribo, #fecha_arribo, #detalle, #origen, #destino, .valor_mercancia, #acreedor_carga_opcional, #tipo_obligacion_opcional, #observaciones_carga").val("");
                            $(".uuid_carga").val(response.data.inter.uuid_intereses);
                            $("#no_liquidacion").val(response.data.inter.no_liquidacion);
                            $("#no_liquidacion").attr('disabled',true);
                            $("#fecha_despacho").val(response.data.inter.fecha_despacho);
                            $("#fecha_despacho").attr('disabled',true);
                            $("#fecha_arribo").val(response.data.inter.fecha_arribo);
                            $("#fecha_arribo").attr('disabled',true);
                            $("#detalle").val(response.data.inter.detalle);
                            $("#detalle").attr('disabled',true);
                            $("#origen").val(response.data.inter.origen);
                            $("#origen").attr('disabled',true);
                            $("#destino").val(response.data.inter.destino);
                            $("#destino").attr('disabled',true);
                            $(".valor_mercancia").val(response.data.inter.valor);
                            $(".valor_mercancia").attr('disabled',true);
                            $("#observaciones_carga").val(response.data.inter.observaciones);
                            $("#observaciones_carga").attr('disabled',true);
                            $(".tipo_empaque").val(response.data.inter.tipo_empaque); //option[value='" + response.data.inter.tipo_empaque + "']
                            $(".tipo_empaque").attr('disabled',true);

                            var y = $(".acreedor_carga option[value='" + response.data.inter.acreedor + "']");
                            if (y.length == 1) {
                                $(".acreedor_carga").val(response.data.inter.acreedor); //option[value='" + response.data.inter.acreedor + "']
                                $(".acreedor_carga").attr('disabled',true);
                                if (response.data.inter.acreedor == "otro") {
                                    $("#acreedor_carga_opcional").val(response.data.inter.acreedor_opcional);
                                }
                                $("#acreedor_carga_opcional").attr('disabled',true);
                            }
                            var w = $(".tipo_obligacion option[value='" + response.data.inter.tipo_obligacion + "']");
                            if (w.length == 1) {
                                $(".tipo_obligacion").val(response.data.inter.tipo_obligacion); //option[value='" + response.data.inter.tipo_obligacion + "']
                                $(".tipo_obligacion").attr('disabled',true);
                                if (response.data.inter.tipo_obligacion == "otro") {
                                    $("#tipo_obligacion_opcional").val(response.data.inter.tipo_obligacion_opcional);
                                }
                                $("#tipo_obligacion_opcional").attr('disabled',true);
                            }

                            $(".condicion_envio").val(response.data.inter.condicion_envio); //option[value='" +  + "']
                            $(".condicion_envio").attr('disabled',true);
                            $(".medio_transporte").val(response.data.inter.medio_transporte); //option[value='" + response.data.inter.medio_transporte + "']
                            $(".medio_transporte").attr('disabled',true);
                            $(".estado_carga").empty();
                            $(".estado_carga").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");
                            $(".estado_carga").attr('disabled',true);

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }
                            /*
                             $(".condicion_envio option[value='" + response.data.inter.condicion_envio + "']").attr("selected", "selected");
                             $(".medio_transporte option[value='" + response.data.inter.medio_transporte + "']").attr("selected", "selected");
                             $(".estado_carga option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/

                        } else if (tipoint == 3 && typeof response.data.inter != "undefined" && ( (id_tipo_poliza == 1  && tipo == "selector") || (id_tipo_poliza == 2  && tipo == "modal")  ) ) {
                            $("#reclamointeres").val(response.data.inter.serie);
                            $(".uuid_aereo, #serie_aereo, #marca_aereo, #modelo_aereo, #matricula_aereo, #valor_aereo, #pasajeros_aereo, #tripulacion_a, #observaciones_aereo").val("");
                            $(".uuid_aereo").val(response.data.inter.uuid_intereses);
                            $("#serie_aereo").val(response.data.inter.serie);
                            $("#serie_aereo").attr('disabled',true);
                            $("#marca_aereo").val(response.data.inter.marca);
                            $("#marca_aereo").attr('disabled',true);
                            $("#modelo_aereo").val(response.data.inter.modelo);
                            $("#modelo_aereo").attr('disabled',true);
                            $("#matricula_aereo").val(response.data.inter.matricula);
                            $("#matricula_aereo").attr('disabled',true);
                            $("#valor_aereo").val(response.data.inter.valor);
                            $("#valor_aereo").attr('disabled',true);
                            $("#pasajeros_aereo").val(response.data.inter.pasajeros);
                            $("#pasajeros_aereo").attr('disabled',true);
                            $("#tripulacion_a").val(response.data.inter.tripulacion);
                            $("#tripulacion_a").attr('disabled',true);
                            $("#observaciones_aereo").val(response.data.inter.observaciones);
                            $("#observaciones_aereo").attr('disabled',true);
                            $(".estado_aereo").empty();
                            $(".estado_aereo").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");  
                            $(".estado_aereo").attr('disabled',true);
                            //$(".estado_aereo option[value='" + response.data.inter.estado + "']").attr("selected", "selected");

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                            conta = 1;
                        } else if (tipoint == 4 && typeof response.data.inter != "undefined" && ( (id_tipo_poliza == 1  && tipo == "selector") || (id_tipo_poliza == 2  && tipo == "modal")  )  ) {
                            $("#reclamointeres").val(response.data.inter.serie);
                            $(".uuid_casco_maritimo, #serie_maritimo, .serier, #marca_maritimo, #nombre_embarcacion, .porcentaje_acreedor_maritimo, #valor_maritimo, #pasajeros_maritimo, #observaciones_maritimo").val("");
                            $(".uuid_casco_maritimo").val(response.data.inter.uuid_intereses);
                            $("#serie_maritimo").val(response.data.inter.serie);
                            $("#serie_maritimo").attr('disabled',true);
                            $(".serier").val(response.data.inter.serie);
                            $(".serier").attr('disabled',true);
                            $("#nombre_embarcacion").val(response.data.inter.nombre_embarcacion);
                            $("#nombre_embarcacion").attr('disabled',true);
                            $("#marca_maritimo").val(response.data.inter.marca);
                            $("#marca_maritimo").attr('disabled',true);
                            $(".porcentaje_acreedor_maritimo").val(response.data.inter.porcentaje_acreedor);
                            $(".porcentaje_acreedor_maritimo").attr('disabled',true);
                            $("#valor_maritimo").val(response.data.inter.valor);
                            $("#valor_maritimo").attr('disabled',true);
                            $("#pasajeros_maritimo").val(response.data.inter.pasajeros);
                            $("#pasajeros_maritimo").attr('disabled',true);
                            $("#observaciones_maritimo").val(response.data.inter.observaciones);
                            $("#observaciones_maritimo").attr('disabled',true);
                            $(".tipo_maritimo option[value='" + response.data.inter.tipo + "']").attr("selected", "selected");
                            $(".tipo_maritimo").attr('disabled',true);
                            $(".acreedor_maritimo option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");
                            $(".acreedor_maritimo").attr('disabled',true);
                            $(".estado_casco").empty();
                            $(".estado_casco").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");
                            $(".estado_casco").attr('disabled',true);
                            /*
                             $(".tipo_maritimo option[value='" + response.data.inter.tipo + "']").attr("selected", "selected");
                             $(".acreedor_maritimo option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");
                             $(".estado_casco option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/

                             if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                        } else if (tipoint == 5 && typeof response.data.inter != "undefined" && ( (id_tipo_poliza == 1  && tipo == "selector") || (id_tipo_poliza == 2  && tipo == "modal")  ) ) {
                            $("#reclamointeres").val(response.data.inter.identificacion);
                            $(".uuid").val(response.data.inter.uuid_intereses);
                            $("#nombrePersona").val(response.data.inter.nombrePersona);
                            $("#nombrePersona").attr('disabled',true);

                            $("input:checkbox").prop('checked',false);
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
                            $("#identificacion").attr('disabled',true);
                            $("#pasaporte").attr('disabled',true);
                            $("#provincia").attr('disabled',true);
                            $("#letra").attr('disabled',true);
                            $("#tomo").attr('disabled',true);
                            $("#asiento").attr('disabled',true);
                            $('.noPAS').attr('disabled',true);
                            $(".PAS").attr('disabled',true);

                            $("#fecha_nacimiento").val(response.data.inter.fecha_nacimiento);
                            $("#fecha_nacimiento").attr('disabled',true);
                            var today = new Date();
                            console.log(today);
                            var format = response.data.inter.fecha_nacimiento.split("-");
                            var dob = new Date(format[0], format[1], format[2]);
                            var diff = (today - dob ) ;
                            var age = Math.floor(diff / 31536000000);
                            $("[id*=edad]").val(age);
                            $("#edad").attr('disabled',true);
                            $('#estado_civil').val(response.data.inter.estado_civil);
                            $('#estado_civil').attr('disabled',true);
                            $('#nacionalidad').val(response.data.inter.nacionalidad);
                            $('#nacionalidad').attr('disabled',true);
                            $('#sexo').val(response.data.inter.sexo);
                            $('#sexo').attr('disabled',true);
                            $('#estatura').val(response.data.inter.estatura);
                            $('#estatura').attr('disabled',true);
                            $('#peso').val(response.data.inter.peso);
                            $('#peso').attr('disabled',true);
                            $('#telefono_residencial').val(response.data.inter.telefono_residencial);
                            $('#telefono_residencial').attr('disabled',true);
                            $('#telefono_oficina').val(response.data.inter.telefono_oficina);
                            $('#telefono_oficina').attr('disabled',true);
                            $('#direccion').val(response.data.inter.direccion_residencial);
                            $('#direccion').attr('disabled',true);
                            $('#direccion_laboral').val(response.data.inter.direccion_laboral);
                            $('#direccion_laboral').attr('disabled',true);
                            $('#observacionesPersona').val(response.data.inter.observaciones);
                            $('#observacionesPersona').attr('disabled',true);
                            $('#estadoPersona').empty();
                            $('#estadoPersona').append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");
                            $('#estadoPersona').attr('disabled',true);
                            $('#idPersona').val(response.data.inter.interesestable_id);
                            $('#idPersona').attr('disabled',true);

                            $('#correoPersona').val(response.data.inter.correo);
                            $('#correoPersona').attr('disabled',true);
                            
                            if(response.data.inter.telefono_principal == 'Residencial'){
                               $('#telefono_residencial_check').prop('checked',true);     
                           }else if(response.data.inter.telefono_principal == 'Laboral'){
                                $('#telefono_oficina_check').prop('checked',true);
                            }

                            if(response.data.inter.direccion_principal=='Residencial'){
                                $('#direccion_residencial_check').prop('checked',true);
                            }else if(response.data.inter.direccion_principal=='Laboral'){
                                $('#direccion_laboral_check').prop('checked',true);
                            }

                            $('#telefono_residencial_check').attr('disabled',true);
                            $('#telefono_oficina_check').attr('disabled',true);
                            $('#direccion_residencial_check').attr('disabled',true);
                            $('#direccion_laboral_check').attr('disabled',true);

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                        } else if (tipoint == 6 && typeof response.data.inter != "undefined" && ( (id_tipo_poliza == 1  && tipo == "selector") || (id_tipo_poliza == 2  && tipo == "modal")  )  ) {
                            $("#reclamointeres").val(response.data.inter.nombre_proyecto);
                            //$('.acreedor_proyecto').removeAttr("selected");
                            //$('.acreedor_proyecto').prop('selected', true);
                            $(".uuid_proyecto, #nombre_proyecto, #contratista_proyecto, #representante_legal_proyecto, #fecha_concurso, #no_orden_proyecto, .no_ordenr, #duracion_proyecto, .fecha_proyecto, .monto_proyecto, #monto_afianzado, #asignado_acreedor, #ubicacion_proyecto, #acreedor_opcional, #validez_fianza_opcional, #observaciones_proyecto").val("");

                            $(".uuid_proyecto").val(response.data.inter.uuid_intereses);
                            $("#nombre_proyecto").val(response.data.inter.nombre_proyecto);
                            $("#nombre_proyecto").attr("disabled",true);
                            $("#contratista_proyecto").val(response.data.inter.contratista);
                            $("#contratista_proyecto").attr("disabled",true);
                            $("#representante_legal_proyecto").val(response.data.inter.representante_legal);
                            $("#representante_legal_proyecto").attr("disabled",true);
                            $("#fecha_concurso").val(response.data.inter.fecha_concurso);
                            $("#fecha_concurso").attr("disabled",true);
                            $("#no_orden_proyecto").val(response.data.inter.no_orden);
                            $("#no_orden_proyecto").attr("disabled",true);
                            $(".no_ordenr").val(response.data.inter.no_orden);
                            $(".no_ordenr").attr("disabled",true);
                            $("#duracion_proyecto").val(response.data.inter.duracion);
                            $("#duracion_proyecto").attr("disabled",true);
                            $(".fecha_proyecto").val(response.data.inter.fecha);
                            $(".fecha_proyecto").attr("disabled",true);
                            $(".monto_proyecto").val(response.data.inter.monto);
                            $(".monto_proyecto").attr("disabled",true);
                            $("#monto_afianzado").val(response.data.inter.monto_afianzado);
                            $("#monto_afianzado").attr("disabled",true);
                            $("#asignado_acreedor").val(response.data.inter.asignado_acreedor);
                            $("#asignado_acreedor").attr("disabled",true);
                            $("#ubicacion_proyecto").val(response.data.inter.ubicacion);
                            $("#ubicacion_proyecto").attr("disabled",true);
                            $("#acreedor_opcional").val(response.data.inter.acreedor_opcional);
                            $("#acreedor_opcional").attr("disabled",true);
                            $("#validez_fianza_opcional").val(response.data.inter.validez_fianza_opcional);
                            $("#validez_fianza_opcional").attr("disabled",true);
                            $("#observaciones_proyecto").val(response.data.inter.observaciones);
                            $("#observaciones_proyecto").attr("disabled",true);
                            $(".tipo_fianza").val(response.data.inter.tipo_fianza); // option[value='" + response.data.inter.tipo_fianza + "']
                            $(".tipo_fianza").attr("disabled",true);
                            $(".tipo_propuesta").val(response.data.inter.tipo_propuesta); //option[value='" + response.data.inter.tipo_propuesta + "']
                            $(".tipo_propuesta").attr("disabled",true);
                            $(".acreedor_proyecto").val(response.data.inter.acreedor); //option[value='" + response.data.inter.acreedor + "']
                            $(".acreedor_proyecto").attr("disabled",true);
                            $(".validez_fianza_pr").val(response.data.inter.validez_fianza_pr); //option[value='" + response.data.inter.validez_fianza_pr + "']
                            $(".validez_fianza_pr").attr("disabled",true);
                            $(".estado_proyecto").empty();
                            $(".estado_proyecto").append("<option value="+response.data.inter.estado+">"+response.data.inter.estado+"</option>");
                            $(".estado_proyecto").attr("disabled",true);

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                        } else if (tipoint == 7 && typeof response.data.inter != "undefined" && ( (id_tipo_poliza == 1  && tipo == "selector") || (id_tipo_poliza == 2  && tipo == "modal")  )  ) {
                            $("#reclamointeres").val(response.data.inter.nombre);
                            $(".uuid_ubicacion, #nombre_ubicacion, #direccion_ubicacion, .serier, #edif_mejoras, #contenido, #maquinaria, #inventario, #acreedor_ubicacion_opcional, #porcentaje_acreedor_ubicacion, #observaciones_ubicacion").val("");
                            $(".uuid_ubicacion").val(response.data.inter.uuid_intereses);
                            $("#nombre_ubicacion").val(response.data.inter.nombre);
                            $("#nombre_ubicacion").attr('disabled',true);
                            $("#direccion_ubicacion").val(response.data.inter.direccion);
                            $("#direccion_ubicacion").attr('disabled',true);
                            $(".serier").val(response.data.inter.direccion);
                            $(".serier").attr('disabled',true);
                            $("#edif_mejoras").val(response.data.inter.edif_mejoras);
                            $("#edif_mejoras").attr('disabled',true);
                            $("#contenido").val(response.data.inter.contenido);
                            $("#contenido").attr('disabled',true);
                            $("#maquinaria").val(response.data.inter.maquinaria);
                            $("#maquinaria").attr('disabled',true);
                            $("#inventario").val(response.data.inter.inventario);
                            $("#inventario").attr('disabled',true);
                            $("#acreedor_ubicacion_opcional").val(response.data.inter.acreedor_ubicacion_opcional);
                            $("#acreedor_ubicacion_opcional").attr('disabled',true);
                            $("#porcentaje_acreedor_ubicacion").val(response.data.inter.porcentaje_acreedor);
                            $("#porcentaje_acreedor_ubicacion").attr('disabled',true);
                            $("#observaciones_ubicacion").val(response.data.inter.observaciones);
                            $("#observaciones_ubicacion").attr('disabled',true);
                            console.log(response.data.inter.acreedor);
                            $("#acreedor_ubicacion").val(response.data.inter.acreedor);  //option[value='" + response.data.inter.acreedor + "']
                            $("#acreedor_ubicacion").attr('disabled',true);
                            $(".estado_ubicacion").empty();
                            $(".estado_ubicacion").append("<option value="+response.data.inter.estado+">"+response.data.inter.estado+"</option>");
                            $(".estado_ubicacion").attr('disabled',true);

                            var acreedor_ubicacion = $('.acreedor_ubicacion').val();
                            if (acreedor_ubicacion === 'otro') {
                                $('#acreedor_ubicacion_opcional').val(response.data.inter.acreedor_opcional);
                                $('#acreedor_ubicacion_opcional').attr('disabled',true);
                            }else{
                                $('#acreedor_ubicacion_opcional').attr('disabled',true);
                            }

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                        } else if (tipoint == 8 && typeof response.data.inter != "undefined" && ( (id_tipo_poliza == 1  && tipo == "selector") || (id_tipo_poliza == 2  && tipo == "modal")  ) ) {
                            $("#reclamointeres").val(response.data.inter.chasis);
                            $("#uuid_vehiculo, #chasis, #unidad, #marca, #modelo, #placa, #ano, #motor, #color, #capacidad, #operador, #extras, #valor_extras, #porcentaje_acreedor, #observaciones_vehiculo ").val("");
                            $("#uuid_vehiculo").val(response.data.inter.uuid_intereses);
                            $("#chasis").val(response.data.inter.chasis);
                            $("#chasis").attr('disabled',true);
                            $("#unidad").val(response.data.inter.unidad);
                            $("#unidad").attr('disabled',true);
                            $("#placa").val(response.data.inter.placa);
                            $("#placa").attr('disabled',true);
                            $(".marca_vehiculo").val(response.data.inter.marca);
                            $(".marca_vehiculo").attr('disabled',true);
                            $(".modelo_vehiculo").val(response.data.inter.modelo);
                            $(".modelo_vehiculo").attr('disabled',true);
                            $("#ano").val(response.data.inter.ano);
                            $("#ano").attr('disabled',true);
                            $("#motor").val(response.data.inter.motor);
                            $("#motor").attr('disabled',true);
                            $("#color").val(response.data.inter.color);
                            $("#color").attr('disabled',true);
                            $("#capacidad").val(response.data.inter.capacidad);
                            $("#capacidad").attr('disabled',true);
                            $("#operador").val(response.data.inter.operador);
                            $("#operador").attr('disabled',true);
                            $("#extras").val(response.data.inter.extras);
                            $("#extras").attr('disabled',true);
                            $("#valor_extras").val(response.data.inter.valor_extras);
                            $("#valor_extras").attr('disabled',true);
                            $(".porcentaje_vehiculo").val(response.data.inter.porcentaje_acreedor);
                            $(".porcentaje_vehiculo").attr('disabled',true);
                            $("#observaciones_vehiculo").val(response.data.inter.observaciones);
                            $("#observaciones_vehiculo").attr('disabled',true);
                            $("#uso").val(response.data.inter.uso); //option[value='" + response.data.inter.uso + "']
                            $("#uso").attr('disabled',true);
                            $(".condicion_vehiculo").val(response.data.inter.condicion); //option[value='" + response.data.inter.condicion + "']
                            $(".condicion_vehiculo").attr('disabled',true);
                            $(".acreedor").val(response.data.inter.acreedor); //option[value='" + response.data.inter.acreedor + "']
                            $(".acreedor").attr('disabled',true);
                            $(".estado").empty();
                            $(".estado").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");
                            $(".estado").attr('disabled',true);

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                            /*
                             $("#uso option[value='" + response.data.inter.uso + "']").attr("selected", "selected");
                             $(".condicion_vehiculo option[value='" + response.data.inter.condicion + "']").attr("selected", "selected");
                             $(".acreedor option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");
                             $(".estado option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/

                        } else if(poliza==0){

                            $("#reclamointeres").val("");
                            $("#reclamoidinteres").val("");
                            if (tipo_int == 1) { $(".uuid_carga, #nombre, #clase_equipo, #marca, #modelo, #anio_articulo, #numero_serie, .valor_articulo, #observaciones_articulo").val(""); }
                            else if (tipo_int == 2) { $(".uuid_carga, #no_liquidacion, #fecha_despacho, #fecha_arribo, #fecha_arribo, #detalle, #origen, #destino, .valor_mercancia, #acreedor_carga_opcional, #tipo_obligacion_opcional, #observaciones_carga").val(""); }
                            else if (tipo_int == 3) { $(".uuid_aereo, #serie_aereo, #marca_aereo, #modelo_aereo, #matricula_aereo, #valor_aereo, #pasajeros_aereo, #tripulacion_a, #observaciones_aereo").val(""); }
                            else if (tipo_int == 4) { $(".uuid_casco_maritimo, #serie_maritimo, .serier, #marca_maritimo, #nombre_embarcacion, .porcentaje_acreedor_maritimo, #valor_maritimo, #pasajeros_maritimo, #observaciones_maritimo").val(""); }
                            else if (tipo_int == 5) {}
                            else if (tipo_int == 6) { $(".uuid_proyecto, #nombre_proyecto, #contratista_proyecto, #representante_legal_proyecto, #fecha_concurso, #no_orden_proyecto, .no_ordenr, #duracion_proyecto, .fecha_proyecto, .monto_proyecto, #monto_afianzado, #asignado_acreedor, #ubicacion_proyecto, #acreedor_opcional, #validez_fianza_opcional, #observaciones_proyecto").val(""); }
                            else if (tipo_int == 7) { $(".uuid_ubicacion, #nombre_ubicacion, #direccion_ubicacion, .serier, #edif_mejoras, #contenido, #maquinaria, #inventario, #acreedor_ubicacion_opcional, #porcentaje_acreedor_ubicacion, #observaciones_ubicacion").val(""); }
                            else if (tipo_int == 8) { $("#uuid_vehiculo, #chasis, #unidad, #marca, #modelo, #placa, #ano, #motor, #color, #capacidad, #operador, #extras, #valor_extras, #porcentaje_acreedor, #observaciones_vehiculo ").val(""); }
                        }
                    }else{
                        console.log("Holaaaa");
                    }
                }
            });
        },
        contactosAjustador: function (ajus){
            if (ajus == "") {
                ajus = 0;
            }
            $("#telefonodetalle").val('');
            var self = this;
            this.$http.post({
                url: phost() + 'reclamos/ajax_get_contactos_ajustador',
                method: 'POST',
                data: {ajustador: ajus, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.$set('listaContactos', response.data.inter);
                }
            });

        },
        telefonoContacto: function (contacto){
            if (contacto == "") {
                contacto = 0;
            }
            var self = this;
            this.$http.post({
                url: phost() + 'reclamos/ajax_get_contacto',
                method: 'POST',
                data: {contacto: contacto, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    if (response.data.inter.length>0) {
                        $("#telefonodetalle").val(response.data.inter[0].telefono);
                    }else{
                        $("#telefonodetalle").val('');
                    }              
                }else{

                }
            });

        },
        grupoInfo: function () {
            var grupo_val = $('#grupo_nombre').val();
            $('#grupoInfo').val(grupo_val);
        },
        direccionInfo: function () {
            var direccion_val = $('#direccion_nombre').val();
            $('#direccionInfo').val(direccion_val);
        },
        coberturasPoliza: function (e) {
            this.getCoberturasPolizaInfo(e);
        },
        clienteDireccion: function () {
            this.getClienteDireccion();
        },
        getClienteDireccion: function () {
            //polula el segundo select del header
            var self = this;
            var centro_id = $('#centro_facturacion').val();
            this.$http.post({
                url: phost() + 'reclamos/ajax-get-direccion',
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
        getCoberturasPolizaInfo: function (pol) {
            //polula el segundo select del header
            var self = this;
            var poliza = pol;
            this.$http.post({
                url: phost() + 'reclamos/ajax_get_coberturas',
                method: 'POST',
                data: {poliza: poliza, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    coberturasForm.$set('coberturasInfo', response.data);
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
                url: phost() + 'reclamos/ajax_get_coberturas_editar',
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
                //if (interes != "") {
                this.$http.post({
                    url: phost() + 'reclamos/ajax_get_tipointereses',
                    method: 'POST',
                    data: {interes: interes, erptkn: tkn}
                }).then(function (response) {
                    if (_.has(response.data, 'session')) {
                        window.location.assign(phost());
                    }
                    if (!_.isEmpty(response.data)) {
                       
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
            var tipointeres = $('#formulario').val();
            if (interes != "") {
                this.$http.post({
                    url: phost() + 'reclamos/ajax_get_intereses',
                    async: false,
                    method: 'POST',
                    data: {interes: interes, tipointeres: tipointeres, erptkn: tkn}
                }).then(function (response) {
                    if (_.has(response.data, 'session')) {
                        window.location.assign(phost());
                    }
                    
                });
            } else {
                $("#uuid_articulo, #nombre, #clase_equipo, #marca_articulo, #modelo_articulo, #anio_articulo, #numero_serie, .valor_articulo, #observaciones_articulo, certificadodetalle_articulo, #sumaaseguradadetalle_articulo, #primadetalle_articulo, #deducibledetalle_articulo").val("");
                $(".uuid_carga, #no_liquidacion, #fecha_despacho, #fecha_arribo, #fecha_arribo, #detalle, #origen, #destino, .valor_mercancia, #acreedor_carga_opcional, #tipo_obligacion_opcional, #observaciones_carga, #certificadodetalle_carga, #sumaaseguradadetalle_carga, #primadetalle_carga, #deducibledetalle_carga").val("");
                $(".uuid_aereo, #serie_aereo, #marca_aereo, #modelo_aereo, #matricula_aereo, #valor_aereo, #pasajeros_aereo, #tripulacion_a, #observaciones_aereo, #certificadodetalle_aereo, #sumaaseguradadetalle_aereo, #primadetalle_aereo, #deducibledetalle_aereo").val("");
                $(".uuid_casco_maritimo, #serie_maritimo, .serier, #nombre_embarcacion, #marca_maritimo, .porcentaje_acreedor_maritimo, #valor_maritimo, #pasajeros_maritimo, #observaciones_maritimo, #certificadodetalle_maritimo, #sumaaseguradadetalle_maritimo, #primadetalle_maritimo, #deducibledetalle_maritimo").val("");
                $(".uuid,#nombrePersona,#provincia,#idPersona,#fecha_nacimiento,#estado_civil,#nacionalidad,#sexo,#estatura,#peso,#telefono_residencial,#telefono_oficina,#direccion,#direccion_laboral,#observacionesPersona,#estadoPersona,#identificacion,#pasaporte,#provinicia,#letra,#tomo,#asiento,#certificadoPersona, #primadetalle_persona, #montodetalle_persona,#participacion_persona,#suma_asegurada_persona").val("");
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
            this.$http.post({
                url: phost() + 'reclamos/ajax_get_asociados',
                method: 'POST',
                data: {unico: unico, erptkn: tkn}
            }).then(function (response) {

                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.$set('InteresesAsociados', response.data.inter);
                    $("#relaciondetalle_persona option").each(function () {

                        if ($(this).val() == "Dependiente") {
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
            coberturasModal.find('.modal-tile').empty();
            coberturasModal.find('.modal-body').empty().append(pantalla);
            coberturasModal.find('.modal-footer').empty().append(botones_coberturas);
            if (vista == "crear" || ( vista == "editar" && typeof this.reclamoInfo.estado != "undefined" && this.reclamoInfo.estado != "Cerrado" && this.reclamoInfo.estado != "Anulado")) {
                coberturasModal.modal('show'); 
            }

            if (vista == "editar" && contacoberturas == 0 ) {
                $('input[name="cobertura_poliza[]').each(function( index ) {
                    if (formularioCrear.reclamoInfoCob.indexOf(parseInt(this.value)) >= 0) {
                        this.checked = true;
                    }                    
                });  
                $('input[name="deduccion_poliza[]').each(function( index ) {
                    if (formularioCrear.reclamoInfoDed.indexOf(parseInt(this.value)) >= 0) {
                        this.checked = true;
                    }                    
                });
                contacoberturas++;
            }         

            $("#closeModalCoberturas").click(function () {
                $("#verCoberturas").modal('hide');
            });

            $("#guardarCoberturasPolizas").click(function () {
                var cober = "";
                var deduc = "";
                $('input[name="cobertura_poliza[]').each(function( index ) {
                    if (this.checked == true) {
                        cober+=this.value+",";
                    }                    
                });
                $('input[name="deduccion_poliza[]').each(function( index ) {
                    if (this.checked == true) {
                        deduc+=this.value+",";
                    }                    
                });
                $("#campocoberturas").val(cober);
                $("#campodeducciones").val(deduc);
                $("#verCoberturas").modal('hide');
            });
        },
        interesesModal: function (e) {
            //Inicializar opciones del Modal
            $('#verIntereses').modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
            });
            //Cerrar modal de opciones        
            var pantalla = $('.div_coberturas');
            pantalla.css('display', 'block');

            interesesModal.find('.modal-tile').empty();
            interesesModal.find('.modal-body').empty().append(pantalla);
            interesesModal.find('.modal-footer').empty().append('');
            interesesModal.modal('show');

            $("#closeModalCoberturas").click(function () {
                $("#verCoberturas").modal('hide');
            });

            $("#guardarCoberturasPolizas").click(function () {
                var cober = "";
                var deduc = "";
                $('input[name="cobertura_poliza[]').each(function( index ) {
                    if (this.checked == true) {
                        cober+=this.value+",";
                    }                    
                });
                $('input[name="deduccion_poliza[]').each(function( index ) {
                    if (this.checked == true) {
                        deduc+=this.value+",";
                    }                    
                });
                $("#campocoberturas").val(cober);
                $("#campodeducciones").val(deduc);
                $("#verCoberturas").modal('hide');
            });
        },       
        documenteshion: function (n_check, nombre, obligatorio, modulo) {
            
            var id_intereses = localStorage.getItem("id_intereses");
            var id_cliente = localStorage.getItem("id_cliente");
            var mensaje = "";
            var cont = 1;
            var cont2 = 0;

            console.log(n_check, nombre, obligatorio, modulo);

            for (var x = 0; x <= $('#cantidad_check').val(); x++) {
                if ($('#documentacion_' + x).prop('checked') === true) {
                    console.log("x="+x);
                    cont = cont + 1;
                }

            }
            console.log(cont);

            if ($('#documentacion_' + n_check).prop('checked') === false && obligatorio === "Si") {
                mensaje = "Campo requerido";
                $('#error_check').val("<label class='error'>" + mensaje + "</label>");
                console.log("aqui1");
            }

            console.log($('#documentacion_' + n_check).prop('checked'));
            if ($('#documentacion_' + n_check).prop('checked') === true) {
                console.log("aqui2");
                $('#requerido_chek_' + n_check).val(nombre);
                //$('#file_tools_solicitudes_' + n_check).before('<div class="file_upload_solicitudes" id="f' + n_check + '"><input readonly="readonly" value="' + nombre + '" name="nombre_documento[]" id="nombre_documento" type="text" style="width: 300px!important; float: left;" class="form-control"><input data-rule-required="true" name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><input type="hidden" value="' + nombre + '"  v-model="modulo" id="nombre'+ n_check +'" name="campodocumentacion[nombre_'+ n_check +']" class="modulo"><input type="hidden" value="' + modulo + '"  v-model="modulo" id="opcion'+ n_check +'" name="campodocumentacion[modulo_'+ n_check +']" class="modulo"><br><br><br></div>');
                $('#file_tools_solicitudes_' + n_check).before('<div class="file_upload_solicitudes" id="f' + n_check + '"><input readonly="readonly" value="' + nombre + '" name="nombre_documento[]" id="nombre_documento" type="text" style="width: 300px!important; float: left;" class="form-control"><input data-rule-required="true" name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><input type="hidden" value="" id="opcion' + n_check + '" name="campomodulo[]" class="modulo"><input type="hidden" value="list" name="campotipodoc[]"><input type="hidden" value="' + id_cliente + '" name="campoidcliente[]"><br><br><br></div>');

            } else if ($('#documentacion_' + n_check).prop('checked') === false) {
                console.log("aqui3");
                console.log(n_check);
                $('#requerido_chek_' + n_check).val("");
                //$('#del_file_solicitudes').hide();
                $('#f' + n_check).remove();
            } else {
                console.log("aqui4");
                $('#f' + n_check).remove();
            }
            
            for (var x = 0; x <= $('#cantidad_check').val(); x++) {
                if ($('#f' + x).length) {
                    console.log(x);
                    console.log("aqui6");
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
        existeDocumento: function(doc) {
            for (var i =0; i< documentacionesgbd.length; i++) {
                if (documentacionesgbd[i].valor == doc) {
                    return true;
                }
            }
            return false;
        },      
        limpiarDetalle: function () {
            $(" #sumaaseguradadetalle_articulo, #primadetalle_articulo, #certificadodetalle_articulo, #deducibledetalle_articulo, #sumaaseguradadetalle_carga, #primadetalle_carga, #certificadodetalle_carga, #deducibledetalle_carga, #sumaaseguradadetalle_aereo, #primadetalle_aereo, #certificadodetalle_aereo, #deducibledetalle_aereo, #sumaaseguradadetalle_maritimo, #primadetalle_maritimo, #certificadodetalle_maritimo, #deducibledetalle_maritimo, #sumaaseguradadetalle_proyecto, #primadetalle_proyecto, #certificadodetalle_proyecto, #deducibledetalle_proyecto, #sumaaseguradadetalle_ubicacion, #primadetalle_ubicacion, #certificadodetalle_ubicacion, #deducibledetalle_ubicacion, #sumaaseguradadetalle_vehiculo, #primadetalle_vehiculo, #certificadodetalle_vehiculo, #deducibledetalle_vehiculo").val("");
        },
        interesesReclamo: function (rec) {
            //polula el segundo select del header
            var self = this;
            var reclamo = rec;
            if(rec==""){reclamo = 0;}
            this.$http.post({
                url: phost() + 'reclamos/ajax_get_intereses_reclamo',
                method: 'POST',
                data: {tipo_interes: id_tipo_int_asegurado, reclamo: reclamo, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.tablaError = "";
                    if (!_.isEmpty(response.data)) {
                        //Se asigna el ID interes asegurado cuando es Individual
                        if (typeof response.data.inter != "undefined") {
                            $("#reclamointeres").val(response.data.inter.no_certificado);
                            $("#reclamoidinteres").val(response.data.inter.id);
                        }          
                        localStorage.setItem("id_intereses", response.data.inter.id);              
                        var tipoint = id_tipo_int_asegurado;
                        if (tipoint == 1 && typeof response.data.inter != "undefined") {
                            $("#reclamointeres").val(response.data.inter.nombre);
                            $(".uuid_carga, #nombre, #clase_equipo, #marca, #modelo, #anio_articulo, #numero_serie, .valor_articulo, #observaciones_articulo").val("");
                            $("#uuid_articulo").val(response.data.inter.uuid_intereses);
                            $("#nombre").val(response.data.inter.nombre);
                            $("#nombre").attr('disabled',true);
                            $("#clase_equipo").val(response.data.inter.clase_equipo);
                            $("#clase_equipo").attr('disabled',true);
                            $("#marca").val(response.data.inter.marca);
                            $("#marca").attr('disabled',true);
                            $("#modelo").val(response.data.inter.modelo);
                            $("#modelo").attr('disabled',true);
                            $("#anio_articulo").val(response.data.inter.anio);
                            $("#anio_articulo").attr('disabled',true);
                            $("#numero_serie").val(response.data.inter.numero_serie);
                            $("#numero_serie").attr('disabled',true);
                            $(".valor_articulo").val(response.data.inter.valor);
                            $(".valor_articulo").attr('disabled',true);
                            $("#observaciones_articulo").val(response.data.inter.observaciones);
                            $("#observaciones_articulo").attr('disabled',true);
                            $("#id_condicion").val(response.data.inter.id_condicion); //option[value='" + response.data.inter.id_condicion + "']
                            $("#id_condicion").attr('disabled',true);
                            $(".estado_articulo").empty();
                            $(".estado_articulo").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");
                            $(".estado_articulo").attr("disabled",true);

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                            /*$("#id_condicion option[value='" + response.data.inter.id_condicion + "']").attr("selected", "selected");
                             $("#campo2[estado] option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/


                        } else if (tipoint == 2 && typeof response.data.inter != "undefined") {

                            $("#reclamointeres").val(response.data.inter.no_liquidacion);
                            $(".uuid_carga, #no_liquidacion, #fecha_despacho, #fecha_arribo, #fecha_arribo, #detalle, #origen, #destino, .valor_mercancia, #acreedor_carga_opcional, #tipo_obligacion_opcional, #observaciones_carga").val("");
                            $(".uuid_carga").val(response.data.inter.uuid_intereses);
                            $("#no_liquidacion").val(response.data.inter.no_liquidacion);
                            $("#no_liquidacion").attr('disabled',true);
                            $("#fecha_despacho").val(response.data.inter.fecha_despacho);
                            $("#fecha_despacho").attr('disabled',true);
                            $("#fecha_arribo").val(response.data.inter.fecha_arribo);
                            $("#fecha_arribo").attr('disabled',true);
                            $("#detalle").val(response.data.inter.detalle);
                            $("#detalle").attr('disabled',true);
                            $("#origen").val(response.data.inter.origen);
                            $("#origen").attr('disabled',true);
                            $("#destino").val(response.data.inter.destino);
                            $("#destino").attr('disabled',true);
                            $(".valor_mercancia").val(response.data.inter.valor);
                            $(".valor_mercancia").attr('disabled',true);
                            $("#observaciones_carga").val(response.data.inter.observaciones);
                            $("#observaciones_carga").attr('disabled',true);
                            $(".tipo_empaque").val(response.data.inter.tipo_empaque); //option[value='" + response.data.inter.tipo_empaque + "']
                            $(".tipo_empaque").attr('disabled',true);

                            var y = $(".acreedor_carga option[value='" + response.data.inter.acreedor + "']");
                            if (y.length == 1) {
                                $(".acreedor_carga").val(response.data.inter.acreedor); //option[value='" + response.data.inter.acreedor + "']
                                $(".acreedor_carga").attr('disabled',true);
                                if (response.data.inter.acreedor == "otro") {
                                    $("#acreedor_carga_opcional").val(response.data.inter.acreedor_opcional);
                                }
                                $("#acreedor_carga_opcional").attr('disabled',true);
                            }
                            var w = $(".tipo_obligacion option[value='" + response.data.inter.tipo_obligacion + "']");
                            if (w.length == 1) {
                                $(".tipo_obligacion").val(response.data.inter.tipo_obligacion); //option[value='" + response.data.inter.tipo_obligacion + "']
                                $(".tipo_obligacion").attr('disabled',true);
                                if (response.data.inter.tipo_obligacion == "otro") {
                                    $("#tipo_obligacion_opcional").val(response.data.inter.tipo_obligacion_opcional);
                                }
                                $("#tipo_obligacion_opcional").attr('disabled',true);
                            }

                            $(".condicion_envio").val(response.data.inter.condicion_envio); //option[value='" +  + "']
                            $(".condicion_envio").attr('disabled',true);
                            $(".medio_transporte").val(response.data.inter.medio_transporte); //option[value='" + response.data.inter.medio_transporte + "']
                            $(".medio_transporte").attr('disabled',true);
                            $(".estado_carga").empty();
                            $(".estado_carga").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");
                            $(".estado_carga").attr('disabled',true);

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }
                            /*
                             $(".condicion_envio option[value='" + response.data.inter.condicion_envio + "']").attr("selected", "selected");
                             $(".medio_transporte option[value='" + response.data.inter.medio_transporte + "']").attr("selected", "selected");
                             $(".estado_carga option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/

                        } else if (tipoint == 3 && typeof response.data.inter != "undefined" ) {
                            $("#reclamointeres").val(response.data.inter.serie);
                            $(".uuid_aereo, #serie_aereo, #marca_aereo, #modelo_aereo, #matricula_aereo, #valor_aereo, #pasajeros_aereo, #tripulacion_a, #observaciones_aereo").val("");
                            $(".uuid_aereo").val(response.data.inter.uuid_intereses);
                            $("#serie_aereo").val(response.data.inter.serie);
                            $("#serie_aereo").attr('disabled',true);
                            $("#marca_aereo").val(response.data.inter.marca);
                            $("#marca_aereo").attr('disabled',true);
                            $("#modelo_aereo").val(response.data.inter.modelo);
                            $("#modelo_aereo").attr('disabled',true);
                            $("#matricula_aereo").val(response.data.inter.matricula);
                            $("#matricula_aereo").attr('disabled',true);
                            $("#valor_aereo").val(response.data.inter.valor);
                            $("#valor_aereo").attr('disabled',true);
                            $("#pasajeros_aereo").val(response.data.inter.pasajeros);
                            $("#pasajeros_aereo").attr('disabled',true);
                            $("#tripulacion_a").val(response.data.inter.tripulacion);
                            $("#tripulacion_a").attr('disabled',true);
                            $("#observaciones_aereo").val(response.data.inter.observaciones);
                            $("#observaciones_aereo").attr('disabled',true);
                            $(".estado_aereo").empty();
                            $(".estado_aereo").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");  
                            $(".estado_aereo").attr('disabled',true);
                            //$(".estado_aereo option[value='" + response.data.inter.estado + "']").attr("selected", "selected");

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                            conta = 1;
                        } else if (tipoint == 4 && typeof response.data.inter != "undefined" ) {
                            $("#reclamointeres").val(response.data.inter.serie);
                            $(".uuid_casco_maritimo, #serie_maritimo, .serier, #marca_maritimo, #nombre_embarcacion, .porcentaje_acreedor_maritimo, #valor_maritimo, #pasajeros_maritimo, #observaciones_maritimo").val("");
                            $(".uuid_casco_maritimo").val(response.data.inter.uuid_intereses);
                            $("#serie_maritimo").val(response.data.inter.serie);
                            $("#serie_maritimo").attr('disabled',true);
                            $(".serier").val(response.data.inter.serie);
                            $(".serier").attr('disabled',true);
                            $("#nombre_embarcacion").val(response.data.inter.nombre_embarcacion);
                            $("#nombre_embarcacion").attr('disabled',true);
                            $("#marca_maritimo").val(response.data.inter.marca);
                            $("#marca_maritimo").attr('disabled',true);
                            $(".porcentaje_acreedor_maritimo").val(response.data.inter.porcentaje_acreedor);
                            $(".porcentaje_acreedor_maritimo").attr('disabled',true);
                            $("#valor_maritimo").val(response.data.inter.valor);
                            $("#valor_maritimo").attr('disabled',true);
                            $("#pasajeros_maritimo").val(response.data.inter.pasajeros);
                            $("#pasajeros_maritimo").attr('disabled',true);
                            $("#observaciones_maritimo").val(response.data.inter.observaciones);
                            $("#observaciones_maritimo").attr('disabled',true);
                            $(".tipo_maritimo option[value='" + response.data.inter.tipo + "']").attr("selected", "selected");
                            $(".tipo_maritimo").attr('disabled',true);
                            $(".acreedor_maritimo option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");
                            $(".acreedor_maritimo").attr('disabled',true);
                            $(".estado_casco").empty();
                            $(".estado_casco").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");
                            $(".estado_casco").attr('disabled',true);
                            /*
                             $(".tipo_maritimo option[value='" + response.data.inter.tipo + "']").attr("selected", "selected");
                             $(".acreedor_maritimo option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");
                             $(".estado_casco option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/

                             if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                        } else if (tipoint == 5 && typeof response.data.inter != "undefined" ) {
                            $("#reclamointeres").val(response.data.inter.identificacion);
                            $(".uuid").val(response.data.inter.uuid_intereses);
                            $("#nombrePersona").val(response.data.inter.nombrePersona);
                            $("#nombrePersona").attr('disabled',true);

                            $("input:checkbox").prop('checked',false);
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
                            $("#identificacion").attr('disabled',true);
                            $("#pasaporte").attr('disabled',true);
                            $("#provincia").attr('disabled',true);
                            $("#letra").attr('disabled',true);
                            $("#tomo").attr('disabled',true);
                            $("#asiento").attr('disabled',true);
                            $('.noPAS').attr('disabled',true);
                            $(".PAS").attr('disabled',true);

                            $("#fecha_nacimiento").val(response.data.inter.fecha_nacimiento);
                            $("#fecha_nacimiento").attr('disabled',true);
                            var today = new Date();
                            console.log(today);
                            var format = response.data.inter.fecha_nacimiento.split("-");
                            var dob = new Date(format[0], format[1], format[2]);
                            var diff = (today - dob ) ;
                            var age = Math.floor(diff / 31536000000);
                            $("[id*=edad]").val(age);
                            $("#edad").attr('disabled',true);
                            $('#estado_civil').val(response.data.inter.estado_civil);
                            $('#estado_civil').attr('disabled',true);
                            $('#nacionalidad').val(response.data.inter.nacionalidad);
                            $('#nacionalidad').attr('disabled',true);
                            $('#sexo').val(response.data.inter.sexo);
                            $('#sexo').attr('disabled',true);
                            $('#estatura').val(response.data.inter.estatura);
                            $('#estatura').attr('disabled',true);
                            $('#peso').val(response.data.inter.peso);
                            $('#peso').attr('disabled',true);
                            $('#telefono_residencial').val(response.data.inter.telefono_residencial);
                            $('#telefono_residencial').attr('disabled',true);
                            $('#telefono_oficina').val(response.data.inter.telefono_oficina);
                            $('#telefono_oficina').attr('disabled',true);
                            $('#direccion').val(response.data.inter.direccion_residencial);
                            $('#direccion').attr('disabled',true);
                            $('#direccion_laboral').val(response.data.inter.direccion_laboral);
                            $('#direccion_laboral').attr('disabled',true);
                            $('#observacionesPersona').val(response.data.inter.observaciones);
                            $('#observacionesPersona').attr('disabled',true);
                            $('#estadoPersona').empty();
                            $('#estadoPersona').append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");
                            $('#estadoPersona').attr('disabled',true);
                            $('#idPersona').val(response.data.inter.interesestable_id);
                            $('#idPersona').attr('disabled',true);

                            $('#correoPersona').val(response.data.inter.correo);
                            $('#correoPersona').attr('disabled',true);
                            
                            if(response.data.inter.telefono_principal == 'Residencial'){
                               $('#telefono_residencial_check').prop('checked',true);     
                           }else if(response.data.inter.telefono_principal == 'Laboral'){
                                $('#telefono_oficina_check').prop('checked',true);
                            }

                            if(response.data.inter.direccion_principal=='Residencial'){
                                $('#direccion_residencial_check').prop('checked',true);
                            }else if(response.data.inter.direccion_principal=='Laboral'){
                                $('#direccion_laboral_check').prop('checked',true);
                            }

                            $('#telefono_residencial_check').attr('disabled',true);
                            $('#telefono_oficina_check').attr('disabled',true);
                            $('#direccion_residencial_check').attr('disabled',true);
                            $('#direccion_laboral_check').attr('disabled',true);

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                        } else if (tipoint == 6 && typeof response.data.inter != "undefined" ) {
                            $("#reclamointeres").val(response.data.inter.nombre_proyecto);
                            //$('.acreedor_proyecto').removeAttr("selected");
                            //$('.acreedor_proyecto').prop('selected', true);
                            $(".uuid_proyecto, #nombre_proyecto, #contratista_proyecto, #representante_legal_proyecto, #fecha_concurso, #no_orden_proyecto, .no_ordenr, #duracion_proyecto, .fecha_proyecto, .monto_proyecto, #monto_afianzado, #asignado_acreedor, #ubicacion_proyecto, #acreedor_opcional, #validez_fianza_opcional, #observaciones_proyecto").val("");

                            $(".uuid_proyecto").val(response.data.inter.uuid_intereses);
                            $("#nombre_proyecto").val(response.data.inter.nombre_proyecto);
                            $("#nombre_proyecto").attr("disabled",true);
                            $("#contratista_proyecto").val(response.data.inter.contratista);
                            $("#contratista_proyecto").attr("disabled",true);
                            $("#representante_legal_proyecto").val(response.data.inter.representante_legal);
                            $("#representante_legal_proyecto").attr("disabled",true);
                            $("#fecha_concurso").val(response.data.inter.fecha_concurso);
                            $("#fecha_concurso").attr("disabled",true);
                            $("#no_orden_proyecto").val(response.data.inter.no_orden);
                            $("#no_orden_proyecto").attr("disabled",true);
                            $(".no_ordenr").val(response.data.inter.no_orden);
                            $(".no_ordenr").attr("disabled",true);
                            $("#duracion_proyecto").val(response.data.inter.duracion);
                            $("#duracion_proyecto").attr("disabled",true);
                            $(".fecha_proyecto").val(response.data.inter.fecha);
                            $(".fecha_proyecto").attr("disabled",true);
                            $(".monto_proyecto").val(response.data.inter.monto);
                            $(".monto_proyecto").attr("disabled",true);
                            $("#monto_afianzado").val(response.data.inter.monto_afianzado);
                            $("#monto_afianzado").attr("disabled",true);
                            $("#asignado_acreedor").val(response.data.inter.asignado_acreedor);
                            $("#asignado_acreedor").attr("disabled",true);
                            $("#ubicacion_proyecto").val(response.data.inter.ubicacion);
                            $("#ubicacion_proyecto").attr("disabled",true);
                            $("#acreedor_opcional").val(response.data.inter.acreedor_opcional);
                            $("#acreedor_opcional").attr("disabled",true);
                            $("#validez_fianza_opcional").val(response.data.inter.validez_fianza_opcional);
                            $("#validez_fianza_opcional").attr("disabled",true);
                            $("#observaciones_proyecto").val(response.data.inter.observaciones);
                            $("#observaciones_proyecto").attr("disabled",true);
                            $(".tipo_fianza").val(response.data.inter.tipo_fianza); // option[value='" + response.data.inter.tipo_fianza + "']
                            $(".tipo_fianza").attr("disabled",true);
                            $(".tipo_propuesta").val(response.data.inter.tipo_propuesta); //option[value='" + response.data.inter.tipo_propuesta + "']
                            $(".tipo_propuesta").attr("disabled",true);
                            $(".acreedor_proyecto").val(response.data.inter.acreedor); //option[value='" + response.data.inter.acreedor + "']
                            $(".acreedor_proyecto").attr("disabled",true);
                            $(".validez_fianza_pr").val(response.data.inter.validez_fianza_pr); //option[value='" + response.data.inter.validez_fianza_pr + "']
                            $(".validez_fianza_pr").attr("disabled",true);
                            $(".estado_proyecto").empty();
                            $(".estado_proyecto").append("<option value="+response.data.inter.estado+">"+response.data.inter.estado+"</option>");
                            $(".estado_proyecto").attr("disabled",true);

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                        } else if (tipoint == 7 && typeof response.data.inter != "undefined" ) {
                            $("#reclamointeres").val(response.data.inter.nombre);
                            $(".uuid_ubicacion, #nombre_ubicacion, #direccion_ubicacion, .serier, #edif_mejoras, #contenido, #maquinaria, #inventario, #acreedor_ubicacion_opcional, #porcentaje_acreedor_ubicacion, #observaciones_ubicacion").val("");
                            $(".uuid_ubicacion").val(response.data.inter.uuid_intereses);
                            $("#nombre_ubicacion").val(response.data.inter.nombre);
                            $("#nombre_ubicacion").attr('disabled',true);
                            $("#direccion_ubicacion").val(response.data.inter.direccion);
                            $("#direccion_ubicacion").attr('disabled',true);
                            $(".serier").val(response.data.inter.direccion);
                            $(".serier").attr('disabled',true);
                            $("#edif_mejoras").val(response.data.inter.edif_mejoras);
                            $("#edif_mejoras").attr('disabled',true);
                            $("#contenido").val(response.data.inter.contenido);
                            $("#contenido").attr('disabled',true);
                            $("#maquinaria").val(response.data.inter.maquinaria);
                            $("#maquinaria").attr('disabled',true);
                            $("#inventario").val(response.data.inter.inventario);
                            $("#inventario").attr('disabled',true);
                            $("#acreedor_ubicacion_opcional").val(response.data.inter.acreedor_ubicacion_opcional);
                            $("#acreedor_ubicacion_opcional").attr('disabled',true);
                            $("#porcentaje_acreedor_ubicacion").val(response.data.inter.porcentaje_acreedor);
                            $("#porcentaje_acreedor_ubicacion").attr('disabled',true);
                            $("#observaciones_ubicacion").val(response.data.inter.observaciones);
                            $("#observaciones_ubicacion").attr('disabled',true);
                            console.log(response.data.inter.acreedor);
                            $("#acreedor_ubicacion").val(response.data.inter.acreedor);  //option[value='" + response.data.inter.acreedor + "']
                            $("#acreedor_ubicacion").attr('disabled',true);
                            $(".estado_ubicacion").empty();
                            $(".estado_ubicacion").append("<option value="+response.data.inter.estado+">"+response.data.inter.estado+"</option>");
                            $(".estado_ubicacion").attr('disabled',true);

                            var acreedor_ubicacion = $('.acreedor_ubicacion').val();
                            if (acreedor_ubicacion === 'otro') {
                                $('#acreedor_ubicacion_opcional').val(response.data.inter.acreedor_opcional);
                                $('#acreedor_ubicacion_opcional').attr('disabled',true);
                            }else{
                                $('#acreedor_ubicacion_opcional').attr('disabled',true);
                            }

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                        } else if (tipoint == 8 && typeof response.data.inter != "undefined" ) {
                            $("#reclamointeres").val(response.data.inter.chasis);
                            $("#uuid_vehiculo, #chasis, #unidad, #marca, #modelo, #placa, #ano, #motor, #color, #capacidad, #operador, #extras, #valor_extras, #porcentaje_acreedor, #observaciones_vehiculo ").val("");
                            $("#uuid_vehiculo").val(response.data.inter.uuid_intereses);
                            $("#chasis").val(response.data.inter.chasis);
                            $("#chasis").attr('disabled',true);
                            $("#unidad").val(response.data.inter.unidad);
                            $("#unidad").attr('disabled',true);
                            $("#placa").val(response.data.inter.placa);
                            $("#placa").attr('disabled',true);
                            $(".marca_vehiculo").val(response.data.inter.marca);
                            $(".marca_vehiculo").attr('disabled',true);
                            $(".modelo_vehiculo").val(response.data.inter.modelo);
                            $(".modelo_vehiculo").attr('disabled',true);
                            $("#ano").val(response.data.inter.ano);
                            $("#ano").attr('disabled',true);
                            $("#motor").val(response.data.inter.motor);
                            $("#motor").attr('disabled',true);
                            $("#color").val(response.data.inter.color);
                            $("#color").attr('disabled',true);
                            $("#capacidad").val(response.data.inter.capacidad);
                            $("#capacidad").attr('disabled',true);
                            $("#operador").val(response.data.inter.operador);
                            $("#operador").attr('disabled',true);
                            $("#extras").val(response.data.inter.extras);
                            $("#extras").attr('disabled',true);
                            $("#valor_extras").val(response.data.inter.valor_extras);
                            $("#valor_extras").attr('disabled',true);
                            $(".porcentaje_vehiculo").val(response.data.inter.porcentaje_acreedor);
                            $(".porcentaje_vehiculo").attr('disabled',true);
                            $("#observaciones_vehiculo").val(response.data.inter.observaciones);
                            $("#observaciones_vehiculo").attr('disabled',true);
                            $("#uso").val(response.data.inter.uso); //option[value='" + response.data.inter.uso + "']
                            $("#uso").attr('disabled',true);
                            $(".condicion_vehiculo").val(response.data.inter.condicion); //option[value='" + response.data.inter.condicion + "']
                            $(".condicion_vehiculo").attr('disabled',true);
                            $(".acreedor").val(response.data.inter.acreedor); //option[value='" + response.data.inter.acreedor + "']
                            $(".acreedor").attr('disabled',true);
                            $(".estado").empty();
                            $(".estado").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");
                            $(".estado").attr('disabled',true);

                            if ( (response.data.inter.observaciones != null) && (response.data.inter.observaciones != "") ) {
                                $("#observa").show();
                            }else{
                                $("#observa").hide();
                            }

                            /*
                             $("#uso option[value='" + response.data.inter.uso + "']").attr("selected", "selected");
                             $(".condicion_vehiculo option[value='" + response.data.inter.condicion + "']").attr("selected", "selected");
                             $(".acreedor option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");
                             $(".estado option[value='" + response.data.inter.estado + "']").attr("selected", "selected");*/

                        }
                    }else{
                        
                    }
                }
            });
        }

    },
    computed: {    

    }

});
formularioCrear.getPolizaSeleccionado();


function verIntereses() {
    formularioCrear.getInteres();
    formularioCrear.limpiarDetalle();
    formularioCrear.setPrimaNeta();
}

$("#reclamante").change(function () {
    var element = $(this).find('option:selected'); 
    var telefono = typeof element.attr("telefono") != "undefined" ? element.attr("telefono") : ''; 
    var correo = typeof element.attr("correo") != "undefined" ? element.attr("correo") : '';
    $("#telefono").val(telefono);
    $("#correo").val(correo);
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
        clearFields: function () {

            clearFields(validateFields);
            $(".error").remove();
            $("#verCoberturas").modal("hide");

        }
    }


});



$(document).ready(function () {

    if (vista === 'editar') {
        if (permiso_estado == 0) { $("#estado").attr("disabled", "disabled"); }
        if (permiso_asignar == 0) { $("#asignado_a").attr("disabled", "disabled"); }
        if (permiso_editar == 0) { $(".guardarsolicitud").remove(); }
        cambiaAjustador(formularioCrear.reclamoInfo.ajustador);
        $("#telefonodetalle").val(formularioCrear.reclamoInfo.telefonodetalle);
        $("#id_reclamo_documento").val(id_reclamo);
        
        $('#documentos_editar').show();
        $('#docentregados_crear').hide();
        $("#nombre_doc_titulo").hide();
        $("#nombre_doc_titulo_editar").hide();
        $('#cantidad_check').val(documentaciones.length);

        console.log(vista);
    } else {
        $("#documentos_editar").hide();
        $('#docentregados_crear').show();
        $("#nombre_doc_titulo").hide();
        $("#nombre_doc_titulo_editar").hide();

        console.log(vista);
        console.log(ramoscadena);
    }
    var counter = 2;
    $('#del_file_solicitudes_adicionales').hide();
    $('#add_file_solicitudes_adicionales').click(function () {

        $('#file_tools_solicitudes_adicionales').before('<div class="file_upload_solicitudes row" id="f' + counter + '"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"></div>');
        $('#del_file_solicitudes_adicionales').fadeIn(0);
        counter++;
    });
    $('#del_file_solicitudes_adicionales').click(function () {
        if (counter == 3) {
            $('#del_file_solicitudes_adicionales').hide();
        }
        counter--;
        $('#f' + counter).remove();
    });

    if (editar_asignado != 1) {
        $("#usuario_id").attr("disabled", "disabled");
    }

});

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

    var exp = new RegExp(/^[0-9]+$/);

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

            console.log(selInteres);
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

            console.log(selInteres);
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

            console.log(selInteres);
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

            console.log(selInteres);
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

            console.log(selInteres);
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


$(function () {
/*    console.log(documentaciones.length());
    jQuery.validator.setDefaults({
        debug: true,
        success: "valid"
    });
    $(formularioCrear).validate({
        rules: {
            documentacion: {
                required: true
            }
       }
    });

    var cantidad_check = $("#cantidad_check").val();
    var $fields = ('input[name="list"]:checked');
    if (!$fields.length) {
        alert('You must check at least one box!');
        return false;
    }
    for (var i = 0; i < cantidad_check; i++) {
    $('input[name="documentacion[]:checked').rules(
            "add", {
                required: true
            });
        }
*/
});