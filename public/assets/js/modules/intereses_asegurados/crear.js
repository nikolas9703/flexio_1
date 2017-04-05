var interesesAsegurados = (function(){
  var objFrom = {
    personaForm: $('#persona')
  };

  $('#persona').validate({
    submitHandler: function(form) {
      angular.element('#campo[guardar]').attr('disabled', true);
	  $('input:text,input:hidden, select:hidden, textarea, select').removeAttr('disabled');
		
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


    };

    //jQuery Daterange
    $('.datepicker').datepicker({
      startDate: '-1y',
      maxDate: '+0d',
      yearRange: '1900:+0d',
      changeMonth: true,
      changeYear: true,
      dateFormat: 'dd-mm-yy',
      onSelect: function (value, ui) {
        var today = new Date();
        var format = value.split("-");
        var dob = new Date(format[2], format[0], format[1]);
        var diff = (today - dob);
        var age = Math.floor(diff / 31536000000);
        $("[id*=edad]").val(age);
        $("[id*=edad]").attr('readonly', true);

      },
      
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
      var letra = $(this).val();

      if(letra === 'cedula'){
        $('.PAS').hide();
        $('.pasaporte').val('');
        $('.noPAS').show();
        if(desde == "solicitudes"){
          
          $('.letra').val($('#id_letras').val());
          $('#provincia').val($('#id_provincia').val());
        }
        //$('.letra').val('');
         
        if($("#provincia").data("disabled")===true){
          $(".provincia").prop("disabled", true);
        }else{
          $(".provincia").prop("disabled", false);
        }
       
      }
      else if(letra === 'pasaporte'){
        $('.PAS').show();
        $('.letra').val('');        
        $('.noPAS').hide();
        $(".provincia").val('').prop("disabled", true);
      }
    });


    $('.letra').on("change", function(){
      var letra = $(this).val();
      arrayLetters=['E','PE','N'];
      
      if(arrayLetters.indexOf(letra)!== -1){
        $(".provincia").prop("disabled", true);

      }else{
       $(".provincia").prop("disabled", false);
     }

     $('#id_letras').val(letra);
     
     
   });

    $('#provincia').on("change", function(){
      var letra = $(this).val(),
      option;
      if(letra !== ""){

        $("#letra").empty();
        option = '<option value="">Seleccione</option>'+'<option value="0">0</option>'+'<option value="PI">PI</option>';
        $('.letra').append(option);

      }else{
        $("#letra").empty();
        option = '<option value="">Seleccione</option>'+'<option value="E">E</option>'+'<option value="N">N</option>'+'<option value="PE">PE</option>';
        $('.letra').append(option);
      }  

      $('#id_provincia').val($("#provincia").val()); 
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
			$('input:text,input:hidden, select:hidden, textarea, select').removeAttr('disabled');
            //Enviar el formulario
            form.submit();                        
          }
        });

    $.validator.addMethod(
      "rgx",
      function(value, element, regexp) {
        var re = new RegExp(regexp);
        return this.optional(element) || re.test(value);
      },
      "Campo alfabetico."
      );
    $('input[name="campo[estatura]').rules(
     "add",{ required: false, 
      number:true, 
      messages: { 
        number:'Campo númerico' 
      } 
    });
    $('input[name="campo[peso]').rules(
     "add",{ required: false, 
      number:true, 
      messages: { 
        number:'Campo númerico' 
      } 
    });            
    $('input[name="campo[nombrePersona]"').rules(
     "add",{ required: true, 
      rgx:'^[a-zA-ZáéíóúñÁÉÍÓÚÑ ]+$',
      messages: { 
        rgx:'Campo alfabético' 
      } 
    }); 

    $('input[name="campo[nacionalidad]"').rules(
      "add",{ required: false, 
        rgx:'^[a-zA-ZáéíóúñÁÉÍÓÚÑ ]+$',
        messages: { 
          rgx:'Campo alfabético' 
        } 
      });
  });

    $( ".asiento" ).on("keyup keypress blur change",function() {
      if (event.ctrlKey==true) {
        return false;
      }
      setTimeout(function () {
        var provincia = $('.provincia').val();    
        var letra = $('.letra').val();
        var tomo = $('.tomo').val();
        var asiento = $('.asiento').val();
        var identificacion = provincia + "-" + letra + "-" + tomo + "-" + asiento;
        register_user_persona(identificacion);
      }, 800);
    });

    $( ".pasaporte" ).on("keyup keypress blur change",function() {
      if (event.ctrlKey==true) {
        return false;
      }
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
        url: phost() + 'intereses_asegurados/ajax_check_persona',
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
      });             
    }
	$(window).load(function(){
		$('#provincia').attr('disabled', 'disabled');
		$('.provincia').attr('disabled', 'disabled');
	});
    $(document).ready(function(){
		if(cliente==='si')
		{
			register_user_persona(datos_cliente.identificacion);
			
			$('#nombrePersona').val(datos_cliente.nombre);
			$('#nombrePersona').attr('disabled', 'disabled');
			$('#formulario').attr('disabled', 'disabled');
			$('#telefono_residencial').val(telefono_cliente_residencial);
			$('#telefono_oficina').val(telefono_cliente_oficina);
			$('#correoPersona').val(correo_cliente);
			$('#direccion').val(direccion_residencial_cliente);
			$('#direccion_laboral').val(direccion_laboral_cliente);
			
			if(datos_cliente.tipo_identificacion==='pasaporte')
			{
				$('#identificacion').val('pasaporte');
				$('#pasaporte').val(datos_cliente.identificacion);
				$('#identificacion').attr('disabled', 'disabled');
				$('#pasaporte').attr('disabled', 'disabled');
			}
			else
			{
				$('#provincia').trigger('click');
				
				$('#identificacion').val('cedula');
				$('#provincia').val(datos_cliente.detalle_identificacion.provincia);
				$('#letra').val(datos_cliente.detalle_identificacion.letra);
				$('#tomo').val(datos_cliente.detalle_identificacion.tomo);
				$('#asiento').val(datos_cliente.detalle_identificacion.asiento);
				
				$('#identificacion').attr('disabled', 'disabled');
				$('#provincia').attr('disabled', 'disabled');
				$('.provincia').attr('disabled', 'disabled');
				$('#letra').attr('disabled', 'disabled');
				$('#tomo').attr('disabled', 'disabled');
				$('#asiento').attr('disabled', 'disabled');
			}
			
			$('#identificacion').trigger('click');
		}
	
      $('.estadoPersona').attr('disabled', 'disabled');
      var counter = 2;
      $('#del_file_persona').hide();
      $('#add_file_persona').click(function(){

        $('#file_tools_persona').before('<div class="file_upload_persona row" id="fpersona'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
        $('#del_file_persona').fadeIn(0);
        counter++;
      });
      $('#del_file_persona').click(function(){
        if(counter==3){
          $('#del_file_persona').hide();
        }   
        counter--;
        $('#fpersona'+counter).remove();
      });  
      $(".telefono_residencial").inputmask("mask", {"mask": "999-9999"});
      $(".telefono_oficina").inputmask("mask", {"mask": "999-9999"});

      
      
      $('#participacion_persona,.pasaporte').bind("cut copy paste",function(e) {
        e.preventDefault();
      });
      $('#persona').validate({

       rules: {

        "campodetalle[interes_asociado]":{
          required:false

        },

      },

      errorPlacement: function(error, element) {
        var placement = $(element).data('error');
        if (element.hasClass("select2-hidden-accessible")) {
          element.parent().append(error);

        } else {
          error.insertAfter(element);
        }
      }
    });
    });

//Popular formulario
new Vue({
  el: '#persona',
  ready:function(){
  	

  },
});

$("input:checkbox").on('click', function() {
  // in the handler, 'this' refers to the box clicked on
  var $box = $(this);
  if ($box.is(":checked")) {
    // the name of the box is retrieved using the .attr() method
    // as it is assumed and expected to be immutable
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    // the checked state of the group/box on the other hand will change
    // and the current value is retrieved using .prop() method
    $(group).prop("checked", false);
    $box.prop("checked", true);
    if($box.val()=='TR'){ 
      $('#telefono_principal').val('Residencial');
    } else if($box.val()=='TL'){
      $('#telefono_principal').val('Laboral');
    }else if($box.val()=='DR'){
      $('#direccion_principal').val('Residencial');
    }else if($box.val()=='DL'){
      $('#direccion_principal').val('Laboral');
    }

  } else {
    $box.prop("checked", false);
  }
});

$('#beneficiodetalle_persona').on('change',function(){
  var monto =$('.montoPersona');
  if($(this).val()=='No'){
    monto.hide();
  }else{
    monto.show();
  }
});

$('#relaciondetalle_persona, .relaciondetalle_persona_vida').on('change',function(){
  var form =$('#persona').validate({});

  if($(this).val()=='Dependiente' ||  $(this).val()=='Beneficiario'){
   $('#asociadodetalle_persona').rules(
     "add",{ required: true, 

     });
   $('#participacion_persona').rules(
     "add",{ required: true, 

     });
   $('#suma_asegurada_persona').rules(
     "add",{ required: false, 

     });
   if(tablaTipo=="salud"){
    $('#primadetalle_persona').rules(
     "add",{ required: false, 

     });
  }
  else{
   $('#primadetalle_persona').rules(
     "add",{ required: false, 

     });
 }

 $('#asociadodetalle_persona').prop("disabled",false);
 $('#tipo_relacion_persona').prop("disabled",false);
}else {
 $('#asociadodetalle_persona').val("").trigger("change");
 $('#asociadodetalle_persona').rules(
   "add",{ required: false, 

   });

 $('#asociadodetalle_persona').prop("disabled",true);
 $('#tipo_relacion_persona').prop("disabled",true);
 $('#suma_asegurada_persona').rules(
   "add",{ required: true, 

   });
 $('#participacion_persona').rules(
   "add",{ required: false, 

   });
 if(tablaTipo=="vida"||tablaTipo=="accidentes" || tablaTipo=="salud"){
  $('#primadetalle_persona').rules(
   "add",{ required: true, 

   });
}else{
  $('#primadetalle_persona').rules(
   "add",{ required: false, 

   });
}
}

});

$('#asociadodetalle_persona').on('change' ,function(){

  if($(this).val()==$('#selInteres').val() && $(this).val()!==""){
    $( '.guardarPersona').prop('disabled',true);
    toastr.error('El interés asegurado ya se encuentra en la lista.');
  }else{
   $( '.guardarPersona').prop('disabled',false);
 }

});

$("#participacion_persona").on("keyup blur",function() {

  setTimeout(function () {
    var detail = $("input[name=detalleunico]").val(); 
    var textValue = $("#participacion_persona").val(),
    id_father = $("#asociadodetalle_persona").val();

    validatePercent(textValue,detail,id_father);
  }, 800);
});

function validatePercent(percent,unikDetail,fatherId)
{
  $.ajax({
    type: "POST",
    data: {
      fatherId: fatherId,
      detail: unikDetail,
      erptkn: tkn
    },
    url: phost() + 'intereses_asegurados/validatePercent',
    success: function(data)
    { 

      var intText=parseInt(percent),
      total = (parseInt(data) + intText);                
      if(total > 100 || intText>100)
      { 
        var validator = $( "#persona" ).validate();
        validator.showErrors({
          "campodetalle[participacion]": "El valor ingresado supera el 100% de participación"
        });
        $('.guardarPersona').attr('disabled', true);
      }
      else{
        $('.guardarPersona').attr('disabled', false);
      }
    }
  });             
}