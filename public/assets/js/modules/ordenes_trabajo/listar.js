//Listar Ordenes de Trabajo
var listarOrdenesTrabajo = (function(){

	//Inicializar Eventos
	var eventos = function(){
		//jQuery Daterange
		$("#fecha_desde").datepicker({
			autoclose:true,
			language: "es",
			defaultDate: "+1w",
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			numberOfMonths: 1,
			startView: 1,
			todayHighlight: true,
			onClose: function( selectedDate ) {
				$("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
			}
		});
		$("#fecha_hasta").datepicker({
			autoclose:true,
			language: "es",
			defaultDate: "+1w",
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			numberOfMonths: 1,
			startView: 1,
			onClose: function( selectedDate ) {
				$("#fecha_desde").datepicker( "option", "maxDate", selectedDate );
		    }
		});
	};

	return{	    
		init: function() {
			eventos();
		}
	};
})();

listarOrdenesTrabajo.init();