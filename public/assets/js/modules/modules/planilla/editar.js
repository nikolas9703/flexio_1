
  		var planillaEditar = (function(){

		  var formulario = '#crearPlanilla';
			var opcionesModal = $('#opcionesModal');
			var pagoEspecialModal = '#pagoEspecialModal';
			var botones = {
 		  		//	editarNoRegulares: "#guardarBtnPlanilla",
          	edicionPlanilla: "#guardarBtnPlanilla",
		  			cancelar: "#cancelarBtnPlanilla",
            abrirModalPagosEspecialesLiquidaciones: "#pagarLiquidacionBtn",
		  			abrirModalPagosEspecialesVacaciones: "#pagarVacacionBtn",
		  			abrirModalPagosEspecialesLicencias: "#pagarLicenciaBtn",
		  			abrirModalPagosDecimos: "#pagarDecimoBtn",
		  			confirmacionImprimir: "#confimrarCrearPagoEspecial",
		  			imprimirTalonarios: "#imprimirTalonarios",
		  			exportarPlanillaRegularCerrada: "#exportarPlanillaRegularCerrada",
		  			exportarPlanillaAbierta: "#exportarPlanillaAbierta",
            validaMultipleColab: "#validaMultipleColab"
 			};

			var campos = function(){
        $(".select2").select2();
  		 		$(formulario).validate({
					focusInvalid: true,
					ignore: '',
					wrapper: '',
				});
 		  		$(formulario).find('#rango_fecha1').val("");
				 $(formulario).find('#rango_fecha2').val("");

		  		if(tipo_planilla_creacion == 'liquidaciones' ){
          		  			//////--------------------//////
          		  			$(formulario).find('select[name="deducciones[]"], select[name="acumulados[]"]').rules(
          		 					"add",{
          		 						required: false
          		 			});
          		 			$(formulario).find('select[name="acumulados[]"]').rules(
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

          		  			/////---------------------//////
           					 $(formulario).find('#rango_fecha1').attr('disabled', true);
          					 $(formulario).find('#rango_fecha2').attr('disabled', true);

				}

        if( tipo_planilla_creacion == 'vacaciones' || tipo_planilla_creacion == 'licencias' || tipo_planilla_creacion == 'xiii_mes' ){
          $(formulario).find('select[name="ciclo_id"]').attr('disabled', true);
          $(formulario).find('select[name="centro_contable_id[]"]').attr('disabled', true);
          $(formulario).find('#rango_fecha1').attr('disabled', true);
          $(formulario).find('#rango_fecha2').attr('disabled', true);

          $(formulario).find('select[name="centro_contable_id[]"],select[name="ciclo_id"]').rules(
               "add",{
                 required: false
           });
           $(formulario).find('select[name="deducciones[]"]').rules(
               "add",{
                 required: false
           });
           $(formulario).find('select[name="acumulados[]"] ').rules(
               "add",{
                 required: false
           });
           if(tipo_planilla_creacion == 'xiii_mes'){
                $(formulario).find('select[name="ciclo_id"]').rules(
                    "add",{
                      required: true
                });
           }
           else {
             $(formulario).find('select[name="ciclo_id"]').rules(
                 "add",{
                   required: false
             });
           }
        }


  			$(formulario).find('select[name="ciclo_id"]').attr('disabled', true);
 				$(formulario).find('#tipo_id').val( tipo_planilla_id );
				$(formulario).find('select[name="tipo_id"]').attr('disabled', true);
			};
      $(document).on('keyup', '.select2-search > input.select2-input', function (e) {
        // Close select2 if enter key
         if(e.keyCode === 13) {
            $('select.select2box').select2("enable", false);
            $(selector).jqGrid('saveRow', lastSel, false, 'clientArray');
            }
        });

        $(botones.edicionPlanilla).on("click", function(e){
          e.preventDefault();
          e.returnValue=false;
          e.stopPropagation();
         		$(formulario).find('input, select').attr( "disabled", false );

          		 if ( $(formulario).valid() != false) {
                      $.ajax({
                         url: phost() + 'planilla/ajax-editar-planilla',
                         data: $(formulario).serialize()+'&planilla_id='+planilla_id,
                         type: "POST",
                         dataType: "json",
                         cache: false,
                     }).done(function(json) {
                         //Check Session
                           if( $.isEmptyObject(json.session) == false){
                             window.location = phost() + "login?expired";
                         }
                          if(json.response == true){
                              window.location.href = phost() + 'planilla/listar';

                         }else{
                             toastr.error(json.mensaje);

                         }
                     });
                 }
         });

			$(botones.exportarPlanillaRegularCerrada).on("click", function(e){
 				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

				window.location.href =  phost() + 'planilla/exportar_ver_reporte_cerrada?planilla_id='+planilla_id;
   		 	});
			$(botones.exportarPlanillaAbierta).on("click", function(e){
 				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
 				//window.location.href =  phost() + 'planilla/exportar_csv_abierta?planilla_id='+planilla_id+'&tipo_planilla_creacion='+tipo_planilla_creacion;
 				window.location.href =  phost() + 'planilla/exportar_csv_abierta2?planilla_id='+planilla_id;
   		 	});
 			$(botones.editarNoRegulares).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

				editarPlanilla();
			});

			$(botones.cancelar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				window.location.href = phost()+'planilla/listar';
		 	});


			$(botones.validaMultipleColab).on("click", function(e){

        e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
	 				$(pagoEspecialModal).modal('hide');
 				opcionesModal.modal('show');
					opcionesModal.find('.modal-title').empty().append('Confirme');
				opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea validar todas las hojas de tiempo? Solo se validarán las horas registradas.');
				opcionesModal.find('.modal-footer')
					.empty()
					.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
					.append('<button id="confimrarValidarColaboradores"   data-id="'+ planilla_id +'"  class="btn btn-w-m btn-danger" type="button">Confirmar</button>');
      });
			$(botones.imprimirTalonarios).on("click", function(e){
 		 		e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
	 				$(pagoEspecialModal).modal('hide');
 				opcionesModal.modal('show');
					opcionesModal.find('.modal-title').empty().append('Confirme');
				opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea imprimir todos los talonarios?');
				opcionesModal.find('.modal-footer')
					.empty()
					.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
					.append('<button id="confimrarImprimirTalonarios"   data-id="'+ planilla_id +'"  class="btn btn-w-m btn-danger" type="button">Imprimir PDF</button>');
		 });
     //Funcion que se encarga de exportar el talonario en el pdf de todos los colaboradores
     $('#opcionesModal').on("click", "#confimrarValidarColaboradores", function(e){
         e.preventDefault();
         e.returnValue=false;
         e.stopPropagation();
         opcionesModal.modal('hide');

          $.ajax({
            url: phost() + 'planilla/ajax-validar-multiples',
            data: {
                planilla_id: planilla_id,
                erptkn: tkn,
            },
            type: "POST",
            dataType: "json",
            cache: false,
        }).done(function(json) {
            //Check Session

             if( $.isEmptyObject(json.session) == false){
                window.location = phost() + "login?expired";
            }
            window.location.href =  phost() + 'planilla/listar';
         });

      });
		//Funcion que se encarga de exportar el talonario en el pdf de todos los colaboradores
	 	$('#opcionesModal').on("click", "#confimrarImprimirTalonarios", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				opcionesModal.modal('hide');

				window.location.href =  phost() + 'planilla/ajax-exportar-talonarios-multiples?planilla_id='+planilla_id+'&tipo_planilla='+tipo_planilla_creacion;

		 });


			$('#opcionesModal').on("click", "#confimrarCerrarPagoEspecialConImprimir", function(e){
  				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

 		  		  if ( $(formulario).valid() != false) {
		              $.ajax({
		                 url: phost() + 'planilla/ajax-cerrar-planilla-especial',
		                 data: $(formulario).serialize()+'&planilla_id='+planilla_id+'&tipo_planilla_creacion='+tipo_planilla_creacion,
		                 type: "POST",
		                 dataType: "json",
		                 cache: false,
		             }).done(function(json) {
		                 //Check Session

		                  if( $.isEmptyObject(json.session) == false){
		                     window.location = phost() + "login?expired";
		                 }

		                 if(json.response == true){
		                     //toastr.success(json.mensaje);
		                     window.location.href = phost()+'planilla/listar';

		                 }else{
		                     toastr.error(json.mensaje);

		                 }
		             });
		         }
			});

 			$('#opcionesModal').on("click", "#confimrarCerrarPagoEspecialSinImprimir", function(e){


 				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

 				//CORREGIR ESTE OAGO DE VACACIONES
		  		  if ( $(formulario).valid() != false) {
		              $.ajax({
		                 url: phost() + 'planilla/ajax-cerrar-planilla-especial',
		                 data: $(formulario).serialize()+'&planilla_id='+planilla_id+'&tipo_planilla_creacion='+tipo_planilla_creacion,
		                 type: "POST",
		                 dataType: "json",
		                 cache: false,
		             }).done(function(json) {
		                 //Check Session

		                  if( $.isEmptyObject(json.session) == false){
		                     window.location = phost() + "login?expired";
		                 }

		                 if(json.response == true){
		                     //toastr.success(json.mensaje);
		                     window.location.href = phost()+'planilla/listar';

		                 }else{
		                     toastr.error(json.mensaje);

		                 }
		             });
		         }
			});
  			$(botones.confirmacionImprimir).on("click", function(e){
                                 if(tipo_planilla_creacion == 'liquidaciones')
                                    url = 'planilla/ajax-cerrar-planilla-liquidacion';
                                else
                                    url = 'planilla/ajax-cerrar-planilla-especial';

  				$("div#pagoEspecialModal").find('#confimrarCrearPagoEspecial').attr('disabled', true);

 				e.preventDefault();
 				e.returnValue=false;
 				e.stopPropagation();
	 		       $.ajax({
		                 url: phost() + url,
		                 data: $(formulario).serialize()+'&planilla_id='+planilla_id+'&tipo_planilla_creacion='+tipo_planilla_creacion,
		                 type: "POST",
		                 dataType: "json",
		                 cache: false,
		             }).done(function(json) {
		                 //Check Session

		                  if( $.isEmptyObject(json.session) == false){
		                     window.location = phost() + "login?expired";
		                 }

		                 if(json.response == true){
		                     //toastr.success(json.mensaje);
		                     window.location.href = phost()+'planilla/listar';

		                 }else{
		                     toastr.error(json.mensaje);

		                 }
		             });

 		 	});

			$(botones.abrirModalPagosDecimos).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

		 		opcionesModal.modal('hide');

		 		$.ajax({
		              url: phost() + 'planilla/ajax_detalles_pago_decimo',
		              data: {
		            	  	planilla_id: planilla_id,
 					erptkn: tkn,
			 			},
		              type: "POST",
		              dataType: "json",
		              cache: false,
		          }).done(function(json) {

		               if( $.isEmptyObject(json.session) == false){
		                  window.location = phost() + "login?expired";
		              }
 		              if(json.response == true){
  		            	 $(pagoEspecialModal).find('#total_colaboradores').text(json.total_colaboradores);
 		            	 $(pagoEspecialModal).find('#salario_bruto').text(json.salario_bruto);
  		            	 $(pagoEspecialModal).find('#deducciones').text(json.deducciones);
 		            	 $(pagoEspecialModal).find('#deducciones_porcentaje').text(json.deducciones_porcentaje);
 		            	 $(pagoEspecialModal).find('#deducciones_progress_bar').width(json.deducciones_progress_bar);
 		            	 $(pagoEspecialModal).find('#salario_neto').text(json.salario_neto);
		            	 $(pagoEspecialModal).find('#salario_neto_porcentaje').text(json.salario_neto_porcentaje);
		            	 $(pagoEspecialModal).find('#salario_neto_progress_bar').width(json.salario_neto_progress_bar);

		               }else{
		                  toastr.error(json.mensaje);
		               }
 		          });

		 		$(pagoEspecialModal).modal('show');

 			});

 			$(botones.abrirModalPagosEspecialesLicencias).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

		 		opcionesModal.modal('hide');

		 		$.ajax({
		              url: phost() + 'planilla/ajax_detalles_pago_especiales',
		              data: {
		            	  	planilla_id: planilla_id,
		            	  	//cantidad_semanas:cantidad_semanas,
		            	  	tipo:"licencias",
							erptkn: tkn,
			 			},
		              type: "POST",
		              dataType: "json",
		              cache: false,
		          }).done(function(json) {

		               if( $.isEmptyObject(json.session) == false){
		                  window.location = phost() + "login?expired";
		              }
 		              if(json.response == true){
  		            	 $(pagoEspecialModal).find('#total_colaboradores').text(json.total_colaboradores);
 		            	 $(pagoEspecialModal).find('#salario_bruto').text(json.salario_bruto);
  		            	 $(pagoEspecialModal).find('#deducciones').text(json.deducciones);
 		            	 $(pagoEspecialModal).find('#deducciones_porcentaje').text(json.deducciones_porcentaje);
 		            	 $(pagoEspecialModal).find('#deducciones_progress_bar').width(json.deducciones_progress_bar);
 		            	 $(pagoEspecialModal).find('#salario_neto').text(json.salario_neto);
		            	 $(pagoEspecialModal).find('#salario_neto_porcentaje').text(json.salario_neto_porcentaje);
		            	 $(pagoEspecialModal).find('#salario_neto_progress_bar').width(json.salario_neto_progress_bar);

		               }else{
		                  toastr.error(json.mensaje);
		               }
 		          });

		 		$(pagoEspecialModal).modal('show');

 			});
 			$(botones.abrirModalPagosEspecialesLiquidaciones).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
 		 		opcionesModal.modal('hide');

		 		$.ajax({
                                url: phost() + 'planilla/ajax-modal-liquidacion',
                                data: {
		            	  planilla_id: planilla_id,
 				  erptkn: tkn,
                                },
                                type: "POST",
                                dataType: "json",
                                cache: false,
		          }).done(function(json) {

		              if( $.isEmptyObject(json.session) == false){
		                  window.location = phost() + "login?expired";
		              }
 		              if(json.response == true){
  		            	 $(pagoEspecialModal).find('#total_colaboradores').text(json.total_colaboradores);
 		            	 $(pagoEspecialModal).find('#salario_bruto').text(json.salario_bruto);

 		            	 $(pagoEspecialModal).find('#deducciones').text(json.deducciones);
 		            	 $(pagoEspecialModal).find('#deducciones_porcentaje').text(json.deducciones_porcentaje);
 		            	 $(pagoEspecialModal).find('#deducciones_progress_bar').width(json.deducciones_progress_bar);

 		            	 $(pagoEspecialModal).find('#salario_neto').text(json.salario_neto);
		            	 $(pagoEspecialModal).find('#salario_neto_porcentaje').text(json.salario_neto_porcentaje);
		            	 $(pagoEspecialModal).find('#salario_neto_progress_bar').width(json.salario_neto_progress_bar);
		               }else{
		                  toastr.error(json.mensaje);
		               }
 		          });

		 		$(pagoEspecialModal).modal('show');

			});

			$(botones.abrirModalPagosEspecialesVacaciones).on("click", function(e){

 				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

		 		opcionesModal.modal('hide');

		 		$.ajax({
		              url: phost() + 'planilla/ajax_detalles_pago_especiales',
		              data: {
		            	  	planilla_id: planilla_id,
		            	  	//cantidad_semanas:cantidad_semanas,
		            		tipo:"vacaciones",
							erptkn: tkn,
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

 		            	 $(pagoEspecialModal).find('#total_colaboradores').text(json.total_colaboradores);
 		            	 $(pagoEspecialModal).find('#salario_bruto').text(json.salario_bruto);
  		            	 $(pagoEspecialModal).find('#deducciones').text(json.deducciones);
 		            	 $(pagoEspecialModal).find('#deducciones_porcentaje').text(json.deducciones_porcentaje);
 		            	 $(pagoEspecialModal).find('#deducciones_progress_bar').width(json.deducciones_progress_bar);
 		            	 $(pagoEspecialModal).find('#salario_neto').text(json.salario_neto);
		            	 $(pagoEspecialModal).find('#salario_neto_porcentaje').text(json.salario_neto_porcentaje);
		            	 $(pagoEspecialModal).find('#salario_neto_progress_bar').width(json.salario_neto_progress_bar);

		               }else{
		                  toastr.error(json.mensaje);
		               }
 		          });

		 		$(pagoEspecialModal).modal('show');

			});
			var editarPlanilla = function(){
				$(formulario).find('input, select').attr( "disabled", false );
				var id_tipo_planilla = $(formulario).find('#tipo_id').val(); //81: Liquidacion

				if(id_tipo_planilla == 81){
					 var url_ajax =  phost() + 'planilla/ajax-editar-planillaNoRegular-liquidacion';
				}
				else{
					 var url_ajax =  phost() + 'planilla/ajax-editar-planillaNoRegular';
				}
 		  		 if ( $(formulario).valid() != false) {
 		              $.ajax({
		                 url: url_ajax,
		                 data: $(formulario).serialize()+'&planilla_id='+planilla_id,
		                 type: "POST",
		                 dataType: "json",
		                 cache: false,
		             }).done(function(json) {
		                 //Check Session

		                  if( $.isEmptyObject(json.session) == false){
		                     window.location = phost() + "login?expired";
		                 }
 		                 if(json.response == true){
		                     //toastr.success(json.mensaje);
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
					//eventos();
				}
 			};
		})();

		planillaEditar.init();
