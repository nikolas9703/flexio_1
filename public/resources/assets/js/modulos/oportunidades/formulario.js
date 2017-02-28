

var formularioCrearOportunidades = new Vue({

    el: '#form_crear_oportunidad_div',

    components:{

        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    ready: function ()
    {
        var context = this;

        if (context.vista == 'editar'){
            context.oportunidad = window.oportunidad;
            context.comentario.comentarios = context.oportunidad.comentario_timeline ;
            context.comentario.comentable_id = context.oportunidad.id;
            context.disabledEstado = false;

            if (context.oportunidad.estado_id > '2')//anulado o terminado
            {
                context.disabledEditar = true;
            }
        }

        Vue.nextTick(function(){
            context.config.enableWatch = true;
        });
    },

    data: {

        comentario: {

            comentarios: [],
            comentable_type: 'Flexio\\Modulo\\Oportunidades\\Models\\Oportunidades',
            comentable_id: '',

          },

          config: {
              vista: window.vista,
              enableWatch: false
          },

        vista: vista,
        disabledHeader: false,
        disabledEstado: true,
        disabledEditar: false,


        oportunidad: {
            id:'',
            empezar_desde_type: '',
            empezar_desde_id: '',
            nombre: '',
            monto: '',
            fecha_cierre: '',
            asignado_a_id: window.usuario_id,
            etapa_id: '1'
        },

        //catalogos
        clientes: window.clientes, //catalogos from controller
        clientes_potenciales: window.clientes_potenciales, //catalogos from controller
        vendedores: window.vendedores, //catalogos from controller
        estados: window.estados //catalogos from controller

    },

    watch:{

        'oportunidad.empezar_desde_type': function(val, oldVal){

            var context = this;
            if(context.config.enableWatch && val.length == '0')
            {
                context.oportunidad.empezar_desde_id = '';
            }

        }

    },

    methods: {

        crearCotizacion: function(form){

            var context = this;
            var opcionesModal = $('#optionsModal');
            var botones = '';

            botones += '    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">';
            botones += '        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">';
            botones += '            <button class="btn btn-danger btn-block btn-crear-cotizacion" data-value="0"><i class="fa fa-times"></i> No</button>';
            botones += '        </div>';
            botones += '        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">';
            botones += '            <button class="btn btn-info btn-block btn-crear-cotizacion" data-value="1"><i class="fa fa-check"></i> Si</button>';
            botones += '        </div>';
            botones += '    </div>';

            opcionesModal.find('.btn-crear-cotizacion').unbind();
            opcionesModal.find('.modal-title').empty().append('¿Desea crear una cotizaci&oacute;n para esta oportunidad?');
            opcionesModal.find('.modal-body').empty().append(botones);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
            opcionesModal.find('.btn-crear-cotizacion').on('click',function(){
                var boton = $(this);
                $(form).find('#crear_cotizacion').val(boton.data('value'));
                opcionesModal.modal('hide');
                form.submit();
            });

        },

        guardar: function () {
            var context = this;
            var $form = $("#form_crear_oportunidad");

            $form.validate({
                ignore: '',
                wrapper: '',
                errorPlacement: function (error, element) {
                    var self = $(element);
                    if (self.closest('div').hasClass('input-group')) {
                        element.parent().parent().append(error);
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function (form) {
                    context.disabledHeader = false;
                    context.disabledEstado = false;
                    $('input, select').prop('disabled', false);
                    Vue.nextTick(function () {

                        if(context.vista == 'crear'){

                            context.crearCotizacion(form);

                        }
                        else{

                            form.submit();

                        }

                    });
                }
            });
        }

    }

});

Vue.nextTick(function () {
    formularioCrearOportunidades.guardar();
});
