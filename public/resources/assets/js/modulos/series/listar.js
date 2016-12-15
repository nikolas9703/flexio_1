
Vue.directive('select2', require('./../../vue/directives/select2.vue'));

import listar from './mixins/listar';

var listar_series = new Vue({

    el: "#listar_series_div",

    mixins: [listar],

    data:{

        config: {

            vista: window.vista,
            select2:{width:'100%'},
            disableDetalle:false,
            modulo:'series'

        },

        catalogos:{

            categorias: window.categorias,
            estados: window.estados,
            bodegas: window.bodegas,
            clientes: window.clientes,
            aux:{}

        },

        dom: {

            no_records_id: 'seriesGridNoRecords',
            grid_id: 'seriesGrid',
            pager_id: 'seriesGridPager',
            modal_id: 'opcionesModal'

        },

        buscar:{
            nombre: '',
            nombre_item: '',
            categorias:[],
            estado:'',
            buscar_en:'',
            bodega_id:'',
            clientes:[],
            centros_facturacion:[]
        },

    },

    components:{

        'buscar': require('./components/buscar.vue'),
        'grid': require('./components/grid.vue')

    },

    ready:function(){

        var context = this;
        //....

    },
});
