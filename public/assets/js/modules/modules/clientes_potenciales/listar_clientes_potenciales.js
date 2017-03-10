$(function() {
	//localStorage.setItem('ml-parent-selected', localStorage.getItem('ms-selected'));
	
	//Convertir a Cliente Juridico
	$('#moduloOpciones ul').on("click", "#confirmarConvertirJuridicoBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var clientes = $("#clientesPotencialesGrid").jqGrid('getGridParam','selarrrow');
		
		//Verificar si ha seleccionado algun cliente
		if(clientes.length==0){
			$('body').click();
			return false;
		}
		
		$('body').click();
		
		//Ventana de Confirmacion
		var footer_buttons = ['<div class="row">',
  		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
  		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
  		   '</div>',
  		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
  		   		'<button id="convertirJuridicoBtn" class="btn btn-w-m btn-success btn-block" type="button">Convertir</button>',
  		   '</div>',
  		   '</div>'
  		].join('\n');
		
		var mensaje = clientes.length > 1 ? '¿Esta seguro que desea convertir estos Clientes Potenciales a Clientes Juridicos?' : '¿Esta seguro que desea convertir este Cliente Potencial a Cliente Juridico?';
  	    
  	    //Init boton de opciones
  		$('#optionsModal').find('.modal-title').empty().append('Confirme');
  		$('#optionsModal').find('.modal-body').empty().append(mensaje);
  		$('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
  		$('#optionsModal').modal('show');
	});
	
	//Convertir a Cliente Juridico
	$('#moduloOpciones ul').on("click", "#confirmarConvertirNaturalBtn", function(e){
		 
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var clientes = $("#clientesPotencialesGrid").jqGrid('getGridParam','selarrrow');
		
		//Verificar si ha seleccionado algun cliente
		if(clientes.length==0){
			$('body').click();
			return false;
		}
		
		$('body').click();
		
		//Ventana de Confirmacion
		var footer_buttons = ['<div class="row">',
  		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
  		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
  		   '</div>',
  		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
  		   		'<button id="convertirNaturalBtn" class="btn btn-w-m btn-success btn-block" type="button">Convertir</button>',
  		   '</div>',
  		   '</div>'
  		].join('\n');
		
		var mensaje = clientes.length > 1 ? '¿Esta seguro que desea convertir estos Clientes Potenciales a Clientes Naturales?' : '¿Esta seguro que desea convertir este Cliente Potencial a Cliente Natural?';
  	    
  	    //Init boton de opciones
  		$('#optionsModal').find('.modal-title').empty().append('Confirme');
  		$('#optionsModal').find('.modal-body').empty().append(mensaje);
  		$('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
  		$('#optionsModal').modal('show');
	});
	
	//Pasando cluientes a Naturales
	$('#optionsModal').on("click", "#convertirNaturalBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var clientes = $("#clientesPotencialesGrid").jqGrid('getGridParam','selarrrow');
		
		$.ajax({
			url: phost() + 'clientes_potenciales/ajax-convertir-natural',
			data: {
				erptkn: tkn,
				id_clientes: clientes
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
			if($.isEmptyObject(json.results[0]) == true){
				return false;
			}
			
			$class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
			
			//Mostrar Mensaje
			mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);
			
			//Recargar grid si la respuesta es true
			if(json.results[0]['respuesta'] == true)
			{
				//Recargar Grid
				$("#clientesPotencialesGrid").setGridParam({
					url: phost() + 'clientes_potenciales/ajax-listar-clientes-potenciales',
					datatype: "json",
					postData: {
						nombre: '',
						compania: '',
						telefono: '',
						correo: '',
						erptkn: tkn
					}
				}).trigger('reloadGrid');
			}
		});
		
		$('#optionsModal').modal('show');
	});
	
        
        
        //pasando a forma modular
        var listarClientesPotenciales = (function(){
            
            var data = {
                grid:'#clientesPotencialesGrid',
                modal:'#optionsModal',
                btns:{
                    eliminar:'.eliminarClientePotencialBtn'
                }
            };
            
            var dom = {};
            
            var catchDom = function(){
                
                dom.grid = $(data.grid);
                dom.modal = $(data.modal);
                
            };
            
            var suscribeEvents = function(){
                
                dom.modal.on('click', data.btns.eliminar, events.eEliminar);
                
            };
            
            var events = {
                
                eEliminar:function(e){
                    e.preventDefault();
                    e.returnValue=false;
                    e.stopPropagation();
                    
                    var self = $(this);
                    var post = {
                        erptkn:tkn,
                        id_cliente_potencial:self.data('id')
                    };
                    var url = phost() + 'clientes_potenciales/ajax-eliminar';
                    var metodo = 'eEliminar';
                    
                    ajax(url, post, metodo);
                }
                
            };
            
             var mostrar_mensaje = function(mensaje){
                
                if( $.isEmptyObject(mensaje) === true){
                    return false;
                }
                
                if(mensaje.tipo == "success")
                {
                    toastr.success("¡&Eacute;xito! Se ha procesado correctamente el << Cliente potencial/Clientes potenciales >>.");
                }
                else
                {
                    toastr.error("¡Error! Su solicitud no fue procesada en el << Cliente potencial/Clientes potenciales >>.");
                }
            };
            
            var ajax = function(url, data, metodo){
                $.ajax({
                    url: url,
                    data: data,
                    type: "POST",
                    dataType: "json",
                    cache: false
                }).done(function(json){

                    //Check Session
                    if( $.isEmptyObject(json.session) == false){
                            window.location = phost() + "login?expired";
                    }

                    mostrar_mensaje(json.mensaje);

                    if(metodo == 'eEliminar')
                    {
                        dom.grid.trigger('reloadGrid');
                        dom.modal.modal('hide');
                    }
                });
            };
            
            var initialize = function(){
                catchDom();
                suscribeEvents();
            };

            return{
                init:initialize
            };
            
        })();
        
        listarClientesPotenciales.init();
        
        
	
	//Eliminar Clientes
	$('#optionsModal').on("click", "#convertirJuridicoBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var clientes = $("#clientesPotencialesGrid").jqGrid('getGridParam','selarrrow');
		
		$.ajax({
			url: phost() + 'clientes_potenciales/ajax-convertir-juridico',
			data: {
				erptkn: tkn,
				id_clientes: clientes
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
			if($.isEmptyObject(json.results[0]) == true){
				return false;
			}
			
			$class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
			
			//Mostrar Mensaje
			mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);
			
			//Recargar grid si la respuesta es true
			if(json.results[0]['respuesta'] == true)
			{
				//Recargar Grid
				$("#clientesPotencialesGrid").setGridParam({
					url: phost() + 'clientes_potenciales/ajax-listar-clientes-potenciales',
					datatype: "json",
					postData: {
						nombre: '',
						compania: '',
						telefono: '',
						correo: '',
						erptkn: tkn
					}
				}).trigger('reloadGrid');
			}
		});
		
		$('#optionsModal').modal('show');
	});
	
	
	//Expotar Cliente Potenciales a CSV
	$('#moduloOpciones ul').on("click", "#exportarClientesBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var clientes = $("#clientesPotencialesGrid").jqGrid('getGridParam','selarrrow');
		
		//Verificar si ha seleccionado algun cliente
		if(clientes.length==0){
			return false;
		}
		
		//Convertir array a srting separado por guion
		var clientes_string = clientes.join('-');
		
		//Armar url
		var url = phost() + 'clientes_potenciales/exportar/'+ clientes_string;
		
		//Ejecutar funcion para descargar archivo
		downloadURL(url);
	});
	
	//Eliminar Cliente Potenciales
	$('#moduloOpciones ul').on("click", "#eliminarClientesBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var clientes = $("#clientesPotencialesGrid").jqGrid('getGridParam','selarrrow');
		
		if(clientes.length==0){
			return false;
		}
		
		var footer_buttons = ['<div class="row">',
   		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
   		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
   		   '</div>',
   		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
   		   		'<button id="eliminarClienteBtn" class="btn btn-w-m btn-danger btn-block" type="button">Eliminar</button>',
   		   '</div>',
   		   '</div>'
   		].join('\n');
   	    
   	    //Init boton de opciones
   		$('#optionsModal').find('.modal-title').empty().append('Confirme');
   		$('#optionsModal').find('.modal-body').empty().append('¿Esta seguro que desea eliminar este Cliente Potencial?');
   		$('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
   		$('#optionsModal').modal('show');
	});
	
	
	//Subir documentos
	
	 $('#optionsModal').on("click", "#subirArchivosBtn", function(e){
			
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var uuid_cliente = $(this).attr("data-cliente");
			
			$('#optionsModal').modal('hide');
			$('#crearDocumentoModal').modal('show');
				 
				 
			 $("#input-dim-1").fileinput({
				    uploadUrl:  phost() + "documentos/ajax-subir-archivos",
				    allowedFileExtensions: null,
				    minImageWidth: 50,
				    minImageHeight: 50,
				    uploadAsync: true,
				    maxFileSize: 500,
				    language: 'es',
				    uploadExtraData: function() {
				          return {
				        	  erptkn: tkn,
				        	  uuid_relacion: uuid_cliente,
				        	  modulo: 'clientes_potenciales' 
				          };
				    }
				     
			});
			  $('#input-dim-1').on('filebatchuploadcomplete', function(event, files, extra) {
					    $('#crearDocumentoModal').modal('hide');
						 //location.reload();
			});
			
		});
	
	
	
});

