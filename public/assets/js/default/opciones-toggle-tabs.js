//Modulo
var opcionesToggleTabs = (function(){
	
	var init = function(){
		var scope = $('#moduloOpciones').find('ul');
		
		if($(scope).length>0){
			
			$(scope).find('a').click(function (e) {
				e.preventDefault()
				$(this).tab('show')
			});
			
			//Agregar atributos de tabs a href
			$.each($(scope).find('a'), function(index,v){
				if(index==0){
					$(this).closest('li').attr('class', 'active');
				}
				$(this).attr('data-toggle', 'tab');
			});
			
			//Agregar evento para controlar elemento activo
			$(scope).find('a').click(function(e){
				var selected = $(this).closest('li').index();
				$(scope).find('li').not(':eq('+ selected +')').removeAttr('class');
				$(this).closest('li').attr('class', 'active');
				
				//get selected href
			    var href = $(this).attr('data-target');    
			    if(typeof href != 'undefined'){
			    	var target = href.split(',');
			    	$.each(target, function(index, val){
			    		$('#sub-panel').find('.nav-tabs li a[href="'+ $.trim(val) +'"]').trigger('click');
			    	});
			    }
			});
		}
	};
	
	return{	    
		init: function() {
			init();
		}
	};
	
})();
opcionesToggleTabs.init();