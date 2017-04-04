var configCajaMenuda = {
	
	rutas:{
		cuentaActivo: phost() +'contabilidad/ajax-cuenta-activo',
		guardarcuentaCaja: phost() + 'configuracion_contabilidad/ajax-cuenta-activo'
	},
	vista:{
		guardarCuenta: $('#Cajamenuda').find('#guardarBtn'),
		botonCancelar: $('table#caja_menuda').find('button#btnCancelar'),
		divCuentas: $('table#caja_menuda').find('div#cuentas_activo'),
		eliminarCuenta: 'a#close_cuenta'
	},
	init:function(){
		this.cargar_cuenta();
	},
	mensaje:function(data){
		if(data.tipo ==='success'){
			toastr.success(data.mensaje);
		}else if(data.tipo ==='error'){
			toastr.error(data.mensaje);
		}else if(data.tipo ==='info'){
			toastr.info(data.mensaje);
		}
	},
	cargar_cuenta:function()
	{
		var self = this;
		var cuentas = moduloConfiguracionContabilidad.getCuentaActivos();
		
		cuentas.done(function(data){
			
			//Inicializar Plugin
			self.treeRender(data);
		});
		
		//Evento click
		self.vista.guardarCuenta.on('click', function(){
			var cuenta_cobrar = $('table#caja_menuda').find("#id_seleccion").val();
			
			if(cuenta_cobrar.length === 0){
				return false;
			}
			
			var parametros = {id:cuenta_cobrar};
			var guardar = moduloConfiguracionContabilidad.guardarCuentaCajaMenuda(parametros);
			
			guardar.done(function(data){
				$('table#caja_menuda').find("#id_seleccion").val("");
				self.mensaje(data);
				if(data.tipo ==='success'){
					var arbol_cuenta = $.jstree.reference($('table#caja_menuda').find('div#cuentas_activo'))._model.data;//.disable=true;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != cuenta_cobrar){
							$('table#caja_menuda').find('div#cuentas_activo').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
		
		$('table#caja_menuda').on('click', self.vista.eliminarCuenta, function(e){
			//el id
			var cuenta_id = $(this).data('item');
			var parametros = {cuenta_id:cuenta_id};
			var cuentaEliminada = moduloConfiguracionContabilidad.eliminarCuentaCajaMenuda(parametros);
			
			cuentaEliminada.done(function(data){
				if(!data.puede_eliminar){
					swal("Caja Menuda", "La cuenta no se puede eliminar porque tiene transacciones", "warning");
				}else{
					self.mensaje(data);
					$('table#caja_menuda').find('div.item-cuenta').fadeIn('slow');
					$('table#caja_menuda').find('div.item-cuenta').remove();
					var arbol_cuenta = $.jstree.reference($('div#cuentas_activo'))._model.data;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.es_padre){
							$('table#caja_menuda').find('div#cuentas_activo').jstree('disable_node',elem.id);
						}else{
							$('table#caja_menuda').find('div#cuentas_activo').jstree('enable_node',elem.id);
						}
					});
					$('table#caja_menuda').find('li#'+cuenta_id+' a').removeClass('jstree-clicked');
					$('table#caja_menuda').find('li#'+cuenta_id+' div').removeClass('jstree-wholerow-clicked');
				}
			});
		});
		
		self.vista.botonCancelar.click(function(){
			//console.log('log()');
		});
	},
	treeRender:function(data){
		
		var arbol = data;
		$('table#caja_menuda').find('div#cuentas_activo').jstree(arbol);
		$('table#caja_menuda').find('div#cuentas_activo').jstree(true).redraw(true);
		$('table#caja_menuda').find('div#cuentas_activo').bind("loaded.jstree", function (event, data){
			
			$("a:contains('1. Activos')").css("visibility","hidden");
			$(".jstree-last .jstree-icon").first().hide();
			
			//busca el selecionado
			var selecionado = moduloConfiguracionContabilidad.getSeleccionCajaMenuda();
			selecionado.done(function(data){
				var obj =data;
				if(!_.isEmpty(obj)){
					$('table#caja_menuda').find('div#cuentas_activo').jstree('select_node',obj[0].cuenta_id);
					
					var arbol_cuenta = $.jstree.reference($('table#caja_menuda').find('div#cuentas_activo'))._model.data;//.disable=true;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != obj[0].cuenta_id){
							$('table#caja_menuda').find('div#cuentas_activo').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
		
		$('table#caja_menuda').find('div#cuentas_activo').bind("select_node.jstree", function(e, data) {
			var cuenta = data.node;
			var contenido = '';

			$('table#caja_menuda').find('#id_seleccion').val(cuenta.id);
			contenido = '<div class="item-cuenta"><div class="pull-left icono-cerrar"><a id="close_cuenta" data-item="'+cuenta.id+'"><i class="fa fa-close fa-lg fa-border "></i></a></div><div class="pull-left text-right cuenta-texto">'+cuenta.text+'</div></div>';
			$('table#caja_menuda').find('#cuenta_seleccionada').html(contenido);
		});
	}
};

configCajaMenuda.init();
