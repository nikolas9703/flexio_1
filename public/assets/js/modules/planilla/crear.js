 $(document).ready(

    function(){

        var st = {
            cTreeMultiselect: ".tree-multiselect",
            iAcordeon: "#accordion",
            iCheckCentros: "#ch_centro",
            iCheckColaboradores: "#ch_colaboradores",
            iChecks: "#ch_centro, #ch_colaboradores", //Checkox de Acordion
            iListaColaboradores: "#lista_colaboradores",
            iListaColaboradoresTo: "#lista_colaboradores_to",
            cCentrosContablesID: ".CentrosContablesID",
            iCrearForm: "form#crearPlanilla",
            iGuardarBtn: "#guardarFormBtn"
        };

        var config = {

            checks:{
                color:"#023859",

            },
            chosen:{
		width: '100%'
            },
            multiSelect: {
                search: {
                    left: '<input type="text" name="q" class="form-control" placeholder="Buscar..." />',
                    right: '<input type="text" name="q" class="form-control" placeholder="Buscar..." />',
                }
            }
        };

        var dom = {};

        var catchDom = function(){
            dom.cTreeMultiselect = $(st.cTreeMultiselect);
            dom.iAcordeon = $(st.iAcordeon);
            dom.iCheckCentros = $(st.iCheckCentros);
            dom.iCheckColaboradores = $(st.iCheckColaboradores);
            dom.iChecks = $(st.iChecks);
            dom.iListaColaboradores = $(st.iListaColaboradores);
            dom.iListaColaboradoresTo = $(st.iListaColaboradoresTo);
            dom.iCrearForm = $(st.iCrearForm);
            dom.iGuardarBtn = $(st.iGuardarBtn);
        };

        var suscribeEvents = function(){
            dom.iAcordeon.on("change", st.iChecks, events.activarPanel);

            //checkbox
            dom.switcheryCentros = new Switchery(dom.iCheckCentros[0], config.checks);
            dom.switcheryColaboradores = new Switchery(dom.iCheckColaboradores[0], config.checks);

            //multiselect
            dom.iListaColaboradores.multiselect(config.multiSelect);

            //Gestion del formulario
            dom.iCrearForm.on('click', st.iGuardarBtn, events.submitFormBtnHlr);
        };

        var events = {
            activarPanel: function(e){
                console.log("me ejecute...");
                var self = $(this);
                var checked = self.prop("checked");
                var id = self.prop("id");

                if(checked)
                {
                    if(id == st.iCheckCentros.replace("#", ""))
                    {
                        if(dom.iCheckColaboradores.prop("checked"))
                        {
                            dom.switcheryColaboradores.setPosition(true);
                            dom.switcheryColaboradores.handleOnchange(true);
                        }
                    }
                    else
                    {
                        if(dom.iCheckCentros.prop("checked"))
                        {
                            dom.switcheryCentros.setPosition(true);
                            dom.switcheryCentros.handleOnchange(true);
                        }
                    }
                }
            },
            submitFormBtnHlr: function(){
                var colaborador_activo = dom.iCheckColaboradores.is(':checked');
                var centro_activo = dom.iCheckCentros.is(':checked');
                var tipo_creacion = '';

                if(colaborador_activo == true){
                    //Esta Activado el colaborador
                    console.log("Colaborador");

                    //marco los colaboradores indicador
                    dom.iListaColaboradoresTo.find("option").prop("selected", true);
                    var lista_colaboradores = dom.iListaColaboradoresTo.val(); //["1", "12"]
                    tipo_creacion = 'colaborador'; //["1", "12"]

                }else if(centro_activo == true){
                    //Esta Activo Centro Activo
                    /* var centro_contable_id= $('#centro_contable_id').val(); //["1", "12"]

                    var crearInputMultiselect = function(){
		                    var items = $(".selected").find(".item");

 		                    tipo_creacion= 'centro'; //["1", "12"]

		                    $.each(items, function(i, r){

  		                            var self = $(this);

		                             if(self.is(':visible'))
		                            {
 		                                $('<input>').attr({
		                                    type: 'hidden',
		                                    class: 'CentrosContablesID',
		                                    value: String(self.data("value")),
		                                    name: 'CentrosContablesID[]'
		                                }).appendTo('form#crearPlanilla');
		                            }

		                        });
                    };
                    crearInputMultiselect();*/
                }

                if (dom.iCrearForm.valid() != false) {

                    $.ajax({
                        url: phost() + 'planilla/crear',
                        data: dom.iCrearForm.serialize()+'&centro_contable='+centro_contable_id+'&lista_colaboradores='+lista_colaboradores+'&tipo_creacion='+tipo_creacion,
                        type: "POST",
                        dataType: "json",
                        cache: false,
                    }).done(function(json) {
                        //Check Session
                        if( $.isEmptyObject(json.session) == false){
                            window.location = phost() + "login?expired";
                        }

                        if(json.results == true){
                            //toastr.success(json.mensaje);
                            //recargar();
                            limpiarFormulario();

                        }else{
                            toastr.error(json.mensaje);

                        }
                    });
                }
            }
        };


        //OTRAS FUNCIONES
        var limpiarFormulario = function(){
            //este limpia el token si se requiere limpiar la fecha
            //usar el identificador para evitar limpiar el token
            //dom.iCrearForm.find('input').prop("value", "");
            dom.iCrearForm.find('select').val('-1').trigger('chosen:updated');

            //reinicio los input type checkbox
            reiniciaCheck(dom.iCheckCentros);
            reiniciaCheck(dom.iCheckColaboradores);
            $('.collapse').collapse("hide");

            //los plugis de la parte inferior de limpian con la funcion
            reiniciaFormulario();
        };

        var preparaCheck = function(){
            dom.iChecks.prop("checked", false);
        };

        var reiniciaCheck = function(elemento){
            var self = elemento;
            var checked = self.prop("checked");
            var id = self.prop("id");

            if(checked)
            {
                if(id == st.iCheckCentros.replace("#", ""))
                {
                    dom.switcheryCentros.setPosition(true);
                    dom.switcheryCentros.handleOnchange(true);
                }
                else
                {
                    dom.switcheryColaboradores.setPosition(true);
                    dom.switcheryColaboradores.handleOnchange(true);
                }
            }
        };

        var reiniciaFormulario = function(){
            //reinicia la lista de centros, subcentros y areas de negocio
            dom.cTreeMultiselect.find("input[type=checkbox]").each(function(){
                var self = $(this);

                if(self.prop("checked"))
                {
                    self.click();
                }
            });

            //remuevo arreglo de centros, subcentros y areas de negocio
            dom.iCrearForm.find(st.cCentrosContablesID).remove();

            //reinicia la lista de colaboradores
            dom.iAcordeon.find(st.iListaColaboradores +"_leftAll").click();
        };

        function a(options)
        {
            $("select#centro_contable_id").treeMultiselect(options);
        }

        function b(options){
            $("select#centro_contable_id").treeMultiselect(options).css("display","block");
        }



        var checarTitulo = function(elemento)
        {
            var seleccioneTitulo = false;
            var elementoItems = elemento.find("div.item");

            var unselectedItems = elementoItems.filter(function() {
                var checkbox = $(this).find("> input[type=checkbox]");
                return !(checkbox.is(":checked"));
            });

            if (unselectedItems.length === 0) {
                var sectionCheckbox = $(this).find("> div.title > input[type=checkbox]");
                sectionCheckbox.prop('checked', true);

                seleccioneTitulo = true;
            }

            return seleccioneTitulo;
        }

        var obtenerIndice = function(elemento, nivel){
            var indice = "";
            var elementoItems = elemento.find("div.item");

            elementoItems.each(function(i, e){
                var self = $(this);

                if(i == 0)
                {
                    var aux = self.data("value").split("-");

                    if(nivel == "abuelo")
                    {
                        indice = aux[0];
                    }
                    else if(nivel == "hijo")
                    {
                        indice = aux[0] + "-" + aux[1];
                    }
                }
            });

            return indice;
        };

        var ocultarElementos = function(indice){
            var items = $(".selected").find(".item");

            $.each(items, function(i, r){
                var self = $(this);
                var value = String(self.data("value"));

                if(value.match(new RegExp(indice, 'g')))
                {
                    self.css("display", "none");
                }
            });
        };

        var mostrarElementos = function(indice){
            var items = $(".selected").find(".item");

            $.each(items, function(i, r){
                var self = $(this);
                var value = String(self.data("value"));

                if(value.match(new RegExp(indice, 'g')))
                {
                    self.css("display", "block");
                }
            });
        };

        var createSelectedDiv2 = function (selection) {
            var text = selection.text;
            var value = selection.value;
            var sectionName = selection.sectionName;

            var item = document.createElement('div');
            item.className = "item";
            item.innerHTML = text;

            $(item).append("<span class='section-name'>" + sectionName + "</span>");

            if (0) {
                $(item).prepend("<span class='remove-selected'>x</span>");
            }

            $(item).attr('data-value', value)
            .appendTo(".tree-multiselect > .selected");
        }

        var mostrarAgrupador = function(elemento, indice, abuelo){
            var titulos = elemento.find(".title");
            var selection = {};

            selection.text = "<b>Centro</b>";

            $.each(titulos, function(i, e){
                var self = $(this);
                if(i == 0)
                {
                    selection.sectionName = self.text();

                    if(elemento[0] != abuelo[0])
                    {
                        var titulosAbuelo = abuelo.find(".title");

                        $.each(titulosAbuelo, function(i, e){
                            var self = $(this);
                            if(i == 0)
                            {
                                selection.sectionName = self.text() +"/"+ selection.sectionName.replace("-", "");
                                selection.text = "<b>Sub-Centro</b>";
                            }
                        });
                    }
                }
            });

            selection.value= indice;

            createSelectedDiv2(selection);
        };

         var options = {
        		 startCollapsed:true,
              sortable: false,
            onChange: function(e)
            {
                 //abuelos
                var abuelos = $(".tree-multiselect").find(".selections > div.section");
                abuelos.each(function(){
                    var abuelo = $(this);
                    var hijos = abuelo.find("div.section");
                    var abueloIndice = "";
                    var checarAbuelo = checarTitulo(abuelo);

                    if(checarAbuelo)//si se selecciona el abuelo
                    {
                        abueloIndice = obtenerIndice(abuelo, "abuelo");
                        mostrarAgrupador(abuelo, abueloIndice, abuelo);
                    }

                    hijos.each(function(){
                        var hijo = $(this);
                        var hijoIndice = "";

                        hijoIndice = obtenerIndice(hijo, "hijo");
                        if(checarTitulo(hijo))//si se selecciona el hijo
                        {
                            ocultarElementos(hijoIndice);
                            if(!checarAbuelo){
                                mostrarAgrupador(hijo, hijoIndice, abuelo);
                            }
                        }
                        else
                        {
                            mostrarElementos(hijoIndice);
                        }
                    });
                });
            },
            freeze:false,
            groupable:true
        };

        a(options);

        //inicializacion
        catchDom();
        preparaCheck();
        suscribeEvents();


    }
);

  var eventos = (function(){



	  $("#crearPlanilla").on("change", 'select[id*="ciclo_id"]', function(e){

 			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

		 	  var ciclo_id = $(this).val();


			 $.ajax({
					url: phost() + 'planilla/ajax-listar-colaboradores-ciclo-pago',
					 data: {
						 ciclo_id: ciclo_id,
						 erptkn: tkn
				      },
					type: "POST",
					dataType: "json",
					cache: false,
				}).done(function(json) {

 					//Check Session
					if( $.isEmptyObject(json.session) == false){
						window.location = phost() + "login?expired";
					}

					if(json.response == true){

						 $("#crearPlanilla").find('#lista_colaboradores').empty();

						 if(json.colaboradores.length > 0){
    							$.each(json.colaboradores, function(i, result){
		 							$("#crearPlanilla").find('#lista_colaboradores').append('<option value="'+result.id+'">'+result.nombre +' '+result.apellido+'</option>');
							});

    							$("#guardarBtnCol").prop('disabled', false);
  						 }else{
  							$("#guardarBtnCol").prop('disabled', true);
 						 }


					}else{
						toastr.error(json.mensaje);
					}

				});

	  });
  });
var planilla = (function(){

  	var formulario = '#crearPlanilla';
	var opcionesModal = $('#opcionesModal');


	var botones = {
			guardarCC: "#guardarBtnCC",
  			guardarCol: "#guardarBtnCol",
  			guardarNoRegulares: "#guardarBtnPlanillaNoRegular",
  			cancelarCol: "#cancelarBtnCol, #cancelarBtnPlanillaNoRegular, #cancelarBtnCC"
	};

	var campos = function(){

 		$("#guardarBtnCol").prop('disabled', true);
  		$(formulario).validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
		});

  		var fecha1 = $(formulario).find('#rango_fecha1');
		var fecha2 = $(formulario).find('#rango_fecha2');

		fecha1.daterangepicker({
	      	  singleDatePicker: true,
	          showDropdowns: true,
	          opens: "left",
	          locale: {
	          	 format: 'DD/MM/YYYY',
	          	 applyLabel: 'Seleccionar',
	             cancelLabel: 'Cancelar',
	          	 daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
	             monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	             firstDay: 1
	          }
	      });
		fecha2.daterangepicker({
	      	  singleDatePicker: true,
	          showDropdowns: true,
	          opens: "left",
	          locale: {
	          	 format: 'DD/MM/YYYY',
	          	 applyLabel: 'Seleccionar',
	             cancelLabel: 'Cancelar',
	          	 daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
	             monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	             firstDay: 1
	          }
	      });
		 fecha1.val("");
		 fecha2.val("");

 		 if(tipo_planilla_creacion == 'liquidaciones' ){
   			$(formulario).find('select[name="deducciones[deducciones][]"], select[name="acumulados[acumulados][]"]').rules(
 					"add",{
 						required: false
 			});
 			$(formulario).find('select[name="acumulados[acumulados][]"]').rules(
 					"add",{
 						required: false
 			});
 			$(formulario).find('select[name="ciclo_id"]').rules(
 					"add",{
 						required: false
 			});
 			$(formulario).find('select[name="pasivo_id"]').rules(
 					"add",{
 						required: true
 			});

			 $(formulario).find('select[name="ciclo_id"]').attr('disabled', true);
			 $(formulario).find('#rango_fecha1').attr('disabled', true);
			 $(formulario).find('#rango_fecha2').attr('disabled', true);

			 $(formulario).find('select[name="deducciones[deducciones][]"], select[name="acumulados[acumulados][]"]').prop('disabled', true);
			 $(formulario).find('select[name="deducciones[deducciones][]"], select[name="acumulados[acumulados][]"]').chosen({width: '100%'}).trigger('chosen:updated');
		 }

		 if( tipo_planilla_creacion == 'vacaciones' || tipo_planilla_creacion == 'licencias'){
			 $(formulario).find('select[name="ciclo_id"]').attr('disabled', true);
			 $(formulario).find('#rango_fecha1').attr('disabled', true);
			 $(formulario).find('#rango_fecha2').attr('disabled', true);

		 }

 		 if(tipo_planilla_creacion != 'regular'){
			 $(formulario).find('#tipo_id').val( tipo_planilla_id );
			 $(formulario).find('select[name="tipo_id"]').attr('disabled', true);

		 }
 		 else{

 			$(formulario).find('#tipo_id option').each(function( index ) {
 				var valor = this.value;
   				    if(valor != 79 && valor != 96 && valor!=''){
   				    	$("#tipo_id option[value="+ valor+ "]").hide();
 				    }
  				});

 		 }
 		 $(formulario).find('select[name="deducciones[deducciones][]"], select[name="acumulados[acumulados][]"]').find('option').removeAttr("selected");
		 $(formulario).find('select[name="tipo_id"],#ciclo_id, #pasivo_id,select[name="deducciones[deducciones][]"], select[name="acumulados[acumulados][]"]').chosen({width: '100%'}).trigger('chosen:updated');

	};


	$(botones.guardarCC).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		agregarPlanillaCentroContable();
	});
	$(botones.guardarCol).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		agregarPlanillaColaboradores();
	});

	$(botones.guardarNoRegulares).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		agregarPlanillaNoRegulares();
	});

	$(botones.cancelarCol).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		window.location.href = phost()+'planilla/listar';
 	});

	  var crearInputMultiselect = function(){
          var items = $(".selected").find(".item");

              tipo_creacion= 'centro'; //["1", "12"]

          $.each(items, function(i, r){

                      var self = $(this);

                   if(self.is(':visible'))
                  {
                          $('<input>').attr({
                          type: 'hidden',
                          class: 'CentrosContablesID',
                          value: String(self.data("value")),
                          name: 'CentrosContablesID[]'
                      }).appendTo('form#crearPlanilla');
                  }

              });
  };

	var agregarPlanillaCentroContable = function(){

	 	//Reglas de Validacion
		$('#centro_contable_id').rules(
				"add",{
					required: true
 		});
		$('#rango_fecha1').rules(
				"add",{
					required: true
 		});
		$('#rango_fecha2').rules(
				"add",{
					required: true
 		});

   	 	 if( $(formulario).validate().form() == true )
	 	{

              var centro_contable_id= $('#centro_contable_id').val(); //["1", "12"]

             crearInputMultiselect();


  	 		 $(formulario).find('select').attr('disabled', false);
 			 $.ajax({
				url: phost() + 'planilla/ajax-crear-planilla',
				data: $(formulario).serialize()+'&tipo_creacion=centro',
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json) {
 				//Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				//Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				if(json.response == true){
						toastr.success(json.mensaje);
						 window.location.href = phost()+'planilla/listar';
 					}else{
					toastr.error(json.mensaje);
				}

			});
		 }

	};
	var agregarPlanillaColaboradores = function(){

		//Cuando se crea desde el colaborador es necesario las fechas
		$('#centro_contable_id').rules(
				"add",{
					required: false
 		});
		 $(formulario).find("#lista_colaboradores_to").find("option").prop("selected", true);
	 	$('#lista_colaboradores_to').rules(
				"add",{
					required: true
 		});
		$('#rango_fecha1').rules(
				"add",{
					required: true
 		});
		$('#rango_fecha2').rules(
				"add",{
					required: true
 		});

  		  if( $(formulario).validate().form() == true )
		  {
  			  $(formulario).find('select').attr('disabled', false);


				$.ajax({
					url: phost() + 'planilla/ajax-crear-planilla',
					data:  $(formulario).serialize()+'&tipo_creacion=colaborador',
					type: "POST",
					dataType: "json",
					cache: false,
				}).done(function(json) {
	 				if( $.isEmptyObject(json.session) == false){
						window.location = phost() + "login?expired";
					}
					 if(json.response == true){
	  						 toastr.success(json.mensaje);
	 						window.location.href = phost()+'planilla/listar';

	 				}else{
						toastr.error(json.mensaje);
					}
 				});
		 }

	};

 	var agregarPlanillaNoRegulares = function(){


  		  if( $(formulario).validate().form() == true )
		  {
			//Operacion para generar la lista de las acciones seleccionadas
			  $(formulario).find('select').attr('disabled', false);
			  var acciones_personales = [];

		 		acciones_personales = $("#tablaAccionPersonalGrid").jqGrid('getGridParam','selarrrow');

				if(acciones_personales.length == 0){
					toastr.warning('Debe seleccionar uno o varias acciones personales de vacaciones.');
					return false;
				}else{

  					//Verificar que las acciones seleccionadas sean SOLO de vacaciones
 					$.each(acciones_personales, function(indice, accion){
 						$(formulario).append('<input type="hidden" name="seleccionados['+ indice +']" value="'+ accion.replace( /^\D+/g, '') +'"  />');

					});

				}



			$.ajax({
				url: phost() + 'planilla/ajax-crear-planillaNoRegulares',
				data:  $(formulario).serialize()+'&tipo_creacion='+tipo_planilla_creacion,
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json) {
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				 if(json.response == true){
 						window.location.href = phost()+'planilla/listar';

				}else{
					toastr.error(json.mensaje);
				}


			});
		 }

	};

	return{
		init: function() {
			campos();
			eventos();
		},
		limpiarFormulario: function(){
 			limpiarFormulario();
		},

	};
})();

planilla.init();
