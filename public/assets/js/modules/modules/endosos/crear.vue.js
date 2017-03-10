Vue.http.options.emulateJSON = true;
var formularioCrear = new Vue({
	el: ".wrapper-content",
	data:{
        clientes: clientes,
        estadosEndosos: estadosEndosos,
        poliza: poliza,
        motivos_endosos: motivos_endosos,
        id_cliente: vista == "crear" ? id_cliente : '',
        id_ramo: vista == "crear" ? id_ramo : '',
        id_poliza: id_poliza != "" ? id_poliza : '',
        tipo_endoso: tipo_endoso != "" ? tipo_endoso : '',
        id_motivo: id_motivo != "" ? id_motivo : '',
        modifica_prima: vista == "editar" ? modifica_prima : '',
        valor_descripcion: vista == "editar" ? valor_descripcion : '',
        estado_endoso: vista == "editar" ? estado_endoso : '',
        id_endoso: vista == "editar" ? endoso_id : '',
        uuid_endoso: vista == "editar" ? uuid_endoso : '',

        comboEstado: vista == "editar" ? estado_solicitud : '',
        estado_pol: vista == "editar" ? estado_pol : '',
        polizaCliente: vista == "editar" ? cliente : '',
        polizaAseguradora: vista == "editar" ? aseguradora : '',
        polizaPlan: vista == "editar" ? plan : '',
        polizaCoberturas: vista == "editar" ? coberturas : '',
        polizaDeducciones: vista == "editar" ? deducciones : '',
        polizaComision: vista == "editar" ? comision : '',
        polizaVigencia: vista == "editar" ? vigencia : '',
        polizaPrima: vista == "editar" ? prima : '',
        polizaCentroFacturacion: vista == "editar" ? centroFacturacion : '',
        polizaParticipacion: vista == "editar" ? participacion : '',
        polizaTotalParticipacion: vista == "editar" ? (agtPrincipal !== '' ? 100.00 : totalParticipacion) : '',
        id_centroContable: vista == "editar" ? id_centroContable : '',
        nombre_centroContable: vista == "editar" ? nombre_centroContable : '',
        catalogoCantidadPagos: vista == "editar" ?cantidadPagos : '',
        catalogoSitioPago: vista == "editar" ? sitioPago : '',
        catalogoMetodoPago: vista == "editar" ? metodoPago : '',
        catalogoFrecuenciaPagos: vista == "editar" ? frecuenciaPagos : '',
        catalogoCentroFacturacion: vista == "editar" ? centrosFacturacion : '',

	},
	methods: {
        getPolizas: function(numero){
            var self = this;

            if(numero == 1){
                var id_ramo = $('#id_ramo').val();
            }else if(numero == 2){
                var id_cliente = $('#cliente_id').val();
            }else if(numero == 3){
                var id_poliza = $('#id_poliza').val();
            }

            console.log(id_ramo);
            console.log(id_cliente);
            console.log(id_poliza);

            this.$http.post({
                url: phost() + 'endosos/ajax_get_polizas',
                method: 'POST',
                data: {id_ramo: id_ramo, id_cliente: id_cliente, id_poliza: id_poliza, erptkn: tkn}
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.data)) {
                    if(numero == 1){
                        self.$set('poliza', response.data);
                    }else if(numero == 2){

                    }else if(numero == 3){

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
        habilitarPoliza: function(){

            var self = this;
            var tipo_endoso = $('#tipo_endoso').val();
            var estado_endoso = $('#estado_endosos').val();
            var motivo_endoso = $('#motivo_endoso').val();

            console.log(tipo_endoso);
            console.log(estado_endoso);
            console.log(motivo_endoso);

            if(tipo_endoso == "Regular" && estado_endoso == "Aprobado"){

                $('.detail').remove();
                $('.renewal').remove();
                
                /*self.$set('disabledfechaInicio',true);
                self.$set('disabledfechaExpiracion',true);
                self.$set('cambiarOpcionesPago',true);
                self.$set('disabledAgente',true);
                self.$set('disableParticipacion',true);
                self.$set('disabledEstadoPoliza',true);*/
            }
        },
	}
});

function verpolizas(numero){
    formularioCrear.getPolizas(numero);
}
function verModificaPrima(){
    formularioCrear.getModificaPrima();
}
