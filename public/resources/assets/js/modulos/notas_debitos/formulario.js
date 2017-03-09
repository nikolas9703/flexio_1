// jshint esversion:6

Vue.directive('select2', require('./../../vue/directives/select2.vue'));
Vue.directive('select2ajax', require('./../../vue/directives/select2ajax.vue'));
Vue.directive('inputmask', require('./../../vue/directives/inputmask.vue'));
Vue.directive('datepicker', require('./../../vue/directives/datepicker.vue'));
Vue.filter('currencyDisplay', require('./../../vue/filters/currency-two-way.vue'));
import {
    formEmpezable
} from './../../vue/state/empezable';

Vue.http.options.emulateJSON = true;
var notaDebitoFormulario = new Vue({
    el:'#crear_nota_debito',
    data:{
        header_empezable: formEmpezable,
        comentario: {

            comentarios: [],
            comentable_type: "Flexio\\Modulo\\NotaDebito\\Models\\NotaDebito",
            comentable_id: '',

        },

        catalogos:{

            proveedores: [],
            centros_contables: window.centros_contables,
            usuarios: window.usuarios,
            estados: window.estados,
            cuentas: window.cuentas,
            impuestos: window.impuestos,
            empresa: window.empresa

        },

        config:{

            vista: window.vista,
            enableWatch: false,
            select2: {width:'100%'},
            datepicker2: {dateFormat: "dd/mm/yy"},
            inputmask:{

                cantidad: {'mask':'9{1,4}','greedy':false},
                descuento: {'mask':'9{1,2}[.9{0,2}]','greedy':false},
                currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
                currency2: {'mask':'9{1,8}[.9{0,4}]','greedy':false}

            },
            disableEmpezarDesde:false,
            disableDetalle:false,
            disableArticulos:false,
            modulo:'notas_debitos'

        },

        detalle:{
            id:'',
            factura_id:0,
            listas_loader:false,
            proveedor_id:'',
            prov_id:'',
            proveedor:{saldo_pendiente:0,credito:0},
            proveedores:[],
            monto_factura:0,
            saldo_factura:0,
            fecha_factura:'',
            fecha:moment().format('DD/MM/YYYY'),
            creado_por:window.usuario_id,
            centro_contable_id:'',
            estado:'por_aprobar',
            no_nota_credito:'',
            //total, subtotal, impuesto
            filas:[
                {id:'', cuenta_id:'', monto:0, precio_total:0, descripcion: '', impuesto_total:0, impuesto_id:'', item_id:0}
            ]
        },

        empezable:{

            label:'Aplicar nota de cr&eacute;dito de proveedor a',
            type:'',
            types:[
                //al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
                {id:'factura',nombre:'Factura'},
            ],
            id:'',
            facturas:window.facturas
        },

        //se heredand e la estructura anterior
        tablaError:'',
        botonDisabled: false,
        estado_actual:''
    },

    components:{

        'empezar_desde': require('./../../vue/components/empezar-desde.vue'),
        'detalle': require('./components/detalle.vue'),
        'nota-debito-items': require('./components/nota-debito-items.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    ready:function(){

        var context = this;

        if(context.config.vista === 'ver'){

            Vue.nextTick(function(){
                context.empezable = $.extend(context.empezable, JSON.parse(JSON.stringify(window.empezable)));
                context.detalle = $.extend(context.detalle, JSON.parse(JSON.stringify(window.nota_debito)));
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.nota_debito.landing_comments));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.nota_debito.id));
                context.estado_actual = context.detalle.estado;
                context.config.disableEmpezarDesde = true;
                if(context.detalle.estado == 'aprobado' || context.detalle.estado == 'anulado')
                {
                    context.config.disableDetalle = true;
                }

            });

        }


    },

    methods:{

        guardar: function () {
            var context = this;
            var $form = $("#form_crear_notaDebito");

            $form.validate({
                //debug:true,
                ignore: '',
                wrapper: '',
                errorPlacement: function (error, element) {
                    var self = $(element);
                    if (self.closest('div').hasClass('input-group') && !self.closest('table').hasClass('itemsTable')) {
                        element.parent().parent().append(error);
                    }else if(self.closest('div').hasClass('form-group') && !self.closest('table').hasClass('itemsTable')){
                        self.closest('div').append(error);
                    }else if(self.closest('table').hasClass('itemsTable')){
                        $form.find('.tabla_dinamica_error').empty().append('<label class="error">Estos campos son obligatorios (*).</label>');
                    }else{
                        error.insertAfter(error);
                    }
                },
                submitHandler: function (form) {
                    $('input, select').prop('disabled', false);
                    $('form').find(':submit').prop('disabled',true);
                    form.submit();
                }
            });
        },



    },
    watch:{
        'empezable.type':function(){
            var context = this;
            var ajaxurl = '';
            if(empezable_type.value=='factura'){
                ajaxurl = 'ajax_catalogo/facturas_nota_debito';
            }

            $("#empezable_id").select2({
                width:'100%',
                ajax: {
                    url: phost() + ajaxurl,
                    method:'POST',
                    dataType: 'json',
                    delay: 100,
                    cache: true,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            limit: 10,
                            erptkn: tkn
                        };
                    },
                    processResults: function (data, params) {
                        console.log(data);
                        let resultados = data.map(resp=> [{'id': resp.id,'text': resp.nombre}]).reduce((a, b) => a.concat(b),[]);
                        context.empezable.facturas = data;
                        return {results:resultados};
                    },
                    escapeMarkup: function (markup) { return markup; },
                }
            });
        },

        'detalle.factura_id':function(val, oldVal){

            if(this.config.vista != 'crear')return;

            if(val === ''  || val === null){
                return '';
            }

            var context = this;
            var prov_temp=this.detalle.proveedor_id;

            this.detalle.proveedor_id=null;
            Vue.nextTick(function () {
                context.detalle.proveedor_id=prov_temp;
            });
            var datos = $.extend({erptkn: tkn},{id:val});
            context.detalle.listas_loader=true;
            this.$http.post({
                url: window.phost() + "notas_debitos/ajax-get-nota",
                method:'POST',
                data:datos
            }).then(function(response){
                if(_.has(response.data, 'session')){
                    window.location.assign(window.phost());
                    return;
                }
                if(!_.isEmpty(response.data)){
                    context.detalle.listas_loader=false;
                    context.detalle.filas = response.data.filas;
                    context.detalle.proveedor_id = response.data.proveedor_id;
                }
            });
        }
    }

});

Vue.nextTick(function () {
    notaDebitoFormulario.guardar();
});
