var cobros_global = '';
Vue.http.options.emulateJSON = true;
var formularioCrear = new Vue({
	el: ".wrapper-content",
	data:{
		informacionRemesas: [],
        id_remesa: vista == "editar" ? id_remesa : '',
        cobros:[],
        ver: ver,
        estado: ver == "editar" ? estado : '',
	},
	methods: {
        getRemesas: function (actualizar,cobros_eliminar) {
            //polula el segundo select del header
            var self = this;
            var id_aseguradora = $('#aseguradora').val();
            var fecha_inicio = $('#fecha_desde').val();
            var fecha_final = $('#fecha_hasta').val();
            var id_ramos = $('#ramos').val(); 
            if(id_ramos[0] == "todos"){
                id_ramos = $('#ramos2').val();
            }
            var vista = $('#vista').val();
            var id_remesa = vista == "editar" ? $('#id_remesa').val() : '';
            var cobros_eliminar = cobros_eliminar;
            var actualiza = actualizar;
            if(actualiza == 1){
                cobros_global = '';
            }

            this.$http.post({
                url: phost() + 'remesas/ajax_get_remesa_saliente',
                method: 'POST',
                data: {id_aseguradora: id_aseguradora, fecha_inicio: fecha_inicio, fecha_final: fecha_final, id_ramos: id_ramos, id_remesa: id_remesa, actualiza: actualiza, cobros_eliminar: cobros_eliminar,vista: vista, erptkn: tkn} 
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }

                if (!_.isEmpty(response.data)) {

                    console.log(response.data.idCobros);
                    if(response.data.idCobros == 0){

                        if(vista == 'crear'){
                            console.log(response.data.remesa[0]);
                            if(response.data.guardar == 0){
                                $('#tabla_remesas').addClass('hidden');
                                $('#error').empty().append('No se encontraron datos.').css({"color": "#868686", "padding": "30px 0 0"}).removeClass('hidden').addClass('text-center lead').append('<input type="hidden" name="remesas[codigo_remesa]" id="codigo_remesa" value="'+response.data.codigoRemesa+'">');
                            }else if(response.data.guardar == 1){
                                toastr.warning('La aseguradora seleccionada ya tiene una remesa en proceso. \n<a href="'+phost()+'remesas/editar/'+response.data.remesa[0].uuid_remesa+'">'+response.data.remesa[0].numero_remesa+'</a>');
                                $('#tabla_remesas').addClass('hidden');
                                $('#error').empty();
                            }
                 
                        }else if(vista == "editar"){
                            if(ver == 1){
                                self.$set('disabledGuardar', true);
                                self.$set('disabledPagar', true);
                                self.$set('disabledActualizar', true);
                            }else{
                                if(estado == "Por pagar" || estado == "Pagada" || estado == "Anulado"){
                                    self.$set('disabledGuardar', true);
                                    self.$set('disabledPagar', true);
                                    self.$set('disabledActualizar', true);
                                }else if(estado == "En proceso"){
                                    self.$set('disabledGuardar', false);
                                    self.$set('disabledPagar', false);
                                    self.$set('disabledActualizar', false);
                                } 
                            }
                        }
                    }else{
                        $('#error').addClass('hidden');
                        $('#tabla_remesas').removeClass('hidden');
                        self.$set('informacionRemesas', response.data.inter);
                        self.$set('cobros',response.data.idCobros);
                        self.$set('valor_cobro',response.data.valor_cobro);
                        $("#id_aseguradora").val(id_aseguradora);
                        $("#monto_remesa").val(response.data.monto);
                        $("#id_ramos").val(id_ramos);

                        if(vista == "editar"){
                            if(ver == 1){
                                self.$set('disabledGuardar', true);
                                self.$set('disabledPagar', true);
                                self.$set('disabledActualizar', true);
                            }else{
                                if(estado == "Por pagar" || estado == "Pagada" || estado == "Anulado"){
                                    self.$set('disabledGuardar', true);
                                    self.$set('disabledPagar', true);
                                    self.$set('disabledActualizar', true);
                                }else if(estado == "En proceso"){
                                    self.$set('disabledGuardar', false);
                                    self.$set('disabledPagar', false);
                                    self.$set('disabledActualizar', false);
                                }
                            }
                        }
                    }
                }
            });
        }
	}
});


$("#ramos").change(function(){
    if($(this).val()=="todos"){
      $("#ramos2 > option").each(function() {
        if (this.value!="todos"){
          $(this).prop("selected","selected");
        }
        $('#ramos2').trigger('chosen:updated');
      });
    }
})


$(document).ready(function(){

    $("#fecha_desde").change(function(){
        var desde = $("#fecha_desde").val();
        $("#fecha_desde_formulario").val(desde);
    });
    $("#fecha_hasta").change(function(){
        var hasta = $("#fecha_hasta").val();
        $("#fecha_hasta_formulario").val(hasta);
    });
    if(vista == "editar"){
        var datosRamos = ramos_id.split(",");
        $('#ramos').val(datosRamos).trigger("chosen:updated");
        formularioCrear.getRemesas(4,0);
        if(estado == "Por pagar" || estado == "Pagada" || estado == "Anulador"){
            $('#eliminarRemesaBtn').remove();
        }
        console.log(datosRamos);
    }

});


$("#imprimirRemesaBtn").click(function(){

    var id_remesa = $("#id_remesa").val();
    window.open('../imprimirRemesa/'+id_remesa); 
});

$("#todos_recibos").click( function(){
    if($(this).is(':checked')) {
        $(".recibos_check").prop('checked', true);
    }else{
        $(".recibos_check").prop('checked', false);
    }
});

$('#eliminarRemesaBtn').on('click',function(){
    var ids_cobros = '';
    $("input[name='recibos[]']").each(function(){
        if($(this).is(':checked')){
            ids_cobros +=$(this).val()+',';
        }
    });
    if(ids_cobros != ''){
        ids_cobros = ids_cobros.split(',');
        cobros_global += ids_cobros;
        cobros_global = cobros_global.split(',');
        console.log(cobros_global);
        if(vista == "editar"){
            $.post(phost()+'remesas/ajax_eliminar_cobro', {ids_cobros: ids_cobros, erptkn: window.tkn}, function(response){
                console.log(response);
                if(response >= 1){
                    formularioCrear.getRemesas(3,0);
                }else{
                    toastr.error("No se pudo eliminar los recibos seleccionados");
                }
            });
        }else if(vista == "crear"){
          formularioCrear.getRemesas(3,cobros_global);  
        }
    }else{
        toastr.error("Debe seleccionar un recibo para eliminar");
    }

});

