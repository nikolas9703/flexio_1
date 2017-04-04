$(function(){
  $('a.agregarBtn').tablaDinamica({
    idTabla: 'notificacionesTable',
     afterAddRow:function(row){
       //Init Bootstrap Calendar Plugin
       $(row).find('.daterange-picker').val('');
 		    $('.daterange-picker').daterangepicker({
 		    	singleDatePicker: true,
 		    	timePicker: true,
 		    	format: 'DD-MM-YYYY h:m:s a',
 		        showDropdowns: true,
 		        opens: "left",
 		        locale: {
 		        	applyLabel: 'Seleccionar',
 		            cancelLabel: 'Cancelar',
 		        	daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
 		            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
 		            firstDay: 1
 		        }
 		    }).on('apply.daterangepicker', function(ev, picker) {
 		    });

        //$(row).find('.daterange-picker').val('');
       if($(row).find('select.chosen-select').attr('class') != undefined){

 	 			//Remover el chosen copiado del campo anterior
 				//Para luego inizializaro mas adelante.
 	 			$(row).find('.chosen-container').remove();
 	 			$(row).find('.chosen-container-single').remove();

 	 			//Hacer update de todos los campos
 	 			//para corregir problemas con el z-index
 	 			$(row).closest('table').find('select.chosen-select').chosen({
 	                width: '100%',
 	            }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
 	                $(row).closest('div.table-responsive').css("overflow", "visible");
 	            }).on('chosen:hiding_dropdown', function(evt, params) {
 	            	$(row).closest('div.table-responsive').css({'overflow-x':'auto !important'});
 	            });
 			}

    },
    onDeleteRow: function(row){
			console.log(row);
		}
   });
   //Inicializar jQuery Validate
 //Set error placement
 $.validator.setDefaults({
     errorPlacement: function(error, element){
       if($(element).attr('id').match('/id_usuario/g') == null){
         $('.formerror').empty().append( $(error).addClass('pull-left') );
       }
       if($(element).hasClass('hasUsuarios') == true){
         element.parent().append( $(error).css({"margin-top":5}) );
       }

     }
 });
   //jQuery Validate
 $('form').validate({
   focusInvalid: true,
   ignore: ".ignore",
   wrapper: '',
 });

 $('#guardarReporteBtn').on('click', submitReportesFormBtnHlr);
});
function submitReportesFormBtnHlr(e)
{
  e.preventDefault();
  e.returnValue=false;
  e.stopPropagation();

  //Desabilitar boton
  $('#guardarReporteBtn').off('click', submitReportesFormBtnHlr).prop('disabled', 'disabled');

  if($('form').validate().form() == true)
  {
    //Habilitar campos, para poder capturarlos
    $('input:disabled').attr("disabled", "");
    $('input').removeAttr("readonly");

    //Enviar Formulario
    //$('form#configuracionReportesForm').submit();
    var guardar = moduloConfiguracion.guardarNotificaciones($('form'));
    guardar.done(function(data){
        var respuesta = $.parseJSON(data);
        if(respuesta.estado==200){
          $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.mensaje+'</div>');
          $("#success-alert").fadeTo(5000, 500).slideUp(1500, function(){
               $("#success-alert").alert('close');
          });
        }
    });

  }
  else{
    //Habilitar boton
    $('#guardarReporteBtn').on('click', submitReportesFormBtnHlr).removeAttr('disabled');
  }
}
