var interesesAsegurados = (function(){
	var objFrom = {
        personaForm: $('#persona'),
    };

    $('#persona').validate({
        submitHandler: function(form) {
            angular.element('#campo[guardar]').attr('disabled', true);
            form.submit();
        }
    });

    var inicializar = function(){
        objFrom.personaForm.validate({
            ignore: '',
            wrapper: '',
        });
    };
	//Inicializar Eventos de Botones
	var eventos = function(){
		
		//Inicializar Chosen plugin
		if ($().chosen){
			if($(".chosen-filtro").attr("class") != undefined){
				$(".chosen-filtro").chosen({
					width: '100%',
					disable_search: true,
					inherit_select_classes: true
				});
			}
		}
		
		//Mostrar barra de filtro formulario
		$('.filtro-formularios').removeClass('hide');
		
		//Evento: Cambio de formulario
		$('#formulario').on('change', function(e){
			e.preventDefault();
			var seleccionado = $(this).find('option:selected').val();
			//console.log( seleccionado );
			
			$('.filtro-formularios').find('ul').find('a[href="#'+ seleccionado +'"]').trigger('click');
		});

		//Verificar si existe variable "formulario_seleccionado"
		if(typeof formulario_seleccionado != "undefined"){
			setTimeout(function(){
				$('.filtro-formularios').find('#formulario').find('option[value*="'+ formulario_seleccionado +'"]').prop("selected", "selected").trigger('change');
				actualizar_chosen();
			}, 800)
			
		}
	};

    //jQuery Daterange
    $('.datepicker').datepicker({
    onSelect: function (value, ui) {
    var today = new Date();
    var format = value.split("/");
    var dob = new Date(format[2], format[0], format[1]);
    var diff = (today - dob);
    var age = Math.floor(diff / 31536000000);
    $("[id*=edad]").val(age);
    $("[id*=edad]").attr('readonly', true);
    
},
    maxDate: '+0d',
    yearRange: '1950:2010',
    changeMonth: true,
    changeYear: true
    });
    
    $('.identificacion').prop('selectedIndex', 0);
    setTimeout(function () {
    $('.identificacion').trigger('change');
    }, 500);
	
	//Funcion para inicializar plugins
	var actualizar_chosen = function() {
		
		//refresh chosen
		setTimeout(function(){
			$('.filtro-formularios').find('select.chosen-filtro').trigger('chosen:updated');
		}, 50);
	};
	
	return{
		init: function(){
			eventos();
		},
	};
})();

interesesAsegurados.init();

$(".PAS").hide();
$(".noPAS").hide();

$('.identificacion').on("change", function(){
    var letra = $(this).val()
    
    if(letra === '166'){
        $('.PAS').hide();
		$('.pasaporte').val('');
        $('.noPAS').show();
        $('.letra').val('');
        $(".provincia").val('').prop("disabled", false);
    }
    else{
        $('.PAS').show();
		$('.letra').val('');        
        $('.noPAS').hide();
        $(".provincia").val('').prop("disabled", true);
    }
});

$('.letra').on("change", function(){
    var letra = $(this).val();   
    if(letra == "PAS"){
    $(".provincia").prop("disabled", true);
    $('.PAS').show();
    $('.noPAS').hide();
    $('.identificacion').prop('selectedIndex', 1);
    }   
});

$('input[name="campo[telefono_residencial_check]').change(function() {
    if(this.checked) {
       $('input[name="campo[telefono_oficina_check]').attr('disabled', true);
    }else{
        $('input[name="campo[telefono_oficina_check]').attr('disabled', false);
    }
});
$('input[name="campo[telefono_oficina_check]').change(function() {
    if(this.checked) {
       $('input[name="campo[telefono_residencial_check]').attr('disabled', true);
    }else{
        $('input[name="campo[telefono_residencial_check]').attr('disabled', false);
    }
});

$('input[name="campo[direccion_residencial_check]').change(function() {
    if(this.checked) {
       $('input[name="campo[direccion_laboral_check]').attr('disabled', true);
    }else{
        $('input[name="campo[direccion_laboral_check]').attr('disabled', false);
    }
});
$('input[name="campo[direccion_laboral_check]').change(function() {
    if(this.checked) {
       $('input[name="campo[direccion_residencial_check]').attr('disabled', true);
    }else{
        $('input[name="campo[direccion_residencial_check]').attr('disabled', false);
    }
});
$(function(){

	//$('input[name="campo[porcentaje_participacion]"]').val("0.00");

	//jQuery Validate
	$('#persona').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		submitHandler: function(form) {			
			//Habilitar campos ocultos
			$('input:hidden, select:hidden, textarea').removeAttr('disabled');

			//Enviar el formulario
			form.submit();                        
		}
	});
       /* $('input[name="campo[estatura]').rules(
			 "add",{ required: false, 
				 	number:true, 
				 	messages: { 
 					number:'Por favor, introduzca una estatura válida.' 
				 } 
			 });
        $('input[name="campo[peso]').rules(
			 "add",{ required: false, 
				 	number:true, 
				 	messages: { 
 					number:'Por favor, introduzca un peso válido.' 
				 } 
			 });   */              
          	
 
});

$( ".asiento" ).keypress(function() {
  setTimeout(function () {
  var provincia = $('.provincia').val();    
  var letra = $('.letra').val();
  var tomo = $('.tomo').val();
  var asiento = $('.asiento').val();
  var identificacion = provincia + "-" + letra + "-" + tomo + "-" + asiento;
  register_user_persona(identificacion);
  }, 800);
});

$( ".pasaporte" ).keypress(function() {
  setTimeout(function () {
  var pasaporte = $('.pasaporte').val(); 
  var identificacion = pasaporte;
  register_user_persona(identificacion);
  }, 800);
});


function register_user_persona(identificacion)
        {
            $.ajax({
                type: "POST",
                data: {
                    identificacion: identificacion,
                    erptkn: tkn
                },
                url: phost() + 'intereses_asegurados/ajax-check-persona',
                success: function(data)
                {                   
                    if(data === 'USER_EXISTS')
                    {
                        toastr.warning('No se puede guardar, registro duplicado');
                        $('.guardarPersona').attr('disabled', true);
                    }
                    else{
                        $('.guardarPersona').attr('disabled', false);
                    }
                }
            })              
        }
$(document).ready(function(){
	var counter = 2;
	$('#del_file_persona').hide();
	$('#add_file_persona').click(function(){
            
		$('#file_tools_persona').before('<div class="file_upload_persona" id="f'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"></div>');
		$('#del_file_persona').fadeIn(0);
	counter++;
	});
	$('#del_file_persona').click(function(){
		if(counter==3){
			$('#del_file_persona').hide();
		}   
		counter--;
		$('#f'+counter).remove();
	});
	$(".telefono_residencial").inputmask("mask", {"mask": "999-9999"});
	$(".telefono_oficina").inputmask("mask", {"mask": "999-9999"});
});

//Popular formulario
new Vue({
  el: '#persona',
  ready:function(){
    if(vista==='ver' && formulario_seleccionado === 'persona'){        
    if(typeof intereses_asegurados_id_persona !== 'undefined'){
    $('.uuid').val(intereses_asegurados_id_persona);
    }
    $('input[name="campo[nombre]').val(data.nombre);
    $('.identificacion').find('option[value=' + identificaciones + ']').prop('selected', 'selected');    
    if(identificaciones == 111){
    var result = data.identificacion.split('-');   
   setTimeout(function () {   
   $('.provincia').find('option[value="8"]').prop('selected', true);
   $('.letra').find('option[value=' + result[1] + ']').prop('selected', true);
   $('.tomo').val(result[2]);
   $('.asiento').val(result[3]);
   }, 500);
    }else{
   $('.pasaporte').val(data.identificacion);     
    }
    $('.datepicker').val(data.fecha_nacimiento);
    $('input[name="campo[edad]').val(data.edad);
    $('.estado_civil').find('option[value=' + data.estado_civil + ']').prop('selected', 'selected');
    $('input[name="campo[nacionalidad]').val(data.nacionalidad);
    $('.sexo').find('option[value=' + data.sexo + ']').prop('selected', 'selected');
    $('input[name="campo[estatura]').val(data.estatura);
    $('input[name="campo[peso]').val(data.peso);
    $('input[name="campo[telefono_residencial]').val(data.telefono_residencial);
    $('input[name="campo[telefono_oficina]').val(data.telefono_oficina);
    $('input[name="campo[direccion_residencial]').val(data.direccion_residencial);
    $('input[name="campo[direccion_laboral]').val(data.direccion_laboral);
    $('#observaciones_persona').val(data.observaciones);
    $('.estado').find('option[value=' + data.estado + ']').prop('selected', 'selected');    
    //Verificar si tiene permisos para editar
    if(typeof permiso_editar !== 'undefined')
    {
            if(permiso_editar == 'true'){
                    setTimeout(function(){
                            $(".guardarPersona").prop('disabled', false);
                           
                    }, 1000);
            }
    }
    }
  },
})