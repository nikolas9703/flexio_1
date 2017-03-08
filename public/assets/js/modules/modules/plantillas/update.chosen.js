
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var moduloUpdateChosen = (function() {
	return {
            actualizar:function(){
                setTimeout(function(){
                    $("#plantillaForm").find('#plantilla_id').trigger('change');
                },1000);
                
            }
	};
})();
