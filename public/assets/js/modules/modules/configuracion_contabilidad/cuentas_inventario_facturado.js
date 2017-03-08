var configCuentaInventarioFacturado = {
	vista:{
		guardarCuenta: $('button#btnGuardarInventarioFacturado'),
		eliminarCuenta: 'a#close_inventario_facturado_activo'
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

		//function de Guaradar
		self.vista.guardarCuenta.click(function(){
			var cuenta_cobrar = $('table#cuentas_activo_inventario_facturado').find("#id_seleccion").val();
			if(cuenta_cobrar.length === 0){
				console.log("error");
				return false;
			}
			var parametros = {id:cuenta_cobrar,tipo:'facturado_activo'};
      //guardar cuenta
			var guardar = moduloConfiguracionContabilidad.guardarCuentaInventario(parametros);
      guardar.success(function(){
        self.vista.guardarCuenta.html('<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...');
      });
			guardar.done(function(data){
        self.vista.guardarCuenta.html('Guardar');
				$('table#cuentas_activo_inventario_facturado').find("#id_seleccion").val("");
				self.mensaje(data);
				if(data.tipo ==='success'){
					var arbol_cuenta = $.jstree.reference($('table#cuentas_activo_inventario_facturado').find('div#cuentas_inventario_facturado'))._model.data;//
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != cuenta_cobrar){
							$('table#cuentas_activo_inventario_facturado').find('div#cuentas_inventario_facturado').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
    //function de eliminar  cuenta_seleccionada
		$('table#cuentas_activo_inventario_facturado div#cuenta_seleccionada').on('click',this.vista.eliminarCuenta,function(e){
			var cuenta_id = $(this).data('item');
			var parametros = {cuenta_id:cuenta_id,tipo:'facturado_activo'};
			var cuentaEliminada = moduloConfiguracionContabilidad.eliminarCuentaInventario(parametros);
			cuentaEliminada.done(function(data){
				if(!data.puede_eliminar){
					swal("Cuenta por Pagar", "La cuenta no se puede eliminar porque tiene transacciones", "warning");
				}else{
					self.mensaje(data);
					$('table#cuentas_activo_inventario_facturado').find('div.item-cuenta').fadeIn('slow');
					$('table#cuentas_activo_inventario_facturado').find('div.item-cuenta').remove();
					var arbol_cuenta = $.jstree.reference($('div#cuentas_inventario_facturado'))._model.data;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.es_padre){
							$('table#cuentas_activo_inventario_facturado').find('div#cuentas_inventario_facturado').jstree('disable_node',elem.id);
						}else{
							$('table#cuentas_activo_inventario_facturado').find('div#cuentas_inventario_facturado').jstree('enable_node',elem.id);
						}
					});
					$('table#cuentas_activo_inventario_facturado').find('li#'+cuenta_id+' a').removeClass('jstree-clicked');
					$('table#cuentas_activo_inventario_facturado').find('li#'+cuenta_id+' div').removeClass('jstree-wholerow-clicked');
				}
			});
		});
	},
	treeRender:function(data){
		var arbol = data;
		$('div#cuentas_inventario_facturado').jstree(arbol);
		$('div#cuentas_inventario_facturado').jstree(true).redraw(true);
		$('div#cuentas_inventario_facturado').bind("loaded.jstree",
				function (event, data){
			$('table#cuentas_activo_inventario_facturado').find("a:contains('1. Activo')").first().css("visibility","hidden");
			$('table#cuentas_activo_inventario_facturado').find(".jstree-last .jstree-icon").first().hide();

			//busca el selecionado
		var selecionado = moduloConfiguracionContabilidad.getCuentaInventario({tipo:'facturado_activo'});
			selecionado.done(function(data){
				var obj =data;
				if(!_.isEmpty(obj)){
					$('table#cuentas_activo_inventario_facturado').find('div#cuentas_inventario_facturado').jstree('select_node',obj[0].cuenta_id);
					var arbol_cuenta = $.jstree.reference($('div#cuentas_inventario_facturado'))._model.data;//.disable=true;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != obj[0].cuenta_id){
							$('table#cuentas_activo_inventario_facturado').find('div#cuentas_inventario_facturado').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
		$('table#cuentas_activo_inventario_facturado').find('div#cuentas_inventario_facturado').bind("select_node.jstree", function(e, data) {

			var cuenta = data.node;
			var contenido = '';

			$('table#cuentas_activo_inventario_facturado').find('#id_seleccion').val(cuenta.id);
			contenido = '<div class="item-cuenta"><div class="pull-left icono-cerrar"><a id="close_inventario_facturado_activo" data-item="'+cuenta.id+'"><i class="fa fa-close fa-lg fa-border "></i></a></div><div class="pull-left text-right cuenta-texto">'+cuenta.text+'</div></div>';
			$('table#cuentas_activo_inventario_facturado').find('#cuenta_seleccionada').html(contenido);

		});
	}
};

(function(){
	configCuentaInventarioFacturado.init();
})();
