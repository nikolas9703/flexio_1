// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var enviarCorreo = (function(){


    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {}


    $("#EnviarAProveedor").on("click", function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();

      var orden_id 			= $("input[name*='campo[id]']").val();
      var correo 			= $("input[name*='campo[correo_proveedor]']").val();
       var codigo =  $("input[name*='campo[codigo]']").val();

    var nombre =  $( "#proveedor_id option:selected" ).text();
      swal({
          title: "<i class='fa fa-envelope-o'></i> "+'Enviar: '+codigo,
          text: "Proveedor: "+nombre,
          html: true,
          type: "input",
          inputValue: correo,
          value: '',
          showCancelButton: true,
          confirmButtonText: "Enviar",
          cancelButtonText: "Cancelar",
          closeOnConfirm: false,
          animation: "slide-from-top",
          confirmButtonColor: "#0070b9",
          cancelButtonColor:"#999898",
          inputPlaceholder: "Correo Electrónico",
          showLoaderOnConfirm: true,
          //closeOnConfirm: false
     }, function(inputValue){
         if (inputValue === false)
           return false;

         if (inputValue === "") {
           swal.showInputError("Introduzca el correo, por favor!");
           return false
         }


        		$.ajax({
       			url: phost() + 'ordenes/ajax-enviar-correo',
       			data: {
       				erptkn: tkn,
       				correo: inputValue,
              orden_id: orden_id
       			},
       			type: "POST",
       			dataType: "json",
       			cache: false,
       		}).done(function(json) {

       				 //Check Session
       				if( $.isEmptyObject(json.session) == false){
       					window.location = phost() + "login?expired";
       				}
       				//If json object is empty.
       				if($.isEmptyObject(json) == true){
       					return false;
       				}

       				//Mostrar Mensaje
       				if(json.response == "" || json.response == undefined || json.response == 0){
       					toastr.error(json.mensaje);
       				}else{
                //setTimeout(function(){     swal("Operacion Finalizada!");   }, 2000);
       					//toastr.success(json.mensaje);
                  setTimeout(function(){     swal("Éxito: Operacion Finalizada!");   }, 2000);
       				}


       		});



        }).trigger('reloadGrid');


    });

    var catchDom = function(){
     };
    var initialize = function(){
        catchDom();

    };

    /* Retorna un objeto literal con el método init haciendo referencia a la
       función initialize. */
    return{
        init:initialize
    }
})();


// Ejecutando el método "init" del módulo tabs.
enviarCorreo.init();
