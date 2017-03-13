
Vue.directive('select2', require('./../../vue/directives/select2.vue'));

var form_crear_pago = new Vue({

	el: "#wrapper-content-div",

	data: {
		config: {
			vista: window.vista,
			select2: {width: '100%'},
			disableDetalle: false,
			modal:{id:'optionsModal', size:'sm'}
		},
		detalle: {
			categoria_id:'',
			categoria_nombre:'',
			//...
			id:'',
			nombre:'',
			requerido:'no',
			en_busqueda_avanzada:'no',
			estado:'activo'
		}
	},

	events:{
		eClearForm:function(){
			var context = this;
			context.detalle = $.extend(context.detalle, {
				id:'',
				nombre:'',
				requerido:'no',
				en_busqueda_avanzada:'no',
				estado:'activo'
			});
		},
		ePopulateDetalle:function(params){
			this.detalle = $.extend(this.detalle, params);
		}
	},

	components: {
		'details': require('./components/details.vue'),
		'main-table': require('./components/main-table.vue'),
		'modal': require('./../../vue/components/modal.vue')
	},

	ready: function () {
		var context = this;
		context.detalle = $.extend(context.detalle, JSON.parse(JSON.stringify(window.categoria)));
	}

});
