//Modulo Tabla de Cargos
var beneficiario = (function(){

	//Inicializar Eventos de Botones
	var eventos = function(){

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
	var iniciar_datepicker = function() {

		//refresh chosen

			$('.datepicker').datepicker({
                autoclose:true,
                startView: 1,
                changeMonth: true,
                format: 'dd-M-yyyy',
                todayHighlight: true
            });

	};

	return{
		init: function(){
			eventos();
		},
	};
})();

beneficiario.init();
