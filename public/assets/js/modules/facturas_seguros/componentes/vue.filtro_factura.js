Vue.component('filtro_factura', {
	template:'#filtro_factura',
	data:function(){
		return {
			ordendesde_id: '',
			tipo_factura_id: '',
			disabled: false,
			formulario: '',
			ordenDesdeOptions: [
		    	{id: 'contrato_venta', nombre: 'Contrato de Venta'},
		    	{id: 'orden_venta', nombre: '&Oacute;rdenes Ventas'},
		    	{id: 'orden_trabajo', nombre: '&Oacute;rdenes de Trabajo'},
		    	{id: 'contrato_alquiler', nombre: 'Contrato de Alquiler'}
		    ],
			tipoFacturasOptions: typeof tiposFacturasArray != 'undefined' ? tiposFacturasArray : [],
			url: {
				orden_venta: 'ordenes_ventas/ajax-seleccionar-orden-venta',
				contrato_venta: 'contratos/ajax-contrato-info',
				contrato_alquiler: 'cargos/ajax-get-cargos',
				orden_trabajo: 'ordenes_trabajo/ajax-seleccionar-orden'
			}
		};
	},
	ready:function(){

		var scope = this;

		// si existe variable infofactura
		// cuando es editar factura
		if(typeof infofactura != 'undefined'){

			this.$nextTick(function () {

				//popular campos del filtro
				scope.ordendesde_id	= infofactura.formulario != '' ? infofactura.formulario : '';

				var tipo_factura_id = '';
				infofactura.formulario = 'orden_venta'
				if(infofactura.formulario.match(/orden_venta/gi)){
					tipo_factura_id = typeof infofactura[scope.ordendesde_id] != 'undefined' ? infofactura[scope.ordendesde_id][0].uuid_venta : '';
				}else if(infofactura.formulario.match(/contrato_venta/gi)){
					tipo_factura_id = typeof infofactura[scope.ordendesde_id] != 'undefined' ? infofactura[scope.ordendesde_id][0].uuid_contrato : '';
				}else if(infofactura.formulario.match(/contrato_alquiler/gi)){
					//tipo_factura_id = typeof infofactura[scope.ordendesde_id] != 'undefined' ? infofactura[scope.ordendesde_id][0].uuid_venta : '';
				}else if(infofactura.formulario.match(/orden_trabajo/gi)){
					//tipo_factura_id = typeof infofactura[scope.ordendesde_id] != 'undefined' ? infofactura[scope.ordendesde_id][0].uuid_contrato : '';
				}

				scope.tipo_factura_id = tipo_factura_id;
				scope.disabled = true;
				scope.$root.actualizar_chosen();
            });
		}
		if(typeof tipo_chosen != 'undefined'){
			setTimeout(function () {
			scope.ordendesde_id = tipo_chosen;
			scope.tipo_factura_id = contrato_alquiler_uuid;
			//scope.tipoFacturaInfo(contrato_alquiler_uuid);
			scope.$root.actualizar_chosen();
		}, 300);
		}

		/*if(typeof vista != 'undefined'){
			setTimeout(function () {
		  //scope.ordendesde_id = tipo_chosen;
			scope.tipo_factura_id = uuid_factura;
			//scope.tipoFacturaInfo(uuid_factura);
			scope.$root.actualizar_chosen();
		}, 300);
		} */

	},
	methods: {
		tipoFacturaInfo: function(tipo_factura_id){                   
			//verificar si existe variable "infofactura"
			//ya que para editar se popula desde cada componente
		/*	if(typeof infofactura != 'undefined'){
				return;
			}*/
			var scope = this;
			var response = this.$parent.ajax(this.url[this.ordendesde_id], {uuid: tipo_factura_id});
    		response.then(function (response) {

    			var items = typeof response.data.contratos_items != 'undefined' && response.data.contratos_items != null ? response.data.contratos_items : response.data.items;
                       
    			//datos de la factura
    			scope.$parent.$broadcast('popularDatosFactura', response.data);

    			//popular tabla items
    			scope.$parent.$broadcast('popularTablaItems', items);

            }, function (response) {
                // error callback
            });
		}
    },
	watch:{
		'ordendesde_id': function (nv, ov) {

			//Cambiar Tabla Item, segun seleccion
        	this.$parent.$broadcast('cambiarTablaItems', nv);

			if(nv==""){
				//limpiar formulario
				this.$parent.$refs.datos_factura.reset();
				this.$parent.actualizar_chosen();
				return;
			}

			this.formulario = nv;

        	//actualizar chosen
			this.$parent.actualizar_chosen();
		},
		'tipo_factura_id': function (nv, ov) {

			if(nv==""){
				//limpiar formulario
				this.$parent.$refs.datos_factura.reset();
				return;
			}

			this.tipoFacturaInfo(nv);
		},
	},
});
