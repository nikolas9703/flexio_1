
Vue.directive('select2', require('./../../vue/directives/select2.vue'));

var form_usuarios = new Vue({

    el: "#form_usuarios_div",

    data:{

        config: {

            vista: window.vista,
            select2:{width:'100%'},
            disableDetalle:false,
            modulo:'usuarios'

        },

        catalogos:{

            empresa: window.empresa,
            rol: window.rol,
            roles: window.roles,
            centros_contables: window.centros_contables,
            tipos_subcontrato: window.tipos_subcontrato,
            categorias:[],
            aux:{}

        },

        detalle:{
            id:'',
            nombre: '',
            apellido: '',
            email:'',
            rol:'',
            password:'',
            repetir_password:'',
            roles:'',
            tipos_subcontrato:[],
            centros_contables:[],
            categorias:[],
        },

    },

    components:{

        'listar-usuarios': require('./components/listar-usuarios.vue')

    },
    created(){
        this.buscarCategoria();
    },
    computed:{

        todosOptionSelected:function(){

            var context = this;
            var todos_option = _.find(context.detalle.centros_contables, function(centro_contable){
                return centro_contable == 'todos';
            });

            return typeof todos_option !== 'undefined' ? true : false;
        },
        categoriaSelectOption(){
             var context = this;
            var todos_option = _.find(context.detalle.categorias, function(categoria){
                return categoria == 'todos';
            });

            return typeof todos_option !== 'undefined' ? true : false;
        },
        getCentrosContables:function(){

            var context = this;
            if(context.todosOptionSelected){
                return [];
            }
            return context.catalogos.centros_contables;
        },

        getCategorias:function(){

            var context = this;
            if(context.categoriaSelectOption){
                return [];
            }
            return context.catalogos.categorias;
        }

    },

    methods:{

        reloadGrid: function(){

            var context = this;
            Vue.nextTick(function(){
                $("#usuariosGrid").trigger('reloadGrid');
            });

        },

        clearForm: function(){

            var context = this;
            context.detalle = {id:'', nombre: '', apellido: '', email:'', rol:'',password:'', repetir_password:'', roles:'', centros_contables:[],categorias:[]};

        },

        guardar: function () {

            var context = this;

            if($('#crearUsuarioForm').validate().form() === false){
                return false;
            }

            context.config.disableDetalle = true;
            $.ajax({
                url: phost() + "usuarios/ajax-guardar-usuario",
                type: "POST",
                data: $.extend({erptkn: tkn, empresa_id:context.catalogos.empresa.id}, context.detalle),
                dataType: "json",
                success: function (response) {
                    if (!_.isEmpty(response)) {
                        if(response.error === false){
                            toastr.success(response.mensaje);
                            context.reloadGrid();
                            context.clearForm();
                        }else if(response.error === true){
                            toastr.error(response.mensaje);
                        }
                        context.config.disableDetalle = false;
                    }
                }
            });


        },
        postAjax(ajaxUrl, datos) {
			return this.$http.post({
				url: window.phost() + ajaxUrl,
				method: 'POST',
				data: datos
			});
		},
        logout(response) {
			if (_.has(response.data, 'session')) {
				window.location.assign(window.phost());
				return;
			}
		},
        buscarCategoria(){
            var datos = {
				erptkn: tkn
			};
			var categorias = this.postAjax('inventarios/ajax_categoria', datos);
            var self = this;
            categorias.then((response) => {
				this.logout(response);
				this.$nextTick(function () {
					self.catalogos.categorias = response.data;
				});
			});
        }

    },

    ready:function(){

        var context = this;
        //....

    },
});

Vue.nextTick(function(){
    $.validator.setDefaults({
        errorPlacement: function(error, element){
            return true;
        }
    });
    $('#crearUsuarioForm').validate({
        focusInvalid: true,
        ignore: '',
        wrapper: ''
    });
});
