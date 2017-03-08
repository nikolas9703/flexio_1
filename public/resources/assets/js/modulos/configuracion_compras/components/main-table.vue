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
                url: phost() + 'configuracion_compras/ajax_listar_terminos_condiciones',
                colNames:['Modulo', 'Categor&iacute;a(s)', 'Descripci&oacute;n', 'Estado', '',  ''],
                colModel:[
                    {name:'modulo', index:'modulo', width:50, sortable:true},
                    {name:'categorias', index:'categorias', width:50, sortable:false, hidden:!context.mCompras(), hidedlg:!context.mCompras()},
                    {name:'descripcion', index:'descripcion', width:70, sortable:false},
                    {name:'estado', index:'estado', width:50, sortable:false, align:'center'},
                    {name:'options', index:'options', width: 40, sortable:false, align:'center'},
                    {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidden: true, hidedlg:true},
                ],
                postData: context.getPostData(),
                sortname: 'modulo',
                sortorder: "asc"
            }
        };
    },

    methods:{
        mCompras:function(){
            return this.config.msSelected == 'compras';
        },
        getPostData:function(){
            var context = this;
            return {
                erptkn: tkn,
                campo:{
                    grupo:context.config.msSelected
                }
            };
        },
        initMyJqueryEvents:function(){
            var context = this;
            $('body').on('click', '.editar-btn', function(){
                var id = $(this).data('id');
                $.ajax({
        			url: phost() + "configuracion_compras/ajax_get_termino_condicion",
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
            states_segment_url: phost() + "configuracion_compras/ajax_get_states_segment",
            title: 'modulo', //column jqrid for modal title
            export_url: '',//no apply
            states_update_url: phost() + "configuracion_compras/ajax_update_state",
        };
        this.initJqgrid(params);
        this.initMyJqueryEvents();
    }

}


</script>
