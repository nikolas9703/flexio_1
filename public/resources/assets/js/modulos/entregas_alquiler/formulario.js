

var formularioCrearEntregaAlquiler = new Vue({

    el: '#form_crear_entrega_alquiler_div',

    ready: function ()
    {
        var context = this;

        context.runPluginsJquery();

        if(context.vista == 'editar')
        {
            var entrega_alquiler_json = JSON.parse(entrega_alquiler);
            context.comentario.comentarios = JSON.parse(JSON.stringify(window.coment));
            context.comentario.comentable_id = JSON.parse(JSON.stringify(entrega_alquiler_json.id));
            context.entrega_alquiler = entrega_alquiler_json;
            context.disabledEstado = false;
            context.disabledHeader = true;

            console.log('ENTREGA', entrega_alquiler_json);

            if(entrega_alquiler_json.estado_id > '2')//anulado o terminado
            {
                context.disabledEditar = true;
            }
        }

        if(window.acceso == '0'){context.disabledEditar = true;}
    },

    data: {

        comentario: {

            comentarios: [],
            comentable_type: 'Flexio\\Modulo\\EntregasAlquiler\\Models\\EntregasAlquiler',
            comentable_id: ''

        },

        config:{

            vista: window.vista

        },

        vista: vista,
        disabledHeader: false,
        disabledEstado: true,
        disabledEditar: false,


        entrega_alquiler: {
            id:'',
            empezar_desde_type: '',
            empezar_desde_id: '',
            codigo: codigo,
            cliente_id:'',
            created_by:'',
            vendedor_id: '',
            estado_id: '1',//por aprobar,
            saldo_cliente:0,
            credito_cliente:0,
            fecha_inicio_contrato:'',
            fecha_fin_contrato:'',
            fecha_entrega:''
            //fecha_contrato_alquiler:''
        },

        //catalogos
        empezables: empezables,
        clientes: clientes, //catalogos from controller
        vendedores: vendedores, //catalogos from controller
        usuarios: usuarios,
        estados: estados //catalogos from controller

    },

    components:{

        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    watch:{

        //al cambiar la fecha de inicio de contrato / empezar desde
        //se asigna al plugins la fecha minima permita
        //para realizar la entrega. No se esta usando directiva
        'entrega_alquiler.fecha_inicio_contrato': function(val, oldVal){

            var context = this;
            Vue.nextTick(function(){
                $(".fecha_entrega").data('daterangepicker').minDate = moment(val, 'DD/MM/YYYY');
            });

        },

        'entrega_alquiler.empezar_desde_id' : function (val, oldVal) {
                if (vista=='crear') {
                    this.cambiarEmpezable(val);
                }
        },

        'entrega_alquiler.cliente_id':function(val, oldVal){

            var context = this;
            if(val == ''){
                context.entrega_alquiler.saldo_cliente = 0;
                context.entrega_alquiler.credito_cliente = 0;
                return '';
            }

            var datos = $.extend({erptkn: tkn},{cliente_id:val});
            this.$http.post({
                url: window.phost() + "clientes/ajax-get-montos",
                method:'POST',
                data:datos
            }).then(function(response){

                if(_.has(response.data, 'session')){
                    window.location.assign(window.phost());
                    return;
                }
                if(!_.isEmpty(response.data)){

                    context.entrega_alquiler.saldo_cliente = response.data.saldo;
                    context.entrega_alquiler.credito_cliente = response.data.credito;

                }
            }).catch(function(err){
                window.toastr['error'](err.statusText + ' ('+err.status+') ');
            });

        }

    },

    methods: {

        runPluginsJquery: function(){

            //mientras se usa con jquery

            $('.fecha_entrega').daterangepicker({
                autoUpdateInput: false,
                timePicker24Hour: true,
                timePicker: true,
                timePickerIncrement: 5,
                singleDatePicker: true,
                showDropdowns: true
            });

            $('.fecha_entrega').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY H:mm'));
            });

            $('.fecha_entrega').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            //...

        },

        cambiarTipo: function (tipo)
        {
            if (_.isEmpty(tipo))
            {
                this.entrega_alquiler.empezar_desde_id = '';
            }
        },

        cambiarEmpezable: function(empezable_id)
        {

            var context = this;
            var empezable = _.find(context.empezables, function(empezable){
                return empezable.id==empezable_id;
            });

            if(_.isEmpty(empezable))
            {
                context.limpiarDatosContrato();
                context.$broadcast('cambiarEmpezable', []);
            }
            else
            {

                context.$broadcast('cambiarEmpezable', empezable.contratos_items);
                context.entrega_alquiler.cliente_id = empezable.cliente_id;
                context.entrega_alquiler.vendedor_id = empezable.created_by;
                context.entrega_alquiler.fecha_inicio_contrato = empezable.fecha_inicio;
                context.entrega_alquiler.fecha_fin_contrato = empezable.fecha_fin;



                //pendiente setear items....
            }

        },

        limpiarDatosContrato: function()
        {
            var context = this;
            context.entrega_alquiler.cliente_id = '';
            context.entrega_alquiler.vendedor_id = '';
        },

        guardar: function () {
            var context = this;
            var $form = $("#form_crear_entrega_alquiler");
            var tableErrors = $("#entregasAlquilerItemsErros");

            $form.validate({
                ignore: '',
                wrapper: '',
                errorPlacement: function (error, element) {

                    var self = $(element);
                    tableErrors.html(' ');
                    if (self.closest('table').length > 0) {
                        tableErrors.html('<label class"error" style="color:red;">Estos campos son obligatorios</label>');
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function (form) {
                    context.disabledHeader = false;
                    context.disabledEstado = false;
                    $('input, select').prop('disabled', false);
                    Vue.nextTick(function () {
                        form.submit();
                    });
                }
            });
        }

    }

});

Vue.nextTick(function () {
    formularioCrearEntregaAlquiler.guardar();
});
