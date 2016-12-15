//Modulo Tabla de Cargos
var comisiones = (function(){

 	var formulario = '#crearComisionForm';
	var campo_colaborador_inicial= $(formulario).find('#colaboradores');
	var campo_colaborador_to= $(formulario).find('#colaboradores_to');
	var campo_departamento= $(formulario).find('#area_negocio_id');
	var campo_centro_contable= $(formulario).find('#centro_contable_id');
	var campo_area_negocio= $(formulario).find('#area_negocio_id');
	var colaboradores_rightAll= $(formulario).find('#colaboradores_rightAll');

	var campos = function(){

		campo_colaborador_inicial.multiselect({
	        search: {
	            left: '<input type="text" id="buscador_colaborador" name="q" class="form-control" placeholder="Buscar..." />',
	            right: '<input type="text" name="q" class="form-control" placeholder="Buscar..." />',
	        }
	    });
		$('#buscador_colaborador').attr("disabled", "disabled");
		$(campo_colaborador_inicial).attr("disabled", "disabled");
 		$(campo_colaborador_inicial).find('option[value=""]').remove();
 		$(campo_area_negocio).prop( "disabled", true );
 		$(colaboradores_rightAll).prop("disabled", true );


		$(formulario).validate({
				focusInvalid: true,
				ignore: '',
				wrapper: '',
		});
		$(formulario).find('#colaboradores_to').rules("add",{ required: true});

  	     $.ajax({
   	         url: phost() + 'comisiones/ajax-cargar-codigo',
   	         data: { erptkn: tkn},
   	         type: "POST",
   	         dataType: "json",
   	         cache: false
   	     }).done(function(data) {
      	    	$(formulario).find('input[name="campo[numero]"]').attr('readonly', true).val(data);
   	     });

    	 //Campos Chosens
  	     $(formulario).find('select[name="acumulados[acumulados][]"], select[name="deducciones[deducciones][]"]').find('option').removeAttr("selected");
 	 	 $(formulario).find('select[name="deducciones[deducciones][]"], select[name="acumulados[acumulados][]"]').chosen({width: '100%'}).trigger('chosen:updated');

    };

 	$(formulario).on("change", '#centro_contable_id', function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		//Formulario a cambiar
 		var centro_contable = this;

 		var centro_contable_id = $(centro_contable).find('option:selected').val();
   		//Mensaje de Loading
		$(campo_departamento).closest('div').append('<div class="departamento-loader"><small class="text-success">Buscando areas de negocio... <i class="fa fa-circle-o-notch fa-spin"></i></small></div>');
 		$(campo_colaborador_to).empty();
		$(campo_departamento).empty();
  		//Popular Campo Cargo
		listar_departamentos({centro_id: centro_contable_id}).done(function(json){
			 //Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}

			//If json object is empty.
			if($.isEmptyObject(json['result']) == true){
				//remover mensaje loading
 				$('.departamento-loader').remove();
 				return false;
			}
 			//Popular Campo Departamento
			$(campo_departamento).empty().append('<option value="">Seleccione</option>').removeAttr('disabled');
			$.each(json['result'], function(i, result){
 				$(campo_departamento).append('<option value="'+ result['id'] +'">'+ result['nombre'] +'</option>');
 			});

 			$('.departamento-loader').remove();

			$(campo_departamento).find('option').removeAttr("selected");

			$(campo_departamento).prop( "disabled", false );

			$(campo_departamento).chosen({
				width: '100%'
			}).trigger('chosen:updated');

        });

		listar_colaboradores({centro_contable_id: centro_contable_id,departamento_id:0}).done(function(json){
			 //Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}

			//If json object is empty.
			if($.isEmptyObject(json['result']) == true){
        	$(colaboradores_rightAll).attr("disabled", true);
  				return false;
			}
 			//Popular Colaboradores
			$(campo_colaborador_inicial).empty();
			$('#buscador_colaborador').attr("disabled", false);
			$(campo_colaborador_inicial).attr("disabled", false);
			$(colaboradores_rightAll).attr("disabled", false);

			$.each(json['result'], function(i, result){
 				$(campo_colaborador_inicial).append('<option value="'+ result.id +'" data-colaborador="'+ result.id +'">'+ result.nombre +' '+ result.apellido +' - '+ result.cedula +'</option>');

			});
        });
	});


	//Al seleccionar Centro Contable se debe reflejar los cambios en los Departamentos
	$(formulario).on("change", '#area_negocio_id', function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

 		var departamento_id = this;
		var departamento = $(departamento_id).find('option:selected').val();
 		var centro_contable_id = $(campo_centro_contable).find('option:selected').val();
		$(campo_colaborador_to).empty();

 		listar_colaboradores({centro_contable_id: centro_contable_id,departamento_id: departamento}).done(function(json){
			 //Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}

			//If json object is empty.
			if($.isEmptyObject(json['result']) == true){
 				return false;
			}
			//Popular Colaboradores
			$(campo_colaborador_inicial).empty();

			$('#buscador_colaborador').attr("disabled", false);
			$(campo_colaborador_inicial).attr("disabled", false);

			$.each(json['result'], function(i, result){
				$(campo_colaborador_inicial).append('<option value="'+ result.id +'" data-colaborador="'+ result.id +'">'+ result.nombre +' '+ result.apellido +' - '+ result.cedula +'</option>');

			});
       });

	});

	var listar_departamentos = function(parametros){
 		if(parametros == ""){
			return false;
		}
 		return $.ajax({
			url: phost() + 'comisiones/ajax-listar-departamento-x-centro',
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		});
	};

	var listar_colaboradores = function(parametros){
 		if(parametros == ""){
			return false;
		}
 		return $.ajax({
			url: phost() + 'comisiones/ajax-listar-colaboradores',
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		});
	};



	return{
		init: function() {
			campos();
		},
  	};
})();

comisiones.init();
