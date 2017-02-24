Vue.http.options.emulateJSON = true;
var opcionesModal = $('#verCoberturas');
var counterCoverage= 1;
var formularioCrear = new Vue({
	el: ".wrapper-content",
	data:{
		comboEstado: estado_solicitud,
		estado_pol: estado_pol,
		polizaCliente: cliente,
		polizaAseguradora: aseguradora,
		polizaPlan: plan,
		polizaComision: comision,
		polizaVigencia: vigencia,
		polizaPrima: prima,
        polizaGrupo : grupo,
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
        catalogoCentroFacturacion:centrosFacturacion

    },
    methods: {
		/*nombrePlan:function(){
		this.getPlanesInfo();
  },*/
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
        renovationModal: function (idPolicy) {

            this.$http.post({
                url: phost() + 'polizas/getRenovationData',
                method:'POST',
                data:{idPoliza:idPolicy,erptkn: tkn}
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
                
                //  this.$set('isEditable',response.data.isEditable);
                // this.$set('idPolicy',idPolicy);
                this.$set('comision',response.data.comision);

                this.$set('disabledComision',permiso_comision);
                
                
                this.$set('disabledAgente',!permiso_agente);
                

                this.$set('disableParticipacion',!permiso_participacion);

                $(".detail").remove();
                $(".detail_endoso").remove();
                $("#renovar").prop("hidden",false);
                
                
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
        },

        getIntereses: function () {
            //polula el segundo select del header
            var self = this;
            var interes = $('#formulario').val();
            var id_poliza = $('#idPoliza').val();

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
                        url: phost() + 'polizas/ajax_get_tipointereses',
                        method: 'POST',
                        data: {interes: interes, id_poliza : id_poliza ,erptkn: tkn}
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
                    self.$set('clienteCentro', response.data[0].direccion);
                }
            });

        },
        getInteres: function () {
            //polula el segundo select del header
            var self = this;
            var interes = $('#selInteres').val();
            console.log(interes);
            var tipointeres = $('#formulario').val();
            console.log(tipointeres);
            if (interes != "") {
                this.$http.post({
                    url: phost() + 'polizas/ajax_get_intereses',
                    async: false,
                    method: 'POST',
                    data: {interes: interes, tipointeres: tipointeres, erptkn: tkn}
                }).then(function (response) {
                    if (_.has(response.data, 'session')) {
                        window.location.assign(phost());
                    }
                    if (!_.isEmpty(response.data)) {
                        console.log(response.data.inter);
                        var tipoint = response.data.inter.tipointeres;
                        if (tipoint == 1) {
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

                            if(tipo_ramo == "colectivo"){
                                $("#certificadodetalle_articulo").val(response.data.inter.detalle_certificado);
                                $("#certificadodetalle_articulo").attr('disabled',true);
                                $("#sumaaseguradadetalle_articulo").val(response.data.inter.detalle_suma_asegurada);
                                $("#sumaaseguradadetalle_articulo").attr('disabled',true);
                                $("#primadetalle_articulo").val(response.data.inter.detalle_prima);
                                $("#primadetalle_articulo").attr('disabled',true);
                                $("#deducibledetalle_articulo").val(response.data.inter.detalle_deducible);
                                $("#deducibledetalle_articulo").attr('disabled',true);
                            }
                            
                        } else if (tipoint == 2) {

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

                            if(tipo_ramo == "colectivo"){
                                $("#certificadodetalle_carga").val(response.data.inter.detalle_certificado);
                                $("#certificadodetalle_carga").attr('disabled',true);
                                $("#sumaaseguradadetalle_carga").val(response.data.inter.detalle_suma_asegurada);
                                $("#sumaaseguradadetalle_carga").attr('disabled',true);
                                $("#primadetalle_carga").val(response.data.inter.detalle_prima);
                                $("#primadetalle_carga").attr('disabled',true);
                                $("#deducibledetalle_carga").val(response.data.inter.detalle_deducible);
                                $("#deducibledetalle_carga").attr('disabled',true);
                            }
                            
                        } else if (tipoint == 3) {

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

                            if(tipo_ramo == "colectivo"){
                                $("#certificadodetalle_aereo").val(response.data.inter.detalle_certificado);
                                $("#certificadodetalle_aereo").attr('disabled',true);
                                $("#sumaaseguradadetalle_aereo").val(response.data.inter.detalle_suma_asegurada);
                                $("#sumaaseguradadetalle_aereo").attr('disabled',true);
                                $("#primadetalle_aereo").val(response.data.inter.detalle_prima);
                                $("#primadetalle_aereo").attr('disabled',true);
                                $("#deducibledetalle_aereo").val(response.data.inter.detalle_deducible);
                                $("#deducibledetalle_aereo").attr('disabled',true);
                            }
                            
                        } else if (tipoint == 4) {

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

                            if(tipo_ramo == "colectivo"){
                                $("#certificadodetalle_maritimo").val(response.data.inter.detalle_certificado);
                                $("#certificadodetalle_maritimo").attr('disabled',true);
                                $("#sumaaseguradadetalle_maritimo").val(response.data.inter.detalle_suma_asegurada);
                                $("#sumaaseguradadetalle_maritimo").attr('disabled',true);
                                $("#primadetalle_maritimo").val(response.data.inter.detalle_prima);
                                $("#primadetalle_maritimo").attr('disabled',true);
                                $("#deducibledetalle_maritimo").val(response.data.inter.detalle_deducible);
                                $("#deducibledetalle_maritimo").attr('disabled',true);
                            }
                            
                        } else if (tipoint == 5) {

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

                        $('.relaciondetalle_persona_vida_otros').val(response.data.inter.detalle_relacion);
                        $('.relaciondetalle_persona_vida').val(response.data.inter.detalle_relacion);
                        $('.relaciondetalle_persona_vida_otros').attr('disabled',true);
                        $('.relaciondetalle_persona_vida').attr('disabled',true);
                        //$('#relaciondetalle_persona').val(response.data.inter.detalle_relacion);
                        //$('#relaciondetalle_persona').attr('disabled',true);
                        response.data.inter.detalle_int_asociado!==0 ? $('#asociadodetalle_persona').val(response.data.inter.detalle_int_asociado).trigger("change"):'';

                        $('#asociadodetalle_persona').attr('disabled',true);
                        $("#certificadoPersona").val(response.data.inter.detalle_certificado);
                        $("#certificadoPersona").attr('disabled',true);
                        $("#beneficiodetalle_persona").val(response.data.inter.detalle_beneficio);
                        $("#beneficiodetalle_persona").attr('disabled',true);
                        $('#suma_asegurada_persona').val(response.data.inter.detalle_suma_asegurada);
                        $('#suma_asegurada_persona').attr('disabled',true);
                        $('#participacion_persona').val(response.data.inter.detalle_participacion);
                        $('#participacion_persona').attr('disabled',true);
                        $("#montodetalle_persona").val(response.data.inter.detalle_monto);
                        $("#montodetalle_persona").attr('disabled',true);
                        $("#primadetalle_persona").val(response.data.inter.detalle_prima);
                        $("#primadetalle_persona").attr('disabled',true);

                    } else if (tipoint == 6) {
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

                            if(tipo_ramo == "colectivo"){
                                $("#certificadodetalle_proyecto").val(response.data.inter.detalle_certificado);
                                $("#certificadodetalle_proyecto").attr('disabled',true);
                                $("#sumaaseguradadetalle_proyecto").val(response.data.inter.detalle_suma_asegurada);
                                $("#sumaaseguradadetalle_proyecto").attr('disabled',true);
                                $("#primadetalle_proyecto").val(response.data.inter.detalle_prima);
                                $("#primadetalle_proyecto").attr('disabled',true);
                                $("#deducibledetalle_proyecto").val(response.data.inter.detalle_deducible);
                                $("#deducibledetalle_proyecto").attr('disabled',true);
                            }

                        } else if (tipoint == 7) {
                        	
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

                          if(tipo_ramo == "colectivo"){
                            $("#certificadodetalle_ubicacion").val(response.data.inter.detalle_certificado);
                            $("#certificadodetalle_ubicacion").attr('disabled',true);
                            $("#sumaaseguradadetalle_ubicacion").val(response.data.inter.detalle_suma_asegurada);
                            $("#sumaaseguradadetalle_ubicacion").attr('disabled',true);
                            $("#primadetalle_ubicacion").val(response.data.inter.detalle_prima);
                            $("#primadetalle_ubicacion").attr('disabled',true);
                            $("#deducibledetalle_ubicacion").val(response.data.inter.detalle_deducible);
                            $("#deducibledetalle_ubicacion").attr('disabled',true);
                        }

                    } else if (tipoint == 8) {

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

                            if(tipo_ramo == "colectivo"){
                                $("#certificadodetalle_vehiculo").val(response.data.inter.detalle_certificado);
                                $("#certificadodetalle_vehiculo").attr('disabled',true);
                                $("#sumaaseguradadetalle_vehiculo").val(response.data.inter.detalle_suma_asegurada);
                                $("#sumaaseguradadetalle_vehiculo").attr('disabled',true);
                                $("#primadetalle_vehiculo").val(response.data.inter.detalle_prima);
                                $("#primadetalle_vehiculo").attr('disabled',true);
                                $("#deducibledetalle_vehiculo").val(response.data.inter.detalle_deducible);
                                $("#deducibledetalle_vehiculo").attr('disabled',true);
                            }

                        } 
                    }
                });
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

    var URL =window.location.href.split("/");
    var urlLastSegment= URL.pop();
    
    
    if(urlLastSegment==="renovar"){
     var uuidPolicy = URL.pop();  
     $('#formPolizasCrear').submit(function(e){
        return false;
    });
     formularioCrear.renovationModal(uuidPolicy);
 }else{
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
$(".botones").remove();
$(".documentos_entregados").remove();
    //$("#articulo, #formCarga, #formcasco_aereo, #formCasco_maritimo, #persona, #formProyecto_actividad, #formUbicacion, #vehiculo").attr('action', ''+window.location.href+'');
    if(tipo_ramo == "individual"){

      $(" #articuloTab, #cargaTab, #casco_aereoTab, #casco_maritimoTab , #personaTab , #proyecto_actividadTab , #ubicacionTab , #vehiculoTab").css("margin-bottom","-31px");

      if(RegExp('\\bvida\\b',"gi").test(nombre_ramo) || RegExp('\\bsalud\\b',"gi").test(nombre_ramo) || RegExp('\\baccidente\\b',"gi").test(nombre_ramo) || RegExp('\\baccidentes\\b',"gi").test(nombre_ramo) ){
        $(".detalleinteres_persona").show();   
    }
    $(".tabladetalle_personas").show();
    

}else if(tipo_ramo == "colectivo"){

    $(" .detalleinteres_articulo, .detalleinteres_carga, .detalleinteres_aereo, .detalleinteres_maritimo, .detalleinteres_proyecto, .detalleinteres_ubicacion, .detalleinteres_vehiculo").show();
    $(" .tabladetalle_articulo, .tabladetalle_carga, .tabladetalle_aereo, .tabladetalle_maritimo, .tabladetalle_proyecto, .tabladetalle_ubicacion, .tabladetalle_vehiculo").show();

    if(RegExp('\\bvida\\b',"gi").test(nombre_ramo) || RegExp('\\bsalud\\b',"gi").test(nombre_ramo) || RegExp('\\baccidente\\b',"gi").test(nombre_ramo) || RegExp('\\baccidentes\\b',"gi").test(nombre_ramo) ){
        $(".detalleinteres_persona").show();   
    }
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


});


function DrawCoverageInModal(id,btnAdd,stringId,del_row){

 var wrapper = $("#"+id); 
 $("#"+btnAdd).unbind().click(function(e){
    e.preventDefault();
    parameters = "'"+id+"','"+del_row+"','"+stringId+"'";
    var text = '<div class="resetModal" id="'+stringId+'_'+ counterCoverage+'"><div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"> <input type="text" name="'+stringId+'Name[]" class="form-control"></div>'+'<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="'+stringId+'Value[]" class="form-control moneda"  value=""></div></div>'+'<div class="col-xs-12 col-sm-3 col-md-3 col-lg-1 '+del_row+'" onclick="deleteFieldsInCoverageModal('+parameters+')"><button class="btn btn-default btn-block "><i class="fa fa-trash"></i></button></div></div>';
    $(wrapper).append(text);
    counterCoverage++;  
});
}

function deleteFieldsInCoverageModal(id,del_row,idToRemove){
    var wrapper = $("#"+id);
    var removeStringClass = '.'+del_row+'';
    $(wrapper).unbind().on("click",removeStringClass, function(e){ //user click on remove text
        e.preventDefault();  
        counterCoverage--; 
        var stringId = "#"+ idToRemove+'_'+ counterCoverage;    
        $(stringId).remove();
    }); 
}