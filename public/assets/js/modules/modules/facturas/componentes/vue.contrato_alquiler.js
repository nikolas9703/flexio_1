Vue.component('contrato_alquiler', {
    template:'#contrato_alquiler',
    props:{
    	categorias: Array,
        impuestos: Array,
        cuenta_transaccionales: Array,
        factura: Object,
    },
    data:function(){

    	return {
        	items: [],
        	cargos_adicionales_checked: 'false',
        };
    },
    ready:function(){

    	var scope = this;
    	var togglecargoadicional = document.querySelector('.toggle-cargoadicional');
    	var switchery = new Switchery(togglecargoadicional, {color:"#1ab394", size: 'small'});

    	// mostrar ocultar cargos adicionales
    	// plugin: switchery
    	togglecargoadicional.onchange = function() {
    		scope.cargos_adicionales_checked = this.checked ? 'true' : 'false';
    	};

		//si existe variable infofactura
		if(typeof infofactura != 'undefined'){

			var items_alquiler = _.filter(infofactura.items, function(o) { return o.item_adicional == 0; });
			var items_adicionales = _.filter(infofactura.items, function(o) { return o.categoria_id !=0 && o.item_adicional==1; });

			scope.$nextTick(function () {
				// popular items de alquiler
				//scope.$refs.items.$emit('popularTablaItems', items_alquiler);

				// popular items adicionales
				scope.$refs.items_adicionales.$emit('popularTablaItemsAdicionales', items_adicionales);

				if(items_adicionales.length > 0){

					//Marcar checkbox item adicionales
					scope.cargos_adicionales_checked = 'true';

					switchery.bindClick();
				}
			});
		}
	},
	events:{
    	//
    	// Popular tabla de items
    	// al seleccionar, empezar factura desde
    	// (orden venta, contrato venta, etc)
    	//
    	popularTablaItems: function(items) {
    		//console.log('llego a qui.....');
    		//popular tabla items alquiler
    		this.$refs.items.$emit('popularTablaItems', items);
    	}
	},
    methods:{
    	calcularPrecioTotal: function(index) {
        	this.$parent.calcularPrecioTotal(index);
        },
    	// Popular campo Items
    	// segun categoria seleccionada
    	popularItems: function(e, index, item, items) {
    		e.preventDefault();

            this.$parent.popularItems(e, index, item, items);
    	},
    	//
    	// Popular Campo de Unidad y Atributo
    	// segun Item Seleccionado
    	//
    	popularUnidadAtributo: function(e, item, index) {

    		e.preventDefault();

    		this.$parent.popularUnidadAtributo(e, item, index);
    	},
    	calcularPrecioSegunUnidad: function(e, item, index) {
    		e.preventDefault();
    		this.$parent.calcularPrecioSegunUnidad(e, item, index);
    	},
    	resetItems: function(){
    		this.$refs.items.resetItems();
    	}
    }
});
