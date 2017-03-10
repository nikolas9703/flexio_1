var configCuentaAnticipos = {
        vista:{
		guardarCuenta: $('button#btnGuardarRetencionPasivo'),
		eliminarCuenta: 'a#close_retencion_pasivo'
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

                //function de Guaradar
		self.vista.guardarCuenta.click(function(){
                                var cuenta_cobrar = $('table#cuentas_retencion').find("#id_seleccion").val();
                                if(cuenta_cobrar.length === 0){
                                        console.log("error");
                                        return false;
                                }

                                var parametros = {id:cuenta_cobrar,tipo:'retencion_pasivo'};
              //guardar cuenta
                                var guardar = moduloConfiguracionContabilidad.guardarContratos(parametros);
              guardar.success(function(){
                self.vista.guardarCuenta.html('<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...');
              });
                                guardar.done(function(data){
                self.vista.guardarCuenta.html('Guardar');
                                        $('table#cuentas_retencion').find("#id_seleccion").val("");
                                        self.mensaje(data);
                                        if(data.tipo ==='success'){
                                                var arbol_cuenta = $.jstree.reference($('table#cuentas_retencion').find('div#cuenta_retencion'))._model.data;//
                                                $.each(arbol_cuenta,function(i, elem){
                                                        if(elem.id != cuenta_cobrar){
                                                                $('table#cuentas_retencion').find('div#cuenta_retencion').jstree('disable_node',elem.id);
                                                        }
                                                });
                                        }
                                });
                        });

                        //function de eliminar  cuenta_seleccionada

		$('table#cuentas_retencion div#cuenta_seleccionada').on('click',this.vista.eliminarCuenta,function(e){
			var cuenta_id = $(this).data('item');

			var parametros = {cuenta_id:cuenta_id,tipo:'retencion_pasivo'};
			var cuentaEliminada = moduloConfiguracionContabilidad.eliminarCuentaContrato(parametros);
			cuentaEliminada.done(function(data){
				if(!data.puede_eliminar){
					swal("Cuenta por Pagar", "La cuenta no se puede eliminar porque tiene transacciones", "warning");
				}else{
					self.mensaje(data);
					$('table#cuentas_retencion').find('div.item-cuenta').fadeIn('slow');
					$('table#cuentas_retencion').find('div.item-cuenta').remove();
					var arbol_cuenta = $.jstree.reference($('div#cuenta_retencion'))._model.data;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.es_padre){
							$('table#cuentas_retencion').find('div#cuenta_retencion').jstree('disable_node',elem.id);
						}else{
							$('table#cuentas_retencion').find('div#cuenta_retencion').jstree('enable_node',elem.id);
						}
					});
					$('table#cuentas_retencion').find('li#'+cuenta_id+' a').removeClass('jstree-clicked');
					$('table#cuentas_retencion').find('li#'+cuenta_id+' div').removeClass('jstree-wholerow-clicked');
				}
			});
		});

        },

        treeRender:function(data){
		var arbol = data;
		$('div#cuenta_retencion').jstree(arbol);
		$('div#cuenta_retencion').jstree(true).redraw(true);
		$('div#cuenta_retencion').bind("loaded.jstree",
				function (event, data){
			$('table#cuentas_retencion').find("a:contains('1. Activo')").first().css("visibility","hidden");
			$('table#cuentas_retencion').find(".jstree-last .jstree-icon").first().hide();

			//busca el selecionado
		var selecionado = moduloConfiguracionContabilidad.getCuentaContrato({tipo:'retencion_pasivo'});
			selecionado.done(function(data){
				var obj =data;
				if(!_.isEmpty(obj)){
					$('table#cuentas_retencion').find('div#cuenta_retencion').jstree('select_node',obj[0].cuenta_id);
					var arbol_cuenta = $.jstree.reference($('div#cuenta_retencion'))._model.data;//.disable=true;
					$.each(arbol_cuenta,function(i, elem){
						if(elem.id != obj[0].cuenta_id){
							$('table#cuentas_retencion').find('div#cuenta_retencion').jstree('disable_node',elem.id);
						}
					});
				}
			});
		});
		$('table#cuentas_retencion').find('div#cuenta_retencion').bind("select_node.jstree", function(e, data) {

			var cuenta = data.node;
			var contenido = '';

			$('table#cuentas_retencion').find('#id_seleccion').val(cuenta.id);
			contenido = '<div class="item-cuenta"><div class="pull-left icono-cerrar"><a id="close_retencion_pasivo" data-item="'+cuenta.id+'"><i class="fa fa-close fa-lg fa-border "></i></a></div><div class="pull-left text-right cuenta-texto">'+cuenta.text+'</div></div>';
			$('table#cuentas_retencion').find('#cuenta_seleccionada').html(contenido);

		});
	}
};

(function(){
	configCuentaAnticipos.init();
})();
