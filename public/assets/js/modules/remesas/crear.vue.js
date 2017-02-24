Vue.http.options.emulateJSON = true;
var formularioCrear = new Vue({
	el: ".wrapper-content",
	data:{
		informacionRemesas: [],
        codigo_remesa: codigo,
        bancos: bancos,
        cobros:[],
	},
	methods: {
        getRemesas: function () {
            //polula el segundo select del header
            var self = this;
            var id_aseguradora = $('#aseguradora').val();
            var fecha_inicio = $('#fecha_desde').val();
            var fecha_final = $('#fecha_hasta').val();
            var id_ramos = $('#ramos').val();
            var codigo_remesa = $('#codigo_remesa').val();
            var vista = $('#vista').val();

            console.log(id_aseguradora);
            console.log(fecha_inicio);
            console.log(fecha_final);
            console.log(id_ramos);
            console.log(codigo_remesa);
            console.log(vista);
            
            this.$http.post({
                url: phost() + 'remesas/ajax_get_remesa_saliente',
                method: 'POST',
                data: {id_aseguradora: id_aseguradora, fecha_inicio: fecha_inicio, fecha_final: fecha_final, id_ramos: id_ramos, codigo_remesa: codigo_remesa, vista: vista,erptkn: tkn} 
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }

                if (!_.isEmpty(response.data)) {

                    console.log(response.data.datos);
                    if(response.data.datos == 0){

                        $('#tabla_remesas').empty().append('No se encontraron datos.').css({"color": "#868686", "padding": "30px 0 0"}).removeClass('hidden').addClass('text-center lead').append('<input type="hidden" name="remesas[codigo_remesa]" id="codigo_remesa" value="'+response.data.codigoRemesa+'">');
                    }else{
                        
                        $('#tabla_remesas').removeClass('hidden');
                        self.$set('informacionRemesas', response.data.inter);
                        self.$set('cobros',response.data.idCobros);
                        console.log(response.data);
                        self.$set('codigo_remesa',response.data.codigoRemesa);
                        $("#id_aseguradora_pagar").val(id_aseguradora);
                        $("#id_aseguradora_guardar").val(id_aseguradora);
                        $("#monto_remesa_pagar").val(response.data.monto);
                        $("#monto_remesa_guardar").val(response.data.monto);
                        $("#id_ramos").val(id_ramos);
                        $("#id_ramos1").val(id_ramos);

                        if(response.data.guardar == 1){
                            self.$set('disabledGuardar', false);
                            self.$set('disabledPagar', false);
                            self.$set('disabledcancelar', false);
                            self.$set('disabledActualizar', false);
                        }else{
                            self.$set('disabledGuardar', true);
                            self.$set('disabledPagar', true);
                            self.$set('disabledcancelar', true);
                            self.$set('disabledActualizar', true);
                        }

                    }

                }
            });
        }
	}
});


$("#pagar_remesa").click( function(e){

    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();

    var options = $('#opciones_modal');
    var codigo_remesa = $('#codigo_remesa').val();

    //Init boton de opciones
    options.css('display', 'block');
    $('#opcionesModal').find('.modal-title').empty().append('Remesa: '+codigo_remesa);
    $('#opcionesModal').find('.modal-body').empty().append(options);
    $('#opcionesModal').find('.modal-footer').empty();
    $('#opcionesModal').modal('show');

    $('#opcionesModal').change('#forma_pago',function(e){
        var forma_pago = $('#forma_pago').val();
        
        if(forma_pago == "Efectivo"){

            $("#banco").attr('disabled',true);
            $("#numero_cheque").attr('disabled',true);
        }else if(forma_pago ==  "Transferencia"){

            $("#banco").attr('disabled',false);
             $("#numero_cheque").attr('disabled',true);
        }else if(forma_pago == "Cheque"){

            $("#banco").attr('disabled',false);
            $("#numero_cheque").attr('disabled',false);
        }
    });

    /*
    $('#opcionesModal').on('click','#procesar_remesa',function(e){

        var forma_pago = $('#forma_pago').val();
        var banco = $("#banco").val();
        var cheque = $("#numero_cheque").val();

        if(forma_pago == '' ){
            $("#errorForma").append('<p style="color:red;">Campo Obligatorio</p>');
        }else if(forma_pago !=  "" && banco == ''){
            $("#errorBanco").append('<p style="color:red;">Campo Obligatorio</p>');
        }else if(forma_pago != '' && forma_pago == "Cheque" && banco != '' && cheque == ''){
            $("#errorCheque").append('<p style="color:red;">Campo Obligatorio</p>');
        }else{
            $("#errorForma").append('');
            $("#errorBanco").append('');
            $("#errorCheque").append('');

            var cobros = $("input[name='id_cobros[]']").map(function () {
                return $(this).val();
            }).get();
            console.log(cobros);

        }
    });*/

});

$(document).ready(function(){

    $("#fecha_desde").change(function(){
        var desde = $("#fecha_desde").val();
        $("#fecha_desde_formulario").val(desde);
        $("#fecha_desde_formulario1").val(desde);
    });

    $("#fecha_hasta").change(function(){
        var hasta = $("#fecha_hasta").val();
        $("#fecha_hasta_formulario").val(hasta);
        $("#fecha_hasta_formulario1").val(hasta);
    });
     
    if(vista == "editar"){
        var prueba = ramos_id.split(",");
        console.log(prueba);
        $('#ramos').val(prueba).trigger("chosen:updated");

        formularioCrear.getRemesas();
    }

});


$("#imprimirRemesaBtn").click(function(){

    var codigo_remesa = $("#codigo_remesa").val();
    var id_aseguradora = $("#aseguradora").val();
    var fecha_desde =  $("#fecha_desde").val();
    fecha_desde = fecha_desde.replace("/", "-").replace("/", "-");
    var fecha_hasta = $("#fecha_hasta").val();
    fecha_hasta = fecha_hasta.replace("/", "-").replace("/", "-");

    console.log(codigo);
    console.log(aseguradora);
    console.log(fecha_desde);
    console.log(fecha_hasta);
    console.log(ramos);



    window.open('../imprimirRemesa/'+codigo_remesa+'/'+id_aseguradora+'/'+fecha_desde+'/'+fecha_hasta); 




});