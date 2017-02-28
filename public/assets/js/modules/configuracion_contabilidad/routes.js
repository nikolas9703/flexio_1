var moduloConfiguracionContabilidad = (function() {
	return {
                eliminarCuentaContrato: function(element) {
                    return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-contratos', $.extend({
				erptkn: tkn
			}, element));
                },
                getCuentaContrato: function (element) {
                    return $.post(phost() + 'configuracion_contabilidad/ajax-get-cuentas-contrato', $.extend({
				erptkn: tkn
			}, element));
                },
                guardarContratos: function(element) {

			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-contratos', $.extend({
				erptkn: tkn
			}, element));

                },
		guardarImpuesto:function(element){
			var parametros = $(element).serialize();
			return $.post(phost() + 'contabilidad/ajax-guardar-impuesto', parametros);
		},
		cambiarEstadoImpuesto:function(parametros){
			return $.post(phost() + 'contabilidad/ajax-cambiar-estado-impuesto', $.extend({
				erptkn: tkn
			}, parametros));
		},
		getCuentaActivos:function(){
			return $.post(phost() + 'configuracion_contabilidad/ajax-cuenta-activo', {erptkn: tkn});
		},
		cuentaPlanilla:function(){
			return $.post(phost() + 'configuracion_contabilidad/ajax-cuenta-planilla', {erptkn: tkn});
		},
		getCatalogoCuentas:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-catalogo-cuentas', $.extend({
				erptkn: tkn
			}, parametros));
		},
		guardarCuentaCobro:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-por-cobrar', $.extend({
				erptkn: tkn
			}, parametros));
		},guardarCuentaPlanilla:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-planilla', $.extend({
				erptkn: tkn
			}, parametros));
		},
		getCuentaPorCobrar:function(){
			return $.post(phost() + 'configuracion_contabilidad/ajax-get-cuenta-por-cobrar', {erptkn: tkn});
		},
		eliminarCuentaCobro:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-cobrar', $.extend({
				erptkn: tkn
			}, parametros));
		},
		eliminarCuentaPlanilla:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-planilla', $.extend({
				erptkn: tkn
			}, parametros));
		},
		getSeleccionCajaMenuda: function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-seleccionar-caja-menuda', {erptkn: tkn});
		},
		guardarCuentaCajaMenuda:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-caja-menuda', $.extend({
				erptkn: tkn
			}, parametros));
		},
		eliminarCuentaCajaMenuda:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-caja-menuda', $.extend({
				erptkn: tkn
			}, parametros));
		},
		getCuentaPasivo:function(){
		  return $.post(phost() + 'configuracion_contabilidad/ajax-cuenta-pasivo', {erptkn: tkn});
	  },
		getCuentaPorPagar:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-get-cuenta-por-pagar', $.extend({
				erptkn: tkn
			}, parametros));
		},
		guardarCuentaPagarProvedor:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-proveedor-por-pagar', $.extend({
				erptkn: tkn
			}, parametros));
		},
		eliminarCuentaProveedor:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-por-pagar', $.extend({
				erptkn: tkn
			}, parametros));
		},
		getCuentaBancos:function(){
			//get cuenta banco seleccionada
			return $.post(phost() + 'configuracion_contabilidad/ajax-cuenta-banco', {erptkn: tkn});
		},
		guardarCuentaBanco:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-banco', $.extend({
				erptkn: tkn
			}, parametros));
		},
		eliminarCuentaBanco:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-banco', $.extend({
				erptkn: tkn
			}, parametros));
		},
		getCuentaPorAbonar:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-get-cuenta-abono', $.extend({
				erptkn: tkn
			}, parametros));
		},
		guardarCuentaPorAbonar:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-abono', $.extend({
				erptkn: tkn
			}, parametros));
		},
		eliminarCuentaAbono:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-abono', $.extend({
				erptkn: tkn
			}, parametros));
		},
		getCuentaInventario:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-get-cuenta-inventario', $.extend({
				erptkn: tkn
			}, parametros));
		},
		guardarCuentaInventario:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-inventario', $.extend({
				erptkn: tkn
			}, parametros));
		},
		eliminarCuentaInventario:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-inventario', $.extend({
				erptkn: tkn
			}, parametros));
		},
		getCuentaPlanilla:function(){
			return $.post(phost() + 'configuracion_contabilidad/ajax-get-cuenta-planilla', {erptkn: tkn});
		},
		getCuentaAseguradoraPagar:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-get-cuenta-aseguradora-pagar', $.extend({
				erptkn: tkn
			}, parametros));
		},eliminarCuentaAseguradorapagar:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-aseguradora-pagar', $.extend({
				erptkn: tkn
			}, parametros));
		},guardarCuentaAseguradoraPagar:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-aseguradora-por-pagar', $.extend({
				erptkn: tkn
			}, parametros));
		},getCuentaAgentePagar:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-get-cuenta-agente-pagar', $.extend({
				erptkn: tkn
			}, parametros));
		},eliminarCuentaAgentepagar:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-agente-pagar', $.extend({
				erptkn: tkn
			}, parametros));
		},guardarCuentaAgentePagar:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-agente-pagar', $.extend({
				erptkn: tkn
			}, parametros));
		},getCuentaRemesaEntrante:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-get-cuenta-remesa-entrante', $.extend({
				erptkn: tkn
			}, parametros));
		},eliminarCuentaRemesaEntrante:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-remesa-entrante', $.extend({
				erptkn: tkn
			}, parametros));
		},guardarCuentaRemesaEntrante:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-remesa-entrante', $.extend({
				erptkn: tkn
			}, parametros));
		},

		getCuentaRemesaSaliente:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-get-cuenta-remesa-saliente', $.extend({
				erptkn: tkn
			}, parametros));
		},eliminarCuentaRemesaSaliente:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-remesa-saliente', $.extend({
				erptkn: tkn
			}, parametros));
		},guardarCuentaRemesaSaliente:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-remesa-saliente', $.extend({
				erptkn: tkn
			}, parametros));
		},

		getCuentaAseguradoraCobrar:function(){
			return $.post(phost() + 'configuracion_contabilidad/ajax_get_cuenta_aseguradora_cobrar', {erptkn: tkn});
		},
		eliminarCuentaAseguradoraCobrar:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-eliminar-cuenta-aseguradora-cobrar', $.extend({
				erptkn: tkn
			}, parametros));
		},
		guardarCuentaAseguradoraCobro:function(parametros){
			return $.post(phost() + 'configuracion_contabilidad/ajax-guardar-cuenta-aseguradora-cobrar', $.extend({
				erptkn: tkn
			}, parametros));
		},
	};
})();
