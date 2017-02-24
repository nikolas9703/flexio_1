
  		var verReporte = (function(){

		  	var formulario = '#crearPlanilla';
			var opcionesModal = $('#opcionesModal');
			var pagoEspecialModal = '#pagoEspecialModal';
			var botones = {
					imprimirTalonariosDecimo: "#ImprimirBtnTalonarioDecimo",
					imprimirTalonarios: "#ImprimirBtnTalonario",
					ExportarDetalles: "#ExportarBtnDetalles"
 			};

			$(botones.imprimirTalonarios).on("click", function(e){
 				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
  				window.location.href =  phost() + 'planilla/ajax-imprimir-talonario?planilla_id='+planilla_id+'&colaborador_id='+colaborador_id;
 		 });
			$(botones.imprimirTalonariosDecimo).on("click", function(e){
 				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
  				window.location.href =  phost() + 'planilla/ajax-imprimir-talonario-decimo?planilla_id='+planilla_id+'&colaborador_id='+colaborador_id;
 		 });
			$(botones.ExportarDetalles).on("click", function(e){
 				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
 				   window.location.href =  phost() + 'planilla/exportar_ver_reporte_cerrada?planilla_id='+planilla_id+'&colaborador_id='+colaborador_id;
    		 });

			return{
				init: function() {
				//	campos();
					//eventos();
				}
 			};
		})();

		verReporte.init();
