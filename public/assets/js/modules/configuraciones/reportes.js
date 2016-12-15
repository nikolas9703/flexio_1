$(function(){
	
	$('.agregarBtn').tablaDinamica({
		idTabla: 'reportesTable',
		afterAddRow: function(row){

			//Init Bootstrap Calendar Plugin
		    $('.daterange-picker').daterangepicker({
		    	singleDatePicker: true,
		    	timePicker: true,
		    	format: 'DD-MM-YYYY h:m a',
		        showDropdowns: true,
		        opens: "left",
		        locale: {
		        	applyLabel: 'Seleccionar',
		            cancelLabel: 'Cancelar',
		        	daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
		            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		            firstDay: 1
		        }
		    });

		    //Verificars si en la fila que se agrego
	 		//existe un campo con la clase
	 		//chosen-select, si existe: hacer update del plugin
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
			//console.log(row);
			//alert( $(row).find('select[id*="id_rol"]').find('option:selected').val() );
		}
	});

	//-------------------------------------
	//Inicializar Chosen plugin
	//-------------------------------------
	//Primero verificar si existe la funcion, para evitar errores de js
	if ($().chosen) {
		if($(".chosen-select").attr("class") != undefined){
			$(".chosen-select").chosen({
				width: '100%'
			});
		}

		//Fix para campos chosen en tabla dinamica
		$('select.chosen-select').chosen({
	        width: '100%',
	    }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
	        $(this).closest('div.table-responsive').css("overflow", "visible");
	    }).on('chosen:hiding_dropdown', function(evt, params) {
	    	$(this).closest('div.table-responsive').css({'overflow-x':'auto !important'});
	    });
	}

	//Init Bootstrap Calendar Plugin
    $('.daterange-picker').daterangepicker({
    	singleDatePicker: true,
    	timePicker: true,
    	format: 'DD-MM-YYYY hh:mm:ss a',
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
	$('#configuracionReportesForm').validate({
		/*focusInvalid: true,*/
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

	if($('#configuracionReportesForm').validate().form() == true)
	{
		//Habilitar campos, para poder capturarlos
	    $('input:disabled').attr("disabled", "");
	    $('input').removeAttr("readonly");

	    //Enviar Formulario
	    var guardar = moduloConfiguracion.guardarNotificaciones($('#configuracionReportesForm'));
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
	else
	{
		//Habilitar boton
		$('#guardarReporteBtn').on('click', submitReportesFormBtnHlr).removeAttr('disabled');
	}
}
