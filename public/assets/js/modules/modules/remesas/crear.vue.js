Vue.http.options.emulateJSON = true;
var formularioCrear = new Vue({
	el: ".wrapper-content",
	data:{
		informacionRemesas: [],
        codigo_remesa: codigo,
        bancos: bancos,
        cobros:[],
        ver: ver,
	},
	methods: {
        getRemesas: function () {
            //polula el segundo select del header
            var self = this;
            var id_aseguradora = $('#aseguradora').val();
            var fecha_inicio = $('#fecha_desde').val();
            var fecha_final = $('#fecha_hasta').val();
            var id_ramos = $('#ramos').val(); 
            if(id_ramos[0] == "todos"){
                id_ramos = $('#ramos2').val();
            }
            var codigo_remesa = $('#codigo_remesa').val();
            var vista = $('#vista').val();

            /*console.log(id_aseguradora);
            console.log(fecha_inicio);
            console.log(fecha_final);
            console.log(id_ramos);
            console.log(codigo_remesa);
            console.log(vista);*/
            
            this.$http.post({
                url: phost() + 'remesas/ajax_get_remesa_saliente',
                method: 'POST',
                data: {id_aseguradora: id_aseguradora, fecha_inicio: fecha_inicio, fecha_final: fecha_final, id_ramos: id_ramos, codigo_remesa: codigo_remesa, vista: vista,erptkn: tkn} 
            }).then(function (response) {
                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }

                if (!_.isEmpty(response.data)) {

                    console.log(response.data.idCobros);
                    if(response.data.idCobros == 0){

                        $('#tabla_remesas').empty().append('No se encontraron datos.').css({"color": "#868686", "padding": "30px 0 0"}).removeClass('hidden').addClass('text-center lead').append('<input type="hidden" name="remesas[codigo_remesa]" id="codigo_remesa" value="'+response.data.codigoRemesa+'">');
                        
                    }else{
                        
                        $('#tabla_remesas').removeClass('hidden');
                        self.$set('informacionRemesas', response.data.inter);
                        self.$set('cobros',response.data.idCobros);
                        console.log(response.data);
                        self.$set('codigo_remesa',response.data.codigoRemesa);
                        $("#id_aseguradora").val(id_aseguradora);
                        $("#monto_remesa").val(response.data.monto);
                        $("#id_ramos").val(id_ramos);

                        if(response.data.guardar == 1){

                            if(ver == 1){

                                self.$set('disabledGuardar', true);
                                self.$set('disabledPagar', true);
                                self.$set('disabledcancelar', true);
                                self.$set('disabledActualizar', true);

                            }else{

                                self.$set('disabledGuardar', false);
                                self.$set('disabledPagar', false);
                                self.$set('disabledcancelar', false);
                                self.$set('disabledActualizar', false); 
                            }  
                        }else{

                            if(vista == "editar"){

                                self.$set('disabledGuardar', true);
                                self.$set('disabledPagar', true);
                                //self.$set('disabledcancelar', true);
                                self.$set('disabledActualizar', true);
                            }else{
                                self.$set('disabledGuardar', true);
                                //self.$set('disabledPagar', true);
                                //self.$set('disabledcancelar', true);
                                self.$set('disabledActualizar', true);
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
      //$("#ramos option[value='todos']").remove();
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
        $("#fecha_desde_formulario1").val(desde);
    });

    $("#fecha_hasta").change(function(){
        var hasta = $("#fecha_hasta").val();
        $("#fecha_hasta_formulario").val(hasta);
        $("#fecha_hasta_formulario1").val(hasta);
    });
     
    if(vista == "editar"){
        var datosRamos = ramos_id.split(",");
        console.log(datosRamos);
        $('#ramos').val(datosRamos).trigger("chosen:updated");

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

