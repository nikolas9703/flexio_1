//Modulo Accion Personal
var accionPersonal = (function(){
	//var comentario =
	//Inicializar Eventos de Botones
	var eventos = function(){

		if(typeof(Storage) !== "undefined") {
			//console.log('local');
			if(typeof comentario_accion_personal != "undefined"){
				//console.log(comentario_accion_personal);
				localStorage.setItem("comentario_accion_personal", comentario_accion_personal);
			}

		}
		//comentario_accion_personal
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
		
		//Mostrar barra de filtro formulario/colaborador
		$('.filtro-formularios').removeClass('hide');
		
		//Evento: Cambio de formulario
		$('#formulario').on('change', function(e){
			e.preventDefault();
			var seleccionado = $(this).find('option:selected').val();
			//console.log( seleccionado );
			
			$('.filtro-formularios').find('ul').find('a[href="#'+ seleccionado +'"]').trigger('click');
		});
		
		//Evento: Cambio de colaborador
		$('select#colaborador_id').on('change', function(e){
			e.preventDefault();
			var seleccionado = $(this).find('option:selected').val();

			//Popular Colaborador id seleccionado.
			$('.filtro-formularios-content').find('div.tab-pane.active').find('input[type="hidden"][name*="colaborador_id"]').val(seleccionado);
			
			//Si el formulario de colaborador esta visible
			//
			// Calcular dias de vacaciones
			//
			$('.vacaciones-loader').remove();
			
			if($('#crearVacacionesForm').is(':visible')){
				
				//mensaje loading
				$('#crearVacacionesForm').find('input[id="campo[dias_disponibles]"]').closest('div').append('<div class="vacaciones-loader"><small class="text-success">Verificando d&iacute;as disponibles ... <i class="fa fa-circle-o-notch fa-spin"></i></small></div>');
				
				calcularDiasDisponiblesVacaciones();
				
			}else{
				$('#crearVacacionesForm').find('input[id="campo[dias_disponibles]"]').val('');
			}
			
		});
		
		//Verificar si existe variable "formulario_seleccionado"
		if(typeof formulario_seleccionado != "undefined"){
			setTimeout(function(){
				$('.filtro-formularios').find('#formulario').find('option[value*="'+ formulario_seleccionado +'"]').prop("selected", "selected").trigger('change');
				actualizar_chosen();
			}, 800);
		}

		//Before using local storage, check browser support for localStorage and sessionStorage
		if(typeof(Storage) !== "undefined") {
			//Verificar si existe variable colaborador_id
			//proveniente de Local Storage
			if(localStorage.getItem("colaborador_id")){
				
				var colaborador_id = localStorage.getItem("colaborador_id");
				
				setTimeout(function(){
					$('.filtro-formularios').find('#colaborador_id').find('option[value="'+ colaborador_id +'"]').prop("selected", "selected").trigger('change');
					actualizar_chosen();
				}, 1000);
				
				//Borrar variable de localstorage
				localStorage.removeItem("colaborador_id");
			}
		}
	};
	
	//Funcion para inicializar plugins
	var actualizar_chosen = function() {
		
		//refresh chosen
		setTimeout(function(){
			$('.filtro-formularios').find('select.chosen-filtro').trigger('chosen:updated');
		}, 50);
	};
	
	var calcularDiasDisponiblesVacaciones = function() {
		
		var colaboradorid = $('select#colaborador_id').find('option:selected').val();
		
		if(colaboradorid == ""){
			$('#crearVacacionesForm').find('input[id="campo[dias_disponibles]"]').val('');
			$('.vacaciones-loader').remove();
		}
		
		$.ajax({
			url: phost() + 'colaboradores/ajax-colaborador-info',
			data: $.extend({erptkn: tkn}, {colaborador_id: colaboradorid}),
			type: "POST",
			dataType: "json",
			cache: false,
		}).done(function(json){

            //Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}
			
			//If json object is empty.
			if($.isEmptyObject(json) == true){
				$('#crearVacacionesForm').find('input[id="campo[dias_disponibles]"]').val('');
				$('.vacaciones-loader').remove();
				return false;
			}
			
			$('.vacaciones-loader').remove();
			$('#crearVacacionesForm').find('input[id="campo[dias_disponibles]"]').val(json.dias_disponibles_vacaciones);
        });
	};
	
	return{
		init: function(){
			eventos();
		},
		actualizar_chosen: function() {
			actualizar_chosen();
		}
	};
})();

accionPersonal.init();