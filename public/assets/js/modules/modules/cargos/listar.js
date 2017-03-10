//Listar Cargos
var listarCargos = (function(){

	//Inicializar Eventos
	var eventos = function(){
		//jQuery Daterange
		$("#fecha_desde").datepicker({
			defaultDate: "+1w",
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
				$("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
			}
		});
		$("#fecha_hasta").datepicker({
			defaultDate: "+1w",
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			numberOfMonths: 1,
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

listarCargos.init();
