var configInventarioRecibidoActivo = {
	vista:{
		guardarCuenta: $('button#btnGuardarInventarioRecibidoActivo'),
		eliminarCuenta: 'a#close_inventario_sin_factura_activo',
		tabla: $('#cuentas_activo_inventario_recibido_activo')
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
			var cuenta_cobrar = self.vista.tabla.find("#id_seleccion").val();
			if(cuenta_cobrar.length === 0){
				console.log("error");
				return false;
			}
			var parametros = {id:cuenta_cobrar,tipo:'sin_factura_activo'};
      //guardar cuenta
			var guardar = moduloConfiguracionContabilidad.guardarCuentaInventario(parametros);
      guardar.success(function(){
        self.vista.guardarCuenta.html('<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...');
      });
			guardar.done(function(data){
        self.vista.guardarCuenta.html('Guardar');
				self.vista.tabla.find("#id_seleccion").val("");
				self.mensaje(data);
				if(data.tipo ==='success'){
					var arbol_cuenta = $.jstree.reference(self.vista.tabla.find('div#inventario_recibido_activo'))._model.data;//
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != cuenta_cobrar){
							self.vista.tabla.find('div#inventario_recibido_activo').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
    //function de eliminar  cuenta_seleccionada
		$('table#cuentas_activo_inventario_recibido_activo div#cuenta_seleccionada').on('click',this.vista.eliminarCuenta,function(e){
			var cuenta_id = $(this).data('item');
			var parametros = {cuenta_id:cuenta_id,tipo:'sin_factura_activo'};
			var cuentaEliminada = moduloConfiguracionContabilidad.eliminarCuentaInventario(parametros);
			cuentaEliminada.done(function(data){
				if(!data.puede_eliminar){
					swal("Cuenta por Pagar", "La cuenta no se puede eliminar porque tiene transacciones", "warning");
				}else{
					self.mensaje(data);
					self.vista.tabla.find('div.item-cuenta').fadeIn('slow');
					self.vista.tabla.find('div.item-cuenta').remove();
					var arbol_cuenta = $.jstree.reference($('div#inventario_recibido_activo'))._model.data;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.es_padre){
							self.vista.tabla.find('div#inventario_recibido_activo').jstree('disable_node',elem.id);
						}else{
							self.vista.tabla.find('div#inventario_recibido_activo').jstree('enable_node',elem.id);
						}
					});
					self.vista.tabla.find('li#'+cuenta_id+' a').removeClass('jstree-clicked');
					self.vista.tabla.find('li#'+cuenta_id+' div').removeClass('jstree-wholerow-clicked');
				}
			});
		});
	},
	treeRender:function(data){
		var arbol = data;
		var self = this;
		$('div#inventario_recibido_activo').jstree(arbol);
		$('div#inventario_recibido_activo').jstree(true).redraw(true);
		$('div#inventario_recibido_activo').bind("loaded.jstree",
				function (event, data){
			self.vista.tabla.find("a:contains('1. Activo')").first().css("visibility","hidden");
			self.vista.tabla.find(".jstree-last .jstree-icon").first().hide();

			//busca el selecionado
		var selecionado = moduloConfiguracionContabilidad.getCuentaInventario({tipo:'sin_factura_activo'});
			selecionado.done(function(data){
				var obj =data;
				if(!_.isEmpty(obj)){

					self.vista.tabla.find('div#inventario_recibido_activo').jstree('select_node',obj[0].cuenta_id);
					var arbol_cuenta = $.jstree.reference($('div#inventario_recibido_activo'))._model.data;//.disable=true;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != obj[0].cuenta_id){
							self.vista.tabla.find('div#inventario_recibido_activo').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
		self.vista.tabla.find('div#inventario_recibido_activo').bind("select_node.jstree", function(e, data) {

			var cuenta = data.node;
			var contenido = '';

			self.vista.tabla.find('#id_seleccion').val(cuenta.id);
			contenido = '<div class="item-cuenta"><div class="pull-left icono-cerrar"><a id="close_inventario_sin_factura_activo" data-item="'+cuenta.id+'"><i class="fa fa-close fa-lg fa-border "></i></a></div><div class="pull-left text-right cuenta-texto">'+cuenta.text+'</div></div>';
			self.vista.tabla.find('#cuenta_seleccionada').html(contenido);

		});
	}
};

(function(){
	configInventarioRecibidoActivo.init();
})();
