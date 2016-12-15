

export default {

    methods:{

        limpiar: function(){

            var context = this;
            context.buscar = {nombre: '', nombre_item: '', categorias:[], estado:'', buscar_en:'', bodega_id:'', clientes:[], centros_facturacion:[]};
            $('#'+ context.dom.grid_id).setGridParam({
                url: phost() + 'series/ajax-listar',
                datatype: "json",
                postData: {erptkn: window.tkn, nombre: '', nombre_item: '', categorias:'', estado:'', buscar_en:'', bodega_id:'', clientes:'', centros_facturacion:''}
            });
            context.reloadGrid();

        },

        reloadGrid: function(){

            var context = this;
            Vue.nextTick(function(){
                $("#" + context.dom.grid_id).trigger('reloadGrid');
            });

        },

    }

};
