$(function(){

    //Ejecutar al remover un permiso ya existente.
    $('#roleForm').on('change', '.remove_perm', function(e){
    	e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		//Set delete form
	 	$('#deletePermisoForm').find('#permiso').prop("name", this.name).prop("value", this.value);

	 	$.ajax({
			url: phost() + 'roles/ajax-eliminar-permiso',
			data: $('#deletePermisoForm').serialize(),
			type: "POST",
			dataType: "json",
			cache: false,
		}).done(function(json) {

			//Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}

			//If json object is not empty.
			if( $.isEmptyObject(json.results[0]) == false ){
			}else{
			}

			$('#deletePermisoForm').find('#permiso').prop("name", '').prop("value", '');
		});

	});
});