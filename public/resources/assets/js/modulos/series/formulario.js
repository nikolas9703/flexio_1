
Vue.directive('select2', require('./../../vue/directives/select2.vue'));

var form_series = new Vue({

    el: "#form_series_div",

    data:{

        comentario: {

            comentarios: [],
            comentable_type: "Flexio\\Modulo\\Inventarios\\Models\\Seriales",
            comentable_id: '',

        },

        config: {

            vista: window.vista,
            select2:{width:'100%'},
            inputmask: {

                cantidad: { mask: '9{1,4}', greedy: false},
                descuento: {'mask':'9{1,2}[.9{0,2}]','greedy':false},
                currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
                currency2: {'mask':'9{1,8}[.9{0,4}]','greedy':false}

            },
            disableDetalle:true,
            modulo:'series'

        },

        catalogos:{

            tipos: window.tipos,
            categorias: window.categorias,
            estados: window.estados,
            unidades: window.unidades,
            aux:{}

        },

        detalle:{
            nombre_item: 'hola mundo',
            adquisicion: 0,
            otros_costos: 0,
            depreciacion: 0,
            valor_actual: 0,
            estado: 'Disponible',
            fecha_compra: 'hoy',
            edad: 'hoy2',
            um:{modulo: '', numero: '', nombre: '', ubicacion: '', fecha_hora: ''},
            item:{
                codigo:'',
                nombre:'',
                descripcion:'',
                tipo_id:'',
                categorias:[],
                codigo_barra:'',
                estado:'9',//por aprobar
                item_unidades:[
                    {id_unidad:'', base:0, factor_conversion:1}
                ],
            }
        },

    },

    components:{

        'detalle': require('./../inventarios/components/detalle.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    computed:{



    },

    methods:{





    },

    ready:function(){

        var context = this;
        Vue.nextTick(function(){
            context.comentario.comentable_id = JSON.parse(JSON.stringify(window.serie.id));
            context.comentario.comentarios = JSON.parse(JSON.stringify(window.serie.comentario_timeline));
            Vue.nextTick(function(){
                context.detalle = JSON.parse(JSON.stringify(window.serie));
            });

            //ver historial
            $(document).ready(function(){
                $('a#verHistorial').click(function(){
                    window.location.href = phost() + "inventarios/trazabilidad/" + window.serie.uuid_serial;
                });
            });
            
        });

    },
});
