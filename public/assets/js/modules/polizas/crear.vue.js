Vue.http.options.emulateJSON = true;
var validateFields = [
{field: {input: "input[name='coverageName[]']", valiation: "alfanúmerico"}},
{field: {input: "input[name='coverageValue[]']", valiation: "numeric"}},
{field: {input: "input[name='deductibleName[]']", valiation: "alfanúmerico"}},
{field: {input: "input[name='deductibleValue[]']", valiation: "numeric", }}];
var opcionesModal = $('#verCoberturas');
var formularioCrear = new Vue({
    el: ".wrapper-content",
    data:{
        comboEstado: estado_solicitud,
        estado_pol: estado_pol,
        polizaCliente: cliente,
        polizaAseguradora: aseguradora,
        polizaPlan: plan,
        polizaCoberturas: coberturas,
        polizaDeducciones: deducciones,
        polizaComision: comision,
        polizaVigencia: vigencia,
        polizaPrima: prima,
        polizaCentroFacturacion: centroFacturacion,
        polizaParticipacion: participacion,
        polizaTotalParticipacion: agtPrincipal !== '' ? 100.00 : totalParticipacion,
        id_centroContable: id_centroContable,
        nombre_centroContable: nombre_centroContable,
        disabledfechaInicio: true,
        cambiarOpcionesPago: true,
        disabledfechaExpiracion: true,
        disabledAgente : true,
        disableParticipacion : true,
        addons: [],
        catalogoCantidadPagos:cantidadPagos,
        catalogoSitioPago  : sitioPago,
        catalogoMetodoPago :metodoPago,
        catalogoFrecuenciaPagos :frecuenciaPagos,
        catalogoCentroFacturacion:centrosFacturacion,
        InteresesAsociados: [],
        centrosContables : centrosContables,
        pagador:pagador,
        polizaGrupo:grupo

    },

    methods: {


        getAsociado: function () {
            var self = this;
            var idpoliza = $("#idPoliza").val();
            this.$http.post({
                url: phost() + 'polizas/ajax_get_asociados',
                method: 'POST',
                data: {idpoliza: idpoliza, erptkn: tkn}
            }).then(function (response) {

                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.$set('InteresesAsociados', response.data.inter);
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
                    $("#campopagador").val(vigencia.pagador);
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
        selectFormToSend:function(){
         var formIdArray=[ 

         { id:1 ,formIdName:"articulo"},
         { id:2 ,formIdName:"formCarga"},
         { id:3 ,formIdName:"formcasco_aereo"},
         { id:4 ,formIdName:"formCasco_maritimo"},
         { id:5 ,formIdName:"persona"},
         { id:6 ,formIdName:"formProyecto_actividad"},
         { id:7 ,formIdName:"formUbicacion"},
         { id:8 ,formIdName:"vehiculo"}] ,values = {}; 

         for (var i =formIdArray.length - 1; i >= 0; i--) {
            var value= formIdArray[i],stringId;
            if (id_tipo_int_asegurado == value.id) {
                stringId= '#' +value.formIdName;
                if ($(stringId).validate().form()) {
                    var inputs = $(stringId+' :input');

                    inputs.each(function () {

                        values[this.name] =$(this).val();
                    });
                }else{
                    values = false;
                }
            }
        }
        return values;
    },
    individualInterest:function(){
        var values = this.selectFormToSend() ,polizaId =$("input[name='campo[id]']").val();
        var unico = $("input[name='detalleunico']").val();

        if(values){
            this.$http.post({
                url: phost() + 'polizas/ajax_save_individual_interest',
                method:'POST',
                data:{
                    camposInteres: JSON.stringify(values),
                    interestType:window.id_tipo_int_asegurado,
                    detalleUnico:unico,
                    polizaId: polizaId,
                    interesId:$("#selInteres").val(),
                    erptkn: tkn}
                }).then(function(response){

                    tablaSolicitudesPersonas.recargar();
                });
            }
            return values;
        },
        renovationModal: function (idPolicy) {
            var unico = $("input[name='detalleunico']").val();
            this.$http.post({
                url: phost() + 'polizas/getRenovationData',
                method:'POST',
                data:{idPoliza:idPolicy,
                    detalleUnico:unico,
                    erptkn: tkn
                }
            }).then(function(response){
                if(_.has(response.data, 'session')){
                    window.location.assign(phost());
                }     
                if(!_.isEmpty(response.data)){ 

                 // this.$set('PolicyData',response.data);
                // this.$set('numero',response.data.numero);
                this.$set('numeroPoliza',response.data.numero);
                this.$set('fechaInicio',response.data.fechaInicio);
                this.$set('fechaExpiracion',response.data.fechaExpiracion);
                this.$set('disabledfechaInicio',response.data.isEditable);
                this.$set('disabledfechaExpiracion',response.data.isEditable);
                this.$set('idPolicy',$('#idPoliza').val());
                this.$set('cambiarOpcionesPago',false);
                
                //  this.$set('isEditable',response.data.isEditable);
                // this.$set('idPolicy',idPolicy);
                this.$set('comision',response.data.comision);

                this.$set('disabledComision',permiso_comision);
                
                
                this.$set('disabledAgente',!permiso_agente);
                

                this.$set('disableParticipacion',!permiso_participacion);

                $(".detail").remove();
                $(".detail_endoso").remove();
                $("#renovar").prop("hidden",false);
                $("#articulo, #formCarga, #formcasco_aereo, #formCasco_maritimo, #persona, #formProyecto_actividad, #formUbicacion, #vehiculo").attr('action', '' + window.location.href + '');
                $(".guardarVehiculo, .guardarArticulo, .guardarCarga, .guardarAereo, .guardarMaritimo, .guardarPersona, .guardarProyecto, .guardarUbicacion").attr("type", "button").val("Agregar");
                $("#articulo #cancelar, #formCarga #cancelar, #formcasco_aereo #cancelar, #formCasco_maritimo #cancelar, #persona #cancelar, #formProyecto_actividad #cancelar, #formUbicacion #cancelar, #vehiculo #cancelar").hide();
                
                
            }           

        });


        },
        sendRenewalData: function(e)
        {   


            if($('#formPolizasCrear').validate().form()){

                var participationArray = [];
                participationArray.push({
                    nombre: $("select[name='agente[]']").map(function () {
                        return $(this).val();
                    }).get(),
                    valor: $("input[name='participacion[]']").map(function () {
                        return $(this).val();
                    }).get()

                });

                if (tipo_ramo == "individual" ) {
                  var values = this.selectFormToSend();  
                  if(values){
                      this.$http.post({
                        url: phost() + 'polizas/policyRenewal',
                        method:'POST',
                        data:{
                            numeroPoliza:this.numeroPoliza,
                            erptkn: tkn,
                            fechaInicio:this.fechaInicio,
                            fechaExpiracion:this.fechaExpiracion,
                            participacion:participationArray[0],
                            renovarPoliza :true,
                            idPolicy :this.idPolicy,
                            comision: this.comision,
                            camposInteres:JSON.stringify(values),
                            interesId:$("#selInteres").val(),
                            clienteGrupo: this.clienteGrupo,
                            clienteTelefono : this.clienteTelefono,
                            clienteCorreo : this.clienteCorreo,
                            clienteDireccion: this.clienteDireccion,
                            clienteExoneradoImp :this.polizaCliente.exonerado_impuesto,
                            planesCoberturas: $("#planesCoberturasDeducibles").val(),
                            sumaAsegurada: this.sumaAsegurada,
                            vigenciapagador : this.vigenciaPagador,
                            vigenciaNombrePagador: this.polizaVigencia.pagador,
                            vigenciaPersonaAsegurada: this.vigenciaPersonaAsegurada,
                            vigenciaPolizaDeclarativa:this.vigenciaPolizaDeclarativa,
                            primaAnual : this.primaAnual,
                            primaDescuentos : this.primaDescuentos,
                            primaOtros : this.primaOtros,
                            primaImpuesto: this.primaImpuesto,
                            primaTotal:this.primaTotal,
                            pagosFrecuencia : this.pagosFrecuencia,
                            pagosMetodo : this.pagosMetodo,
                            pagosPrimerPago: this.pagosPrimerPago,
                            pagosCantidad : this.pagosCantidad,
                            pagosSitio :this.pagosSitio,
                            pagosCentroFac :this.pagosCentroFac,
                            pagosDireccion:this.pagosDireccion,
                            centroContable :this.centroContable
                        }
                    }).then(function(response){
                        if (!_.isEmpty(response.data) && response.data.msg =='OK') {

                            window.location= phost()+"polizas/listar";
                        }else{

                            msg='Ocurrido un error al guardar la renovación '+'<br>'+response.data.field+'<b>';

                            toastr.error(msg);
                        }           
                    }); 
                }
                else {
                    window.location.href = "#divintereses";

                }   
            }

        }

    },

    getIntereses: function () {
            //polula el segundo select del header
            var self      = this;
            var interes    = $('#formulario').val();
            var id_poliza  = $('#idPoliza').val();
            var getInteresUrl = window.vista==="renovar" ? 'solicitudes/ajax_get_tipointereses' : 'polizas/ajax_get_tipointereses';
            var unico = $("input[name='detalleunico']").val();
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
                $("#formulario").attr('disabled',true);
                
                $("#" + interes + "").addClass("active");

                if (interes != "") {
                    this.$http.post({
                        url: phost() + getInteresUrl,
                        method: 'POST',
                        data: {interes: interes, id_poliza : id_poliza ,unico:unico ,erptkn: tkn}
                    }).then(function (response) {
                        if (_.has(response.data, 'session')) {
                            window.location.assign(phost());
                        }
                        if (!_.isEmpty(response.data)) {
                            //console.log("aqui");
                            //console.log(response.data.inter);
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

                            console.log(response.data.inter);
                            if(tipo_ramo == "individual"){
                                $(document).ajaxStop(function(){
                                    if(response.data.inter.length > 0){
                                        var selInteres = response.data.inter[0].id;
                                        console.log(selInteres);
                                        $("#selInteres").val(selInteres).trigger('change'); 
                                        formularioCrear.getInteres();
                                        //this.getInteres();
                                    }
                                    
                                });
                            }   
                            
                        }
                    });
                }
            }
        },
        total: function() {

           /* var sum= this.addons.reduce((a, b) => parseInt(a) + parseInt(b));
            console.log(sum);
            this.$set("polizaTotalParticipacion",sum);*/

            var valor_final=0;
            var total=0;
            $("input[name='participacion[]']").map(function (index,dato) {
                if(isNaN(dato.value) || dato.value==='' || dato.value===null)
                   total=parseFloat(0);
               else
                   total=parseFloat(dato.value);
                //console.log(dato.value);
                valor_final+=parseFloat(total);
            }).get();

            // console.log(valor_final);
            
            if(agtPrincipal!="")
            {
               $('#participacionTotal').val(parseFloat(100));
           }
           else
           {
               this.$set("polizaTotalParticipacion",valor_final);
                //$('#participacionTotal').val(parseFloat(valor_final));
            }

            if(isNaN(valor_final))
               $('#porcAgentePrincipal').val(parseFloat(100).toFixed(2));
           else
            $('#porcAgentePrincipal').val(parseFloat(parseFloat(100).toFixed(2)-parseFloat(valor_final)).toFixed(2));   

    },
    enablePayFields: function() {

        this.$set("cambiarOpcionesPago",false);
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
                    self.$set('pagosDireccion', response.data[0].direccion);
                }
            });

        },
        getInteres: function () {
            //polula el segundo select del header
            var self = this;
            var interes = $('#selInteres').val();
            var tipointeres = $('#formulario').val();
            var getInteresUrl = window.vista == 'renovar' ? 'solicitudes/ajax_get_intereses':'polizas/ajax_get_intereses';
            

            if (interes != "") {
                this.$http.post({
                    url: phost() + getInteresUrl,
                    async: false,
                    method: 'POST',
                    data: {interes: interes, tipointeres: tipointeres, erptkn: tkn}
                }).then(function (response) {
                    if (_.has(response.data, 'session')) {
                        window.location.assign(phost());
                    }
                    if (!_.isEmpty(response.data)) {
                        var tipoint = response.data.inter.tipointeres;
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

                            $("#id_condicion").val(response.data.inter.id_condicion); //option[value='" + response.data.inter.id_condicion + "']
                            
                            $(".estado_articulo").empty();
                            $(".estado_articulo").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");

                            if(tipo_ramo == "colectivo"){
                                $("#certificadodetalle_articulo").val(response.data.inter.detalle_certificado);
                                
                                $("#sumaaseguradadetalle_articulo").val(response.data.inter.detalle_suma_asegurada);
                                
                                $("#primadetalle_articulo").val(response.data.inter.detalle_prima);
                                
                                $("#deducibledetalle_articulo").val(response.data.inter.detalle_deducible);
                                
                                

                            }
                            if(window.vista!=="renovar"){
                             //disabled Fields
                             $("#id_condicion").attr('disabled',true);
                             $("#observaciones_articulo").attr('disabled',true);
                             $(".estado_articulo").attr("disabled",true);
                             $("#nombre").attr('disabled',true);
                             $("#clase_equipo").attr('disabled',true);
                             $("#marca").attr('disabled',true);
                             $("#modelo").attr('disabled',true);
                             $("#anio_articulo").attr('disabled',true);
                             $(".valor_articulo").attr('disabled',true);
                             $("#numero_serie").attr('disabled',true);
                            //disabled fields 
                            $("#certificadodetalle_articulo").attr('disabled',true);
                            $("#sumaaseguradadetalle_articulo").attr('disabled',true);
                            $("#primadetalle_articulo").attr('disabled',true);
                            $("#deducibledetalle_articulo").attr('disabled',true);
                        }

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

                            $(".tipo_empaque").val(response.data.inter.tipo_empaque); //option[value='" + response.data.inter.tipo_empaque + "']
                            
                            //disabled fields
                            if(window.vista!=="renovar"){
                                $(".tipo_empaque").attr('disabled',true);
                                $("#observaciones_carga").attr('disabled',true);
                                $("#destino").attr('disabled',true);
                                $("#origen").attr('disabled',true);
                                $("#detalle").attr('disabled',true);
                                $("#fecha_arribo").attr('disabled',true);
                                $(".valor_mercancia").attr('disabled',true);
                                $("#fecha_despacho").attr('disabled',true);
                                $("#no_liquidacion").attr('disabled',true);
                                $(".condicion_envio").attr('disabled',true);
                                $(".medio_transporte").attr('disabled',true);
                                $(".estado_carga").attr('disabled',true);
                             //disabled fields
                             $("#deducibledetalle_carga").attr('disabled',true);
                             $("#certificadodetalle_carga").attr('disabled',true);
                             $("#sumaaseguradadetalle_carga").attr('disabled',true);
                             $("#primadetalle_carga").attr('disabled',true);
                             $(".acreedor_carga").attr('disabled',true);
                             $(".tipo_obligacion").attr('disabled',true);
                         }
                         var y = $(".acreedor_carga option[value='" + response.data.inter.acreedor + "']");
                         if (y.length == 1) {
                                $(".acreedor_carga").val(response.data.inter.acreedor); //option[value='" + response.data.inter.acreedor + "']
                                
                                if (response.data.inter.acreedor == "otro") {
                                    $("#acreedor_carga_opcional").val(response.data.inter.acreedor_opcional);
                                }
                                $("#acreedor_carga_opcional").attr('disabled',true);
                                
                            }
                            var w = $(".tipo_obligacion option[value='" + response.data.inter.tipo_obligacion + "']");
                            if (w.length == 1) {
                                $(".tipo_obligacion").val(response.data.inter.tipo_obligacion); //option[value='" + response.data.inter.tipo_obligacion + "']
                                
                                if (response.data.inter.tipo_obligacion == "otro") {
                                    $("#tipo_obligacion_opcional").val(response.data.inter.tipo_obligacion_opcional);
                                }
                                $("#tipo_obligacion_opcional").attr('disabled',true);
                                
                            }

                            $(".condicion_envio").val(response.data.inter.condicion_envio); //option[value='" +  + "']

                            $(".medio_transporte").val(response.data.inter.medio_transporte); //option[value='" + response.data.inter.medio_transporte + "']
                            
                            $(".estado_carga").empty();
                            $(".estado_carga").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");


                            if(tipo_ramo == "colectivo"){
                                $("#certificadodetalle_carga").val(response.data.inter.detalle_certificado);
                                
                                $("#sumaaseguradadetalle_carga").val(response.data.inter.detalle_suma_asegurada);
                                
                                $("#primadetalle_carga").val(response.data.inter.detalle_prima);
                                
                                $("#deducibledetalle_carga").val(response.data.inter.detalle_deducible);

                            }
                            
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
                            
                            $(".estado_aereo").empty();
                            $(".estado_aereo").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");  
                            


                            //disabled fields
                            if(window.vista!=="renovar"){
                                $("#serie_aereo").attr('disabled',true);
                                $("#marca_aereo").attr('disabled',true);
                                $("#modelo_aereo").attr('disabled',true);
                                $("#matricula_aereo").attr('disabled',true);
                                $("#valor_aereo").attr('disabled',true);
                                $("#pasajeros_aereo").attr('disabled',true);
                                $("#tripulacion_a").attr('disabled',true);
                                $("#observaciones_aereo").attr('disabled',true);
                                $(".estado_aereo").attr('disabled',true);
                            // disabled fields 
                            $("#certificadodetalle_aereo").attr('disabled',true);
                            $("#sumaaseguradadetalle_aereo").attr('disabled',true);
                            $("#primadetalle_aereo").attr('disabled',true);
                            $("#deducibledetalle_aereo").attr('disabled',true);
                        }

                        if(tipo_ramo == "colectivo"){
                            $("#certificadodetalle_aereo").val(response.data.inter.detalle_certificado);

                            $("#sumaaseguradadetalle_aereo").val(response.data.inter.detalle_suma_asegurada);

                            $("#primadetalle_aereo").val(response.data.inter.detalle_prima);

                            $("#deducibledetalle_aereo").val(response.data.inter.detalle_deducible);




                        }

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

                        $(".tipo_maritimo option[value='" + response.data.inter.tipo + "']").attr("selected", "selected");

                        $(".acreedor_maritimo option[value='" + response.data.inter.acreedor + "']").attr("selected", "selected");

                        $(".estado_casco").empty();
                        $(".estado_casco").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");


                            //disabled fields 
                            if(window.vista!=="renovar"){
                                $(".estado_casco").attr('disabled',true);
                                $(".acreedor_maritimo").attr('disabled',true);
                                $(".tipo_maritimo").attr('disabled',true);
                                $("#observaciones_maritimo").attr('disabled',true);
                                $("#pasajeros_maritimo").attr('disabled',true);
                                $("#valor_maritimo").attr('disabled',true);
                                $(".porcentaje_acreedor_maritimo").attr('disabled',true);
                                $("#marca_maritimo").attr('disabled',true);
                                $("#nombre_embarcacion").attr('disabled',true);
                                $(".serier").attr('disabled',true);
                                $("#serie_maritimo").attr('disabled',true);
                             //disabled fields
                             $("#deducibledetalle_maritimo").attr('disabled',true);
                             $("#primadetalle_maritimo").attr('disabled',true);
                             $("#sumaaseguradadetalle_maritimo").attr('disabled',true);
                             $("#certificadodetalle_maritimo").attr('disabled',true);
                         }
                         if(tipo_ramo == "colectivo"){
                            $("#certificadodetalle_maritimo").val(response.data.inter.detalle_certificado);

                            $("#sumaaseguradadetalle_maritimo").val(response.data.inter.detalle_suma_asegurada);

                            $("#primadetalle_maritimo").val(response.data.inter.detalle_prima);

                            $("#deducibledetalle_maritimo").val(response.data.inter.detalle_deducible);

                        }

                    } else if (tipoint == 5) {

                        $(".uuid").val(response.data.inter.uuid_intereses);
                        $("#nombrePersona").val(response.data.inter.nombrePersona);


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

                        $("#fecha_nacimiento").val(response.data.inter.fecha_nacimiento);

                        var today = new Date();
                        var format = response.data.inter.fecha_nacimiento.split("-");
                        var dob = new Date(format[0], format[1], format[2]);
                        var diff = (today - dob ) ;
                        var age = Math.floor(diff / 31536000000);
                        $("[id*=edad]").val(age);

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

                        $('#estadoPersona').empty();
                        $('#estadoPersona').append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");

                        $('#idPersona').val(response.data.inter.interesestable_id);


                        $('#correoPersona').val(response.data.inter.correo);


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



                    $('.relaciondetalle_persona_vida_otros').val(response.data.inter.detalle_relacion);
                    $('.relaciondetalle_persona_vida').val(response.data.inter.detalle_relacion);
                    

                        //$('#relaciondetalle_persona').val(response.data.inter.detalle_relacion);
                        //$('#relaciondetalle_persona').attr('disabled',true);
                        response.data.inter.detalle_int_asociado!==0 ? $('#asociadodetalle_persona').val(response.data.inter.detalle_int_asociado).trigger("change"):'';

                        
                        $("#certificadoPersona").val(response.data.inter.detalle_certificado);

                        $("#beneficiodetalle_persona").val(response.data.inter.detalle_beneficio);
                        
                        $('#suma_asegurada_persona').val(response.data.inter.detalle_suma_asegurada);
                        
                        $('#participacion_persona').val(response.data.inter.detalle_participacion);
                        
                        $("#montodetalle_persona").val(response.data.inter.detalle_monto);
                        
                        $("#primadetalle_persona").val(response.data.inter.detalle_prima);
                        
                        //disabled fields
                        if(window.vista!=="renovar"){
                            $("#identificacion").attr('disabled',true);
                            $("#pasaporte").attr('disabled',true);
                            $("#provincia").attr('disabled',true);
                            $("#letra").attr('disabled',true);
                            $("#tomo").attr('disabled',true);
                            $("#asiento").attr('disabled',true);
                            $('.noPAS').attr('disabled',true);
                            $(".PAS").attr('disabled',true);
                        // validacion
                        $("#nombrePersona").attr('disabled',true);
                        $("#fecha_nacimiento").attr('disabled',true);
                        $("#primadetalle_persona").attr('disabled',true);
                        $("#montodetalle_persona").attr('disabled',true);
                        $('#participacion_persona').attr('disabled',true);
                        $('#suma_asegurada_persona').attr('disabled',true);
                        $("#beneficiodetalle_persona").attr('disabled',true);
                        $("#certificadoPersona").attr('disabled',true);
                        $('#asociadodetalle_persona').attr('disabled',true);
                        $('.relaciondetalle_persona_vida').attr('disabled',true);
                        $('#telefono_residencial_check').attr('disabled',true);
                        $('#telefono_oficina_check').attr('disabled',true);
                        $('#direccion_residencial_check').attr('disabled',true);
                        $('#direccion_laboral_check').attr('disabled',true);
                        $('#correoPersona').attr('disabled',true);
                        $("#edad").attr('disabled',true);
                        $('#estado_civil').attr('disabled',true);
                        $('#idPersona').attr('disabled',true);
                        $('#estadoPersona').attr('disabled',true);
                        $('#observacionesPersona').attr('disabled',true);
                        $('#nacionalidad').attr('disabled',true);
                        $('#sexo').attr('disabled',true);
                        $('#estatura').attr('disabled',true);
                        $('#peso').attr('disabled',true);
                        $('#telefono_residencial').attr('disabled',true);
                        $('#telefono_oficina').attr('disabled',true);
                        $('#direccion').attr('disabled',true);
                        $('#direccion_laboral').attr('disabled',true);
                        $('.relaciondetalle_persona_vida_otros').attr('disabled',true);
                    }
                } else if (tipoint == 6) {
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

                    $("#asignado_acreedor").val(response.data.inter.asignado_acreedor);

                    $("#ubicacion_proyecto").val(response.data.inter.ubicacion);

                    $("#acreedor_opcional").val(response.data.inter.acreedor_opcional);

                    $("#validez_fianza_opcional").val(response.data.inter.validez_fianza_opcional);

                    $("#observaciones_proyecto").val(response.data.inter.observaciones);

                            $(".tipo_fianza").val(response.data.inter.tipo_fianza); // option[value='" + response.data.inter.tipo_fianza + "']
                            
                            $(".tipo_propuesta").val(response.data.inter.tipo_propuesta); //option[value='" + response.data.inter.tipo_propuesta + "']
                            
                            $(".acreedor_proyecto").val(response.data.inter.acreedor); //option[value='" + response.data.inter.acreedor + "']
                            
                            $(".validez_fianza_pr").val(response.data.inter.validez_fianza_pr); //option[value='" + response.data.inter.validez_fianza_pr + "']

                            $(".estado_proyecto").empty();
                            $(".estado_proyecto").append("<option value="+response.data.inter.estado+">"+response.data.inter.estado+"</option>");
                            

                            if(tipo_ramo == "colectivo"){
                                $("#certificadodetalle_proyecto").val(response.data.inter.detalle_certificado);

                                $("#sumaaseguradadetalle_proyecto").val(response.data.inter.detalle_suma_asegurada);
                                
                                $("#primadetalle_proyecto").val(response.data.inter.detalle_prima);
                                
                                $("#deducibledetalle_proyecto").val(response.data.inter.detalle_deducible);
                                


                            }
                            //disabled fields
                            if(window.vista!=="renovar"){
                                $("#nombre_proyecto").attr("disabled",true);
                                $("#contratista_proyecto").attr("disabled",true);
                                $(".estado_proyecto").attr("disabled",true);
                                $(".validez_fianza_pr").attr("disabled",true);
                                $(".acreedor_proyecto").attr("disabled",true);
                                $(".tipo_propuesta").attr("disabled",true);
                                $(".tipo_fianza").attr("disabled",true);
                                $("#observaciones_proyecto").attr("disabled",true);
                                $("#validez_fianza_opcional").attr("disabled",true);
                                $("#acreedor_opcional").attr("disabled",true);
                                $("#ubicacion_proyecto").attr("disabled",true);
                                $("#asignado_acreedor").attr("disabled",true);
                                $("#monto_afianzado").attr("disabled",true);
                                $(".monto_proyecto").attr("disabled",true);
                                $("#representante_legal_proyecto").attr("disabled",true);
                                $(".fecha_proyecto").attr("disabled",true);
                                $(".no_ordenr").attr("disabled",true);
                                $("#no_orden_proyecto").attr("disabled",true);
                                $("#fecha_concurso").attr("disabled",true);

                           //disabled fields 
                           $("#duracion_proyecto").attr("disabled",true);
                           $("#deducibledetalle_proyecto").attr('disabled',true);
                           $("#certificadodetalle_proyecto").attr('disabled',true);
                           $("#primadetalle_proyecto").attr('disabled',true);
                           $("#sumaaseguradadetalle_proyecto").attr('disabled',true);
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


                            $("#acreedor_ubicacion").val(response.data.inter.acreedor);  //option[value='" + response.data.inter.acreedor + "']
                            
                            $(".estado_ubicacion").empty();
                            $(".estado_ubicacion").append("<option value="+response.data.inter.estado+">"+response.data.inter.estado+"</option>");
                            


                            var acreedor_ubicacion = $('.acreedor_ubicacion').val();
                            if (acreedor_ubicacion === 'otro') {
                                $('#acreedor_ubicacion_opcional').val(response.data.inter.acreedor_opcional);
                                $('#acreedor_ubicacion_opcional').attr('disabled',true);
                            }else{
                              $('#acreedor_ubicacion_opcional').attr('disabled',true);
                          }

                          if(tipo_ramo == "colectivo"){
                            $("#certificadodetalle_ubicacion").val(response.data.inter.detalle_certificado);
                            
                            $("#sumaaseguradadetalle_ubicacion").val(response.data.inter.detalle_suma_asegurada);
                            
                            $("#primadetalle_ubicacion").val(response.data.inter.detalle_prima);
                            
                            $("#deducibledetalle_ubicacion").val(response.data.inter.detalle_deducible);
                            
                        }

                         //disabled fields 
                         if(window.vista!=="renovar"){
                             $(".estado_ubicacion").attr('disabled',true);
                             $("#acreedor_ubicacion").attr('disabled',true);
                             $("#observaciones_ubicacion").attr('disabled',true);
                             $("#porcentaje_acreedor_ubicacion").attr('disabled',true);
                             $("#acreedor_ubicacion_opcional").attr('disabled',true);
                             $("#inventario").attr('disabled',true);
                             $("#maquinaria").attr('disabled',true);
                             $("#contenido").attr('disabled',true);
                             $("#edif_mejoras").attr('disabled',true);
                             $(".serier").attr('disabled',true);
                             $("#direccion_ubicacion").attr('disabled',true);
                             $("#nombre_ubicacion").attr('disabled',true);
                             $("#certificadodetalle_ubicacion").attr('disabled',true);
                             $("#sumaaseguradadetalle_ubicacion").attr('disabled',true);
                             $("#primadetalle_ubicacion").attr('disabled',true);
                             $("#deducibledetalle_ubicacion").attr('disabled',true);
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

                            $("#uso").val(response.data.inter.uso); //option[value='" + response.data.inter.uso + "']

                            $(".condicion_vehiculo").val(response.data.inter.condicion); //option[value='" + response.data.inter.condicion + "']

                            $(".acreedor").val(response.data.inter.acreedor); //option[value='" + response.data.inter.acreedor + "']
                            
                            $(".estado").empty();
                            $(".estado").append("<option value='"+response.data.inter.estado+"'>"+response.data.inter.estado+"</option>");


                            if(tipo_ramo == "colectivo"){
                                $("#certificadodetalle_vehiculo").val(response.data.inter.detalle_certificado);
                                
                                $("#sumaaseguradadetalle_vehiculo").val(response.data.inter.detalle_suma_asegurada);
                                
                                $("#primadetalle_vehiculo").val(response.data.inter.detalle_prima);

                                $("#deducibledetalle_vehiculo").val(response.data.inter.detalle_deducible);
                                
                            }
                             //disabled field
                             if(window.vista!=="renovar"){
                                 $("#chasis").attr('disabled',true);
                                 $("#unidad").attr('disabled',true); 
                                 $("#deducibledetalle_vehiculo").attr('disabled',true);
                                 $("#primadetalle_vehiculo").attr('disabled',true);
                                 $("#sumaaseguradadetalle_vehiculo").attr('disabled',true);
                                 $("#certificadodetalle_vehiculo").attr('disabled',true);
                                 $(".estado").attr('disabled',true);
                                 $(".acreedor").attr('disabled',true);
                                 $(".condicion_vehiculo").attr('disabled',true);
                                 $("#observaciones_vehiculo").attr('disabled',true);
                                 $(".porcentaje_vehiculo").attr('disabled',true);
                                 $("#uso").attr('disabled',true);
                                 $("#valor_extras").attr('disabled',true);
                                 $("#extras").attr('disabled',true);
                                 $("#operador").attr('disabled',true);
                                 $("#capacidad").attr('disabled',true);
                                 $("#color").attr('disabled',true);
                                 $("#motor").attr('disabled',true);
                                 $("#ano").attr('disabled',true); 
                                 $(".modelo_vehiculo").attr('disabled',true);
                                 $("#placa").attr('disabled',true);
                                 $(".marca_vehiculo").attr('disabled',true); 
                             } 
                         } 
                     }
                 });
}
else{
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
 //$("input:checkbox").prop('checked', false);
}
},
}
});


formularioCrear.getIntereses();
var setting={};
var colectivePeople=['vida','salud','accidente','accidentes'],
valid=0,
sendIdividualForm,
setting={},
tablaTipo2;

function isColective(data){

    for (var i = colectivePeople.length - 1; i >= 0; i--) {

        var myWord = colectivePeople[i];
        if(new RegExp('\\b'+ myWord + '\\b',"gi").test(data) ){ // 
            valid++;
            tablaTipo2 = myWord;
        }
    }
}

isColective(ramo);


if (window.vista == "crear") {
    var counter_acre = 2;
    var counter_acre2 = 2;
    $('.del_file_acreedores_adicionales').hide();
}else if (window.vista == "editar") {
    //console.log(contacre);
    //var counter_acre = contacre+2;
    //var counter_acre2 = contacre+2;
    var counter_acre = 2;
    var counter_acre2 = 2;
    $('#del_acre').hide();
}


$(document).ready(function () {

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
    populateStoredCovergeData('indCoveragefields','coverage','removecoverage',coberturas,"cobertura","valor_cobertura");
    populateStoredCovergeData('indDeductiblefields','deductible','removeDeductible',deducciones,"deduccion","valor_deduccion");
    var buttonClass = setButtonClickListener();
    $("."+buttonClass).click(function(){
        done =formularioCrear.individualInterest();
        if(!done){
         window.location.href = "#divintereses";

     }
 });

    $(".select2").select2();
    var URL =window.location.href.split("/");
    var urlLastSegment= URL.pop();
    if(window.vista==="renovar"){

     var uuidPolicy = URL.pop();
     $(".coverage").removeAttr(false);
     $(".deductible").removeAttr(false);  
     $('#formPolizasCrear').submit(function(e){
        return false;
    });
     formularioCrear.renovationModal(uuidPolicy);
     $("span.switchery-default").remove();
     var elem = document.querySelector('#polizaDeclarativa');
     var init = new Switchery(elem);
     init.enable(); 
     if(tipo_ramo =="individual"){
        $("#poliza_suma_asegurada").prop("disabled",false);
    }

}else{
 $(".botones").remove();
 $(".renewal").remove();
 $('.detail_endoso').remove();
}
if (estado_pol=="Por Facturar"){
    var estado=$("#estado_poliza").val();
    formularioCrear.enablePayFields();
}
$(".moneda").inputmask('currency',{
  prefix: "",
  autoUnmask : true,
  removeMaskOnSubmit: true
}); 
$('.datepicker').datepicker({

    maxDate: '+0d',
    endDate: '2010-12-31',
    changeMonth: true,
    changeYear: true
});
$('.datepicker2').datepicker({

    changeMonth: true,
    changeYear: true
});

if(tablaTipo2 == "vida" || tablaTipo2 == "accidentes" || tablaTipo2 == "accidente"){
    console.log(tablaTipo2);  
    $('.salud').hide();
}else if(tablaTipo2=="salud"){
    console.log(tablaTipo2); 
    $('.vida').hide();
}else{
    console.log(tablaTipo2); 
    $('.persona').hide();
    $('.salud').hide();   
}


if( tablaTipo2 == 'vida' || tablaTipo2 == "accidentes" || tablaTipo2 == "accidente"){

        if(tablaTipo == 'vida'){ //id_tipo_poliza == 1 &&

            $(".relaciondetalle_persona_vida_otros").addClass('hidden');
            $(".relaciondetalle_persona_vida_otros").attr('disabled',true);
            $(".relaciondetalle_persona_vida").removeClass('hidden');
            $(".relaciondetalle_persona_vida").attr('disabled',false);
            $("#suma_asegurada_persona").attr("data-rule-required", "false").attr('disabled',true);
        }

        setting = {
            nacionalidad:true,
            relacion:false,
            participacion:false,
            estatura:true,
            peso:true,
            treeview:true
        };
    }else if(tablaTipo2=='salud'){
        setting = {
            nacionalidad:false,
            relacion:false,
            participacion:true,
            estatura:true,
            peso:true,
            treeview:true
        }; 
    }else{
        setting = {
            nacionalidad:true,
            relacion:true,
            participacion:true,
            estatura:false,
            peso:false,
            treeview:false
        }; 
    }

    $(".campodesde").val(desde);
    $(".documentos_entregados").remove();
    //$("#articulo, #formCarga, #formcasco_aereo, #formCasco_maritimo, #persona, #formProyecto_actividad, #formUbicacion, #vehiculo").attr('action', ''+window.location.href+'');
    if(tipo_ramo == "individual"){

        $(" #articuloTab, #cargaTab, #casco_aereoTab, #casco_maritimoTab , #personaTab , #proyecto_actividadTab , #ubicacionTab , #vehiculoTab").css("margin-bottom","-31px");

        if(RegExp('\\bvida\\b',"gi").test(nombre_ramo) || RegExp('\\bsalud\\b',"gi").test(nombre_ramo) || RegExp('\\baccidente\\b',"gi").test(nombre_ramo) || RegExp('\\baccidentes\\b',"gi").test(nombre_ramo) ){
            $(".detalleinteres_persona").show();
            $(".tabladetalle_personas").show();   
        }else{
         $(".botones").remove(); 
     }



 }else if(tipo_ramo == "colectivo"){

    $(" .detalleinteres_articulo, .detalleinteres_carga, .detalleinteres_aereo, .detalleinteres_maritimo, .detalleinteres_proyecto, .detalleinteres_ubicacion, .detalleinteres_vehiculo").show();
    $(" .tabladetalle_articulo, .tabladetalle_carga, .tabladetalle_aereo, .tabladetalle_maritimo, .tabladetalle_proyecto, .tabladetalle_ubicacion, .tabladetalle_vehiculo").show();

        //if(RegExp('\\bvida\\b',"gi").test(nombre_ramo) || RegExp('\\bsalud\\b',"gi").test(nombre_ramo) || RegExp('\\baccidente\\b',"gi").test(nombre_ramo) || RegExp('\\baccidentes\\b',"gi").test(nombre_ramo) ){
            $(".detalleinteres_persona").show();   
        //}
        $(".tabladetalle_personas").show();
    }

    if(estado_pol == "Facturada"){
        $('#estado_poliza').attr('disabled',true);
        $('#guardar_poliza').attr('disabled',true);
    }



    var stickyNavTop = $('.tab-principal').offset().top;

    var stickyNav = function(){
        var scrollTop = $(window).scrollTop();
        
        if (scrollTop > stickyNavTop) { 
          $('.tab-principal').addClass('sticky');
      } else {
          $('.tab-principal').removeClass('sticky'); 
      }
  };

  stickyNav();

  $(window).scroll(function() {
    stickyNav();

    if(poliza_declarativa == 'no'){
        $('#id_tab_endoso').addClass('hidden');
    }


});

  formularioCrear.getAsociado();

  if (id_tipo_poliza == 2) {
    $("#poliza_prima_anual").attr("readonly", "readonly");
}


    //----------------------------------------------------------------------
    //Inicializa Parametros para acreedores
    //----------------------------------------------------------------------
    $(".monto_cesion_acreedor").inputmask('currency',{ 
        prefix: "", 
        autoUnmask : true, 
        removeMaskOnSubmit: true 
    });

    $(".porcentaje_cesion_acreedor").inputmask('Regex', { regex: "^[1-9][0-9][.][0-9][0-9]?$|^100[.]00$" });
    //$(".porcentaje_cesion_acreedor").inputmask('decimal',{min:0, max:100});

    $(".monto_cesion_acreedor").keyup(function(){
        var id = $(this).attr("id");
        var x = id.split('_');
        var monto = $("#montocesion_"+x[1]).val();

        var sumaasegurada = $("#suma_asegurada").val();
        if (sumaasegurada == "") { sumaasegurada = 0;}
        var porcentaje = (monto * 100 )/(sumaasegurada);
        console.log(porcentaje);
        $("#porcentajecesion_"+x[1]).val(porcentaje);
    });

    $(".porcentaje_cesion_acreedor").keyup(function(){
        var id = $(this).attr("id");
        var x = id.split('_');
        var porcentaje = $("#porcentajecesion_"+x[1]).val();
        if (porcentaje == "") { porcentaje = 0;}
        var sumaasegurada = $("#suma_asegurada").val();
        var monto = (porcentaje * sumaasegurada )/(100);
        console.log(monto);
        if (porcentaje>100) {
            $("#montocesion_"+x[1]).val(sumaasegurada);
        }else{
            $("#montocesion_"+x[1]).val(monto);
        }            
    });
    //----------------------------------------------------------------------
    //Fin Inicializacion Parametros para acreedores
    //----------------------------------------------------------------------

    /*if (validavida == 1) {
        if (id_tipo_poliza == 1) {
            $("#vigencia_vida_individual").show();
            $("#vigencia_vida_colectivo").remove();
        }else{
            $("#vigencia_vida_colectivo").show();
            $("#vigencia_vida_individual").remove();
        }
    }else{
        $("#vigencia_vida_individual").remove();
    }*/

    //----------------------------------------------------------------------

});

function agregaracre(){
    $(".add_file_acreedores_adicionales").hide();
    $(".del_file_acreedores_adicionales").show();
    $('#agrega_acre').before('<div class="row" id="a' + counter_acre2 + '"><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px"><input type="text" name="campoacreedores[]" id="acreedor_'+counter_acre2+'" class="form-control"></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">%</span> <input type="text" name="campoacreedores_por[]" id="porcentajecesion_'+counter_acre2+'" class="form-control porcentaje_cesion_acreedor" value="0"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">$</span> <input type="text" name="campoacreedores_mon[]" id="montocesion_'+counter_acre2+'" value="0" class="form-control monto_cesion_acreedor"></div></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><button type="button" data="'+counter_acre2+'" class="btn btn-default btn-block add_file_acreedores_adicionales" onclick="agregaracre()" style="float: left; width: 40px; margin-right:5px;" ><i class="fa fa-plus"></i></button><button type="button" data="'+counter_acre2+'" onclick="eliminaracre('+counter_acre2+')" style="float: left; width: 40px; margin-top:0px!important; display: none" class="btn btn-default btn-block del_file_acreedores_adicionales"><i class="fa fa-trash"></i></button></div></div>');
        //$('#del_file_acreedores_adicionales').fadeIn(0);
        //-----------------------------------------------------
        $(".monto_cesion_acreedor").inputmask('currency',{ 
            prefix: "", 
            autoUnmask : true, 
            removeMaskOnSubmit: true 
        });
        $(".porcentaje_cesion_acreedor").inputmask('Regex', { regex: "^[1-9][0-9][.][0-9][0-9]?$|^100[.]00$" });
        //$(".porcentaje_cesion_acreedor").inputmask('decimal',{min:0, max:100});

        $(".monto_cesion_acreedor").keyup(function(){
            var id = $(this).attr("id");
            var x = id.split('_');
            var monto = $("#montocesion_"+x[1]).val();
            var sumaasegurada = $("#suma_asegurada").val();
            if (sumaasegurada == "") { sumaasegurada = 0;}
            var porcentaje = (monto * 100 )/(sumaasegurada);
            console.log(porcentaje);
            $("#porcentajecesion_"+x[1]).val(porcentaje);
        });

        $(".porcentaje_cesion_acreedor").keyup(function(){
            var id = $(this).attr("id");
            var x = id.split('_');
            var porcentaje = $("#porcentajecesion_"+x[1]).val();
            if (porcentaje == "") { porcentaje = 0;}
            var sumaasegurada = $("#suma_asegurada").val();
            var monto = (porcentaje * sumaasegurada )/(100);
            console.log(monto);
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
  value.nombre = inputValue === undefined ? "" : inputValue.nombre;
  value.monetario = inputValue === undefined ? "" : inputValue.monetario;
  enabled = window.vista === "renovar" ? "" : "disabled";
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

        });

    }

}
function setPlanValues(){
    planesCoberturasDeducibles =[];
    
    if (customModalValidation(validateFields) === 0) {
        planesCoberturasDeducibles.push({
            'coberturas': {nombre: $("input[name='coverageName[]']").map(function () {
                return $(this).val();
            }).get(),
            valor: $("input[name='coverageValue[]']").map(function () {
                return $(this).val();
            }).get()
        },
        'deducibles': {nombre: $("input[name='deductibleName[]']").map(function () {
            return $(this).val();
        }).get(),
        valor: $("input[name='deductibleValue[]']").map(function () {
            return $(this).val();
        }).get()
    }

});
        $("#planesCoberturasDeducibles").val(JSON.stringify(planesCoberturasDeducibles[0]));
        $("#verCoberturas").modal("hide");

    } 

}

function resetModalInputs(){

    clearFields(validateFields);
    $(".error").remove();
    $("#planesCoberturasDeducibles").val("");
    $("#verCoberturas").modal("hide");

}

function setButtonClickListener(){

    var formButtonIdArray=[ 

    { id:1 ,formIdName:"guardarArticulo"},
    { id:2 ,formIdName:"guardarCarga"},
    { id:3 ,formIdName:"guardarAereo"},
    { id:4 ,formIdName:"guardarMaritimo"},
    { id:5 ,formIdName:"guardarPersona"},
    { id:6 ,formIdName:"guardarProyecto"},
    { id:7 ,formIdName:"guardarUbicacion"},
    { id:8 ,formIdName:"guardarVehiculo"}];    

    for (var i = formButtonIdArray.length - 1; i >= 0; i--) {
     var  value= formButtonIdArray[i];

     if(window.id_tipo_int_asegurado ==value.id){
         return value.formIdName;
     }
 }        
}
