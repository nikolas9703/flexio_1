Vue.http.options.emulateJSON = true;
if(vista == "crear"){
    var ramo = JSON.parse(ramos);   
}
var tmpVar;
var tmpVar2;
var selectActive = 0;
var id_tipo_int_asegurado;
var tablaEndososPersonas;
var id_poliza_endoso;
var tipo_ramo;
var formularioCrearEndosos = new Vue({
	el: ".wrapper-content-1",
	data:{
        ramos: vista == "crear" ? ramo : '',
        clientes: vista == "crear" ? clientes : '',
        poliza: poliza,
        estadosEndosos: estadosEndosos,
        motivos_endosos: motivos_endosos,
        id_cliente: vista == "crear" ? id_cliente : '',
        id_ramo: vista == "crear" ? id_ramo : '',
        id_poliza: id_poliza != "" ? id_poliza : '',
        tipo_endoso: tipo_endoso != "" ? tipo_endoso : '',
        id_motivo: id_motivo != "" ? id_motivo : '',
        modifica_prima: vista == "editar" ? modifica_prima : '',
        estado_endoso: vista == "editar" ? estado_endoso : '',
        id_endoso: vista == "editar" ? endoso_id : '',
        uuid_endoso: vista == "editar" ? uuid_endoso : '',

        clienteNombre: '',
	},
	methods: {
        getPolizas: function(){

            var self = this;
            var id_ramo = $('#id_ramo').val();
            var id_cliente = $('#cliente_id').val();
            var id_poliza = $('#id_poliza').val();

            this.$http.post({
                url: phost() + 'endosos/ajax_get_polizas',
                method: 'POST',
                data: {id_ramo: id_ramo, id_cliente: id_cliente, id_poliza: id_poliza ,erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    if(id_cliente == ""){
                        self.$set('clientes', response.data.clientes);        
                    }
                    if( (id_ramo == "" && id_cliente != '') || (id_ramo == "" && id_cliente == '' ) ){
                        self.$set('ramos', response.data.ramos); 
                             
                    }
                    if(id_poliza == ''){
                        self.$set('poliza', response.data.polizas);
                    }else if(id_poliza != '' && id_ramo == "" && id_cliente == ""){
                        setTimeout(function(){$('#id_ramo').val(response.data.ramos[0].id).trigger('change.select2')},900);
                        setTimeout(function(){$('#cliente_id').val(response.data.clientes[0].id).trigger('change.select2')},900);
                    }
                }else{
                    self.$set('poliza', '');
                }
            });
        },
        getTipoMotivo: function(){
            var self = this;
            var motivo = $('#tipo_endoso').val();
            console.log(motivo);
            this.$http.post({
                url: phost() + 'endosos/ajax_get_motivo',
                method: 'POST',
                data: {motivo: motivo , erptkn: tkn}
            }).then(function(response){
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    self.$set('motivos_endosos', '');
                    self.$set('motivos_endosos', response.data);
                }
            });

        },
        getModificaPrima: function(){
            var selt = this;
            var id_motivo = $('#motivo_endoso').val();
            console.log(id_motivo);
            this.$http.post({
                url: phost() + 'endosos/ajax_get_prima',
                method: 'POST',
                data: {id_motivo: id_motivo , erptkn: tkn}
            }).then(function(response){
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {

                    if(response.data == "si"){
                        $('#modifica_prima_endoso').val('si');
                    }else{
                        $('#modifica_prima_endoso').val('no');
                    }
                }
            });
        },
        getPolizaEndoso: function(){

            var self = this;
            var id_poliza = $('#id_poliza').val();
            var tipo_endoso = $('#tipo_endoso').val();

            if( tipo_endoso != '' && id_poliza != '' ){

                this.$http.post({
                    url: phost() + 'endosos/ajax_get_uuid_poliza',
                    method: 'POST',
                    data: {id_poliza: id_poliza , erptkn: tkn}
                }).then(function(response){
                    if (_.has(response.data, 'session')) {
                        window.location.assign(phost());
                    }
                    if (!_.isEmpty(response.data)) {
                        console.log(response.data.uuid);
                        $('#datosdepoliza-5').css('margin-left','-15px');

                        if(vista == 'crear'){
                            $('#id_tab_polizas').show();
                            $('#id_tab_polizas').addClass('active');
                            $('#tablaPolizas').addClass('active');
                        }
                        
                        this.$http.post({
                            url: phost() + 'endosos/ocultoGetPoliza',
                            method: 'POST',
                            data: {uuid_poliza: response.data.uuid , erptkn: tkn}
                        }).then(function(responseDatos){
                            if (_.has(responseDatos.data, 'session')) {
                                window.location.assign(phost());
                            }
                            if (!_.isEmpty(responseDatos.data)) {
                                console.log(responseDatos.data);

                                formularioCrear.$set('comboEstado',responseDatos.data.estado_solicitud);
                                formularioCrear.$set('estado_pol',responseDatos.data.estado_pol);
                                formularioCrear.$set('polizaCliente',responseDatos.data.cliente);
                                formularioCrear.$set('polizaAseguradora', responseDatos.data.aseguradora);
                                formularioCrear.$set('polizaPlan', responseDatos.data.plan);
                                formularioCrear.$set('polizaCoberturas', responseDatos.data.coberturas);
                                formularioCrear.$set('polizaDeducciones', responseDatos.data.deducciones);
                                formularioCrear.$set('polizaComision', responseDatos.data.comision);
                                formularioCrear.$set('polizaVigencia', responseDatos.data.vigencia);
                                formularioCrear.$set('polizaPrima', responseDatos.data.prima);
                                formularioCrear.$set('polizaCentroFacturacion', responseDatos.data.centroFacturacion);
                                formularioCrear.$set('polizaParticipacion', responseDatos.data.participacion);
                                tipo_ramo = responseDatos.data.tipo_ramo;
                                if(responseDatos.data.agtPrincipal != ''){
                                    formularioCrear.$set('polizaTotalParticipacion', 100.00);
                                }else{
                                    formularioCrear.$set('polizaTotalParticipacion', responseDatos.data.totalParticipacion); 
                                }
                                formularioCrear.$set('pagador', responseDatos.data.pagador);
                                formularioCrear.$set('id_centroContable', responseDatos.data.id_centroContable);
                                formularioCrear.$set('nombre_centroContable', responseDatos.data.nombre_centroContable);
                                formularioCrear.$set('catalogoCantidadPagos', responseDatos.data.cantidadPagos);
                                formularioCrear.$set('catalogoSitioPago', responseDatos.data.sitioPago);
                                formularioCrear.$set('catalogoMetodoPago', responseDatos.data.metodoPago);
                                formularioCrear.$set('catalogoFrecuenciaPagos', responseDatos.data.frecuenciaPagos);
                                formularioCrear.$set('catalogoCentroFacturacion', responseDatos.data.centrosFacturacion);
                                formularioCrear.$set('centrosContables', responseDatos.data.centrosContables);
                                formularioCrear.$set('polizaGrupo', responseDatos.data.grupo);
                                if(responseDatos.data.acreedores != "undefined"){
                                    formularioCrear.$set('acreedores', responseDatos.data.acreedores);
                                }else{
                                    formularioCrear.$set('acreedores', false);
                                }
                                formularioCrear.$set('categoria_poliza', responseDatos.data.categoria_poliza);
                                formularioCrear.$set('primaAnual', responseDatos.data.prima.prima_anual);
                                formularioCrear.$set('impuestoPlan', parseFloat(((parseFloat(responseDatos.data.prima.impuesto)+parseFloat(responseDatos.data.prima.otros)-parseFloat(responseDatos.data.prima.descuentos))*100)/parseFloat(responseDatos.data.prima.prima_anual)));
                                formularioCrear.$set('primaImpuesto', responseDatos.data.prima.impuesto);
                                formularioCrear.$set('primaOtros', responseDatos.data.prima.otros);
                                formularioCrear.$set('primaDescuentos', responseDatos.data.prima.descuentos);
                                formularioCrear.$set('primaTotal', responseDatos.data.prima.total);
                                formularioCrear.$set('agentes', responseDatos.data.agentes);

                                id_tipo_int_asegurado = responseDatos.data.id_tipo_int_asegurado;
                                id_poliza_endoso = responseDatos.data.id_poliza;
                                $('#idPoliza').val(responseDatos.data.id_poliza);
                                $('.tab_intereses_endosos').addClass('active');
                                $('.filtro-formularios-content').addClass('active');

                                if(responseDatos.data.agtPrincipal !="" ){
                                    $('.agentePrincipal').show();
                                    $('#nombreAgentePrincipal').append('<option value="" selected="selected">'+responseDatos.data.agtPrincipal+'</option>');
                                    $('#porcAgentePrincipal').val(parseFloat(responseDatos.data.agtPrincipalporcentaje).toFixed(2));
                                }else{
                                    $('.agentePrincipal').hide();
                                }
                                $('#poliza_agentes_participacion').addClass('moneda');
                                $('#numeroPoliza').remove();
                                $('#renovar').remove();
                                if(responseDatos.data.poliza_declarativa == 'si'){
                                    $("span.switchery-default").remove();
                                    var elem = document.querySelector('#polizaDeclarativa');
                                    var init = new Switchery(elem);
                                    init.enable(); 
                                    $('#polizaDeclarativa').trigger('click'); 
                                }

                                setTimeout(formularioCrear.getIntereses(),2000);                                
                                
                                if(id_tipo_int_asegurado == 1){
                                    $('.endosos_articulo').css('display','block');
                                    $('.endosos_carga').hide();
                                    $('.endosos_casco_aereo').hide();
                                    $('.endosos_casco_maritimo').hide();
                                    $('.endosos_persona').hide();
                                    $('.endosos_proyecto_actividad').hide();
                                    $('.endosos_ubicacion').hide();
                                    $('.endosos_vehiculo').hide();
                                    tablaSolicitudesArticulo.init();
                                }else if(id_tipo_int_asegurado == 2){
                                    $('.endosos_articulo').hide();
                                    $('.endosos_carga').css('display','block');
                                    $('.endosos_casco_aereo').hide();
                                    $('.endosos_casco_maritimo').hide();
                                    $('.endosos_persona').hide();
                                    $('.endosos_proyecto_actividad').hide();
                                    $('.endosos_ubicacion').hide();
                                    $('.endosos_vehiculo').hide();
                                    tablaSolicitudesCarga.init();
                                }else if(id_tipo_int_asegurado == 3){
                                    $('.endosos_articulo').hide();
                                    $('.endosos_carga').hide();
                                    $('.endosos_casco_aereo').css('display','block');
                                    $('.endosos_casco_maritimo').hide();
                                    $('.endosos_persona').hide();
                                    $('.endosos_proyecto_actividad').hide();
                                    $('.endosos_ubicacion').hide();
                                    $('.endosos_vehiculo').hide();
                                    tablaSolicitudesAereo.init();
                                }else if(id_tipo_int_asegurado == 4){
                                    $('.endosos_articulo').hide();
                                    $('.endosos_carga').hide();
                                    $('.endosos_casco_aereo').hide();
                                    $('.endosos_casco_maritimo').css('display','block');
                                    $('.endosos_persona').hide();
                                    $('.endosos_proyecto_actividad').hide();
                                    $('.endosos_ubicacion').hide();
                                    $('.endosos_vehiculo').hide();
                                    tablaSolicitudesMaritimo.init();
                                }else if(id_tipo_int_asegurado == 5){
                                    $('.endosos_articulo').hide();
                                    $('.endosos_carga').hide();
                                    $('.endosos_casco_aereo').hide();
                                    $('.endosos_casco_maritimo').hide();
                                    $('.endosos_persona').css('display','block');
                                    $('.endosos_proyecto_actividad').hide();
                                    $('.endosos_ubicacion').hide();
                                    $('.endosos_vehiculo').hide();
                                    tablaSolicitudesPersonas.init();
                                }else if(id_tipo_int_asegurado == 6){
                                    $('.endosos_articulo').hide();
                                    $('.endosos_carga').hide();
                                    $('.endosos_casco_aereo').hide();
                                    $('.endosos_casco_maritimo').hide();
                                    $('.endosos_persona').hide();
                                    $('.endosos_proyecto_actividad').css('display','block');
                                    $('.endosos_ubicacion').hide();
                                    $('.endosos_vehiculo').hide();
                                    tablaSolicitudesProyecto.init();
                                }else if(id_tipo_int_asegurado == 7){
                                    $('.endosos_articulo').hide();
                                    $('.endosos_carga').hide();
                                    $('.endosos_casco_aereo').hide();
                                    $('.endosos_casco_maritimo').hide();
                                    $('.endosos_persona').hide();
                                    $('.endosos_proyecto_actividad').hide();
                                    $('.endosos_ubicacion').css('display','block');
                                    $('.endosos_vehiculo').hide();
                                    tablaSolicitudesUbicacion.init();
                                }else if(id_tipo_int_asegurado == 8){
                                    $('.endosos_articulo').hide();
                                    $('.endosos_carga').hide();
                                    $('.endosos_casco_aereo').hide();
                                    $('.endosos_casco_maritimo').hide();
                                    $('.endosos_persona').hide();
                                    $('.endosos_proyecto_actividad').hide();
                                    $('.endosos_ubicacion').hide();
                                    $('.endosos_vehiculo').css('display','block');
                                    tablaSolicitudesVehiculo.init();
                                }

                                $(".documentos_entregados").remove();
                                $(".botones").remove();
                                isColective(responseDatos.data.ramo);
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

                                    if(tablaTipo2 == 'vida'){

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

                                if(responseDatos.data.tipo_ramo == "individual"){

                                    $(" #articuloTab, #cargaTab, #casco_aereoTab, #casco_maritimoTab , #personaTab , #proyecto_actividadTab , #ubicacionTab , #vehiculoTab").css("margin-bottom","-31px");
                                    if(RegExp('\\bvida\\b',"gi").test(responseDatos.data.nombre_ramo) || RegExp('\\bsalud\\b',"gi").test(responseDatos.data.nombre_ramo) || RegExp('\\baccidente\\b',"gi").test(responseDatos.data.nombre_ramo) || RegExp('\\baccidentes\\b',"gi").test(responseDatos.data.nombre_ramo) ){
                                        $(".detalleinteres_persona").show();
                                        $(".tabladetalle_personas").show();
                                    }else{
                                        $(".botones").remove();
                                    }

                                }else if(responseDatos.data.tipo_ramo == "colectivo"){

                                    $(" .detalleinteres_articulo, .detalleinteres_carga, .detalleinteres_aereo, .detalleinteres_maritimo, .detalleinteres_proyecto, .detalleinteres_ubicacion, .detalleinteres_vehiculo").show();
                                    $(" .tabladetalle_articulo, .tabladetalle_carga, .tabladetalle_aereo, .tabladetalle_maritimo, .tabladetalle_proyecto, .tabladetalle_ubicacion, .tabladetalle_vehiculo").show();
                                    $(".detalleinteres_persona").show();   
                                    $(".tabladetalle_personas").show();  
                                } 

                                tmpVar = setInterval(formularioCrearEndosos.moneda, 1000);
                                tmpVar2 = setInterval(formularioCrearEndosos.datosSelectPoliza, 1000);

                                $('#guardar_endoso').remove();
                                $('#cancelar_endoso').remove();
                                $('#guardar_poliza').remove();
                                $('#estado_poliza').attr('disabled',true);
                                
                                /*
                                InteresesAsociados: [],
                                interesesSeleccionados: typeof intSeleccionados != 'undefined' ? $.parseJSON(intSeleccionados) : [],
                                */    
                            }
                        });  
                    }
                });
            }else{
                if(id_poliza == ''){
                    toastr.error('Debe seleccionar una poliza');
                }else{
                    toastr.error('Debe seleccionar un tipo de endoso');  
                }
                
            }
        },
        guardarPoliza: function(){
            console.log(this.clienteTelefono);
            /*this.$http.post({
                url: phost() + 'endosos/guardarPoliza',
                method:'POST',
                data:{
                    clienteNombre: this.clienteNombre,
                    clienteIdentificacion: this.clienteIdentificacion,
                    clienteNoIdentificacion: this.clienteNoIdentificacion,
                    clienteGrupo: this.clienteGrupo,
                    clienteTelefono: this.clienteTelefono,
                    clienteCorreo: this.clienteCorreo,
                    clienteDireccion: this.clienteDireccion,
                    clienteExoneradoImp: '',//this.polizaCliente.exonerado_impuesto,
                    //numeroPoliza:this.numeroPoliza,
                    //detalleUnico:unico,
                    //fechaInicio:this.fechaInicio,
                    //fechaExpiracion:this.fechaExpiracion,
                    //participacion:participationArray[0],
                    //renovarPoliza :true,
                    //idPolicy :this.idPolicy,
                    //comision: this.comision,
                    //camposInteres:JSON.stringify(values),
                    //interesId: idint,
                    //planesCoberturas: $("#planesCoberturasDeducibles").val(),
                    //sumaAsegurada: this.sumaAsegurada,
                    //vigenciapagador : this.vigenciaPagador,
                    //vigenciaNombrePagador: this.polizaVigencia.pagador,
                    //vigenciaPersonaAsegurada: this.vigenciaPersonaAsegurada,
                    //vigenciaPolizaDeclarativa:this.vigenciaPolizaDeclarativa,
                    //primaAnual : this.primaAnual,
                    //primaDescuentos : this.primaDescuentos,
                    //primaOtros : this.primaOtros,
                    //primaImpuesto: this.primaImpuesto,
                    //primaTotal:this.primaTotal,
                    //pagosFrecuencia : this.pagosFrecuencia,
                    //pagosMetodo : this.pagosMetodo,
                    //pagosPrimerPago: this.pagosPrimerPago,
                    //pagosCantidad : this.pagosCantidad,
                    //pagosSitio :this.pagosSitio,
                    //pagosCentroFac :this.pagosCentroFac,
                    //pagosDireccion:this.pagosDireccion,
                    //centroContable :this.centroContable,
                    //campoacreedores : acreedoresArray,
                    //campoacreedores_mon : acreedores_monArray,
                    //campoacreedores_por : acreedores_porArray,
                    //campoacreedores_ini : acreedores_iniArray,
                    //campoacreedores_fin : acreedores_finArray,
                    //campoacreedores_id : acreedores_idArray
                    erptkn: tkn,
                }
            }).then(function(response){
                if (!_.isEmpty(response.data) && response.data.msg =='OK') {

                    $('#CrearEndososForm').submit();

                    //window.location= phost()+"polizas/listar";
                }else{

                    //msg='Ocurrido un error al guardar la renovación '+'<br>'+response.data.field+'<b>';
                    //toastr.error(msg);
                }           
            });*/
        },
        moneda: function(){
            if($('#poliza_agentes_participacion').val() != ''){
                $(".moneda").inputmask('currency',{
                  prefix: "",
                  autoUnmask : true,
                  removeMaskOnSubmit: true
                }); 
                clearInterval(tmpVar);
            }
        },
        datosSelectPoliza: function(){

            if( $('#pagador').is(':visible') == true && $('#pagador > option').length >= 2  ){
                
                $('#direccion_nombre').val(formularioCrear.polizaCliente.direccion);
                $('#grupo_nombre').val(formularioCrear.polizaCliente.grupo);
                $('#pagador').val(formularioCrear.polizaVigencia.tipo_pagador);
                $('#frecuenciapagos').val(formularioCrear.polizaPrima.frecuencia_pago);
                $('#metodopago').val(formularioCrear.polizaPrima.metodo_pago);
                $('#cantidadpagos').val(formularioCrear.polizaPrima.cantidad_pagos);
                $('#sitiopago').val(formularioCrear.polizaPrima.sitio_pago);
                $('#centro_facturacion').val(formularioCrear.polizaPrima.centro_facturacion);
                $('#centro_contable > option').each(function(){
                    if($(this).text() == formularioCrear.nombre_centroContable){
                        $('#centro_contable').val($(this).val());
                    }
                });
                populateStoredCovergeData('indCoveragefields','coverage','removecoverage',formularioCrear.polizaCoberturas,"cobertura","valor_cobertura");
                populateStoredCovergeData('indDeductiblefields','deductible','removeDeductible',formularioCrear.polizaDeducciones,"deduccion","valor_deduccion");

                clearInterval(tmpVar2);
            }
        }

	}
});

$('select').on('select2:select', function (evt) {
    if(selectActive == 0){
        selectActive = $(this).attr('id');
    }

    if($(this).attr('id') == "id_ramo" && selectActive == "id_ramo"){
        $('#cliente_id').val('').trigger('change.select2');
        $('#id_poliza').val('').trigger('change.select2');
    }else if($(this).attr('id') == "cliente_id" && selectActive == "cliente_id"){
        $('#id_ramo').val('').trigger('change.select2');
        $('#id_poliza').val('').trigger('change.select2');
    }else if($(this).attr('id') == "id_poliza" && selectActive == "id_poliza"){
        $('#id_ramo').val('').trigger('change.select2');
        $('#cliente_id').val('').trigger('change.select2');
    }
   
    formularioCrearEndosos.getPolizas();

    if( $(this).attr('id') == selectActive && $(this).val() == '' ){
        selectActive = 0;
    }
});

function verModificaPrima(){
    formularioCrearEndosos.getModificaPrima();
    formularioCrearEndosos.getPolizaEndoso();  
}

$(document).ready(function(){
    $('#poliza_agentes_participacion').removeClass('moneda');
})
