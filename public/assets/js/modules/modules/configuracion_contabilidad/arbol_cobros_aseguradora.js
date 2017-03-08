var arbolCuentaActivo = {
	rutas:{
		cuentaActivo: phost() +'contabilidad/ajax-cuenta-activo',
		guardarcuentaCobro: phost() + 'configuracion_contabilidad/ajax-cuenta-activo'
	},
	vista:{
		guardarCuenta: $('button#btnGuardarAseguradoraCobrar'),
		//botonCancelar: $('table#aseguradora_cobrar').find('button#btnCancelar'),
		divCuentas: $('table#aseguradora_cobrar').find('div#aseguradoras_cobrar'),
		eliminarCuenta: 'a#close_aseguradora_cobrar'
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
		var cuentas = moduloConfiguracionContabilidad.getCuentaActivos();
		cuentas.done(function(data){
			self.treeRender(data);
		});
		self.vista.guardarCuenta.click(function(){
			var cuenta_cobrar = $('table#aseguradora_cobrar').find("#id_seleccion").val();
			if(cuenta_cobrar.length === 0){
				console.log("error");
				return false;
			}
			var parametros = {id:cuenta_cobrar};
			var guardar = moduloConfiguracionContabilidad.guardarCuentaAseguradoraCobro(parametros);

			guardar.done(function(data){
				$('table#aseguradora_cobrar').find("#id_seleccion").val("");
				self.mensaje(data);
				if(data.tipo ==='success'){
					var arbol_cuenta = $.jstree.reference($('table#aseguradora_cobrar').find('div#aseguradoras_cobrar'))._model.data;//.disable=true;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != cuenta_cobrar){
							$('table#aseguradora_cobrar').find('div#aseguradoras_cobrar').jstree('disable_node',elem.id);
						}
					});
				}
			});

		});
		$('table#aseguradora_cobrar div#cuenta_seleccionada').on('click',self.vista.eliminarCuenta,function(e){
			//el id
			var cuenta_id = $(this).data('item');
			var parametros = {cuenta_id:cuenta_id};
			var cuentaEliminada = moduloConfiguracionContabilidad.eliminarCuentaAseguradoraCobrar(parametros);
			cuentaEliminada.done(function(data){
				if(!data.puede_eliminar){
					swal("Cuenta por Cobrar", "La cuenta no se puede eliminar porque tiene transacciones", "warning");
				}else{
					self.mensaje(data);
					$('table#aseguradora_cobrar').find('div.item-cuenta').fadeIn('slow');
					$('table#aseguradora_cobrar').find('div.item-cuenta').remove();
					var arbol_cuenta = $.jstree.reference($('div#aseguradoras_cobrar'))._model.data;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.es_padre){
							$('table#aseguradora_cobrar').find('div#aseguradoras_cobrar').jstree('disable_node',elem.id);
						}else{
							$('table#aseguradora_cobrar').find('div#aseguradoras_cobrar').jstree('enable_node',elem.id);
						}
					});
					$('table#aseguradora_cobrar').find('li#'+cuenta_id+' a').removeClass('jstree-clicked');
					$('table#aseguradora_cobrar').find('li#'+cuenta_id+' div').removeClass('jstree-wholerow-clicked');
				}
			});
		});
		/*self.vista.botonCancelar.click(function(){
			console.log('log()');
		});*/
	},
	treeRender:function(data){
		var arbol = data;
		$('div#aseguradoras_cobrar').jstree(arbol);
		$('div#aseguradoras_cobrar').jstree(true).redraw(true);
		$('div#aseguradoras_cobrar').bind("loaded.jstree",
				function (event, data){
			$('table#aseguradora_cobrar').find("a:contains('1. Activos')").css("visibility","hidden");
			$('table#aseguradora_cobrar').find(".jstree-last .jstree-icon").first().hide();
			//$('div#aseguradoras_cobrar').jstree('select_node', 11);
			//busca el selecionado
			var selecionado = moduloConfiguracionContabilidad.getCuentaAseguradoraCobrar();
			selecionado.done(function(data){
				var obj =data;
				if(!_.isEmpty(obj)){
					$('table#aseguradora_cobrar').find('div#aseguradoras_cobrar').jstree('select_node',obj[0].cuenta_id);
					//$.jstree.reference($('div#aseguradoras_cobrar'))._model.default_state.disabled=true;
					var arbol_cuenta = $.jstree.reference($('div#aseguradoras_cobrar'))._model.data;//.disable=true;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != obj[0].cuenta_id){
							$('table#aseguradora_cobrar').find('div#aseguradoras_cobrar').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
		$('table#aseguradora_cobrar').find('div#aseguradoras_cobrar').bind("select_node.jstree", function(e, data) {

			var cuenta = data.node;
			var contenido = '';

			$('table#aseguradora_cobrar').find('#id_seleccion').val(cuenta.id);
			contenido = '<div class="item-cuenta"><div class="pull-left icono-cerrar"><a id="close_aseguradora_cobrar" data-item="'+cuenta.id+'"><i class="fa fa-close fa-lg fa-border "></i></a></div><div class="pull-left text-right cuenta-texto">'+cuenta.text+'</div></div>';
			$('table#aseguradora_cobrar').find('#cuenta_seleccionada').html(contenido);

			//console.log($('ul.jstree-children').find('li'));
		});
		/*$('div#aseguradoras_cobrar').bind('hover_node.jstree', function (e, data) {
   console.log("changed",data);
 });*/
	}
};

(function(){
	arbolCuentaActivo.init();
})();
