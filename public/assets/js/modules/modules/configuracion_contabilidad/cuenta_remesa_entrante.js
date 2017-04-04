var configCuentaPorPagar = {
	vista:{
		guardarCuentaPagarProvedores: $('button#btnGuardarRemesaEntrante'),
		eliminarCuentaProvedores: 'a#close_remesa_entrante'
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
	cargar_cuenta:function(){
		var self = this;
		//var cuentas = moduloConfiguracionContabilidad.getCuentaPasivo();
		var cuentas = moduloConfiguracionContabilidad.getCuentaIngresos();
		cuentas.done(function(data){
			self.treeRender(data);
		});
		self.vista.guardarCuentaPagarProvedores.click(function(){
			var cuenta_cobrar = $('table#remesa_entrante').find("#id_seleccion").val();
			if(cuenta_cobrar.length === 0){
				console.log("error");
				return false;
			}
			var parametros = {id:cuenta_cobrar};
      //guardar cuenta
			var guardar = moduloConfiguracionContabilidad.guardarCuentaRemesaEntrante(parametros);
      //var blanda = this.ladda();
      guardar.success(function(){
        //blanda.ladda( 'start' );
      });
			guardar.done(function(data){
         //blanda.ladda('stop');
				$('table#remesa_entrante').find("#id_seleccion").val("");
				self.mensaje(data);
				if(data.tipo ==='success'){
					var arbol_cuenta = $.jstree.reference($('table#remesa_entrante').find('div#remesas_entrantes'))._model.data;//
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != cuenta_cobrar){
							$('table#remesa_entrante').find('div#remesas_entrantes').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
    //function de eliminar  cuenta_seleccionada
		$('table#remesa_entrante div#cuenta_seleccionada').on('click',this.vista.eliminarCuentaProvedores,function(e){
			var cuenta_id = $(this).data('item');
			var parametros = {cuenta_id:cuenta_id};
			var cuentaEliminada = moduloConfiguracionContabilidad.eliminarCuentaRemesaEntrante(parametros);
			cuentaEliminada.done(function(data){
				if(!data.puede_eliminar){
					swal("Cuenta por Pagar", "La cuenta no se puede eliminar porque tiene transacciones", "warning");
				}else{
					self.mensaje(data);
					$('table#remesa_entrante').find('div.item-cuenta').fadeIn('slow');
					$('table#remesa_entrante').find('div.item-cuenta').remove();
					var arbol_cuenta = $.jstree.reference($('div#remesas_entrantes'))._model.data;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.es_padre){
							$('table#remesa_entrante').find('div#remesas_entrantes').jstree('disable_node',elem.id);
						}else{
							$('table#remesa_entrante').find('div#remesas_entrantes').jstree('enable_node',elem.id);
						}
					});
					$('table#remesa_entrante').find('li#'+cuenta_id+' a').removeClass('jstree-clicked');
					$('table#remesa_entrante').find('li#'+cuenta_id+' div').removeClass('jstree-wholerow-clicked');
				}
			});
		});
	},
	treeRender:function(data){
		console.log(data);
		var arbol = data;
		$('div#remesas_entrantes').jstree(arbol);
		$('div#remesas_entrantes').jstree(true).redraw(true);
		$('div#remesas_entrantes').bind("loaded.jstree",
			function (event, data){
				$('table#remesa_entrante').find("a:contains('4. Ingresos')").first().css("visibility","hidden");
				$('table#remesa_entrante').find(".jstree-last .jstree-icon").first().hide();

				$('table#remesa_entrante').find("a:contains('4. Ingresos')").dblclick();

				//busca el selecionado
				//var selecionado = moduloConfiguracionContabilidad.getCuentaRemesaEntrante();
				var selecionado = moduloConfiguracionContabilidad.getCuentaRemesaEntrante();
				selecionado.done(function(data){
					var obj =data;
					console.log(obj);
					if(!_.isEmpty(obj)){
						$('table#remesa_entrante').find('div#remesas_entrantes').jstree('select_node',obj[0].cuenta_id);
						var arbol_cuenta = $.jstree.reference($('div#remesas_entrantes'))._model.data;//.disable=true;
						$.each(arbol_cuenta,function(i, elem){
							if(elem.id != obj[0].cuenta_id){
								$('table#remesa_entrante').find('div#remesas_entrantes').jstree('disable_node',elem.id);
							}
						});
					}
				});
			});
			$('table#remesa_entrante').find('div#remesas_entrantes').bind("select_node.jstree", function(e, data) {

				var cuenta = data.node;
				var contenido = '';

				$('table#remesa_entrante').find('#id_seleccion').val(cuenta.id);
				contenido = '<div class="item-cuenta"><div class="pull-left icono-cerrar"><a id="close_remesa_entrante" data-item="'+cuenta.id+'"><i class="fa fa-close fa-lg fa-border "></i></a></div><div class="pull-left text-right cuenta-texto">'+cuenta.text+'</div></div>';
				$('table#remesa_entrante').find('#cuenta_seleccionada').html(contenido);

			}
		);
	}
};

(function(){
	configCuentaPorPagar.init();
})();
