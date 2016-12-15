
var arbolCuentaActivo = {
	vars:{

			tab:'activo',//se usa para armar el json del catalogo de cuentas
			table:'table#arbol_activo_table',
			div:'div#arbol_activo_div',
			eliminarCuentaId:'close_cuenta_activo',
			eliminarCuenta:'a#close_cuenta_activo',
			cuentaSeleccionada:'div#cuenta_seleccionada_activo_div',
			seleccion_id:'input#activo_seleccion_id'

	},

	vista:{

		divCuentas: $('table#arbol_activo_table').find('div#arbol_activo_div')

	},

	rutas:{

		cuentaActivo: phost() +'contabilidad/ajax-cuenta-activo'

	},

	init:function(){

		this.cargar_cuenta();

	},

	cargar_cuenta:function(){

		var self = this;
		var cuentas = moduloConfiguracionContabilidad.getCatalogoCuentas({tipo_cuenta_id: 1});
		cuentas.done(function(data){
			self.treeRender(data);
		});

		//eliminar cuenta
		$(self.vars.table +' '+ self.vars.cuentaSeleccionada).on('click',self.vars.eliminarCuenta,function(e){

			var selfelement = this;
			var cuenta_id = $(this).data('item');
			var parametros = {cuenta_id:cuenta_id};

			//verificar si la cuenta tiene transacciones...
			$(selfelement).parent().parent().fadeIn('slow');
			$(self.vars.table).find(self.vars.seleccion_id).val(cuenta_id);
			$(selfelement).parent().parent().remove();

			var arbol_cuenta = $.jstree.reference($(self.vars.div))._model.data;
			$.each(arbol_cuenta,function(i, elem){
				if(elem.es_padre){
					self.vista.divCuentas.jstree('disable_node',elem.id);
				}else{
					self.vista.divCuentas.jstree('enable_node',elem.id);
				}
			});
			$(self.vars.table).find('li#'+cuenta_id+' a').removeClass('jstree-clicked');
			$(self.vars.table).find('li#'+cuenta_id+' div').removeClass('jstree-wholerow-clicked');


		});

	},

	treeRender:function(data){

		var self = this;
		var arbol = data;

		$(self.vars.div).jstree(arbol);
		$(self.vars.div).jstree(true).redraw(true);
		$(self.vars.div).bind(
			"loaded.jstree",
			function (event, data){
				//revisar la siguiente linea "contains"....
				//$(self.vars.table).find("a:contains('1. Activos')").css("visibility","hidden");
				//$(self.vars.table).find(".jstree-last .jstree-icon").first().hide();

				if(window.vista == "editar")
				{
					var cuentas = _.filter(JSON.parse(window.item.cuentas),function(o){return o.indexOf('activo') > -1});
					_.forEach(cuentas, function(cuenta){
						self.vista.divCuentas.jstree('select_node',cuenta.replace('activo:','').toString());
					});

				}
		});

		$(self.vars.table).find(self.vars.div).bind("select_node.jstree", function(e, data) {

			var cuenta = data.node;
			var contenido = '';

			//si es transaccional permito marcar, de lo contrario no permito.
			if(data.node.children.length == 0)
			{
				$(self.vars.table).find(self.vars.seleccion_id).val(cuenta.id);
				contenido = '	<div class="item-cuenta"><div class="pull-left icono-cerrar">';//se abren dos divs
				contenido+= '		<input type="hidden" name="cuentas[]" value="activo:'+cuenta.id+'">';
				contenido+= '		<a id="'+ self.vars.eliminarCuentaId +'" data-item="'+cuenta.id+'">';
				contenido+= '			<i class="fa fa-close fa-lg fa-border "></i>';
				contenido+= '		</a>';
				contenido+= '	</div>';
				contenido+= '	<div class="pull-left text-right cuenta-texto">'+cuenta.text+'</div></div>';//se cierran dos divs
				$(self.vars.table).find(self.vars.cuentaSeleccionada).html(contenido);
			}

		});

	}
};

(function(){
	arbolCuentaActivo.init();
})();
