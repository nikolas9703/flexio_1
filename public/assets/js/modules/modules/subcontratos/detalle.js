//Documentos Modal en  DETALLE 
		$('#moduloOpciones').on("click", "#subirArchivoBtn", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			//Cerrar modal de opciones
            $('#documentosModal').modal('hide');
			
			var subcontrato_id = campo.subcontrato;
			//Inicializar opciones del Modal
			$('#documentosModal').modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			
			var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
		    scope.safeApply(function(){
		    	scope.campos.subcontrato_id = subcontrato_id;
		    });
			$('#documentosModal').modal('show');   
        $('.guardarDocBoton').on("click", function(e){
            console.log("entrara");      
        setTimeout(function(){
            $('#tablaDocumentosGrid').trigger( 'reloadGrid' );
            }, 500);  
        });             
		}); 