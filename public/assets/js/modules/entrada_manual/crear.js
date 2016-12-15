var entradaCrear={
  settings: {
    formulario : $('#crearEntradaManualForm')
  },
  botones:{
    guardar: $('button.guardarEntradaManual'), cancelar: $('button.cancelarEntradaManual')
  },
  init:function(){
    this.inicializar_plugin();
    this.actualizar_chosen();
    this.eventos();
}, inicializar_plugin:function(){

      this.settings.formulario.find('#transaccionesTable .chosen-select').each(function(index, element){
        $('#'+this.id +' option:first').val("");
      });

      this.settings.formulario.find('div.ibox').each(function(index,element){
        var div = $(this).children();
        if(index === 0){
          $(div[1]).css('display','none');
        }else if(index === 1){
          $(div[1]).css('display','block');
        }
      });

      
       $("table#transaccionesTable").find('input.debito, input.credito').each(function(index,element){
         var campo_input = this.id;
        if(this.id.match(/debito/)){
           var campo_input_cambiar = campo_input.replace("debito", "credito");
            
          $('#'+this.id).blur(function(){
                if( $(this).val().length > 0 ) {
                     $("#"+campo_input_cambiar).attr('readonly', true);
                  //$("input.credito").attr('readonly', true);
                }else if( $(this).val().length === 0 ) {
                  //$("input.credito").attr('readonly', false);
                   $("#"+campo_input_cambiar).attr('readonly', false);
                }
           });

        }
        if(this.id.match(/credito/)){
            var campo_input_cambiar = campo_input.replace("credito", "debito");
          $('#'+this.id).blur(function(){
                if($(this).val().length > 0 ) {
                    $("#"+campo_input_cambiar).attr('readonly', true);
                   //$("input.debito").attr('readonly', true);

                }else if( $(this).val().length === 0 ) {
                    $("#"+campo_input_cambiar).attr('readonly', false);
                  //$("input.debito").attr('readonly', false);
                }
           });

        }

      });

     this.settings.formulario.find('.agregarTransaccionesBtn').tablaDinamica({

     			afterAddRow: function(row){
            $(row).find('input').inputmask();
     				entradaCrear.actualizar_chosen();
            $('input.debito').bind('input',entradaCrear.calcular_total_debito);
            $('input.credito').bind('input',entradaCrear.calcular_total_credito);
            if($('.chekbox-incluir').is(':checked')){
              var nombre = $('#crearEntradaManualForm').find('input[id*="campo[nombre]"]').val();
              if(nombre !== ""){
                  $.each($('#crearEntradaManualForm').find('#transaccionesTable input[id*="nombre"]'), function(i, campo) {
                     $('#centro_id'+i).removeAttr('data-rule-required',"true"); //*****dath
                    $(this).val(nombre);
                  });
              }
            }else{
              $.each($('#crearEntradaManualForm').find('#transaccionesTable input[id*="nombre"]'), function(i, campo) {
                  $('#centro_id'+i).removeAttr('data-rule-required',"true"); //*****dath
                //$(this).val("");
              });
            }
            $(row).find('input.debito, input.credito').each(function(index,element){
              $("#"+this.id).attr('readonly', false);
               var campo_input = this.id;
              if(this.id.match(/debito/)){
                    var campo_input_cambiar = campo_input.replace("debito", "credito");
                $('#'+this.id).blur(function(){
                      if( $(this).val().length > 0 ) {
                       $("#"+campo_input_cambiar).attr('readonly', true);

//                        $("input.credito").attr('readonly', true);
                      }else if( $(this).val().length === 0 ) {
                        //$("input.credito").attr('readonly', false);
                         $("#"+campo_input_cambiar).attr('readonly', false);

                      }
                 });

              }
              if(this.id.match(/credito/)){
                var campo_input_cambiar = campo_input.replace("credito", "debito");
                $('#'+this.id).blur(function(){
                      if($(this).val().length > 0 ) {
                        //$("input.debito").attr('readonly', true);
                        $("#"+campo_input_cambiar).attr('readonly', true);
                      }else if( $(this).val().length === 0 ) {
                        //$("input.debito").attr('readonly', false);
                         $("#"+campo_input_cambiar).attr('readonly', false);
                      }
                 });

              }

            });
     			},
          afterDeleteRow:function(row){
            entradaCrear.calcular_total_debito();
            entradaCrear.calcular_total_credito();
          }

    });

      $.validator.prototype.elements = function() {
    var validator = this,
    rulesCache = {};

    return $( this.currentForm )
          .find( "input, select, textarea" )
          .not( ":submit, :reset, :image, [disabled]") // changed from: .not( ":submit, :reset, :image, [disabled], [readonly]" )
          .not( this.settings.ignore )
          .filter( function() {
              if ( !this.name && validator.settings.debug && window.console ) {
                  console.error( "%o has no name assigned", this );
              }

              if ( this.name in rulesCache || !validator.objectLength( $( this ).rules() ) ) {
                  return false;
              }

              rulesCache[ this.name ] = true;
              return true;
          });
      };
      //Inicializar Validate en Formularios
  			this.settings.formulario.validate({
  				//focusInvalid: true,
  				ignore: ':hidden:not(select)',
  				wrapper: '',
          errorPlacement:function(error, element){

            if($('#transaccionesTable').find('input[id*="nombre"]').length > 0 ||  $('#transaccionesTable').find('input[id*="cuenta_id"]').length > 0 || element.attr("id") == "totalCredito" || element.attr("id") == "totalDebito") {
              //error.appendTo( $("tfoot td:nth-child(1)") );
              $("tfoot td:nth-child(1)").html(error);
             }else {
              error.insertAfter(element);
            }
          }
  			});

      /**
       * Init Bootstrap Calendar Plugin
       */
      this.settings.formulario.find('.fecha-tarea').datepicker({
          singleDatePicker: true,
          dateFormat: 'dd/mm/yy',
          showDropdowns: true,
          opens: "left",
          applyLabel: 'Seleccionar',
          cancelLabel: 'Cancelar',
          daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
          monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
          
            
        }).datepicker("setDate", new Date());
        

        $('input.debito').bind('input',entradaCrear.calcular_total_debito);
        $('input.credito').bind('input',entradaCrear.calcular_total_credito);
        entradaCrear.calcular_total_debito();
        entradaCrear.calcular_total_credito();
   },
   //Actualizar campos chosen
  actualizar_chosen:function(){
     this.settings.formulario.find('.chosen-select').chosen({
       width: '100%',
          }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
           $(this).closest('div.table-responsive').css("overflow", "visible");
          }).on('chosen:hiding_dropdown', function(evt, params) {
           $(this).closest('div.table-responsive').css({'overflow-x':'auto !important'});
          });
   },
       //Esta funcion calcula la columna "Monto de debito" de la tabla
    calcular_total_debito:function(){
    	var total_monto = 0;

    	$.each($('#crearEntradaManualForm').find('#transaccionesTable input[id*="debito"]'), function(i, campo) {
    		if(this.value !== "" && isNumber(parseFloat(this.value))){
    			total_monto += parseFloat(this.value);
    		}
    	});
    	$('#crearEntradaManualForm').find('#totalDebito').prop("value", roundNumber(total_monto, 2));
    },
    calcular_total_credito:function(){
    	var total_monto = 0;

    	$.each($('#crearEntradaManualForm').find('#transaccionesTable input[id*="credito"]'), function(i, campo) {
    		if(this.value !== "" && isNumber(parseFloat(this.value))){
    			total_monto += parseFloat(this.value);
    		}
    	});
    	$('#crearEntradaManualForm').find('#totalCredito').prop("value", roundNumber(total_monto, 2));
    },
    eventos:function(){
      $('.chekbox-incluir').on('change', function(){
        if($(this).is(':checked')){
          var nombre = $('#crearEntradaManualForm').find('input[id*="campo[nombre]"]').val();
          if(nombre !== ""){
              $.each($('#crearEntradaManualForm').find('#transaccionesTable input[id*="nombre"]'), function(i, campo) {
                $(this).val(nombre);
              });
          }
        }else{
          $.each($('#crearEntradaManualForm').find('#transaccionesTable input[id*="nombre"]'), function(i, campo) {
            $(this).val("");
          });
        }
      });
      this.botones.guardar.click(function(event){
        event.preventDefault();
        var selfButton = this;

        if(entradaCrear.settings.formulario.valid() === true){
          $(selfButton).unbind("click");
          $(selfButton).bind("click");
          var guardar = moduloEntradaManual.guardarEntradaManual(entradaCrear.settings.formulario);
          guardar.done(function(data){
            var respuesta = $.parseJSON(data);
            if(respuesta.estado==200)
            {
              $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.mensaje+'</div>');
              window.location = respuesta.redireccionar;
            }
          });

      }

    });
    this.botones.cancelar.click(function(event){
        event.preventDefault();
         window.history.back();
    });
    }
};

$(document).ready(function(){
  $('#cancelarFormBoton').parent().parent().prepend('<div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>');
  $('#totalCredito').attr('name','total_credito');
  $('#totalDebito').attr('name','total_debito');
  $('#totalCredito').attr('data-rule-required',"true");
  $('#totalDebito').attr('data-rule-required',"true");
  $('#totalCredito').attr('data-rule-equalTo',"#totalDebito");
  $('#transaccionesTable .chosen-select').attr('data-rule-required',"true");
  
  //Formateo de botones
  $('#cancelarFormBoton').closest('div').removeClass('col-lg-3').addClass('col-lg-2');
  $('#guardarFormBoton').closest('div').removeClass('col-lg-3').addClass('col-lg-2');
  $('.comentarios').closest('div').removeClass('col-lg-12').addClass('col-lg-6');
  $('.comentarios').css('height', '100px');
  $('.narracion').val().length > 0 ? $('.eliminarTransaccionesBtn').css('display', 'none') : '';
  $('.narracion').val().length > 0 ? $('.agregarTransaccionesBtn').css('display', 'none') : '';
  $('.narracion').val().length > 0 ? $('.comentarios').attr('disabled', true) : '';
  
    entradaCrear.init();

});
