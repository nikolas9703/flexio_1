/**
 * Controlador Formulario Crear/Editar Rol
 */
bluapp.controller("crearRolFormCtrl", function($scope, $http, $rootScope){

	$scope.rol = {
		erptkn: tkn,
		rol_id: "",
		nombre: "",
		descripcion: "",
		superusuario: 0,
		defaultRol: 0,
		guardarFormBtn: "Guardar"
	};

	//Copiar los valores vacios del formulario
	$scope.emptyfields = angular.copy($scope.rol);

	//Event: Guardar Departamento
    $scope.check = function(e){

		if($('#defaultRol').is(':checked') == true){
			$scope.rol.defaultRol = 1
		}else{
			$scope.rol.defaultRol = 0;
		}
    };

	//Event: Guardar Departamento
    $scope.guardar = function(e){
		e.preventDefault();

		//Mostrar en el Boton Progreso
		$('#guardarFormBtn').addClass('disabled').empty().append('<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...');

		if($('#crearRolForm').validate().form() == true)
		{
			var url = window.phost() +'roles/ajax-crear-rol';
			$http({
				 method: 'POST',
				 url: url,
				 data: $.param($scope.rol),
				 cache: false,
				 xsrfCookieName: 'erptknckie_secure',
				 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function (results) {

				if(results.data.id == false && results.data.id == undefined){
					toastr.error(results.data.mensaje);
				}else{
					toastr.success(results.data.mensaje);
				}

				//Recargar Grid
				$("#rolesGrid").setGridParam({
					url: phost() + 'roles/ajax-listar',
					datatype: "json",
					postData: {
						nombre: '',
						descripcion: '',
						erptkn: tkn
					}
				}).trigger('reloadGrid');

				 //Habilitar boton
			    $('#guardarFormBtn').removeClass('disabled').empty().append('Guardar');
		    });

			//Hacer un reset de los valores del formulario
			$scope.rol = angular.copy($scope.emptyfields);
		    $scope.crearRolForm.$setPristine();

		  //reiniciar seleccion
			//campo super usuario
		    setTimeout(function(){
			    if($("#superusuario").is(':not(:checked)')){
			    	switchery.bindClick();
			    }
		    }, 800);
		}
		else
		{
			 //Habilitar boton
		    $('#guardarFormBtn').removeClass('disabled').empty().append('Guardar');
		}
    };

  //Event: Cancelar Formulario
    $scope.cancelar = function(e){
		e.preventDefault();

		//Hacer un reset de los valores del formulario
		$scope.rol = angular.copy($scope.emptyfields);
	    $scope.crearRolForm.$setPristine();

	    //reiniciar seleccion
		//campo super usuario
	    setTimeout(function(){
		    if($("#superusuario").is(':not(:checked)')){
		    	switchery.bindClick();
		    }
	    }, 800);
    };
});

$(function() {

	var superuserCheckbox = document.querySelector('.js-switch');
	var scope = angular.element($("#crearRolBox")).scope();

	superuserCheckbox.onchange = function() {
	  //$(this).attr("checked", superuserCheckbox.checked);
		scope.rol.superusuario = (superuserCheckbox.checked==true ? 1 : 0);
	};

	//-------------------------
	// Redimensioanr Grid al cambiar tama�o de la ventanas.
	//-------------------------
	$(window).resizeEnd(function() {
		$(".ui-jqgrid").each(function(){
			var w = parseInt( $(this).parent().width()) - 6;
			var tmpId = $(this).attr("id");
			var gId = tmpId.replace("gbox_","");
			$("#"+gId).setGridWidth(w);
		});
	});

	//-------------------------
	// Botones de formulario de Busqueda
	//-------------------------
	$('#searchBtn').bind('click', searchBtnHlr);
	$('#clearBtn').click(function(e){
		e.preventDefault();

		$("#rolesGrid").setGridParam({
			url: phost() + 'roles/ajax-listar',
			datatype: "json",
			postData: {
				nombre: '',
				estado: '',
				descripcion: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');

		//Reset Fields
		$('#nombre, #descripcion,  #estado').val('');
	});

	//-------------------------
	// Formulario de Crear Rol
	//-------------------------
	$('#crearRolForm').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: ''
	});
});

function searchBtnHlr(e) {
	e.preventDefault();
	$('#searchBtn').unbind('click', searchBtnHlr);

	var nombre 		= $('#nombre').val();
	var descripcion = $('#descripcion').val();
	var estado = $('#estado').val();

	if(nombre != "" || descripcion != "" || estado != "")
	{
		$("#rolesGrid").setGridParam({
			url: phost() + 'roles/ajax-listar',
			datatype: "json",
			postData: {
				nombre: nombre,
				descripcion: descripcion,
				estado: estado,
				erptkn: tkn
			}
		}).trigger('reloadGrid');

		$('#searchBtn').bind('click', searchBtnHlr);
	}else{
		$('#searchBtn').bind('click', searchBtnHlr);
	}
}
