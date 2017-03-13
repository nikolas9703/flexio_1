var cuentaBancoConfig = {

	vars:{

			table:'table#cuentas_bancos',
			div:'div#cuentas_activo_banco'

	},

	cuenta_de_bancos:[],
	vista:{
		guardarCuenta: $('button#btnGuardarBanco'),
		eliminarCuenta: 'a#close_cuenta_banco'
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

			if(self.cuenta_de_bancos.length === 0){
				console.log("error");
				return false;
			}
			var parametros = {id:self.cuenta_de_bancos};
			var guardar = moduloConfiguracionContabilidad.guardarCuentaBanco(parametros);
			guardar.done(function(data){
				$(self.vars.table).find("#id_seleccion").val("");
				self.mensaje(data);
				if(data.tipo ==='success'){
					var arbol_cuenta = $.jstree.reference($(self.vars.table).find(self.vars.div))._model.data;//.disable=true;
					$.each(arbol_cuenta,function(i, elem){
						if(_.includes(self.cuenta_de_bancos, elem.id)){
							$(self.vars.table).find(self.vars.div).jstree('disable_node',elem.id);
						}
					});
				}
			});
		});

		//Se ejecuta cuando hago click en la x de la izquierda (eliminar una cuenta)
		$(self.vars.table +' div#cuenta_seleccionada').on('click', self.vista.eliminarCuenta, function(e){
			//el id
			var selfelement = this;
			var cuenta_id = $(this).data('item');
			var parametros = {cuenta_id:cuenta_id};
			var cuentaEliminada = moduloConfiguracionContabilidad.eliminarCuentaBanco(parametros);
			cuentaEliminada.done(function(data){
				if(!data.puede_eliminar){
					swal("Cuenta de Bancos", "La cuenta no se puede eliminar porque tiene transacciones", "warning");
				}else{
					self.mensaje(data);
					$(selfelement).parent().parent().fadeIn('slow');
					self.cuenta_de_bancos = _.pull(self.cuenta_de_bancos,cuenta_id.toString());
					$(self.vars.table).find('#id_seleccion').val(self.cuenta_de_bancos);
					$(selfelement).parent().parent().remove();
					var arbol_cuenta = $.jstree.reference($(self.vars.div))._model.data;
					$(self.vars.table).find(self.vars.div).jstree('enable_node',cuenta_id.toString());
					$(self.vars.table).find('li#'+cuenta_id+' a').removeClass('jstree-clicked');
					$(self.vars.table).find('li#'+cuenta_id+' div').removeClass('jstree-wholerow-clicked');
				}
			});
		});
	},

	//se ejecuta cuando se carga la pagina (ready)
	treeRender:function(data){
		var arbol = data;
		var self = this;
		$(self.vars.div).jstree(arbol);
		$(self.vars.div).jstree(true).redraw(true);
		$(self.vars.div).bind("loaded.jstree",
			function (event, data){
				$(self.vars.table).find("a:contains('1. Activos')").css("visibility","hidden");
				$(self.vars.table).find(".jstree-last .jstree-icon").first().hide();

				//busca el selecionado
				var selecionado = moduloConfiguracionContabilidad.getCuentaBancos();
				selecionado.done(function(data){
					var obj =data;
					if(!_.isEmpty(obj)){
						var items = [];
						obj.forEach(function(att){
							items.push(att.cuenta_id.toString());
						});

						var arbol_cuenta = $.jstree.reference($(self.vars.div))._model.data;//.disable=true;

					$.each(arbol_cuenta,function(i, elem){
						if(_.includes(items,elem.id)){
							$(self.vars.table).find(self.vars.div).jstree('disable_node',elem.id);
							$(self.vars.table).find(self.vars.div).jstree('select_node',elem.id);
						}
					});


				}
			});
		});

		//se ejecuta cuando hago click en una cuenta transaccional de la derecha
		$(self.vars.table).find(self.vars.div).bind("select_node.jstree", function(e, data) {

			var cuenta = data.node;

			//si es transaccional permito marcar, de lo contrario no permito.
			if(data.node.children.length == '0')
			{
				self.cuenta_de_bancos.push(cuenta.id);//se usa para el post (ajax)
				$(self.vars.table).find(self.vars.div).jstree('disable_node',cuenta.id);

				var contenido = '';
				$(self.vars.table).find('#id_seleccion').val(self.cuenta_de_bancos);
				contenido = '<div class="item-cuenta"><div class="pull-left icono-cerrar"><a id="close_cuenta_banco" data-item="'+cuenta.id+'"><i class="fa fa-close fa-lg fa-border "></i></a></div><div class="pull-left text-right cuenta-texto">'+cuenta.text+'</div></div>';
				$(self.vars.table).find('#cuenta_seleccionada').append(contenido);
			}

		});

	}
};

(function(){
	cuentaBancoConfig.init();
})();
