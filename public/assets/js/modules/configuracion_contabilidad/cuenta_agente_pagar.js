var configCuentaPorPagar = {
	vista:{
		guardarCuentaPagarProvedores: $('button#btnGuardarAgentePagar'),
		eliminarCuentaProvedores: 'a#close_agente_pagar'
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
		var cuentas = moduloConfiguracionContabilidad.getCuentaPasivo();
		cuentas.done(function(data){
			self.treeRender(data);
		});
		self.vista.guardarCuentaPagarProvedores.click(function(){
			var cuenta_cobrar = $('table#agente_pagar').find("#id_seleccion").val();
			if(cuenta_cobrar.length === 0){
				console.log("error");
				return false;
			}
			var parametros = {id:cuenta_cobrar};
      //guardar cuenta
			var guardar = moduloConfiguracionContabilidad.guardarCuentaAgentePagar(parametros);
      //var blanda = this.ladda();
      guardar.success(function(){
        //blanda.ladda( 'start' );
      });
			guardar.done(function(data){
         //blanda.ladda('stop');
				$('table#agente_pagar').find("#id_seleccion").val("");
				self.mensaje(data);
				if(data.tipo ==='success'){
					var arbol_cuenta = $.jstree.reference($('table#agente_pagar').find('div#angentes_pagar'))._model.data;//
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != cuenta_cobrar){
							$('table#agente_pagar').find('div#angentes_pagar').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
    //function de eliminar  cuenta_seleccionada
		$('table#agente_pagar div#cuenta_seleccionada').on('click',this.vista.eliminarCuentaProvedores,function(e){
			var cuenta_id = $(this).data('item');
			var parametros = {cuenta_id:cuenta_id};
			var cuentaEliminada = moduloConfiguracionContabilidad.eliminarCuentaAgentepagar(parametros);
			cuentaEliminada.done(function(data){
				if(!data.puede_eliminar){
					swal("Cuenta por Pagar", "La cuenta no se puede eliminar porque tiene transacciones", "warning");
				}else{
					self.mensaje(data);
					$('table#agente_pagar').find('div.item-cuenta').fadeIn('slow');
					$('table#agente_pagar').find('div.item-cuenta').remove();
					var arbol_cuenta = $.jstree.reference($('div#angentes_pagar'))._model.data;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.es_padre){
							$('table#agente_pagar').find('div#angentes_pagar').jstree('disable_node',elem.id);
						}else{
							$('table#agente_pagar').find('div#angentes_pagar').jstree('enable_node',elem.id);
						}
					});
					$('table#agente_pagar').find('li#'+cuenta_id+' a').removeClass('jstree-clicked');
					$('table#agente_pagar').find('li#'+cuenta_id+' div').removeClass('jstree-wholerow-clicked');
				}
			});
		});
	},
	treeRender:function(data){
		var arbol = data;
		$('div#angentes_pagar').jstree(arbol);
		$('div#angentes_pagar').jstree(true).redraw(true);
		$('div#angentes_pagar').bind("loaded.jstree",
				function (event, data){
			$('table#agente_pagar').find("a:contains('2. Pasivo')").first().css("visibility","hidden");
			$('table#agente_pagar').find(".jstree-last .jstree-icon").first().hide();

			//busca el selecionado
		var selecionado = moduloConfiguracionContabilidad.getCuentaAgentePagar();
			selecionado.done(function(data){
				var obj =data;
				if(!_.isEmpty(obj)){
					$('table#agente_pagar').find('div#angentes_pagar').jstree('select_node',obj[0].cuenta_id);
					var arbol_cuenta = $.jstree.reference($('div#angentes_pagar'))._model.data;//.disable=true;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != obj[0].cuenta_id){
							$('table#agente_pagar').find('div#angentes_pagar').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
		$('table#agente_pagar').find('div#angentes_pagar').bind("select_node.jstree", function(e, data) {

			var cuenta = data.node;
			var contenido = '';

			$('table#agente_pagar').find('#id_seleccion').val(cuenta.id);
			contenido = '<div class="item-cuenta"><div class="pull-left icono-cerrar"><a id="close_agente_pagar" data-item="'+cuenta.id+'"><i class="fa fa-close fa-lg fa-border "></i></a></div><div class="pull-left text-right cuenta-texto">'+cuenta.text+'</div></div>';
			$('table#agente_pagar').find('#cuenta_seleccionada').html(contenido);

		});
	}
};

(function(){
	configCuentaPorPagar.init();
})();
