$(function() {
 
	$(":input").inputmask();

	$("#tipo_liquidacion, #estado_id, #id_deducciones, #id_acumulados, #deducciones_pagos_normales0, #pagos_aplicables_normales0, #pagos_aplicables_acumulados0, #deducciones_pagos_acumulados0").chosen({
		width: '100%'
	});
	
  	activaTab(tab);
	function activaTab(tab){
	    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
	};
	 
   });
 
