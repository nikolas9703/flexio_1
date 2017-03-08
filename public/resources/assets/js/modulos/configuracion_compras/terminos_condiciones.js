
Vue.directive('select2', require('./../../vue/directives/select2.vue'));
Vue.directive('tinymce', require('./../../vue/directives/tinymce.vue'));

var form_crear_pago = new Vue({

	el: "#terminos-condiciones-div",

	data: {
		catalogos:{
			categorias:window.categorias
		},
		config: {
			vista: window.vista,
			msSelected: window.localStorage.getItem('ms-selected'),
			select2: {width: '100%'},
			disableDetalle: false,
			modal:{id:'optionsModal', size:'sm'},
			tinymce:{id:'editor1'}
		},
		detalle: {
			id:'',
			modulo:'',
			categorias:[],
			descripcion:'',
			estado:'activo',
			content:''
		}
	},

	events:{
		eClearForm:function(){
			var context = this;
			context.detalle = $.extend(context.detalle, {
				id:'',
				modulo:'',
				categorias:[],
				descripcion:'',
				estado:'activo',
				content:''
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
	}

});
