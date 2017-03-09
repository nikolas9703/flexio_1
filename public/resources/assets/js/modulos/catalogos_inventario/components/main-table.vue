<template>

    <table :id="table_id"></table>
    <div :id="table_id + 'pager'"></div>
    <div :id="table_id + 'no-result'"></div>

</template>

<script>

import init_jqgrid from './../../../vue/mixins/jqgrid';

export default {

    mixins: [init_jqgrid],

    props:{
        config: Object,
        detalle: Object,
        table_id: String
    },

    data:function(){
        var context = this;
        return {
            jqgrid:{
                url: phost() + 'catalogos_inventario/ajax-listar-datos-adicionales',
                colNames:['Nombre del campo', 'Requerido', 'En b&uacute;squeda avanzada', 'Estado', '',  ''],
                colModel:[
                    {name:'nombre', index:'nombre', width:70, sortable:true},
                    {name:'requerido', index:'requerido', width:50, sortable:false},
                    {name:'en_busqueda_avanzada', index:'en_busqueda_avanzada', width:60, sortable:false},
                    {name:'estado', index:'estado', width:50, sortable:false, align:'center'},
                    {name:'options', index:'options', width: 40, sortable:false, align:'center'},
                    {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidden: true, hidedlg:true},
                ],
                postData: context.getPostData(),
                sortname: 'nombre',
                sortorder: "asc"
            }
        };
    },

    methods:{
        getPostData:function(){
            var context = this;
            return {
                erptkn: tkn,
                campo:{categoria:window.categoria.categoria_id}
            };
        },
        initMyJqueryEvents:function(){
            var context = this;
            $('body').on('click', '.editar-btn', function(){
                var id = $(this).data('id');
                $.ajax({
        			url: phost() + "catalogos_inventario/ajax_get_dato_adicional",
        			type: "POST",
        			data: {erptkn:window.tkn, id:id},
        			dataType: "json",
        			success: function (response) {
        				if (!_.isEmpty(response)) {
        					context.$root.$emit('ePopulateDetalle', response.data);
        				}
        			}
        		});
                context.$root.$broadcast('eHideModal');
            });
        }
    },

    events:{
        eReloadGrid:function(){
            $('#'+ this.table_id).trigger('reloadGrid');
        }
    },

    ready: function(){
        var params = {
            states_segment_url: phost() + "catalogos_inventario/ajax_get_states_segment",
            title: 'nombre', //column jqrid for modal title
            export_url: phost()+ "catalogos_inventario/ajax_exportar_datos_adicionales",
            states_update_url: phost() + "catalogos_inventario/ajax_update_state",
        };
        this.initJqgrid(params);
        this.initMyJqueryEvents();
    }

}


</script>
