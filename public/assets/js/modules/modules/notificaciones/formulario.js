var formularioNotificaciones = new Vue({
    el: '#crearNotificacionesForm',
    data: {
        catalogos:{
            operadores: window.operadores,
            estados : window.estados,
            usuarios:window.usuarios,
            notificaciones_tipos: window.notificaciones_tipo,
            roles:window.roles,
            transaccion: [],
            modulos: window.modulos,
           // dias: window.dias,
            categorias: window.categorias
        },
        detalle:{
            id:'',
            modulo_id:'',
            transaccion_id:'1',
            rol_id :[],
            usuario_id:[],
            categoria_item_id :'',
            operador_id : '',
            monto :'0.00',
            trasaccion_dias :'',
            notificacion:[],
            estado_id : 'activo',
            mensaje:''
        },
        empresa_id:window.empresa_id,

        config: {
            //vista: window.vista,
            enableWatch:false,
            select2:{width:'100%'},
            desabilitar: false
        },

    },

    components: {},
    ready: function () {
        Vue.nextTick(function () {
            if ($().ckeditor != undefined) {
                //inicializar ckeditor
                $('.inline-ckeditor').ckeditor(config);
            }
        });
    },
    computed: {

        getUsuarios:function () {
            var context = this;
            if(!_.isEmpty(roles)){
                var aux = [];
                _.forEach(context.catalogos.usuarios, function(user){
                    var incluido = false;
                    _.forEach(user.roles, function(user_role){
                        if ( _.indexOf(context.detalle.rol_id, _.toString(user_role.id)) > -1 && incluido == false){
                            incluido = true;
                            aux.push(user);
                        }
                     });
                });
                return aux;
            }
           return [];
        }
    },
    methods: {
        moduloSelect: function (modulo_id) {
            var self = this;
            Vue.http.options.emulateJSON = true;
           // var transaccion = moduloNotificacion.transaccion(modulo_id);
            Vue.http({
                url: phost() + 'notificaciones/ajax-transaccion',
                method: 'POST',
                headers: {
                    erptkn: tkn,
                },

                data: {
                    erptkn: tkn,
                    modulo_id: this.detalle.modulo_id,
                    empresa_id:self.empresa_id
                }
            }).then(function (response) {
                // success callback
                //Check Session
                if ($.isEmptyObject(response.data.session) == false) {
                    window.location = phost() + "login?expired";
                }
                Vue.nextTick(function () {
                    //Se carga el catalogo de estados o transacciones del modulo de pedido.
                   self.catalogos.transaccion = response.data.estados;
                });

            });
        },
        guardar: function () {
            console.log("guardar");
            var context = this;
            var $form = $("#crearNotificacionesForm");

            var formValidado = $form.validate();
            if (formValidado.form() === true) {
                var guardar = moduloNotificacion.guardar($form);
                guardar.done(function (data) {
                    var respuesta = $.parseJSON(data);
                    if (respuesta.estado == 200) {
                        //context.config.desabilitar = true;
                        Vue.nextTick(function () {
                            context.detalle.rol_id = [];
                            context.detalle.usuario_id =[];
                            context.detalle.notificacion =[];
                            context.detalle.mensaje='';
                        });
                        toastr.success(respuesta.mensaje);
                       // toastr.options.onHidden = function() { context.config.desabilitar = false; };
                       // $form.submit();
                        $form.trigger('reset');
                        //$('#notificacionesGrid').trigger('reloadGrid');
                        //window.top.location = 0;
                        context.recargar();
                    }else{
                        toastr.error(respuesta.mensaje);
                    }
                });
            }
        },
        limpiar: function () {

        },
        recargar:function () {
            var context = this;
            //var empresa_id = window.empresa_id;
            //Reload Grid
            $('#notificacionesGrid').setGridParam({
                url: phost() + 'notificaciones/ajax-listar',
                datatype: "json",
                postData: {
                    erptkn: tkn,
                    empresa_id:context.empresa_id
                }
            }).trigger('reloadGrid');
        }
    }
});